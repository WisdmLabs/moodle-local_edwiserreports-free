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
use moodle_url;
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
        global $DB;

        // Layout related data.
        $this->layout->id = 'siteaccessblock';
        $this->layout->name = get_string('accessinfo', 'local_edwiserreports');
        $this->layout->info = get_string('accessinfoblockhelp', 'local_edwiserreports');

        if (is_siteadmin()) {
            $lastrun = $DB->get_field('task_scheduled', 'lastruntime', array(
                'component' => 'local_edwiserreports',
                'classname' => '\local_edwiserreports\task\site_access_data'
            ));

            $url = new moodle_url(
                '/admin/tool/task/schedule_task.php',
                array('task' => 'local_edwiserreports\task\site_access_data')
            );
            if (get_config('local_edwiserreports', 'siteaccessrecalculate')) {
                $this->block->cronwarning = get_string(
                    'siteaccessrecalculate',
                    'local_edwiserreports',
                    $url->out()
                );
            } else if (($lastrun == false || $lastrun < time() - LOCAL_SITEREPORT_ONEDAY)) {
                $this->block->cronwarning = get_string(
                    'siteaccessinformationcronwarning',
                    'local_edwiserreports',
                    $url->out()
                );
            }

        }

        // Add block view in layout.
        $this->layout->blockview = $this->render_block('siteaccessblock', $this->block);
        // Set block edit capabilities.
        $this->set_block_edit_capabilities($this->layout->id);

        // Return blocks layout.
        return $this->layout;
    }

    /**
     * Get Site access inforamtion data
     * @param  object $params Parameters
     * @return object         Site access information
     */
    public function get_data($params = false) {
        global $DB, $USER;
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

        // Getting site access information object.
        $response = new stdClass();

        $accesslog = get_config('local_edwiserreports', 'siteaccessinformation');
        if ($accesslog && $accesslog = json_decode($accesslog, true)) {
            $response->siteaccess = $accesslog;
            return $response;
        }

        $response->siteaccess = [];

        // Initialize access value for site access.
        $data = array(0, 0, 0, 0, 0, 0, 0);

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
            $response->siteaccess[] = array(
                "name" => $time,
                "data" => $data
            );
        }

        return $response;
    }
}
