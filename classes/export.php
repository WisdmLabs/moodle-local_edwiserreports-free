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
            default:
                new moodle_exception(403);

        }

        return $export;
    }

    /**
     * Get Block Data in specific format
     */
    private function exportable_data_block($blockname, $filter) {
        $export = null;
        switch ($blockname) {
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
            case "courseprogress":
                $export[] = course_progress_block::get_header();
                $courses = \report_elucidsitereport\utility::get_courses();
                foreach ($courses as $key => $course) {
                    $courseprogress = course_progress_block::get_data($course->id);
                    $coursecontext = context_course::instance($course->id);
                    $enrolledstudents = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');
                    $export[] = array_merge(
                        array(
                            $course->fullname,
                            count($enrolledstudents)
                        ),
                        $courseprogress->data
                    );
                }
            default:
                // code...
                break;
        }

        return $export;
    }

    /**
     * Get report page data for a block
     */
    private function exportable_data_report($blockname, $filter) {
		$export = null;
        switch ($blockname) {
            case "activeusers":
                $export[] = active_users_block::get_header_report();
                $activeusersdata = active_users_block::get_data($filter);
                foreach ($activeusersdata->labels as $key => $lable) {
                    $export = array_merge($export,
                        $this->get_usersdata($lable, "activeusers"),
                        $this->get_usersdata($lable, "enrolments"),
                        $this->get_usersdata($lable, "completions")
                    );
                }
                break;
            case "courseprogress":
                break;
            default:
                // code...
                break;
        }
        return $export;
	}

    private function get_usersdata($lable, $action) {
        $usersdata = array();
        $users = active_users_block::get_userslist(strtotime($lable), "activeusers");
        foreach ($users as $key => $user) {
            $user = array_merge(
                array(
                    $lable
                ),
                $user,
                array(
                    get_string($action . "_status", "report_elucidsitereport")
                )
            );
            $usersdata[] = $user;
        }
        return $usersdata;
    }
	
	private function get_activeusers_report_data($lable) {
		global $DB;
		$export = array();

		$sql = "SELECT DISTINCT userid
                FROM {logstore_standard_log}
                WHERE eventname = ?
                AND timecreated > ?
                AND timecreated <= ?";

		$date = strtotime($lable);
		$records = $DB->get_records_sql($sql, array('\core\event\user_loggedin',
							$date, $date + active_users_block::$oneday));
		foreach ($records as $record) {
			$user = core_user::get_user($record->userid);
			$export[] = array(
				$lable,
				fullname($user),
				$user->email,
				get_string("useractive", "report_elucidsitereport")
			);
		}
		return $export;
	}
}
