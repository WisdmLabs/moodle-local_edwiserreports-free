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
 * Learner course progress report page.
 *
 * @package     local_edwiserreports
 * @category    reports
 * @author      Yogesh Shirsath
 * @copyright   2022 Wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserreports\reports;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/edwiserreports/lib.php');

use local_edwiserreports\utility;
use moodle_exception;
use context_system;
use html_writer;
use moodle_url;
use core_user;

class learnercourseprogress extends base {
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
                    'item' => get_string('learnercourseprogress', 'local_edwiserreports')
                )
            )
        );
    }

    /**
     * Check if current user is learner of have higher capability.
     *
     * @param int $userid User id
     *
     * @return boolean
     */
    public function is_learner($userid = null) {
        global $USER;
    }

    /**
     * Get filter data
     *
     * @param  int   $activecourse Active course from url.
     * @return array
     */
    public function get_filter($activelearner = 0) {
        global $USER;
        $courses = $this->bb->get_courses_of_user();
        unset($courses[SITEID]);
        $users = $this->bb->get_users_of_courses($USER->id, $courses);

        if ($activelearner == 0) {
            $activelearner = reset($users)->id;
        }

        // Invalid user.
        if (!isset($users[$activelearner])) {
            throw new moodle_exception('invaliduser', 'core_error');
        }

        $users[$activelearner]->selected = 'selected';

        return [
            'activelearner' => $activelearner,
            'learners' => array_values($users),
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

        $search = html_writer::tag('i', '', ['class' => 'fa fa-search-plus']);
        $link = html_writer::link(
                    new moodle_url(
                        "/local/edwiserreports/learnercourseprogress.php",
                        array("learner" => 2)
                    ),
                    $search,
                    array(
                        'style' => 'margin-left: 0.5rem;'
                    )
                );

        for ($i=0; $i < 5; $i++) {
          $name = 'High School Course ';
          $response[] = [
                "course" => $name . $i,
                "status" => $i % 2 != 0 ? 0 : 1,
                "enrolledon" => '28 Sept 2022',
                "completedon" => $i % 2 != 0 ? '02 Jan 2023' : get_string('never', 'local_edwiserreports'),
                "lastaccess" => '12 Jan 2023 <br> 09:39 AM',
                "progress" => '70%',
                "grade" => '300',
                "totalactivities" => '20' . $link,
                "completedactivities" => '20',
                "attemptedactivities" => '20',
                "visits" => 204,
                "timespent" => '02:30:45'
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
            $filter['enrolment'] = 'all';
        }
        $user = $DB->get_record('user', ['id' => $filter->learner]);
        if (empty($user)) {
            throw new moodle_exception('invaliduser', 'core_error');
        }
        return (object) [
            'data' => (new self())->get_data($filter, false),
            'options' => [
                'content' => get_string('student', 'local_edwiserreports') . ': ' . fullname($user),
                'format' => 'a3',
                'orientation' => 'l',
            ]
        ];
    }



    /**
     * Get Exportable data for Course Completion Page
     * @param  object $filters    Filter string
     * @param  bool   $filterdata If enabled then filter data
     * @return array              Array of LP Stats
     */
    public function get_summary_data($filters) {
        global $CFG, $DB;

        $student = $DB->get_record('user', ['id' => $filters->learner]);

        return array(
            'header' => array(
                'learner' => true,
                'learnername' => fullname($student),
                'isactive' => 1,
                'lastaccess' => '12 Jan 2023 09:39 AM'
            ),
            'body' => array(
                array(
                    'title'   => get_string('visitsoncourse', 'local_edwiserreports'),
                    'data' => '3007'
                ),
                array(
                    'title'   => get_string('timespentoncourseheader', 'local_edwiserreports'),
                    'data' => '01:30:54'
                ),
                array(
                    'title'   => get_string('timespentonsite', 'local_edwiserreports'),
                    'data' => '20:34:43'
                )
            ),
            'footer' => array(
                array(
                    'icon'  => file_get_contents($CFG->dirroot . '/local/edwiserreports/pix/summary-card/enrolledcourses.svg'),
                    'title' => get_string('enrolledcourses', 'local_edwiserreports'),
                    'data'  => '5'
                ),
                array(
                    'icon'  => file_get_contents($CFG->dirroot . '/local/edwiserreports/pix/summary-card/completed.svg'),
                    'title' => get_string('completionprogress', 'local_edwiserreports'),
                    'data'  => '70%'
                ),
                array(
                    'icon'  => file_get_contents($CFG->dirroot . '/local/edwiserreports/pix/summary-card/totalmarks.svg'),
                    'title' => get_string('totalmarks', 'local_edwiserreports'),
                    'data'  => '300'
                ),
                array(
                    'icon'  => file_get_contents($CFG->dirroot . '/local/edwiserreports/pix/summary-card/grade.svg'),
                    'title' => get_string('totalgrade', 'local_edwiserreports'),
                    'data'  => '65%'
                )
            )
        );
    }




}
