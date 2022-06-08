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

namespace local_edwiserreports\task;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . "/local/edwiserreports/classes/constants.php");

use local_edwiserreports\controller\progress;
use cache;

/**
 * Scheduled Task to Update Report Plugin Table.
 */
class site_access_data extends \core\task\scheduled_task {

    /**
     * Can run cron task.
     *
     * @return boolean
     */
    public function can_run(): bool {
        return true;
    }

    /**
     * Set response object for site access information
     * @var array
     */
    private $siteaccess = array();


    /**
     * Object to show progress of task
     * @var \local_edwiserreports\task\progress
     */
    private $progress;

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('siteaccessinformationtask', 'local_edwiserreports');
    }

    /**
     * Constructoe
     */
    public function __construct() {

        $this->progress = new progress('siteaccessdata');

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
            $this->siteaccess[] = array(
                "name" => $time,
                "data" => $data
            );
        }
    }

    /**
     * Execute the task.
     */
    public function execute() {
        global $DB;

        // SQL to gey access info log.
        $sql = "SELECT id, action, timecreated
            FROM {logstore_standard_log}
            WHERE action = :action
            AND timecreated > :timecreated";

        // Getting access log.
        $params = array (
            "action" => "viewed",
            "timecreated" => time() - LOCAL_SITEREPORT_ONEYEAR
        );

        if (defined('EDWISER_REPORTS_WEB_SCRIPT')) {
            // Increase memory limit.
            raise_memory_limit(MEMORY_UNLIMITED);
        }
        $this->progress->start_progress();
        $accesslog = $DB->get_records_sql($sql, $params);

        // Getting site access information object.
        $siteaccess = $this->get_accessinfo(array_values($accesslog));
        $this->progress->end_progress();
        set_config('siteaccessinformation', json_encode($siteaccess), 'local_edwiserreports');

        unset_config('siteaccessrecalculate', 'local_edwiserreports');

        cache::make('local_edwiserreports', 'siteaccess')->purge();

        return true;
    }

    /**
     * Get Access information
     * @param  array $accesslog Array of access log
     * @return object Site Access Information
     */
    public function get_accessinfo($accesslog) {

        // Getting number of weeks to get access log.
        $timeduration = end($accesslog)->timecreated - $accesslog[0]->timecreated;
        $weeks = ceil($timeduration / LOCAL_SITEREPORT_ONEWEEK); // Weeks in time duaration.
        $weekmax = 0;
        // If weeks are there then.
        if ($weeks) {
            $progress = 0;
            $updater = 0;
            $increament = 100 / count($accesslog);
            // Parse access log to save in access inforamtion object.
            foreach ($accesslog as $log) {
                // Column for weeks.
                $col = number_format(date("w", $log->timecreated));

                // Row for hours.
                $row = number_format(date("H", $log->timecreated));

                // Calculate site access for row and colums.
                $this->siteaccess[$row]['data'][$col]++;

                $progress += $increament;
                if (++$updater >= 500) {
                    $updater = 0;
                    $this->progress->update_progress($progress);
                }
            }
        }
        return $this->siteaccess;
    }
}
