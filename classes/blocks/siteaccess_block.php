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
 * Class Site Access Inforamtion Block
 * To get the data related to site access
 */
class siteaccess_block extends utility {
    public static function get_data() {
        $response = new stdClass();
        $response->data = self::get_siteaccess_info();
        return $response;
    }

    public static function get_siteaccess_info() {
        global $DB;

        $accesslog = $DB->get_records("logstore_standard_log", array());
        $siteaccess = new stdClass();
        $siteaccess->weekly = array();
        for ($time = 0; $time < 24; $time++) {
            $timeaccess = new stdClass();
            $timeaccess->access = self::get_accessinfo($accesslog, $time * 60 * 60);
            $timeaccess->time = gmdate("h:i A", $time * 60 * 60);
            $siteaccess->weekly[] = $timeaccess;
        }
        return $siteaccess;
    }

    public static function get_accessinfo($accesslog, $time) {
        global $DB;
        $allusers = $DB->get_records("user", array("deleted" => false));
        $access = array();
        $weeklyaccess = array(
            0 => array(),
            1 => array(),
            2 => array(),
            3 => array(),
            4 => array(),
            5 => array(),
            6 => array()
        );

        $starttime = explode("%", gmdate("H:i%D", $time));
        $endtime = explode("%", gmdate("H:i%D", $time + 60 * 60));
        foreach ($accesslog as $log) {
            $logtime = explode("%", date("H:i%D", $log->timecreated));
            $timecompare = $logtime[0] < $endtime[0] && $logtime[0] > $starttime[0];

            switch (true) {
                case ($log->userid > 0 && $timecompare && "Mon" == $logtime[1]):
                    if ($log->userid > 0 && !in_array($log->userid, $weeklyaccess[0])) {
                        $weeklyaccess[0][] = $log->userid;
                    }
                    break;
                case ($log->userid > 0 && $timecompare && "Tue" == $logtime[1]):
                    if (!in_array($log->userid, $weeklyaccess[1])) {
                        $weeklyaccess[1][] = $log->userid;
                    }
                    break;
                case ($log->userid > 0 && $timecompare && "Wed" == $logtime[1]):
                    if (!in_array($log->userid, $weeklyaccess[2])) {
                        $weeklyaccess[2][] = $log->userid;
                    }
                    break;
                case ($log->userid > 0 && $timecompare && "Thu" == $logtime[1]):
                    if (!in_array($log->userid, $weeklyaccess[3])) {
                        $weeklyaccess[3][] = $log->userid;
                    }
                    break;
                case ($log->userid > 0 && $timecompare && "Fri" == $logtime[1]):
                    if (!in_array($log->userid, $weeklyaccess[4])) {
                        $weeklyaccess[4][] = $log->userid;
                    }
                    break;
                case ($log->userid > 0 && $timecompare && "Sat" == $logtime[1]):
                    if (!in_array($log->userid, $weeklyaccess[5])) {
                        $weeklyaccess[5][] = $log->userid;
                    }
                    break;
                case ($log->userid > 0 && $timecompare && "Sun" == $logtime[1]):
                    if (!in_array($log->userid, $weeklyaccess[6])) {
                        $weeklyaccess[6][] = $log->userid;
                    }
                    break;
            }
        }

        foreach ($weeklyaccess as $key => $eachweekaccess) {
            $access[$key] = count($eachweekaccess) / count($allusers);
        }
        return $access;
    }
}