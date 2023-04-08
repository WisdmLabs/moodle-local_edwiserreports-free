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
 * Learner course activities report page.
 *
 * @package     local_edwiserreports
 * @category    reports
 * @author      Yogesh Shirsath
 * @copyright   2022 Wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserreports\reports;

use core_course_category;
use moodle_exception;
use html_writer;
use moodle_url;
use core_user;

class learnercourseactivities extends base {

    /**
     * Get Breadcrumbs for Course All courses summary
     * @return object Breadcrumbs for All courses summary
     */
    public function get_breadcrumb() {
        return array(
            'items' => array(
                array(
                    'item' => html_writer::link(
                        new moodle_url(
                            "/local/edwiserreports/studentengagement.php",
                        ),
                        get_string('alllearnersummary', 'local_edwiserreports'),
                        array(
                            'style' => 'margin-left: 0.5rem;'
                        )
                    )
                ),
                array(
                    'item' => html_writer::link(
                        new moodle_url(
                            "/local/edwiserreports/learnercourseprogress.php",
                        ),
                        get_string('learnercourseprogress', 'local_edwiserreports'),
                        array(
                            'style' => 'margin-left: 0.5rem;'
                        )
                    )
                ),
                array(
                    'item' => get_string('learnercourseactivities', 'local_edwiserreports')
                )
            )
        );
    }

    /**
     * Get filter data
     *
     * @param  int   $activecourse  Active course from url.
     * @param  int   $activelearner    Active user from url.
     * @return array
     */
    public function get_filter($activecourse, $activelearner = 0) {
        global $USER;

        $courses = $this->bb->get_courses_of_user();
        unset($courses[SITEID]);

        if ($activecourse == 0) {
            $activecourse = reset($courses)->id;
        }

        // Invalid course.
        if (!isset($courses[$activecourse])) {
            throw new moodle_exception('invalidcourse', 'core_error');
        }

        $courses[$activecourse]->selected = 'selected';

        $users = $this->bb->get_user_from_cohort_course_group(0, $activecourse, 0, $USER->id);

        if ($activelearner == 0 && !empty($users)) {
            $activelearner = reset($users)->id;
        }

        // Invalid user.
        if (isset($users[$activelearner])) {
            $users[$activelearner]->selected = 'selected';
        }

        $categories = core_course_category::make_categories_list();
        $coursecategories = [];
        foreach ($categories as $id => $name) {
            $coursecategories[$id] = [
                'id' => $id,
                'name' => $name,
                'visible' => false,
                'courses' => []
            ];
        }
        $courses[$activecourse]->selected = 'selected';
        foreach ($courses as $id => $course) {
            $coursecategories[$course->category]['visible'] = true;
            $coursecategories[$course->category]['courses'][] = $course;
        }

        $sectionlist = [[
            'id' => 0,
            'name' => get_string('allsections', 'local_edwiserreports'),
            'selected' => 'selected'
        ]];

        return [
            'activecourse' => $activecourse,
            'activelearner' => $activelearner,
            'students' => array_values($users),
            'coursecategories' => array_values($coursecategories),
            'sections' => $sectionlist,
            'modules' => $this->get_modules($activecourse)
        ];
    }

    /**
     * Get data for table/export.
     *
     * @param object  $filters  Filters object
     * @param boolean $table    True if need data for table.
     *
     * @return array
     */
    public function get_data($filters, $table = true) {
        global $DB;

        for ($i=0; $i < 25; $i++) {
            $response[] = [
                'activity' => 'Activity ' . $i,
                'type' => 'Assignment ' . $i,
                "status" => $i % 2 != 0 ? 0 : 1,
                "completedon" => $i % 2 != 0 ? '02 Jan 2023' : get_string('never', 'local_edwiserreports'),
                'grade' => 80,
                'gradedon' => '25 Dec 2022',
                'attempts' => 1,
                'highestgrade' => 100,
                'lowestgrade' => 20,
                'firstaccess' => '01 March 2022 <br> 09:39 AM',
                'lastaccess' => '01 March 2023 <br> 09:39 AM',
                'visits' => 20,
                'timespent' => '00:20:30',
            ];
        }

        return $response;
    }

    /**
     * Get exportable data report for course activities summaryt
     *
     * @param string    $filter     Filter to apply on data
     * @param bool      $filterdata If true then only filtered data will be export`
     *
     * @return array                Returning filtered exported data
     */
    public static function get_exportable_data_report($filter, $filterdata) {
        global $DB;
        $filter = json_decode($filter);
        if (!$filterdata) {
            $filter["section"] = "all";
            $filter["module"] = "all";
            $filter["completion"] = "all";
        }

        $course = $DB->get_record('course', ['id' => $filter->course]);
        if (empty($course)) {
            throw new moodle_exception('invalidcourse', 'core_error');
        }

        $user = $DB->get_record('user', ['id' => $filter->learner]);
        if (empty($course)) {
            throw new moodle_exception('invaliduser', 'core_error');
        }
        $obj = new self();
        return (object) [
            'data' => $obj->get_data($filter, false),
            'options' => [
                'content' => get_string('learnercourseactivitiespdfcontent', 'local_edwiserreports', [
                    'course' => $course->fullname,
                    'student' => fullname($user)
                ]),
                'format' => 'a3',
                'orientation' => 'l',
            ]
        ];
    }


    /**
     * Get Exportable data for Course Completion Page
     * @param  object $filters Filter string
     * @return array           Array of LP Stats
     */
    public static function get_summary_data($filters) {
        global $DB, $CFG;


        $customheader = '<div>
            <div class="mb-1 summary-card-subtitle">
                <span class="font-weight-bold">'. get_string('course', 'local_edwiserreports') .' : </span>
                <span> High School Biology </span>
            </div>
        </div>';

        return array(
            'header' => array(
                'learner' => true,
                'learnername' => 'Student 1',
                'isactive' => 1,
                'lastaccess' => date("d M Y h:i:s A", time()),
                'customheaderinfo' => $customheader
            ),
            'body' => array(
                array(
                    'title'   => get_string('visitsoncourse', 'local_edwiserreports'),
                    'data' => 300
                ),
                array(
                    'title'   => get_string('enrolmentdate', 'local_edwiserreports'),
                    'data' => date("d M Y", time())
                )
            ),
            'footer' => array(
                array(
                    'icon'  => file_get_contents($CFG->dirroot . '/local/edwiserreports/pix/summary-card/time.svg'),
                    'title' => get_string('timespent', 'local_edwiserreports'),
                    'data'  => '02:56:45'
                ),
                array(
                    'icon'  => file_get_contents($CFG->dirroot . '/local/edwiserreports/pix/summary-card/marks.svg'),
                    'title' => get_string('marks', 'local_edwiserreports'),
                    'data'  => '400'
                ),
                array(
                    'icon'  => file_get_contents($CFG->dirroot . '/local/edwiserreports/pix/summary-card/grade.svg'),
                    'title' => get_string('grade', 'local_edwiserreports'),
                    'data'  => '80%'
                )
            )
        );
    }


}
