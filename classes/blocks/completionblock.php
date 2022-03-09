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
 * Plugin administration pages are defined here.
 *
 * @package     local_edwiserreports
 * @category    admin
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserreports;

defined('MOODLE_INTERNAL') || die();

use stdClass;
use context_course;
use html_writer;

require_once($CFG->dirroot . '/local/edwiserreports/classes/blocks/courseprogressblock.php');

/**
 * Class Course Completion Block. To get the data related to active users block.
 */
class completionblock extends utility {
    /**
     * Get Data for Course Completion
     * @param  int    $courseid Course Id
     * @param  int    $cohortid Cohort Id
     * @return object           Response for Course Completion
     */
    public static function get_data($courseid, $cohortid) {
        $response = new stdClass();
        $response->data = self::get_completions($courseid, $cohortid);
        return $response;
    }

    /**
     * Get Course Completion data
     * @param  int   $courseid Course Id
     * @param  int   $cohortid Cohort Id
     * @return array           Array of users with course Completion
     */
    public static function get_completions($courseid, $cohortid) {
        global $DB;
        $timenow = time();
        $enrolsql = "SELECT ue.*, e.enrol
            FROM {user_enrolments} ue
            JOIN {enrol} e ON (e.id = ue.enrolid AND e.courseid = :courseid)
            JOIN {user} u ON u.id = ue.userid
            WHERE ue.userid = :userid AND u.deleted = 0";

        $coursecontext = context_course::instance($courseid);
        // Get only enrolled students.
        $enrolledstudents = courseprogressblock::rep_get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');
        $course = get_course($courseid);

        $userscompletion = array();
        foreach ($enrolledstudents as $user) {
            if ($cohortid) {
                $cohorts = cohort_get_user_cohorts($user->id);
                if (!array_key_exists($cohortid, $cohorts)) {
                    continue;
                }
            }

            $params = array('courseid' => $courseid, 'userid' => $user->id);
            $enrolinfo = $DB->get_record_sql($enrolsql, $params, IGNORE_MULTIPLE);

            $completion = self::get_course_completion_info($course, $user->id);

            if (empty($completion)) {
                $progressper = "NA";
            } else {
                $progressper = $completion["progresspercentage"] . "%";
            }

            $completioninfo = new stdClass();
            $completioninfo->username = html_writer::div(
                fullname($user), '',
                array (
                    "data-toggle" => "tooltip",
                    "title" => $user->email
                )
            );

            $visits = self::get_visits_by_users($courseid, $user->id);

            if (empty($visits)) {
                $lastvisits = get_string("never");
            } else {
                $lastvisits = format_time($timenow - array_values((array) $visits)[0]->timecreated);
            }

            $gradeval = 0;
            $grade = self::get_grades($courseid, $user->id);
            if (isset($grade->finalgrade)) {
                $gradeval = round($grade->finalgrade, 2);
            }

            $completioninfo->enrolledon = date("d M Y", $enrolinfo->timemodified);
            $completioninfo->enrolltype = $enrolinfo->enrol;
            $completioninfo->noofvisits = count(self::get_visits_by_users($courseid, $user->id));
            $completioninfo->completion = $progressper;
            $completioninfo->compleiontime = self::get_timecompleted($courseid, $user->id);
            $completioninfo->grade = $gradeval;
            $completioninfo->lastaccess = $lastvisits;
            $userscompletion[] = $completioninfo;
        }
        return $userscompletion;
    }

    /**
     * Get Course completion time by a user
     * @param  int    $courseid Course Id
     * @param  int    $userid   User Id
     * @return string           Date | not completed
     */
    public static function get_timecompleted($courseid, $userid) {
        global $DB;

        // Get completions.
        $compobj = new \local_edwiserreports\completions();
        $completions = $compobj->get_course_completions($courseid);

        if (isset($completions[$userid]) && $completions[$userid]->completiontime) {
            return date("d M Y", $completions[$userid]->completiontime);
        } else {
            return get_string("notyet", "local_edwiserreports");
        }
    }

    /**
     * Get export header string
     * @return array Header array
     */
    public static function get_header() {
        return array(
            get_string("fullname", "local_edwiserreports"),
            get_string("enrolledon", "local_edwiserreports"),
            get_string("enrolltype", "local_edwiserreports"),
            get_string("noofvisits", "local_edwiserreports"),
            get_string("coursecompletion", "local_edwiserreports"),
            get_string("completiontime", "local_edwiserreports"),
            get_string("grade", "local_edwiserreports"),
            get_string("lastaccess", "local_edwiserreports")
        );
    }

    /**
     * Get Exportable data for Course Completion Page
     * @param  int   $courseid Course id
     * @return array           Array of LP Stats
     */
    public static function get_exportable_data_report($courseid) {
        global $DB;
        $cohortid = optional_param("cohortid", 0, PARAM_INT);
        $completions = self::get_completions($courseid, $cohortid);

        $export = array();
        $export[] = self::get_header();
        foreach ($completions as $completion) {
            $completion->username = strip_tags($completion->username);
            $export[] = array_values((array)$completion);
        }
        return $export;
    }
}
