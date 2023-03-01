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
 * Course Activity Compeltion report page.
 *
 * @package     local_edwiserreports
 * @category    reports
 * @author      Yogesh Shirsath
 * @copyright   2022 Wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserreports\reports;

use local_edwiserreports\utility;
use core_course_category;
use moodle_exception;
use html_writer;
use moodle_url;

class courseactivitycompletion extends base {
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
                            "/local/edwiserreports/allcoursessummary.php",
                        ),
                        get_string('allcoursessummary', 'local_edwiserreports'),
                        array(
                            'style' => 'margin-left: 0.5rem;'
                        )
                    )
                ),
                array(
                    'item' => html_writer::link(
                        new moodle_url(
                            "/local/edwiserreports/courseactivitiessummary.php",
                        ),
                        get_string('courseactivitiessummary', 'local_edwiserreports'),
                        array(
                            'style' => 'margin-left: 0.5rem;'
                        )
                    )
                ),
                array(
                    'item' => get_string('courseactivitycompletion', 'local_edwiserreports')
                )
            )
        );
    }

    /**
     * Get filter data
     *
     * @param  int      $activecourse Active course from url.
     * @param  int      $activecm Active course module type from url.
     * @return array
     */
    public function get_filter($activecourse, $activecm) {

        $courses = [[
            'id' => 1,
            'name' => 'Dummy Course',
            'selected' => 'selected'
        ]];

        $coursecategories = [
            2 => [
            'id' => 2,
            'name' => 'Category 1',
            'visible' => true,
            'courses' => $courses
        ]];

        $sectionlist = [[
            'id' => 0,
            'name' => get_string('allsections', 'local_edwiserreports'),
            'selected' => 'selected'
        ]];

        $modules = [[
            'type' => 'all',
            'name' => get_string('allmodules', 'local_edwiserreports'),
            'selected' => 'selected'
        ]];

        $activecm = [
            2 => [
            'id' => 0,
            'name' => 'Announcement',
            'selected' => 'selected'
        ]];

        $groups = [[
            'id' => 0,
            'name' => get_string('allgroups', 'local_edwiserreports')
        ]];

        return [
            'activecourse' => 2,
            'activecm' => 2,
            'courses' => $courses,
            'coursecategories' => array_values($coursecategories),
            'cms' => array_values($activecm),
            'groups' => $groups
        ];
    }

    /**
     * Get users for table with filters.
     *
     * @param object $filters Filters
     *
     * @return array
     */
    public function get_users($filters) {
        global $DB;
        $userid = $this->bb->get_current_user();

        // User fields.
        $fields = 'u.id, ' . $DB->sql_fullname("u.firstname", "u.lastname") . ' AS fullname';

        // All users.
        $allusers = $this->bb->get_user_from_cohort_course_group(0, $filters->course, $filters->group, $userid);

        // User temporary table.
        $usertable = utility::create_temp_table('tmp_cac_uf', array_keys($allusers));

        $params = [];
        $condition = "";
        $conditions = [];

        if ($filters->enrolment !== 'all') {
            list($starttime, $endtime) = $this->bb->get_date_range($filters->enrolment);
            $conditions['enrolment'] = 'floor(ue.timestart / 86400) between :starttime AND :endtime';
            $params['starttime'] = floor($starttime / 86400);
            $params['endtime'] = floor($endtime / 86400);
        }

        if (!empty($conditions)) {
            $condition = " WHERE " . implode(" AND ", $conditions);
        } else {
            // Drop temporary table.
            utility::drop_temp_table($usertable);
            return $allusers;
        }

        $sql = "SELECT DISTINCT $fields
                  FROM {{$usertable}} ut
                  JOIN {user_enrolments} ue ON ue.userid = ut.tempid
                  JOIN {enrol} e ON ue.enrolid = e.id AND e.courseid = :course
                  JOIN {user} u ON ue.userid = u.id
                  $condition";
        $params['course'] = $filters->course;
        $users = $DB->get_records_sql($sql, $params);

        // Drop temporary table.
        utility::drop_temp_table($usertable);
        return $users;
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
        $response = array(
          array(
            'learner' => 'John Doe',
            'email' => 'johndoe@doe.com',
            'status' => 1,
            'completedon' => '21 Nov 2022',
            'grade' => '300',
            'gradedon' => '1 Nov 2022',
            'firstaccess' => '1 Sept 2022 <br> 09:39 AM',
            'lastaccess' => '21 Nov 2022 <br> 09:39 AM',
            'visits' => 300,
            'timespent' => '02:10:59'
          ),
          array(
            'learner' => 'John Doe',
            'email' => 'johndoe@doe.com',
            'status' => 1,
            'completedon' => '21 Nov 2022',
            'grade' => '300',
            'gradedon' => '1 Nov 2022',
            'firstaccess' => '1 Sept 2022 <br> 09:39 AM',
            'lastaccess' => '21 Nov 2022 <br> 09:39 AM',
            'visits' => 300,
            'timespent' => '02:10:59'
          ),
          array(
            'learner' => 'John Doe',
            'email' => 'johndoe@doe.com',
            'status' => 1,
            'completedon' => '21 Nov 2022',
            'grade' => '300',
            'gradedon' => '1 Nov 2022',
            'firstaccess' => '1 Sept 2022 <br> 09:39 AM',
            'lastaccess' => '21 Nov 2022 <br> 09:39 AM',
            'visits' => 300,
            'timespent' => '02:10:59'
          ),
          array(
            'learner' => 'John Doe',
            'email' => 'johndoe@doe.com',
            'status' => 1,
            'completedon' => '21 Nov 2022',
            'grade' => '300',
            'gradedon' => '1 Nov 2022',
            'firstaccess' => '1 Sept 2022 <br> 09:39 AM',
            'lastaccess' => '21 Nov 2022 <br> 09:39 AM',
            'visits' => 300,
            'timespent' => '02:10:59'
          ),
          array(
            'learner' => 'John Doe',
            'email' => 'johndoe@doe.com',
            'status' => 1,
            'completedon' => '21 Nov 2022',
            'grade' => '300',
            'gradedon' => '1 Nov 2022',
            'firstaccess' => '1 Sept 2022 <br> 09:39 AM',
            'lastaccess' => '21 Nov 2022 <br> 09:39 AM',
            'visits' => 300,
            'timespent' => '02:10:59'
          ),
          array(
            'learner' => 'John Doe',
            'email' => 'johndoe@doe.com',
            'status' => 1,
            'completedon' => '21 Nov 2022',
            'grade' => '300',
            'gradedon' => '1 Nov 2022',
            'firstaccess' => '1 Sept 2022 <br> 09:39 AM',
            'lastaccess' => '21 Nov 2022 <br> 09:39 AM',
            'visits' => 300,
            'timespent' => '02:10:59'
          ),
          array(
            'learner' => 'John Doe',
            'email' => 'johndoe@doe.com',
            'status' => 1,
            'completedon' => '21 Nov 2022',
            'grade' => '300',
            'gradedon' => '1 Nov 2022',
            'firstaccess' => '1 Sept 2022 <br> 09:39 AM',
            'lastaccess' => '21 Nov 2022 <br> 09:39 AM',
            'visits' => 300,
            'timespent' => '02:10:59'
          ),
          array(
            'learner' => 'John Doe',
            'email' => 'johndoe@doe.com',
            'status' => 1,
            'completedon' => '21 Nov 2022',
            'grade' => '300',
            'gradedon' => '1 Nov 2022',
            'firstaccess' => '1 Sept 2022 <br> 09:39 AM',
            'lastaccess' => '21 Nov 2022 <br> 09:39 AM',
            'visits' => 300,
            'timespent' => '02:10:59'
          ),
        );

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
        $filter = json_decode($filter);
        if (!$filterdata) {
            $filter['group'] = 0;
            $filter['enrolment'] = 'all';
        }
        $obj = new self();
        $data = $obj->get_data($filter, false);
        return (object) [
            'data' => $data['data'],
            'options' => [
                'content' => get_string('course') . ': ' . $data['course'] .
                    '<br>' . get_string('activity', 'local_edwiserreports') . ': ' . $data['activity'],
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

        $customheader = '<div>
            <div class="mb-1 summary-card-subtitle">
                <span class="font-weight-bold">'. get_string('course', 'local_edwiserreports') .' : </span>
                <span> High School Biology </span>
            </div>
            <div class="summary-card-title font-weight-bold">
                Announcement
            </div>
        </div>';

        return array(
            'header' => array(
                'customheaderinfo' => $customheader
            ),
            'body' => array(
                array(
                    'title'   => get_string('totalvisits', 'local_edwiserreports'),
                    'data' => '4000'
                ),
                array(
                    'title'   => get_string('avgvisits', 'local_edwiserreports'),
                    'data' => '100'
                ),
                array(
                    'title'   => get_string('totaltimespent', 'local_edwiserreports'),
                    'data' => '201:10:26'
                ),
                array(
                    'title'   => get_string('avgtimespent', 'local_edwiserreports'),
                    'data' => '01:30:02'
                )
            ),
            'footer' => array(
                array(
                    'icon'  => file_get_contents($CFG->dirroot . '/local/edwiserreports/pix/summary-card/passgrade.svg'),
                    'title' => get_string('passgrade', 'local_edwiserreports'),
                    'data'  => '40%'
                ),
                array(
                    'icon'  => file_get_contents($CFG->dirroot . '/local/edwiserreports/pix/summary-card/grade.svg'),
                    'title' => get_string('avggrade', 'local_edwiserreports'),
                    'data'  => '60'
                ),
                array(
                    'icon'  => file_get_contents($CFG->dirroot . '/local/edwiserreports/pix/summary-card/highgrade.svg'),
                    'title' => get_string('highgrade', 'local_edwiserreports'),
                    'data'  => '90'
                ),
                array(
                    'icon'  => file_get_contents($CFG->dirroot . '/local/edwiserreports/pix/summary-card/lowgrade.svg'),
                    'title' => get_string('lowgrade', 'local_edwiserreports'),
                    'data'  => '25'
                ),
                array(
                    'icon'  => file_get_contents($CFG->dirroot . '/local/edwiserreports/pix/summary-card/progress.svg'),
                    'title' => get_string('completionrate', 'local_edwiserreports'),
                    'data'  => '52%'
                )
            )
        );
    }
}
