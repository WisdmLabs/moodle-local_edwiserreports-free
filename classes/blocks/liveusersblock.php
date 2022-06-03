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

use stdClass;
use html_writer;

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
        $this->layout->filters = $this->get_filters();

        // Add block view in layout.
        $this->layout->blockview = $this->render_block('liveusersblock', $this->block);
        // Set block edit capabilities.
        $this->set_block_edit_capabilities($this->layout->id);

        // Return blocks layout.
        return $this->layout;
    }

    /**
     * Prepare Inactive users filter
     * @return string Filter HTML content
     */
    public function get_filters() {
        global $OUTPUT;
        return $OUTPUT->render_from_template('local_edwiserreports/common-table-search-filter', [
            'searchicon' => $this->image_icon('actions/search'),
            'placeholder' => get_string('searchuser', 'local_edwiserreports')
        ]);
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
     * Get the listing of online users
     *
     * @param int $now Time now
     * @param int $timetoshowusers Number of seconds to show online users
     * @return array
     */
    protected static function get_users($now, $timetoshowusers) {
        global $USER, $DB, $CFG;

        $timefrom = 100 * floor(($now - $timetoshowusers) / 100); // Round to nearest 100 seconds for better query cache.

        $groupmembers = "";
        $groupselect  = "";
        $groupby       = "";
        $lastaccess    = ", lastaccess, currentlogin, lastlogin";
        $uservisibility = "";
        $uservisibilityselect = "";
        if ($CFG->block_online_users_onlinestatushiding) {
            $uservisibility = ", up.value AS uservisibility";
            $uservisibilityselect = "AND (" . $DB->sql_cast_char2int('up.value') . " = 1
                                    OR up.value IS NULL
                                    OR u.id = :userid)";
        }
        $params = array();

        if (class_exists('\core_user\fields')) {
            $userfieldsapi = \core_user\fields::for_userpic()->including('username', 'deleted');
            $userfields = $userfieldsapi->get_sql('u', false, '', '', false)->selects;
        } else {
            // Using fallback deprecated method for backword compatibility.
            $extrafields = get_extra_user_fields(\context_system::instance());
            $extrafields[] = 'username';
            $extrafields[] = 'deleted';
            $userfields = \user_picture::fields('u', $extrafields);
        }

        $params['now'] = $now;
        $params['timefrom'] = $timefrom;
        $params['userid'] = $USER->id;
        $params['name'] = 'block_online_users_uservisibility';

        $sql = "SELECT $userfields $lastaccess $uservisibility
                    FROM {user} u $groupmembers
                LEFT JOIN {user_preferences} up ON up.userid = u.id
                        AND up.name = :name
                    WHERE u.lastaccess > :timefrom
                        AND u.lastaccess <= :now
                        AND u.deleted = 0
                        $uservisibilityselect
                        $groupselect $groupby
                ORDER BY lastaccess DESC ";
        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Get online users data
     * @return array Array of online users
     */
    public static function get_online_users() {
        global $DB;

        $timenow = time();
        $activeusertimeout = 60;
        $inactiveusertimeout = 30 * 60;

        $activeusers = self::get_users($timenow, $activeusertimeout);
        $inactiveusers = self::get_users($timenow, $inactiveusertimeout);

        $users = array();
        foreach ($inactiveusers as $inactiveuser) {
            $user = array();
            $user["name"] = fullname($inactiveuser);

            if ($inactiveuser->lastlogin != 0) {
                $user["lastlogin"] = '<div class="d-none">' . $inactiveuser->lastlogin . '</div>';
                $user["lastlogin"] .= format_time($timenow - $inactiveuser->lastlogin);
            } else if ($inactiveuser->currentlogin != 0) {
                $user["lastlogin"] = '<div class="d-none">' . $inactiveuser->currentlogin . '</div>';
                $user["lastlogin"] .= format_time($timenow - $inactiveuser->currentlogin);
            } else {
                $user["lastlogin"] = get_string('never');
            }

            if (array_key_exists($inactiveuser->id, $activeusers)) {
                $user["status"] = html_writer::tag("span", get_string('active'), array("class" => "text-success"));
            } else {
                $user["status"] = html_writer::tag("span", get_string('inactive'), array(
                        "class" => "text-danger"
                    )
                );
            }
            $users[] = array_values($user);
        }
        return $users;
    }
}
