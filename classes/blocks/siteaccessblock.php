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

require_once($CFG->dirroot . "/local/edwiserreports/classes/constants.php");

use stdClass;
use cache;

/**
 * Class Site Access Inforamtion Block. To get the data related to site access.
 */
class siteaccessblock extends block_base {
    /**
     * Set response object for site access information
     * @var array
     */
    public $siteaccess = array();

    /**
     * Preapre layout for each block
     */
    public function get_layout() {

        // Layout related data.
        $this->layout->id = 'siteaccessblock';
        $this->layout->name = get_string('accessinfo', 'local_edwiserreports');
        $this->layout->info = get_string('accessinfoblockhelp', 'local_edwiserreports');

        // Block related data.
        $this->block = new stdClass();
        $this->block->displaytype = 'line-chart';

        // Add block view in layout.
        $this->layout->blockview = $this->render_block('siteaccessblock', $this->block);
        // Set block edit capabilities.
        $this->set_block_edit_capabilities($this->layout->id);

        // Return blocks layout.
        return $this->layout;
    }

    /**
     * Constructoe
     */
    public function __construct() {
        // Call parent constructor.
        parent::__construct();

        // Initialize the site access information response.
        $value = array(
            "opacity" => 0,
            "value" => 0
        );

        // Initialize access value for site access.
        $access = array($value, $value, $value, $value, $value, $value, $value);

        // Getting time strings for access inforamtion block.
        $times = array(
            get_string("time00", "local_edwiserreports"),
            get_string("time01", "local_edwiserreports"),
            get_string("time02", "local_edwiserreports"),
            get_string("time03", "local_edwiserreports"),
            get_string("time04", "local_edwiserreports"),
            get_string("time05", "local_edwiserreports"),
            get_string("time06", "local_edwiserreports"),
            get_string("time07", "local_edwiserreports"),
            get_string("time08", "local_edwiserreports"),
            get_string("time09", "local_edwiserreports"),
            get_string("time10", "local_edwiserreports"),
            get_string("time11", "local_edwiserreports"),
            get_string("time12", "local_edwiserreports"),
            get_string("time13", "local_edwiserreports"),
            get_string("time14", "local_edwiserreports"),
            get_string("time15", "local_edwiserreports"),
            get_string("time16", "local_edwiserreports"),
            get_string("time17", "local_edwiserreports"),
            get_string("time18", "local_edwiserreports"),
            get_string("time19", "local_edwiserreports"),
            get_string("time20", "local_edwiserreports"),
            get_string("time21", "local_edwiserreports"),
            get_string("time22", "local_edwiserreports"),
            get_string("time23", "local_edwiserreports")
        );

        // Initialize access inforamtion object.
        foreach ($times as $time) {
            $value = array(
                "access" => $access,
                "time" => $time
            );
            $this->siteaccess[] = $value;
        }
    }

    /**
     * Get Site access inforamtion data
     * @param  object $params Parameters
     * @return object         Site access information
     */
    public function get_data($params = false) {
        $response = new stdClass();

        $cache = cache::make('local_edwiserreports', 'siteaccess');

        if (!$data = $cache->get('siteaccessinfodata')) {
            $data = $this->get_siteaccess_info();
            $cache->set('siteaccessinfodata', $data);
        }

        $response->data = $data;
        return $response;
    }

    /**
     * Get site access information
     * @return [object] Site access inforamtion
     */
    public function get_siteaccess_info() {
        global $DB;
        // SQL to gey access info log.
        $sql = "SELECT id, action, timecreated
            FROM {logstore_standard_log}
            WHERE action = :action
            AND timecreated > :timecreated";

        // Getting access log.
        $timenow = time();
        $params = array (
            "action" => "viewed",
            "timecreated" => $timenow - LOCAL_SITEREPORT_ONEYEAR
        );
        $accesslog = $DB->get_records_sql($sql, $params);

        // Getting site access information object.
        $response = new stdClass();
        $response->siteaccess = $this->get_accessinfo(array_values($accesslog));
        return $response;
    }

    /**
     * Get Access information
     * @param  [array] $accesslog Array of access log
     * @return [object] Site Access Information
     */
    public function get_accessinfo($accesslog) {

        // Getting number of weeks to get access log.
        $timeduration = end($accesslog)->timecreated - $accesslog[0]->timecreated;
        $weeks = ceil($timeduration / LOCAL_SITEREPORT_ONEWEEK); // Weeks in time duaration.
        $weekmax = 0;
        // If weeks are there then.
        if ($weeks) {
            // Parse access log to save in access inforamtion object.
            foreach ($accesslog as $log) {
                // Column for weeks.
                $col = number_format(date("w", $log->timecreated));

                // Row for hours.
                $row = number_format(date("H", $log->timecreated));

                // Calculate site access for row and colums.
                $this->siteaccess[$row]["access"][$col]["value"] += (1 / ($weeks * 10));

                // Maximum value in week.
                if ($weekmax < $this->siteaccess[$row]["access"][$col]["value"]) {
                    $weekmax = $this->siteaccess[$row]["access"][$col]["value"];
                }
            }

            // Get Opacity value for siteaccess inforamtion.
            foreach ($this->siteaccess as $row => $value) {
                if ($weekmax) {
                    foreach ($value["access"] as $col => $val) {
                        $this->siteaccess[$row]["access"][$col]["opacity"] = $val["value"] / $weekmax;
                        $this->siteaccess[$row]["access"][$col]["value"] = (string)number_format($val['value'], 2);
                    }
                }
            }
        }
        return $this->siteaccess;
    }
}
