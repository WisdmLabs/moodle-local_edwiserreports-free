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
    public static function get_active_users_graph_data($filter) {
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
     * Get all active users
     * @param string $filter apply filter duration
     * @return array Array of all active users based
     */
    public static function get_active_users($filter) {
        global $DB;

        $sql = "SELECT DISTINCT userid
                FROM {logstore_standard_log}
                WHERE eventname = ? AND timecreated BETWEEN ? AND ?";

        $labels = array();
        $activeusers = array();
        for ($i = self::$xlabelcount; $i > 0; $i--) {
            if (!isset($endtime)) {
                $endtime = self::$timenow;
                $starttime = self::$timenow - self::$oneday;
            } else {
                $endtime = $starttime - 1;
                $starttime = $endtime - self::$oneday;
            }

            $labels[] = date("d M y", $endtime);
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
                WHERE eventname = ? AND timecreated BETWEEN ? AND ?";

        $enrolments = array();
        for ($i = self::$xlabelcount; $i > 0; $i--) {
            if (!isset($endtime)) {
                $endtime = self::$timenow;
                $starttime = self::$timenow - self::$oneday;
            } else {
                $endtime = $starttime - 1;
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
                WHERE timecompleted BETWEEN ? AND ?";

        $completionrate = array();
        for ($i = self::$xlabelcount; $i > 0; $i--) {
            if (!isset($endtime)) {
                $endtime = self::$timenow;
                $starttime = self::$timenow - self::$oneday;
            } else {
                $endtime = $starttime - 1;
                $starttime = $endtime - self::$oneday;
            }

            $completionrate[] = count($DB->get_records_sql($sql, array($starttime, $endtime)));
        }
        return array_reverse($completionrate);
    }

    /**
     * Set all global values from graph
     */
    public static function set_global_values_for_graph ($filter) {
        global $DB;

        $sql = "SELECT id, userid, timecreated
                FROM {logstore_standard_log}";
        $params = array();

        $sql .= " ORDER BY timecreated ASC";
        $records = array_values($DB->get_records_sql($sql, $params));

        if (!empty($records)) {
            self::$firstaccess = $records[0]->timecreated;
            switch ($filter) {
                case 'weekly':
                    self::$xlabelcount = 7;
                    break;
                case 'monthly':
                    self::$xlabelcount = 30;
                    break;
                case 'yearly':
                    self::$xlabelcount = 365;
                    break;
                case 'yearly':
                    self::$xlabelcount = 365;
                    break;
                case 'fiveyearly':
                    self::$xlabelcount = 5 * 365;
                    break;
                default:
                    self::$xlabelcount = ceil((self::$timenow - self::$firstaccess) / (24 * 60 * 60));
                    break;
            }
        } else {
            self::$firstaccess = self::$timenow;
        }
    }
}