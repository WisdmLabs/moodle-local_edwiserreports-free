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
 * @package     local_edwiserreports
 * @category    admin
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserreports;

defined('MOODLE_INTERNAL') || die();

use stdClass;
use html_writer;
use context_system;
use block_online_users\fetcher;

/**
 * Class live users Block. To get the data of live users.
 */
class liveusersblock extends block_base {
    /**
     * Preapre layout for active courses block
     * @return object Layout object
     */
    public function get_layout() {

        // Layout related data.
        $this->layout->id = 'liveusersblock';
        $this->layout->name = get_string('realtimeusers', 'local_edwiserreports');
        $this->layout->info = get_string('realtimeusersblockhelp', 'local_edwiserreports');

        // Add block view in layout.
        $this->layout->blockview = $this->render_block('liveusersblock', $this->block);
        // Set block edit capabilities.
        $this->set_block_edit_capabilities($this->layout->id);

        // Return blocks layout.
        return $this->layout;
    }

    /**
     * Get blocks data
     * @param  array $params Parameters
     * @return object        Response
     */
    public function get_data($params = false) {
        $response = new stdClass();
        $response->data = self::get_online_users();
        return $response;
    }

    /**
     * Get online users data
     * @return array Array of online users
     */
    public static function get_online_users() {
        global $DB;

        $timenow = time();
        $context = context_system::instance();
        $activeusertimeout = 60;
        $inactiveusertimeout = 30 * 60;

        $activefetcher = new fetcher(null, $timenow, $activeusertimeout, $context);
        $inactivefetcher = new fetcher(null, $timenow, $inactiveusertimeout, $context);

        $activeusers = $activefetcher->get_users(0);
        $inactiveusers = $inactivefetcher->get_users(0);

        $users = array();
        foreach ($inactiveusers as $inactiveuser) {
            $user = array();
            $user["name"] = fullname($inactiveuser);

            $lastlogin = array_values(
                $DB->get_records("logstore_standard_log", array(
                    "target" => "user",
                    "action" => "loggedin",
                    "userid" => $inactiveuser->id
                ), "timecreated DESC", "timecreated", 0, 1)
            );

            if (isset($lastlogin[0]->timecreated) && $lastlogin[0]->timecreated) {
                $user["lastlogin"] = '<div class="d-none">'.$lastlogin[0]->timecreated.'</div>';
                $user["lastlogin"] .= format_time($timenow - $lastlogin[0]->timecreated);
            } else {
                $user["lastlogin"] = get_string('never');
            }

            if (array_key_exists($inactiveuser->id, $activeusers)) {
                $user["status"] = html_writer::tag("span", "active", array("class" => "badge badge-success"));
            } else {
                $user["status"] = html_writer::tag("span", "inactive", array(
                        "class" => "badge badge-danger"
                    )
                );
            }
            $users[] = array_values($user);
        }
        return $users;
    }
}
