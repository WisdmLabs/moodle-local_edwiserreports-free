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

use core_user;
use html_table;
use html_table_cell;
use html_table_row;
use html_writer;
use stdClass;

/**
 * Class Acive Users Block
 * To get the data related to active users block
 */

class active_users_block extends utility {
    public static $firstaccess;
    public static $timenow;
    public static $oneday = 24*60*60;
    public static $labels;
    public static $xlabelcount;

    /**
     * Time interval for table x-axis
     * 0 - daily
     * 1 - weekly
     * 2 - monthly
     * 3 - yearly
     */
    public static $interval = 3;

    /**
     * Get active user, enrolment, completion
     * @param  string $filter date filter to get data
     * @return stdClass active users graph data
     */
    public static function get_data($filter, $cohortid = false) {
        self::$timenow = time();

        $response = new stdClass();
        $response->data = new stdClass();
        self::set_global_values_for_graph($filter);
        $response->data->activeUsers = self::get_active_users($filter, $cohortid);
        $response->data->enrolments = self::get_enrolments($filter, $cohortid);
        $response->data->completionRate = self::get_course_completionrate($filter, $cohortid);
        $response->labels = self::$labels;
        return $response;
    }

    /**
     * Get header for export data actvive users
     * @return [array] Array of headers of exportable data
     */
    public static function get_header() {
        $header = array(
            get_string("date", "report_elucidsitereport"),
            get_string("noofactiveusers", "report_elucidsitereport"),
            get_string("noofenrolledusers", "report_elucidsitereport"),
            get_string("noofcompletedusers", "report_elucidsitereport"),
        );

        return $header;
    }

    /**
     * Get header for export data actvive users individual page
     * @return [array] Array of headers of exportable data
     */
    public static function get_header_report() {
        $header = array(
            get_string("date", "report_elucidsitereport"),
            get_string("fullname", "report_elucidsitereport"),
            get_string("email", "report_elucidsitereport"),
            get_string("status", "report_elucidsitereport"),
        );

        return $header;
    }

    /**
     * Create users list table for active users block
     * @param [string] $filter Time filter to get users for this day
     * @param [string] $action Get users list for this action
     * @param [int] $cohortid Get users list for this action
     * @return [array] Array of users data fields (Full Name, Email)
     */
    public static function get_userslist_table($filter, $action, $cohortid) {
        $table = new html_table();
        $table->head = array(
            get_string("fullname", "report_elucidsitereport"),
            get_string("email", "report_elucidsitereport"),
        );
        $table->attributes = array (
            "class" => "generaltable modal-table"
        );
        $data = self::get_userslist($filter, $action, $cohortid);

        if (empty($data)) {
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
            $table->data = $data;
        }
        return html_writer::table($table);
    }
    
    /**
     * Get users list data for active users block
     * @param [string] $filter Time filter to get users for this day
     * @param [string] $action Get users list for this action
     * @param [int] $cohortid Cohort Id
     * @return [string] HTML table string of users list
     * Columns are (Full Name, Email)
     */
    public static function get_userslist($filter, $action, $cohortid = false) {
        global $DB;
        
        $params = array();
        if ($cohortid) {
            $params["cohortid"] = $cohortid;
        }

        switch($action) {
            case "activeusers":
                $params["eventname"] = '\core\event\user_loggedin';
                break;
            case "enrolments":
                $params["eventname"] = '\core\event\user_enrolment_created';
                break;
        }

        $sql = self::get_sql_query($action, $filter, $cohortid);
        $params["starttime"] = $filter;
        $params["endtime"] = $filter + self::$oneday;

        $data = array();
        $records = $DB->get_records_sql($sql, $params);
        if (!empty($records)) {
            foreach ($records as $record) {
                $user = core_user::get_user($record->userid);
                $userdata = array();
                $userdata[] = fullname($user);
                $userdata[] = $user->email;
                if ($action == "completions") {
                    $course = get_course($record->course);
                    $userdata[] = $course->name;
                }
                $data[] = $userdata;
            }
        }
        return $data;
    }

    /**
     * Get all active users
     * @param [string] $filter Duration String
     * @param [int] $cohortid Cohort ID
     * @return array Array of all active users based
     */
    public static function get_active_users($filter, $cohortid) {
        global $DB;

        $params = array(
            "eventname" => '\core\event\user_loggedin'
        );

        $sql = self::get_sql_query("activeusers", $filter, $cohortid);
        if ($cohortid) {
            $params["cohortid"] = $cohortid;
        }

        $labels = array();
        $activeusers = array();
        for ($i = self::$xlabelcount; $i > 0; $i--) {
            if (!isset($params["endtime"])) {
                $params["endtime"] = self::$timenow;
                $params["starttime"] = strtotime(date("d M y", self::$timenow));
            } else {
                $params["endtime"] = $params["starttime"];
                $params["starttime"] = $params["starttime"] - self::$oneday;
            }

            $labels[] = date("d M y", $params["starttime"]);
            $users = $DB->get_records_sql($sql, $params);
            $activeusers[] = count($users);
        }

        /* Reverse the array because the graph take
        value from left to right */
        self::$labels = array_reverse($labels);
        return array_reverse($activeusers);
    }

    /**
     * Get all Enrolments
     * @param string $filter apply filter duration
     * @return array Array of all active users based
     */
    public static function get_enrolments($filter, $cohortid) {
        global $DB;

        $params = array(
            "eventname" => '\core\event\user_enrolment_created'
        );

        $sql = self::get_sql_query("enrolments", $filter, $cohortid);
        if ($cohortid) {
            $params["cohortid"] = $cohortid;
        }

        $enrolments = array();
        for ($i = self::$xlabelcount; $i > 0; $i--) {
            if (!isset($params["endtime"])) {
                $params["endtime"] = self::$timenow;
                $params["starttime"] = strtotime('today midnight');
            } else {
                $params["endtime"] = $params["starttime"];
                $params["starttime"] = $params["starttime"] - self::$oneday;
            }

            $enrolments[] = count($DB->get_records_sql($sql, $params));
        }
        return array_reverse($enrolments);
    }

    /**
     * Get all Enrolments
     * @param string $filter apply filter duration
     * @return array Array of all active users based
     */
    public static function get_course_completionrate($filter, $cohortid) {
        global $DB;

        $params = array();
        $sql = self::get_sql_query("completions", $filter, $cohortid);
        if ($cohortid) {
            $params["cohortid"] = $cohortid;
        }

        $completionrate = array();
        for ($i = self::$xlabelcount; $i > 0; $i--) {
            if (!isset($params["endtime"])) {
                $params["endtime"] = self::$timenow;
                $params["starttime"] = strtotime('today midnight');
            } else {
                $params["endtime"] = $params["starttime"];
                $params["starttime"] = $params["starttime"] - self::$oneday;
            }

            $completionrate[] = count($DB->get_records_sql($sql, $params));
        }
        return array_reverse($completionrate);
    }

    /**
     * Set all global values from graph
     * @param [string] $filter Range selector
     */
    public static function set_global_values_for_graph($filter) {
        global $DB;

        $sql = "SELECT id, userid, timecreated
                FROM {logstore_standard_log}";
        $params = array();

        $sql .= " ORDER BY timecreated ASC";
        $records = array_values($DB->get_records_sql($sql, $params));

        if (!empty($records)) {
            self::$firstaccess = $records[0]->timecreated;
            switch ($filter) {
                case 'all':
                    self::$xlabelcount = ceil((self::$timenow-self::$firstaccess)/self::$oneday);
                    break;
                case 'monthly':
                    self::$xlabelcount = 30;
                    break;
                case 'yearly':
                    self::$xlabelcount = 365;
                    break;
                case 'weekly':
                    self::$xlabelcount = 7;
                    break;
                default:
                    $dates = explode(" to ", $filter);
                    if (count($dates) == 2) {
                        $startdate = strtotime($dates[0]." 00:00:00");
                        $enddate = strtotime($dates[1]." 23:59:59");
                    }
                    if ($startdate && $enddate) {
                        self::$xlabelcount = ceil($enddate-$startdate)/self::$oneday;
                        self::$timenow = $enddate;
                    } else {
                        self::$xlabelcount = 7;
                    }
            }
        } else {
            self::$firstaccess = self::$timenow;
        }
    }

    /**
     * Get Exportable data for Active Users Block
     * @param $filter [string] Filter to get data from specific range
     * @return [array] Array of exportable data
     */
    public static function get_exportable_data_block($filter) {
        $cohortid = optional_param("cohortid", 0, PARAM_INT);
        $export = array();
        $export[] = self::get_header();
        $activeusersdata = self::get_data($filter);
        foreach ($activeusersdata->labels as $key => $lable) {
            $export[] = array(
                $lable,
                $activeusersdata->data->activeUsers[$key],
                $activeusersdata->data->enrolments[$key],
                $activeusersdata->data->completionRate[$key],
            );
        }

        return $export;
    }

    /**
     * Get Exportable data for Active Users Page
     * @param $filter [string] Filter to get data from specific range
     * @return [array] Array of exportable data
     */
    public static function get_exportable_data_report($filter) {
        $export = array();
        $export[] = active_users_block::get_header_report();
        $activeusersdata = active_users_block::get_data($filter);
        foreach ($activeusersdata->labels as $key => $lable) {
            $export = array_merge($export,
                self::get_usersdata($lable, "activeusers"),
                self::get_usersdata($lable, "enrolments"),
                self::get_usersdata($lable, "completions")
            );
        }

        return $export;
    }

    /**
     * Get User Data for Active Users Block
     * @param [string] $lable Date for lable
     * @param [string] $action Action for getting data
     */
    public static function get_usersdata($lable, $action) {
        $usersdata = array();
        $users = active_users_block::get_userslist(strtotime($lable), "activeusers");
        foreach ($users as $key => $user) {
            $user = array_merge(
                array(
                    $lable
                ),
                $user,
                array(
                    get_string($action . "_status", "report_elucidsitereport")
                )
            );
            $usersdata[] = $user;
        }
        return $usersdata;
    }

    /**
     * Get sql for active users data
     * @param [string] $action Action to perform
     * @param [string] $filter Date range selector
     * @param [int] $cohortid Cohort Id
     * @return [string] String of sql to get data
     */
    public static function get_sql_query($action, $filter, $cohortid) {
        $sql = '';
        switch($action) {
            case "activeusers":
            case "enrolments":
                /* If cohort filter is added then get
                only cohort members */
                if ($cohortid) {
                    $params["cohortid"] = $cohortid;
                    $sql = "SELECT DISTINCT l.userid
                        FROM {logstore_standard_log} l
                        JOIN {cohort_members} cm
                        ON l.userid = cm.userid
                        WHERE cm.cohortid = :cohortid
                        AND l.eventname = :eventname
                        AND l.timecreated > :starttime
                        AND l.timecreated <= :endtime";
                } else {
                    $sql = "SELECT DISTINCT userid
                        FROM {logstore_standard_log}
                        WHERE eventname = :eventname
                        AND timecreated > :starttime
                        AND timecreated <= :endtime";
                }
                break;
            case "completions":
                /* If cohort filter is added then get
                only cohort members */
                if ($cohortid) {
                    $params["cohortid"] = $cohortid;
                    $sql = "SELECT DISTINCT cc.userid, cc.course
                        FROM {course_completions} cc
                        JOIN {cohort_members} cm
                        ON cc.userid = cm.userid
                        WHERE cm.cohortid = :cohortid
                        AND cc.timecompleted > :starttime
                        AND cc.timecompleted <= :endtime";
                } else {
                    $sql = "SELECT DISTINCT userid, course
                        FROM {course_completions}
                        WHERE timecompleted > :starttime
                        AND timecompleted <= :endtime";
                }
        }
        return $sql;
    }
}