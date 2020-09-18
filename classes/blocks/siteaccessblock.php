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

require_once($CFG->dirroot . "/report/elucidsitereport/classes/constants.php");

use stdClass;
use cache;

/**
 * Class Site Access Inforamtion Block
 * To get the data related to site access
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
        global $CFG;

        // Layout related data
        $this->layout->id = 'siteaccesssblock';
        $this->layout->name = get_string('accessinfo', 'report_elucidsitereport');
        $this->layout->info = get_string('accessinfoblockhelp', 'report_elucidsitereport');

        // Block related data
        $this->block = new stdClass();
        $this->block->displaytype = 'line-chart';

        // Add block view in layout
        $this->layout->blockview = $this->render_block('siteaccessblock', $this->block);

        // Return blocks layout
        return $this->layout;
    }

    /**
     * Constructoe
     */
    public function __construct() {
        // Call parent constructor
        parent::__construct();

        // Initialize the site access information response
        $value = array(
            "opacity" => 0,
            "value" => 0
        );

        // Initialize access value for site access
        $access = array($value, $value, $value, $value, $value, $value, $value);

        // Getting time strings for access inforamtion block
        $times = array(
            get_string("time00", "report_elucidsitereport"),
            get_string("time01", "report_elucidsitereport"),
            get_string("time02", "report_elucidsitereport"),
            get_string("time03", "report_elucidsitereport"),
            get_string("time04", "report_elucidsitereport"),
            get_string("time05", "report_elucidsitereport"),
            get_string("time06", "report_elucidsitereport"),
            get_string("time07", "report_elucidsitereport"),
            get_string("time08", "report_elucidsitereport"),
            get_string("time09", "report_elucidsitereport"),
            get_string("time10", "report_elucidsitereport"),
            get_string("time11", "report_elucidsitereport"),
            get_string("time12", "report_elucidsitereport"),
            get_string("time13", "report_elucidsitereport"),
            get_string("time14", "report_elucidsitereport"),
            get_string("time15", "report_elucidsitereport"),
            get_string("time16", "report_elucidsitereport"),
            get_string("time17", "report_elucidsitereport"),
            get_string("time18", "report_elucidsitereport"),
            get_string("time19", "report_elucidsitereport"),
            get_string("time20", "report_elucidsitereport"),
            get_string("time21", "report_elucidsitereport"),
            get_string("time22", "report_elucidsitereport"),
            get_string("time23", "report_elucidsitereport")
        );

        // Initialize access inforamtion object
        foreach($times as $time) {
            $value = array(
                "access" => $access,
                "time" => $time
            );
            $this->siteaccess[] = $value;
        }
    }

    /**
     * Get Site access inforamtion data
     * @return [object] Site access information
     */
    public function get_data($params = false) {
        $response = new stdClass();

         // Create reporting manager instance
        $rpm = reporting_manager::get_instance();
        $cache = cache::make('report_elucidsitereport', 'siteaccess');

        if(!$data = $cache->get('siteaccessinfodata'.$rpm->rpmcache)) {
            $data = $this->get_siteaccess_info();
            $cache->set('siteaccessinfodata'.$rpm->rpmcache, $data);
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
        // Create reporting manager instance
        $rpm = reporting_manager::get_instance();
        // SQL to gey access info log
        $sql = "SELECT id, action, timecreated
            FROM {logstore_standard_log}
            WHERE action = :action
            AND timecreated > :timecreated
            AND userid ".$rpm->insql."";

        // Getting access log
        $timenow = time();
        $params = array (
            "action" => "viewed",
            "timecreated" => $timenow - ONEYEAR
        );
        $params = array_merge($params, $rpm->inparams);
        $accesslog = $DB->get_records_sql($sql, $params);

        // Getting site access information object
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
        global $DB;
        
        // Getting number of weeks to get access log
        $timeduration = end($accesslog)->timecreated - $accesslog[0]->timecreated;
        $weeks = ceil($timeduration / ONEWEEK); // Weeks in time duaration
        $weekmax = 0;
        // If weeks are there then 
        if ($weeks) {
            // Parse access log to save in access inforamtion object
            foreach($accesslog as $log) {
                // Column for weeks
                $col = number_format(date("w", $log->timecreated));

                // Row for hours
                $row = number_format(date("H", $log->timecreated));

                // Calculate site access for row and colums
                $this->siteaccess[$row]["access"][$col]["value"] += (1 / ($weeks * 10));

                // Maximum value in week
                if ($weekmax < $this->siteaccess[$row]["access"][$col]["value"]) {
                    $weekmax = $this->siteaccess[$row]["access"][$col]["value"];
                }
            }

            // Get Opacity value for siteaccess inforamtion
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