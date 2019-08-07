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

require_once $CFG->libdir."/csvlib.class.php";
require_once $CFG->dirroot."/report/elucidsitereport/classes/blocks/active_users_block.php";
require_once $CFG->dirroot."/report/elucidsitereport/classes/blocks/active_courses_block.php";

use csv_export_writer;
use moodle_exception;
use core_user;
use context_course;

class export {
    /**
     * Export data in this format
     */
    public $format = null;

    /**
     * Region to download reports
     * This may be block or report
     */
    public $region = null;

    /**
     * Action to get data for specific block
     */
    public $blockname = null;

    /**
     * Constructor to create export object
     * @param $format type os export object
     */
    public function __construct($format, $region, $blockname) {
        $this->type   = $format;
        $this->region = $region;
        $this->blockname = $blockname;
    }

    /**
     * Export data in CSV format
     * @param $filenme file name to export data
     * @param $data data to be export
     * @return Return status after export the data
     */
    public function data_export_csv($filename, $data) {
        if (csv_export_writer::download_array($filename, $data)) {
            return true;
        }
        return false;
    }

    /**
     * Get exportable data to export
     * @param $filenme file name to export data
     * @param $data data to be export
     * @return Return status after export the data
     */
    public function get_exportable_data($filter) {
        $export = null;

        switch ($this->region) {
            case "block":
                $export = $this->exportable_data_block($this->blockname, $filter);
                break;
            case "report":
                $export = $this->exportable_data_report($this->blockname, $filter);
                break;
        }

        return $export;
    }

    /**
     * Get exportable data for dashboard block
     * @param [string] $blockname Block to get exportable data
     * @param [string] $filter Filter to get data
     * @return [array] Array of exportable data
     */
    private function exportable_data_block($blockname, $filter) {
        $export = null;
        switch ($blockname) {
            case "activeusers":
                $export = active_users_block::get_exportable_data_block($filter);
                break;
            case "activecourses":
                $export = active_courses_block::get_exportable_data_block();
                break;
            case "courseprogress":
                $export = course_progress_block::get_exportable_data_block($filter);
                break;
            case "certificates":
                $export = certificates_block::get_exportable_data_block($filter);
                break;
        }
        return $export;
    }

    /**
     * Get exportable data for individual page
     * @param [string] $blockname Block to get exportable data
     * @param [string] $filter Filter to get data
     * @return [array] Array of exportable data
     */
    private function exportable_data_report($blockname, $filter) {
        $export = null;
        switch ($blockname) {
            case "activeusers":
                $export = active_users_block::get_exportable_data_report($filter);
                break;
            case "courseprogress":
                $export = course_progress_block::get_exportable_data_report($filter);
                break;
            case "certificates":
                $export = certificates_block::get_exportable_data_report($filter);
                break;
        }
        return $export;
    }
}
