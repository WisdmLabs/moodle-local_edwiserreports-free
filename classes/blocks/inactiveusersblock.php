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

defined('MOODLE_INTERNAL') || die;

use stdClass;
use cache;

require_once($CFG->dirroot . "/local/edwiserreports/classes/constants.php");
/**
 * Class Inacive Users Block. To get the data related to inactive users block.
 */
class inactiveusersblock extends block_base {

    /**
     * Preapre layout for each block
     * @return object Layout object
     */
    public function get_layout() {
        // Layout related data.
        $this->layout->id = 'inactiveusersblock';
        $this->layout->name = get_string('inactiveusers', 'local_edwiserreports');
        $this->layout->info = get_string('inactiveusersblockhelp', 'local_edwiserreports');
        $this->layout->hasdownloadlink = true;
        $this->layout->filters = $this->get_inactiveusers_filter();

        // Block related data.
        $this->block = new stdClass();
        $this->block->displaytype = 'line-chart';

        // Add block view in layout.
        $this->layout->blockview = $this->render_block('inactiveusersblock', $this->block);
        // Set block edit capabilities.
        $this->set_block_edit_capabilities($this->layout->id);

        // Return blocks layout.
        return $this->layout;
    }

    /**
     * Prepare Inactive users filter
     * @return string Filter HTML content
     */
    public function get_inactiveusers_filter() {
        $html = '<button type="button" class="btn btn-sm dropdown-toggle mt-3" data-toggle="dropdown"
                 aria-expanded="true">Never</button>
                <div class="dropdown-menu dropdown-sm" role="menu" x-placement="top-start">
                <a class="active dropdown-item" href="javascript:void(0)" data-value="never" role="menuitem">
                    Never
                </a>
                <a class="dropdown-item" href="javascript:void(0)" data-value="1month" role="menuitem">
                    Before 1 Month
                </a>
                <a class="dropdown-item" href="javascript:void(0)" data-value="3month" role="menuitem">
                    Before 3 Month
                </a>
                <a class="dropdown-item" href="javascript:void(0)" data-value="6month" role="menuitem">
                    Before 6 Month
                </a>
                </div>';
        return $html;
    }

    /**
     * Get Inactive users data
     * @param  Object $params Parameters
     * @return object         Response object
     */
    public function get_data($params = false) {
        $filter = isset($params->filter) ? $params->filter : false;

        // Make cache for inactive users block.
        $cache = cache::make("local_edwiserreports", "courseprogress");

        $cachekey = "inactiveusers-" . $filter;

        // If cache not set for course progress.
        if (!$response = $cache->get($cachekey)) {
            $response = new stdClass();

            // Get response data.
            $response->data = self::get_inactiveusers($filter);

            // Set cache to get data for course progress.
            $cache->set($cachekey, $response);
        }

        // Return response.
        return $response;
    }

    /**
     * Get inactive users list
     * @param  string $filter Filter string
     * @param  bool   $iscsv  True if user list is for csv
     * @return array          Array of inactive users
     */
    public static function get_inactiveusers($filter = 'all', $iscsv = false) {
        global $DB;

        // Get current time.
        $timenow = time();

        // Get last login time using filter.
        switch ($filter) {
            case '1month':
                $lastlogin = $timenow - 1 * LOCAL_SITEREPORT_ONEMONTH;
                break;
            case '3month':
                $lastlogin = $timenow - 3 * LOCAL_SITEREPORT_ONEMONTH;
                break;
            case '6month':
                $lastlogin = $timenow - 6 * LOCAL_SITEREPORT_ONEMONTH;
                break;
            default:
                $lastlogin = 0;
        }

        // Query to get users who have not logged in.
        $sql = "SELECT * FROM {user} WHERE lastaccess <= :lastlogin
                AND deleted = 0 AND id > 1";
        $inparams['lastlogin'] = $lastlogin;

        // Get all users who are inactive.
        $users = $DB->get_records_sql($sql, $inparams);

        // Geenerate Inactive users return array.
        $inactiveusers = array();
        foreach ($users as $user) {
            $inactiveuser = array(
                "name" => fullname($user),
                "email" => $user->email
            );

            // If downloading the reports.
            if (!$iscsv) {
                $inactiveuser["lastlogin"] = '<div class="d-none">'.$user->lastlogin.'</div>';
            } else {
                $inactiveuser["lastlogin"] = '';
            }

            // Get last login by users.
            if ($user->lastlogin) {
                $inactiveuser["lastlogin"] .= format_time($timenow - $user->lastlogin);
            } else {
                $inactiveuser["lastlogin"] .= get_string('never');
            }

            // Put inactive users in inactive users table.
            $inactiveusers[] = array_values($inactiveuser);
        }

        // Return inactive users array.
        return $inactiveusers;
    }

    /**
     * Get headers for exportable data
     * @return array Header array
     */
    private static function get_headers() {
        return array(
            get_string('fullname', 'local_edwiserreports'),
            get_string('email', 'local_edwiserreports'),
            get_string('lastaccess', 'local_edwiserreports')
        );
    }

    /**
     * Get exportable data for inactive users
     * @param  string $filter Filter string
     * @return array          Inactive users array
     */
    public static function get_exportable_data_block($filter) {
        // Prepare inactive users data.
        $inactiveusers = array();
        $inactiveusers[] = self::get_headers();
        $inactiveusers = array_merge($inactiveusers, self::get_inactiveusers($filter, true));

        // Return all inactive users.
        return $inactiveusers;
    }
}
