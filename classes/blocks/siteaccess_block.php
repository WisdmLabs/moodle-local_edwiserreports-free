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
    public $siteaccess = array(
        array(
            "access" => array(0, 0, 0, 0, 0, 0, 0),
            "time" => "12:00 AM"
        ),
        array(
            "access" => array(0, 0, 0, 0, 0, 0, 0),
            "time" => "01:00 AM"
        ),
        array(
            "access" => array(0, 0, 0, 0, 0, 0, 0),
            "time" => "02:00 AM"
        ),
        array(
            "access" => array(0, 0, 0, 0, 0, 0, 0),
            "time" => "03:00 AM"
        ),
        array(
            "access" => array(0, 0, 0, 0, 0, 0, 0),
            "time" => "04:00 AM"
        ),
        array(
            "access" => array(0, 0, 0, 0, 0, 0, 0),
            "time" => "05:00 AM"
        ),
        array(
            "access" => array(0, 0, 0, 0, 0, 0, 0),
            "time" => "06:00 AM"
        ),
        array(
            "access" => array(0, 0, 0, 0, 0, 0, 0),
            "time" => "07:00 AM"
        ),
        array(
            "access" => array(0, 0, 0, 0, 0, 0, 0),
            "time" => "08:00 AM"
        ),
        array(
            "access" => array(0, 0, 0, 0, 0, 0, 0),
            "time" => "09:00 AM"
        ),
        array(
            "access" => array(0, 0, 0, 0, 0, 0, 0),
            "time" => "10:00 AM"
        ),
        array(
            "access" => array(0, 0, 0, 0, 0, 0, 0),
            "time" => "11:00 AM"
        ),
        array(
            "access" => array(0, 0, 0, 0, 0, 0, 0),
            "time" => "12:00 AM"
        ),
        array(
            "access" => array(0, 0, 0, 0, 0, 0, 0),
            "time" => "12:00 AM"
        ),
        array(
            "access" => array(0, 0, 0, 0, 0, 0, 0),
            "time" => "01:00 PM"
        ),
        array(
            "access" => array(0, 0, 0, 0, 0, 0, 0),
            "time" => "02:00 PM"
        ),
        array(
            "access" => array(0, 0, 0, 0, 0, 0, 0),
            "time" => "03:00 PM"
        ),
        array(
            "access" => array(0, 0, 0, 0, 0, 0, 0),
            "time" => "04:00 PM"
        ),
        array(
            "access" => array(0, 0, 0, 0, 0, 0, 0),
            "time" => "05:00 PM"
        ),
        array(
            "access" => array(0, 0, 0, 0, 0, 0, 0),
            "time" => "06:00 PM"
        ),
        array(
            "access" => array(0, 0, 0, 0, 0, 0, 0),
            "time" => "07:00 PM"
        ),
        array(
            "access" => array(0, 0, 0, 0, 0, 0, 0),
            "time" => "08:00 PM"
        ),
        array(
            "access" => array(0, 0, 0, 0, 0, 0, 0),
            "time" => "09:00 PM"
        ),
        array(
            "access" => array(0, 0, 0, 0, 0, 0, 0),
            "time" => "10:00 PM"
        ),
        array(
            "access" => array(0, 0, 0, 0, 0, 0, 0),
            "time" => "11:00 PM"
        )
    );

    public static function get_data() {
        $response = new stdClass();
        $siteaccessblock = new siteaccess_block();
        $response->data = $siteaccessblock->get_siteaccess_info();
        return $response;
    }

    /**
     * Get site access information
     * @return [object] Site access inforamtion 
     */
    public function get_siteaccess_info() {
        global $DB;

        $oneyear = 365 * 24 * 60 * 60;
        $fromtime = time() - $oneyear;

        $sql = "SELECT id, action, timecreated FROM {logstore_standard_log}
            WHERE action = ? AND timecreated > ?";
        $accesslog = $DB->get_records_sql($sql, array("viewed", $fromtime));
        $siteaccess = new stdClass();
        $siteaccess->siteaccess = $this->get_accessinfo(array_values($accesslog));
        return $siteaccess;
    }

    /**
     * Get Access information
     * @param  [array] $accesslog Array of access log
     * @return [object] Site Access Information
     */
    public function get_accessinfo($accesslog) {
        global $DB;

        $oneweek = 7 * 24 * 60 * 60;
        $timeduration = end($accesslog)->timecreated - $accesslog[0]->timecreated;
        $userscount = $DB->count_records("user", array("deleted" => false));
        $weeks = ceil($timeduration / $oneweek);

        $weekmax = 0;
        if ($weeks) {
            foreach($accesslog as $log) {
                $col = number_format(date("w", $log->timecreated));
                $row = number_format(date("H", $log->timecreated));

                $this->siteaccess[$row]["access"][$col] += (1 / ($weeks));
                if ($weekmax < $this->siteaccess[$row]["access"][$col]) {
                    $weekmax > $this->siteaccess[$row]["access"][$col];
                }
            }
        }

        foreach ($this->siteaccess as $x => $value) {
            if ($weekmax) {
                foreach ($value as $y => $val) {
                    $this->siteaccess[$row]["access"][$col] = $val / $weekmax;
                }
            }
        }

        return $this->siteaccess;
    }

    /*public static function get_accessinfo($accesslog, $time) {
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
    }*/
}