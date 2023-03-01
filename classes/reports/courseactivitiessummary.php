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
 * Course Activities Summary report page.
 *
 * @package     local_edwiserreports
 * @category    reports
 * @author      Yogesh Shirsath
 * @copyright   2022 Wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserreports\reports;

defined('MOODLE_INTERNAL') || die;

use local_edwiserreports\utility;
use core_course_category;
use moodle_exception;
use html_writer;
use moodle_url;

// Requiring constants.
require_once($CFG->dirroot . '/local/edwiserreports/classes/constants.php');

class courseactivitiessummary extends base {
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
                    'item' => get_string('courseactivitiessummary', 'local_edwiserreports')
                )
            )
        );
    }

    /**
     * Get filter data
     *
     * @param  int   $activecourse Active course from url.
     * @return array
     */
    public function get_filter($activecourse) {

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

        $groups = [[
            'id' => 0,
            'name' => get_string('allgroups', 'local_edwiserreports')
        ]];

        return [
            'activecourse' => 1,
            'courses' => $courses,
            'coursecategories' => array_values($coursecategories),
            'sections' => $sectionlist,
            'modules' => $modules,
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
        $usertable = utility::create_temp_table('tmp_cas_uf', array_keys($allusers));

        $params = [
            'action' => 'viewed',
            'courseid' => $filters->course
        ];
        $condition = "";
        $conditions = [];

        if (array_search(SUSPENDEDUSERS, $filters->exclude) !== false) {
            $conditions['suspended'] = "u.suspended = :suspended";
            $conditions['uesuspended'] = "ue.status = :uesuspended";
            $params['suspended'] = $params['uesuspended'] = 0;
        }

        if (array_search(INACTIVESINCE1YEAR, $filters->exclude) !== false) {
            $conditions['inactive'] = "logs.lastaccess > :lastaccess";
            $params['lastaccess'] = time() - (86400 * 365);
        }

        if (array_search(INACTIVESINCE1MONTH, $filters->exclude) !== false) {
            $conditions['inactive'] = "logs.lastaccess > :lastaccess";
            $params['lastaccess'] = time() - (86400 * 30);
        }

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

        $lslaction = $DB->sql_compare_text('lsl.action');
        $sql = "SELECT DISTINCT $fields
                  FROM {{$usertable}} ut
                  JOIN {user_enrolments} ue ON ue.userid = ut.tempid
                  JOIN {enrol} e ON ue.enrolid = e.id AND e.courseid = :course
                  JOIN {user} u ON ue.userid = u.id
             LEFT JOIN (SELECT lsl.userid, MAX(lsl.timecreated) lastaccess
                          FROM {logstore_standard_log} lsl
                         WHERE $lslaction = :action
                           AND lsl.courseid = :courseid
                      GROUP BY lsl.userid) logs ON u.id = logs.userid
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
        global $DB;

        $search = html_writer::tag('i', '', ['class' => 'fa fa-search-plus']);
        $name = html_writer::link(
                    new moodle_url(
                        "/local/edwiserreports/courseactivitycompletion.php",
                        array(
                            "course" => 2,
                            "cm" => 10
                        )
                    ),
                    $search,
                    array(
                        'style' => 'margin-left: 0.5rem;'
                    )
                );

        $response= array(
            array(
                'activity' => 'Activity 1' . $name, // Activity name.
                'type' => 'Forum',
                'status' => 1, // Status: completed, not yet started, in progress.
                'learnerscompleted' => 12, // Number of Learners completed activity.
                'completionrate' => '50%',
                'maxgrade' => '100',
                'passgrade' => '40',
                'averagegrade' => '60',
                'highestgrade' => '90',
                'lowestgrade' => '20',
                'totaltimespent' => '52:00:14',
                'averagetimespent' => '02:00:14',
                'totalvisits' => '1020',
                'averagevisits' => '100'
            ),
            array(
                'activity' => 'Page activity' . $name, // Activity name.
                'type' => 'Page',
                'status' => 0, // Status: completed, not yet started, in progress.
                'learnerscompleted' => 5, // Number of Learners completed activity.
                'completionrate' => '50%',
                'maxgrade' => '100',
                'passgrade' => '40',
                'averagegrade' => '60',
                'highestgrade' => '90',
                'lowestgrade' => '20',
                'totaltimespent' => '52:00:14',
                'averagetimespent' => '02:00:14',
                'totalvisits' => '1020',
                'averagevisits' => '100'
            ),
            array(
                'activity' => 'Activity 2' . $name, // Activity name.
                'type' => 'Forum',
                'status' => 2, // Status: completed, not yet started, in progress.
                'learnerscompleted' => 3, // Number of Learners completed activity.
                'completionrate' => '50%',
                'maxgrade' => '100',
                'passgrade' => '40',
                'averagegrade' => '60',
                'highestgrade' => '90',
                'lowestgrade' => '20',
                'totaltimespent' => '52:00:14',
                'averagetimespent' => '02:00:14',
                'totalvisits' => '1020',
                'averagevisits' => '100'
            ),
            array(
                'activity' => 'Assignment 3' . $name, // Activity name.
                'type' => 'Assignment',
                'status' => 1, // Status: completed, not yet started, in progress.
                'learnerscompleted' => 0, // Number of Learners completed activity.
                'completionrate' => '50%',
                'maxgrade' => '100',
                'passgrade' => '40',
                'averagegrade' => '60',
                'highestgrade' => '90',
                'lowestgrade' => '20',
                'totaltimespent' => '52:00:14',
                'averagetimespent' => '02:00:14',
                'totalvisits' => '120',
                'averagevisits' => '100'
            ),
            array(
                'activity' => 'Activity 4' . $name, // Activity name.
                'type' => 'Forum',
                'status' => 0, // Status: completed, not yet started, in progress.
                'learnerscompleted' => 10, // Number of Learners completed activity.
                'completionrate' => '50%',
                'maxgrade' => '100',
                'passgrade' => '40',
                'averagegrade' => '60',
                'highestgrade' => '90',
                'lowestgrade' => '20',
                'totaltimespent' => '52:00:14',
                'averagetimespent' => '02:00:14',
                'totalvisits' => '1020',
                'averagevisits' => '100'
            ),
            array(
                'activity' => 'Page activity 1' . $name, // Activity name.
                'type' => 'Page',
                'status' => 2, // Status: completed, not yet started, in progress.
                'learnerscompleted' => 8, // Number of Learners completed activity.
                'completionrate' => '50%',
                'maxgrade' => '100',
                'passgrade' => '40',
                'averagegrade' => '60',
                'highestgrade' => '90',
                'lowestgrade' => '20',
                'totaltimespent' => '52:00:14',
                'averagetimespent' => '02:00:14',
                'totalvisits' => '1020',
                'averagevisits' => '100'
            )
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
            $filter['section'] = 'all';
            $filter['module'] = 'all';
            $filter['group'] = 0;
            $filter['enrolment'] = 'all';
            $filter['exclude'] = [];
        }
        $obj = new self();
        return (object) [
            'data' => $obj->get_data($filter, false),
            'options' => [
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
    public function get_summary_data($filters, $filterdata = true) {
        global $DB, $CFG;

        return array(
            'header' => array(
                'course' => true,
                'coursename' => 'High School Biology',
                'category' => 'Biology',
            ),
            'body' => array(
                array(
                    'title'   => get_string('totalvisits', 'local_edwiserreports'),
                    'data' => '3002'
                ),
                array(
                    'title'   => get_string('avgvisits', 'local_edwiserreports'),
                    'data' => '200'
                ),
                array(
                    'title'   => get_string('totaltimespent', 'local_edwiserreports'),
                    'data' => '88:23:00'
                ),
                array(
                    'title'   => get_string('avgtimespent', 'local_edwiserreports'),
                    'data' => '03:09:12'
                )
            ),
            'footer' => array(
                array(
                    'icon'  => file_get_contents($CFG->dirroot . '/local/edwiserreports/pix/summary-card/totalsections.svg'),
                    'title' => get_string('totalsections', 'local_edwiserreports'),
                    'data'  => '4'
                ),
                array(
                    'icon'  => file_get_contents($CFG->dirroot . '/local/edwiserreports/pix/summary-card/totalactivities.svg'),
                    'title' => get_string('totalactivities', 'local_edwiserreports'),
                    'data'  => '9'
                ),
                array(
                    'icon'  => file_get_contents($CFG->dirroot . '/local/edwiserreports/pix/summary-card/progress.svg'),
                    'title' => get_string('avgprogress', 'local_edwiserreports'),
                    'data'  => '20' . '%'
                )
            )
        );
    }


}
