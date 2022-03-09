<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * Block layout and ajax service methods are defined in this file.
 *
 * @package     local_edwiserreports
 * @author      Yogesh Shirsath
 * @copyright   2022 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserreports;

defined('MOODLE_INTERNAL') or die;

use stdClass;
use moodle_url;
use context_system;
/**
 * Class Visits on site. To get the data related to Visits on site.
 */
class learnercourseprogressblock extends block_base {

    /**
     * Preapre layout for Visits on site
     * @return object Layout object
     */
    public function get_layout() {
        global $CFG;

        // Layout related data.
        $this->layout->id = 'learnercourseprogressblock';
        $this->layout->name = get_string('learnercourseprogressheader', 'local_edwiserreports');
        $this->layout->info = get_string('learnercourseprogressblockhelp', 'local_edwiserreports');
        $this->layout->filters = $this->get_filter();
        $this->layout->filter = '0';
        $this->layout->morelink = new moodle_url($CFG->wwwroot . "/local/edwiserreports/learner.php");
        $this->layout->pro = $this->image_icon('lock');

        // Add block view in layout.
        $this->layout->blockview = $this->render_block('learnercourseprogressblock', $this->block);

        // Set block edit capabilities.
        $this->set_block_edit_capabilities($this->layout->id);

        // Return blocks layout.
        return $this->layout;
    }

    /**
     * Prepare active users block filters
     * @return array filters array
     */
    public function get_filter() {
        global $OUTPUT, $USER, $COURSE, $USER, $DB;

        if (is_siteadmin() || has_capability('moodle/site:configview', context_system::instance())) {
            $courses = get_courses();
        } else {
            $courses = enrol_get_users_courses($USER->id);
        }
        unset($courses[$COURSE->id]);

        // Temporary course table.
        $coursetable = 'tmp_learner_courses';
        // Creating temporary table.
        utility::create_temp_table($coursetable, array_keys($courses));
        $sql = "SELECT c.id
                  FROM {{$coursetable}} ct
                  JOIN {course} c ON ct.tempid = c.id
                 WHERE c.enablecompletion <> 0";
        $records = $DB->get_records_sql($sql);

        // Droppping course table.
        utility::drop_temp_table($coursetable);
        $filtercourses = [
            0 => [
                'id' => 0,
                'fullname' => get_string('fulllistofcourses')
            ]
        ];

        if (!empty($records)) {
            foreach ($records as $record) {
                $filtercourses[] = [
                    'id' => $record->id,
                    'fullname' => $courses[$record->id]->fullname
                ];
            }
        }

        $sql = 'SELECT id, firstname, lastname
                  FROM {user}
                 WHERE confirmed = 1
              ORDER BY firstname asc';
        $recordset = $DB->get_recordset_sql($sql);
        $users = [[
            'id' => 0,
            'name' => get_string('allusers', 'search')
        ]];
        foreach ($recordset as $user) {
            $users[] = [
                'id' => $user->id,
                'name' => $user->firstname . ' ' . $user->lastname
            ];
        }
        return $OUTPUT->render_from_template('local_edwiserreports/learnercourseprogressblockfilters', [
            'courses' => $filtercourses
        ]);
    }

    /**
     * Get user using secret key or global $USER
     *
     * @return int
     */
    private function get_user() {
        global $USER;
        $secret = optional_param('secret', null, PARAM_TEXT);
        if ($secret !== null) {
            $authentication = new \local_edwiserreports\controller\authentication();
            $userid = $authentication->get_user($secret);
        } else {
            $userid = $USER->id;
        }
        return $userid;
    }

    /**
     * Use this method to return data for block.
     * Get Data for block
     * @param  object $filter Filter object
     * @return object         Response
     */
    public function get_data($filter = false) {
        global $DB, $COURSE;
        $course = $filter->course;
        $userid = $this->get_user();
        $labels = [];
        $progress = [];
        if ($course === 0) { // Course is selected in dropdown.
            if (is_siteadmin($userid) || has_capability('moodle/site:configview', context_system::instance(), $userid)) {
                $courses = get_courses();
            } else {
                $courses = enrol_get_users_courses($userid);
            }
            unset($courses[$COURSE->id]);

            // Temporary course table.
            $coursetable = 'tmp_learner_courses';
            // Creating temporary table.
            utility::create_temp_table($coursetable, array_keys($courses));

            $sql = "SELECT c.id
                  FROM {{$coursetable}} ct
                  JOIN {course} c ON ct.tempid = c.id
                 WHERE c.enablecompletion <> 0";
            $filteredcourses = $DB->get_records_sql($sql);

            $sql = "SELECT cp.courseid id, cp.progress
                      FROM {{$coursetable}} ct
                      JOIN {edwreports_course_progress} cp ON ct.tempid = cp.courseid
                      JOIN {course} c ON cp.courseid = c.id
                     WHERE cp.userid = :userid
                       AND c.enablecompletion <> 0";
            $params = ['userid' => $userid];
            $records = $DB->get_records_sql($sql, $params);
            // Droppping course table.
            utility::drop_temp_table($coursetable);
            $hasdata = false;
            if (!empty($records)) {
                foreach ($filteredcourses as $record) {
                    $labels[] = $courses[$record->id]->fullname;
                    $prog = isset($records[$record->id]) ? (int)$records[$record->id]->progress : 0;
                    if ($prog > 0) {
                        $hasdata = true;
                    }
                    $progress[] = $prog;
                }
                if (!$hasdata) {
                    $progress = [];
                }
            }
        } else {
            $sql = "SELECT cp.courseid, cp.totalmodules total, count(cm.id) modules
                      FROM {course_modules} cm
                      JOIN {edwreports_course_progress} cp ON cm.course = cp.courseid
                     WHERE cp.userid = :userid
                       AND cm.course = :courseid
                       AND cm.completion <> 0
                     GROUP BY cp.courseid, cp.totalmodules";
            $params = ['userid' => $userid, 'courseid' => $course];
            $record = $DB->get_record_sql($sql, $params);
            if (!empty($record)) {
                // Completed.
                $labels[] = get_string('completion-y', 'core_completion');
                $progress[] = (int)$record->total;

                // Incomplete.
                $labels[] = get_string('completion-n', 'core_completion');
                $progress[] = (int)$record->modules - (int)$record->total;
            }
        }

        return [
            'labels' => $labels,
            'progress' => $progress
        ];
    }
}
