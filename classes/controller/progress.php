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
 * @category    controller
 * @copyright   2021 wisdmlabs <support@wisdmlabs.com>
 * @author      Yogesh Shirsath
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserreports\controller;

class progress {

    /**
     * Task id
     * @var string
     */
    private $task;

    /**
     * Precision points upto which % should be shown
     * @var int
     */
    private $precision;

    public function __construct($task = null, $precision = 2) {
        ob_implicit_flush();
        $this->task = $task;
        $this->precision = $precision;
    }

    /**
     * Start task progress
     */
    public function start_progress() {
        if (!defined('EDWISER_REPORTS_WEB_SCRIPT')) {
            echo "\nCompleted - 0%";
            return;
        }
        echo "<div class='text-center col-4 mb-25 mx-auto'>
        <div class='progress my-25'>
            <div id='" . $this->task . "' class='progress-bar text-dark' role='progressbar' style='width: 0%;' aria-valuenow='0'
            aria-valuemin='0' aria-valuemax='100'>0%</div>
        </div>";
        flush();
        ob_flush();
    }

    /**
     * End task progress
     */
    public function end_progress() {
        if (!defined('EDWISER_REPORTS_WEB_SCRIPT')) {
            echo "\rCompleted - 100%";
            return;
        }
        $this->update_progress_web(100);
    }

    /**
     * Update progress status of task run in cli
     * @param float  progress Task Progress
     * @param string task     Task id
     */
    protected function update_progress_cli($progress) {
        echo "\rCompleted - " . $progress . "%";
    }

    /**
     * Update progress status of task run in web
     * @param float  progress Task Progress
     * @param string task     Task id
     */
    protected function update_progress_web($progress) {
        echo "<script>
            document.getElementById('" . $this->task . "').setAttribute('aria-valuenow', " . $progress . ");
            document.getElementById('" . $this->task . "').style.width = '" . $progress . "%';
            document.getElementById('" . $this->task . "').innerText = '" . $progress . "%';
        </script>";
        flush();
        ob_flush();
    }

    /**
     * Update progress status of task
     * @param float  progress Task Progress
     * @param string task     Task id
     */
    public function update_progress($progress, $precision = null) {
        $progress = round($progress, $precision == null ? $this->precision : $precision);
        if (!defined('EDWISER_REPORTS_WEB_SCRIPT')) {
            $this->update_progress_cli($progress);
            return;
        }
        $this->update_progress_web($progress);
    }
}
