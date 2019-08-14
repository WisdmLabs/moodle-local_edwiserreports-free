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

defined('MOODLE_INTERNAL') || die();

use stdClass;
use core_user;
use context_course;
use html_writer;
use html_table;
use html_table_cell;
use html_table_row;

/**
 * Class Course Engagement Block
 * To get the data related to course engagement block
 */
class courseengage_block extends utility {
    /** Get data for course engagement block
     * @return [object] Information about the course engage
     * block
     */
    public static function get_data($cohortid) {
        $response = new stdClass();
        $response->data = self::get_courseengage($cohortid);

        return $response;
    }

    /**
     * Get Course Engagement Data
     * @return [array] Array of course engagement
     */
    public static function get_courseengage($cohortid) {
        $engagedata = array();
        $courses = self::get_courses(true);
        foreach($courses as $course) {
            $engagedata[] = self::get_engagement($course, $cohortid);
        }
        return $engagedata;
    }

    /**
     * Get Course Engagement for a course
     * @param [int] $courseid Courese ID to get course engagement
     * @return [object] 
     */
    public static function get_engagement($course, $cohortid) {
        global $DB;

        $coursecontext = context_course::instance($course->id);
        $enrolledstudents = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');

        /* If cohort filter is there then select only cohort users */
        if($cohortid) {
            foreach($enrolledstudents as $key => $user) {
                $cohorts = cohort_get_user_cohorts($user->id);
                if (!array_key_exists($cohortid, $cohorts)) {
                    unset($enrolledstudents[$key]);
                }
            }
        }

        $engagement = new stdClass();
        $engagement->coursename = $course->fullname;
        $engagement->enrolment = self::get_engagement_attr(
            "enrolment",
            $course,
            count($enrolledstudents)
        );
        $engagement->visited = self::get_engagement_attr(
            "visited",
            $course,
            count(self::get_course_visites($course->id, $cohortid))
        );
        $engagement->activitystart = self::get_engagement_attr(
            "activitystart",
            $course,
            count(self::users_completed_a_module($course, $enrolledstudents, $cohortid))
        );
        $engagement->completedhalf = self::get_engagement_attr(
            "completedhalf",
            $course,
            count(self::users_completed_half_modules($course, $enrolledstudents, $cohortid))
        );
        $engagement->coursecompleted = self::get_engagement_attr(
            "coursecompleted",
            $course,
            count(self::users_completed_all_module($course, $enrolledstudents, $cohortid))
        );
        return $engagement;
    }

    /**
     * Get Engagement Attributes
     * @param [object] $course Course Object
     * @param [object] $user Users List 
     */
    public static function get_engagement_attr($attrname, $course, $val) {
        return html_writer::link("javascript:void(0)", $val,
            array(
                "class" => "modal-trigger text-decoration-none",
                "data-courseid" => $course->id,
                "data-coursename" => $course->fullname,
                "data-action" => $attrname
            )
        );
    }

    /**
     * Get HTML table for userslist
     * @param [int] $courseid Course Id
     * @param [string] $action Action for users list
     * @return [string] HTML table of users list
     */
    public static function get_userslist_table($courseid, $action, $cohortid) {
        $table = new html_table();
        $table->attributes = array (
            "class" => "generaltable modal-table"
        );
        $data = self::get_userslist($courseid, $action, $cohortid);

        $table->head = $data->head;
        if (empty($data->data)) {
            $notavail = get_string("usersnotavailable", "report_elucidsitereport");
            $emptycell = new html_table_cell($notavail);
            $row = new html_table_row();
            $emptycell->colspan = count($table->head);
            $emptycell->attributes = array(
                "class" => "text-center"
            );
            $row->cells = array($emptycell);
            $table->data = array($row);
        } else {
            $table->data = $data->data;
        }
        return html_writer::table($table);
    }

    /**
     * Get Users list
     * @param [int] $courseid Course ID
     * @param [string] $action Action to get Users Data
     * @return [array] Users Data Array
     */
    public static function get_userslist($courseid, $action, $cohortid) {
        $course = get_course($courseid);

        switch($action) {
            case "enrolment":
                $usersdata = self::get_enrolled_users($course, $cohortid);
                ;
                break;
            case "visited":
                $usersdata = self::get_visited_users($course, $cohortid);
                ;
                break;
            case "activitystart":
                $usersdata = self::get_users_started_an_activity($course, $cohortid);
                break;
            case "completedhalf":
                $usersdata = self::get_users_completed_half_courses($course, $cohortid);
                break;
            case "coursecompleted":
                $usersdata = self::get_users_completed_courses($course, $cohortid);
                break;
        }
        return $usersdata;
    }

    /**
     * Get Enrolled users in a course
     * @param [object] $course Course Object
     * @return [array] Array of users list
     */
    public static function get_enrolled_users($course, $cohortid) {
        $coursecontext = context_course::instance($course->id);
        $users = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');

        $usersdata = new stdClass();
        $usresdata->head = array(
            get_string("name", "report_elucidsitereport"),
            get_string("email", "report_elucidsitereport")
        );

        $userdata->data = array();
        foreach($users as $user) {
            /* If cohort filter is there then get only users from cohort */
            if ($cohortid) {
                $cohorts = cohort_get_user_cohorts($user->id);
                if (!array_key_exists($cohortid, $cohorts)) {
                    continue;
                }
            }

            $usresdata->data[] = array(
                fullname($user),
                $user->email,
            );
        }
        return $usresdata;
    }

    /**
     * Get Visited users in a course
     * @param [object] $course Course Object
     * @return [array] Array of users list
     */
    public static function get_visited_users($course, $cohortid) {
        $users = self::get_course_visites($course->id);
        $usersdata = new stdClass();
        $userdata->head = array(
            get_string("name", "report_elucidsitereport"),
            get_string("email", "report_elucidsitereport")
        );

        $userdata->data = array();
        foreach($users as $user) {
            /* If cohort filter is there then get only users from cohort */
            if ($cohortid) {
                $cohorts = cohort_get_user_cohorts($user->id);
                if (!array_key_exists($cohortid, $cohorts)) {
                    continue;
                }
            }

            $user = core_user::get_user($user->userid);
            $userdata->data[] = array(
                fullname($user),
                $user->email,
            );
        }
        return $userdata;
    }

    /**
     * Get users who have completed an activity
     * @param [object] $course Course Object
     * @return [array] Array of users list
     */
    public static function get_users_started_an_activity($course, $cohortid) {
        $coursecontext = context_course::instance($course->id);
        $enrolledusers = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');
        $users = self::users_completed_a_module($course, $enrolledusers, $cohortid);

        $usersdata = new stdClass();
        $userdata->head = array(
            get_string("name", "report_elucidsitereport"),
            get_string("email", "report_elucidsitereport")
        );

        $userdata->data = array();
        foreach($users as $user) {
            /* If cohort filter is there then get only users from cohort */
            if ($cohortid) {
                $cohorts = cohort_get_user_cohorts($user->id);
                if (!array_key_exists($cohortid, $cohorts)) {
                    continue;
                }
            }

            $userdata->data[] = array(
                fullname($user),
                $user->email,
            );
        }
        return $userdata;
    }

    /**
     * Get users who have completed half of the course
     * @param [object] $course Course Object
     * @return [array] Array of users list
     */
    public static function get_users_completed_half_courses($course, $cohortid) {
        $coursecontext = context_course::instance($course->id);
        $enrolledusers = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');
        $users = self::users_completed_half_modules($course, $enrolledusers, $cohortid);

        $usersdata = new stdClass();
        $userdata->head = array(
            get_string("name", "report_elucidsitereport"),
            get_string("email", "report_elucidsitereport")
        );

        $userdata->data = array();
        foreach($users as $user) {
            /* If cohort filter is there then get only users from cohort */
            if ($cohortid) {
                $cohorts = cohort_get_user_cohorts($user->id);
                if (!array_key_exists($cohortid, $cohorts)) {
                    continue;
                }
            }

            $userdata->data[] = array(
                fullname($user),
                $user->email,
            );
        }
        return $userdata;
    }

    /**
     * Get users who have completed the course
     * @param [object] $course Course Object
     * @return [array] Array of users list
     */
    public static function get_users_completed_courses($course, $cohortid) {
        $coursecontext = context_course::instance($course->id);
        $enrolledusers = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');
        $users = self::users_completed_all_module($course, $enrolledusers, $cohortid);

        $usersdata = new stdClass();
        $userdata->head = array(
            get_string("name", "report_elucidsitereport"),
            get_string("email", "report_elucidsitereport")
        );

        $userdata->data = array();
        foreach($users as $user) {
            /* If cohort filter is there then get only users from cohort */
            if ($cohortid) {
                $cohorts = cohort_get_user_cohorts($user->id);
                if (!array_key_exists($cohortid, $cohorts)) {
                    continue;
                }
            }

            $userdata->data[] = array(
                fullname($user),
                $user->email,
            );
        }
        return $userdata;
    }
}