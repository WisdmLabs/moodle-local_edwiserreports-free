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
 * All Courses Summary report page.
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
use context_course;
use html_writer;
use moodle_url;

// Requiring constants.
require_once($CFG->dirroot . '/local/edwiserreports/classes/constants.php');

class allcoursessummary extends base {
    /**
     * Get Breadcrumbs for Course All courses summary
     * @return object Breadcrumbs for All courses summary
     */
    public function get_breadcrumb() {

        return array(
            'items' => array(
                array(
                    'item' => get_string('allcoursessummary', 'local_edwiserreports')
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
    public function get_filter() {
        $groups = [[
            'id' => 0,
            'name' => get_string('allgroups', 'local_edwiserreports')
        ]];

        $cohorts = [[
            'id' => 0,
            'name' => get_string('allcohorts', 'local_edwiserreports')
        ]];


        return [
            'cohorts' => $cohorts,
            'groups' => $groups
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
        global $COURSE, $DB;
        $response = array();
        $search = html_writer::tag('i', '', ['class' => 'fa fa-search-plus']);

        for ($i=1; $i < 20; $i++) {
            $data['coursename'] = 'Course ' . $i;
            $data['category'] = 'Category ' . $i;
            $data['enrolments'] = 20 + $i;

            $data['enrolments'] .= html_writer::link(
                    new moodle_url(
                        "/local/edwiserreports/completion.php",
                        array("courseid" => 2)
                    ),
                    $search,
                    array(
                        'style' => 'margin-left: 0.5rem;'
                    )
                );

            $data['completed'] = $i % 2 != 0 ? 0 : 1;
            $data['notstarted'] = 2;
            $data['inprogress'] = 18;
            $data['atleastoneactivitystarted'] = 5;
            $data['totalactivities'] = 5;
            $data['totalactivities'] .= html_writer::link(
                    new moodle_url(
                        "/local/edwiserreports/completion.php",
                        array("courseid" => 2)
                    ),
                    $search,
                    array(
                        'style' => 'margin-left: 0.5rem;'
                    )
                );
            $data['avgprogress'] = '70%';
            $data['avggrade'] = 50;
            $data['highestgrade'] = 100;
            $data['lowestgrade'] = 10;
            $data['totaltimespent'] = '03:34:56';
            $data['avgtimespent'] = '00:39:45';
            $response[] = $data;
        }

        // Return response.
        return $response;
    }


    /**
     * Get course spenttime
     * @param   int     $course     Course id
     * @param   string  $userstable Temporary users table
     * @return  int                 Users time spent on course
     */
    public function get_course_timespent($course, $userstable) {
        global $DB;

        $sql = "SELECT SUM(eal.timespent) timespent
                  FROM {edwreports_activity_log} eal
                  JOIN {{$userstable}} ut ON eal.userid = ut.tempid
                 WHERE eal.course = :course
                 GROUP BY eal.course";

        $params = array('course' => $course);

        return $DB->get_field_sql($sql, $params);
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
            $filter['cohort'] = 0;
            $filter['coursegroup'] = 0;
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
}
