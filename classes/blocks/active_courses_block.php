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
 * @package     report_elucidsitereport
 * @category    admin
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_elucidsitereport;
use stdClass;
use context_course;
use cache;

/**
 * Class Acive Users Block
 * To get the data related to active users block
 */
class active_courses_block extends utility {
    /**
     * Get Data for Active Courses
     * @return [objext] Response for Active Courses
     */
    public static function get_data() {
        $response = new stdClass();

        $cache = cache::make('report_elucidsitereport', 'activecourses');
        // Create reporting manager instance
        $rpm = reporting_manager::get_instance();
        if(!$data = $cache->get('activecoursesdata'.$rpm->rpmcache)) {
            $data = self::get_course_data();
            $cache->set('activecoursesdata'.$rpm->rpmcache, $data);
        }

        $response->data = $data;
        return $response;
    }

    /**
     * Get headers for Active Courses Block
     * @return [array] Array of header of course block
     */
    public static function get_header() {
        $header = array(
            get_string("rank", "report_elucidsitereport"),
            get_string("coursename", "report_elucidsitereport"),
            get_string("enrolments", "report_elucidsitereport"),
            get_string("visits", "report_elucidsitereport"),
            get_string("completions", "report_elucidsitereport"),
        );

        return $header;
    }

    /**
     * Get Active Courses data
     * @return [array] Array of course active records
     */
    public static function get_course_data() {
        global $DB;

        $courses = get_courses();

        $count = 0;
        $response = array();
        // $completions = parent::get_course_completions();
        // Create reporting manager instance
        $rpm = reporting_manager::get_instance();
        // Calculate Completion Count for All Course 
        $sql = "SELECT courseid, COUNT(userid) AS users
            FROM {elucidsitereport_completion}
            WHERE completion = :completion
            AND userid ".$rpm->insql."
            GROUP BY courseid";
        $params = array("completion" => 100);
        $params = array_merge($params, $rpm->inparams);
        // Get records with 100% completions
        $coursecompletion = $DB->get_records_sql($sql, $params);

        foreach ($courses as $course) {
            // If moodle course then return false
            if ($course->id == 1) {
                continue;
            }

            // Create a record for responce
            $res = array(
                $count++,
                $course->fullname
            );


            // Get Course Context
            $coursecontext = context_course::instance($course->id);

            // Get Enrolled users
            $enrolledstudents = course_progress_block::rep_get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');
            if (empty($enrolledstudents)) {
                continue;
            }
            $res[] = count($enrolledstudents);

            // Get Completion count
            if (!isset($coursecompletion[$course->id])) {
                $completedusers = 0;
            } else{
                $completedusers = $coursecompletion[$course->id]->users;
            }

            $res[] = self::get_courseview_count($course->id, $enrolledstudents);
            $res[] = $completedusers;
            $response[] = $res;
        }
        return $response;
    }

    /**
     * Get Course View Count by users
     * @param  [int] $courseid Course Id
     * @param  [array] $enrolledstudents Array of enrolled uesers
     * @return [int] Number of course views by users
     */
    public static function get_courseview_count($courseid, $enrolledstudents) {
        global $DB;

        $sqlcourseview = "SELECT COUNT(userid) as usercount FROM
            (SELECT DISTINCT userid
            FROM {logstore_standard_log}
            WHERE action = ? AND courseid = ?";

        if (!empty($enrolledstudents)) {
            $extsql = " AND userid IN (" . implode(",", array_keys($enrolledstudents)) . ")";
            $sqlcourseview .= $extsql;
        }

        $sqlcourseview .= ") as users";
        $views = $DB->get_record_sql($sqlcourseview, array(
            'viewed',
            $courseid
        ));
        return $views->usercount;
    }

    /**
     * Get Exportable data for Active Courses Block
     * @return [array] Array of exportable data
     */
    public static function get_exportable_data_block() {
        $export = array();
        $header = active_courses_block::get_header();
        $activecoursesdata = active_courses_block::get_data();
        $export = array_merge(
            array($header),
            $activecoursesdata->data
        );

        return $export;
    }

    
}