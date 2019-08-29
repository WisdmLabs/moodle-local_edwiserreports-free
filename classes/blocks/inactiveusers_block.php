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
 * Class Inacive Users Block
 * To get the data related to inactive users block
 */
class inactiveusers_block extends utility {
    public static function get_data($filter) {
        $response = new stdClass();
        $response->data = self::get_inactiveusers($filter);
        return $response;
    }

    public static function get_inactiveusers($filter) {
        global $DB;

        $lastlogin = 0;
        $timenow = time();
        switch ($filter) {
            case '1month':
                $lastlogin = $timenow - 1 * 30 * 24 * 60 * 60;
                break;
            case '3month':
                $lastlogin = $timenow - 3 * 30 * 24 * 60 * 60;
                break;
            case '6month':
                $lastlogin = $timenow - 6 * 30 * 24 * 60 * 60;
                break;
        }

        $sql = "SELECT * FROM {user} WHERE lastlogin <= ?
                AND deleted = 0 AND id > 1";
        $inactiveusers = array();
        $users = $DB->get_records_sql($sql, array($lastlogin));

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
        return $inactiveusers;
    }
}