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
use report_elucidsitereport\active_users_block;

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
    public $action = null;

    /**
     * Constructor to create export object
     * @param $format type os export object
     */
    public function __construct($format, $region, $action) {
        $this->type   = $format;
        $this->region = $region;
        $this->action = $action;
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
                $export = $this->exportable_data_block($this->action, $filter);
                break;
            case "report":
                $export = $this->exportable_data_report($this->action);
                break;
            default:
                new moodle_exception(403);

        }

        return $export;
    }

    /**
     * Get Block Data in specific format
     */
    private function exportable_data_block($action, $filter) {
        $export = null;

        switch ($action) {
            case "activeusers":
                $export[] = active_users_block::get_header();
                $activeusersdata = active_users_block::get_data($filter);
                foreach ($activeusersdata->labels as $key => $lable) {
                    $export[] = array(
                        $lable,
                        $activeusersdata->data->activeUsers[$key],
                        $activeusersdata->data->enrolments[$key],
                        $activeusersdata->data->completionRate[$key],
                    );
                }
                break;
            case "activecourses":
                $header = active_courses_block::get_header();
                $activecoursesdata = active_courses_block::get_data();
                $export = array_merge(
                    array($header),
                    $activecoursesdata->data
                );
                break;
            default:
                // code...
                break;
        }

        return $export;
    }
}
