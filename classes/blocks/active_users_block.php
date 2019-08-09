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
    public static function get_data($filter) {
        self::$timenow = time();

        $response = new stdClass();
        $response->data = new stdClass();
        self::set_global_values_for_graph($filter);
        $response->data->activeUsers    = self::get_active_users($filter);
        $response->data->enrolments     = self::get_enrolments($filter);
        $response->data->completionRate = self::get_course_completionrate($filter);
        $response->labels               = self::$labels;
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
     * @param $filter [string] Time filter to get users for this day
     * @param $action [string] Get users list for this action
     * @return [array] Array of users data fields (Full Name, Email)
     */
    public static function get_userslist_table($filter, $action) {
        $table = new html_table();
        $table->head = array(
            get_string("fullname", "report_elucidsitereport"),
            get_string("email", "report_elucidsitereport"),
        );
        $table->attributes = array (
            "class" => "generaltable modal-table"
        );
        $data = self::get_userslist($filter, $action);

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
     * @param $filter [string] Time filter to get users for this day
     * @param $action [string] Get users list for this action
     * @return [string] HTML table string of users list
     * Columns are (Full Name, Email)
     */
    public static function get_userslist($filter, $action) {
        global $DB;
        $sql = "SELECT DISTINCT userid
                FROM {logstore_standard_log}
                WHERE eventname = ?
                AND timecreated > ?
                AND timecreated <= ?";
        $params = array();

        switch($action) {
            case "activeusers":
                $params = array(
                    '\core\event\user_loggedin',
                    $filter,
                    $filter + self::$oneday
                );
                break;
            case "enrolments":
                $params = array(
                    '\core\event\user_enrolment_created',
                    $filter,
                    $filter + self::$oneday
                );
                break;
            case "completions":
                $sql = "SELECT userid, course
                    FROM {course_completions}
                    WHERE timecompleted > ?
                    AND timecompleted <= ?" ;
                $params = array(
                    $filter,
                    $filter + self::$oneday
                );
                break;
        }

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
     * @param string $filter apply filter duration
     * @return array Array of all active users based
     */
    public static function get_active_users($filter) {
        global $DB;

        $sql = "SELECT DISTINCT userid
                FROM {logstore_standard_log}
                WHERE eventname = ?
                AND timecreated > ?
                AND timecreated <= ?";

        $labels      = array();
        $activeusers = array();
        for ($i = self::$xlabelcount; $i > 0; $i--) {
            if (!isset($endtime)) {
                $endtime   = self::$timenow;
                $starttime = strtotime('today midnight');
            } else {
                $endtime   = $starttime;
                $starttime = $endtime-self::$oneday;
            }

            $labels[]      = date("d M y", $starttime);
            $activeusers[] = count($DB->get_records_sql($sql, array('\core\event\user_loggedin', $starttime, $endtime)));
        }
        self::$labels = array_reverse($labels);
        return array_reverse($activeusers);
    }

    /**
     * Get all Enrolments
     * @param string $filter apply filter duration
     * @return array Array of all active users based
     */
    public static function get_enrolments($filter) {
        global $DB;

        $sql = "SELECT DISTINCT userid
                FROM {logstore_standard_log}
                WHERE eventname = ?
                AND timecreated > ?
                AND timecreated <= ?";

        $enrolments = array();
        for ($i = self::$xlabelcount; $i > 0; $i--) {
            if (!isset($endtime)) {
                $endtime   = self::$timenow;
                $starttime = strtotime('today midnight');
            } else {
                $endtime   = $starttime;
                $starttime = $endtime-self::$oneday;
            }

            $enrolments[] = count($DB->get_records_sql($sql, array('\core\event\user_enrolment_created', $starttime, $endtime)));
        }
        return array_reverse($enrolments);
    }

    /**
     * Get all Enrolments
     * @param string $filter apply filter duration
     * @return array Array of all active users based
     */
    public static function get_course_completionrate($filter) {
        global $DB;

        $sql = "SELECT DISTINCT userid
                FROM {course_completions}
                WHERE timecompleted > ?
                AND timecompleted <= ?";

        $completionrate = array();
        for ($i = self::$xlabelcount; $i > 0; $i--) {
            if (!isset($endtime)) {
                $endtime   = self::$timenow;
                $starttime = strtotime('today midnight');
            } else {
                $endtime   = $starttime;
                $starttime = $endtime-self::$oneday;
            }

            $completionrate[] = count($DB->get_records_sql($sql, array($starttime, $endtime)));
        }
        return array_reverse($completionrate);
    }

    /**
     * Set all global values from graph
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
                        $enddate   = strtotime($dates[1]." 23:59:59");
                    }

                    if ($startdate && $enddate) {
                        self::$xlabelcount = ceil($enddate-$startdate)/self::$oneday;
                        self::$timenow     = $enddate;
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
}