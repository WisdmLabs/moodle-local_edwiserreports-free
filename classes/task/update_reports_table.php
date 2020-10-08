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

defined('MOODLE_INTERNAL') || die();

//@codingStandardsIgnoreStart
// namespace local_edwiserreports\task;
// require_once($CFG->libdir . "/enrollib.php");
// require_once($CFG->dirroot . "/local/edwiserreports/classes/completions.php");

// use stdClass;
// use context_course;

// /**
//  * Scheduled Task to Update Report Plugin Table.
//  */
// class update_reports_table extends \core\task\scheduled_task {

//     /**
//      * Return the task's name as shown in admin screens.
//      *
//      * @return string
//      */
//     public function get_name() {
//         return get_string('updatetables', 'local_edwiserreports');
//     }

//     /**
//      * Execute the task.
//      */
//     public function execute() {
//         global $DB;

//         // Update completions table in report plugin
//         $completions = new \local_edwiserreports\completions();

//         // Updating reports table
//         mtrace(get_string('updatingreportstable', 'local_edwiserreports'));
//         $completions->update_local_completion_table();
//     }
// }
