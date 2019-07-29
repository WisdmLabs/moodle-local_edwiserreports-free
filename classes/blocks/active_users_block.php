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
use core_user;
use html_table;
use html_writer;
use html_table_cell;
use html_table_row;

/**
 * Class Acive Users Block
 * To get the data related to active users block
 */
class active_users_block extends utility {
    public static $firstaccess;
    public static $timenow;
    public static $oneday = 24 * 60 * 60;
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
        $response->data->activeUsers = self::get_active_users($filter);
        $response->data->enrolments = self::get_enrolments($filter);
        $response->data->completionRate = self::get_course_completionrate($filter);
        $response->labels = self::$labels;
        return $response;
    }

    /**
     * Get users list for a perticuler action
     */
    public static function get_userslist($filter, $action) {
        global $DB;

        $sql = "SELECT DISTINCT userid
                FROM {logstore_standard_log}
                WHERE eventname = ?
                AND timecreated > ?
                AND timecreated <= ?";

        $table = new html_table();
        $table->head = array(
            get_string("fullname", "report_elucidsitereport"),
            get_string("email", "report_elucidsitereport")
        );
        $table->attributes["class"] = "generaltable modal-table";

        $records = array();
        switch ($action) {
            case "activeusers":
                $records = $DB->get_records_sql($sql, array('\core\event\user_loggedin', $filter, $filter + self::$oneday));
                if (empty($records)) {
                    $emptycell = new html_table_cell(get_string("usersnotavailable", "report_elucidsitereport"));
                    $row = new html_table_row();
                    $emptycell->colspan = count($table->head);
                    $emptycell->attributes['class'] = "text-center";
                    $row->cells = array($emptycell);
                    $table->data = array($row);
                } else {
                    foreach ($records as $record) {
                        $user = core_user::get_user($record->userid);
                        $table->data[] = array(
                            fullname($user),
                            $user->email
                        );
                    }
                }
                break;
            case "enrolments":
                $records = $DB->get_records_sql($sql, array('\core\event\user_enrolment_created', $filter, $filter + self::$oneday));
                if (empty($records)) {
                    $emptycell = new html_table_cell(get_string("usersnotavailable", "report_elucidsitereport"));
                    $row = new html_table_row();
                    $emptycell->colspan = count($table->head);
                    $emptycell->attributes['class'] = "text-center";
                    $row->cells = array($emptycell);
                    $table->data = array($row);
                } else {
                    foreach ($records as $record) {
                        $user = core_user::get_user($record->userid);
                        $table->data[] = array(
                            fullname($user),
                            $user->email
                        );
                    }
                }
                break;
            case "completions":
                $sql = "SELECT userid, course
                    FROM {course_completions}
                    WHERE timecompleted > ?
                    AND timecompleted <= ?";
                $records = $DB->get_records_sql($sql, array($filter, $filter + self::$oneday));

                if (empty($records)) {
                    $emptycell = new html_table_cell(get_string("usersnotavailable", "report_elucidsitereport"));
                    $row = new html_table_row();
                    $emptycell->colspan = count($table->head);
                    $emptycell->attributes['class'] = "text-center";
                    $row->cells = array($emptycell);
                    $table->data = array($row);
                } else {
                    foreach ($records as $record) {
                        $user = core_user::get_user($record->userid);
                        $course = $DB->get_record('course', array('id' => $record->course));
                        $table->data[] = array(
                            fullname($user),
                            $user->email,
                            $course->fullname
                        );
                    }
                }
                break;
        }

        return html_writer::table($table);
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

        $labels = array();
        $activeusers = array();
        for ($i = self::$xlabelcount; $i > 0; $i--) {
            if (!isset($endtime)) {
                $endtime = self::$timenow;
                $starttime = strtotime('today midnight');
            } else {
                $endtime = $starttime;
                $starttime = $endtime - self::$oneday;
            }

            $labels[] = date("d M y", $starttime);
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
                $endtime = self::$timenow;
                $starttime = strtotime('today midnight');
            } else {
                $endtime = $starttime;
                $starttime = $endtime - self::$oneday;
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
                $endtime = self::$timenow;
                $starttime = strtotime('today midnight');
            } else {
                $endtime = $starttime;
                $starttime = $endtime - self::$oneday;
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
                    self::$xlabelcount = ceil((self::$timenow - self::$firstaccess) / self::$oneday);
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
                        $startdate = strtotime($dates[0] . " 00:00:00");
                        $enddate = strtotime($dates[1] . " 23:59:59");
                    }

                    if ($startdate && $enddate) {
                        self::$xlabelcount = ceil($enddate - $startdate) / self::$oneday;
                        self::$timenow = $enddate;
                    } else {
                        self::$xlabelcount = 7;
                    }
            }
        } else {
            self::$firstaccess = self::$timenow;
        }
    }
}