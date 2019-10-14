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
use cache;

require_once($CFG->dirroot . "/report/elucidsitereport/classes/constants.php");

/**
 * Class Inacive Users Block
 * To get the data related to inactive users block
 */
class inactiveusers_block extends utility {
    /**
     * Get Inactive users data
     * @param  [String] $filter Filter
     * @return [object] response object
     */
    public static function get_data($filter) {
        // Make cache for inactive users block
        $cache = cache::make("report_elucidsitereport", "courseprogress");
        $cachekey = "inactiveusers-" . $filter;

        // If cache not set for course progress
        if (!$response = $cache->get($cachekey)) {
            $response = new stdClass();

            // Get response data
            $response->data = self::get_inactiveusers($filter);

            // Set cache to get data for course progress
            $cache->set($cachekey, $response);
        }

        // Return response
        return $response;
    }

    /**
     * Get inactive users list
     * @param  [String] $filter Filter
     * @return [Array] Array of inactive users
     */
    public static function get_inactiveusers($filter) {
        global $DB;

        // Get current time
        $timenow = time();

        // Get last login time using filter
        switch ($filter) {
            case '1month':
                $lastlogin = $timenow - 1 * ONEMONTH;
                break;
            case '3month':
                $lastlogin = $timenow - 3 * ONEMONTH;
                break;
            case '6month':
                $lastlogin = $timenow - 6 * ONEMONTH;
                break;
            default:
                $lastlogin = 0;
        }

        // Query to get users who have not logged in
        $sql = "SELECT * FROM {user} WHERE lastlogin <= ?
                AND deleted = 0 AND id > 1";

        // Get all users who are inactive
        $users = $DB->get_records_sql($sql, array($lastlogin));

        // Geenerate Inactive users return array
        $inactiveusers = array();
        foreach ($users as $user) {
            $inactiveuser = array(
                "name" => fullname($user),
                "email" => $user->email
            );

            $inactiveuser["lastlogin"] = '<div class="d-none">'.$user->lastlogin.'</div>';
            if ($user->lastlogin) {
                $inactiveuser["lastlogin"] .= format_time($timenow - $user->lastlogin);
            } else {
                $inactiveuser["lastlogin"] .= get_string('never');
            }

            $inactiveusers[] = array_values($inactiveuser);
        }

        // Return inactive users array
        return $inactiveusers;
    }
}