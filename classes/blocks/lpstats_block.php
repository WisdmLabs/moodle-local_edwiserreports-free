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
use stdClass;
use core_user;

/**
 * Learning Program Stats Block
 * To get the data related to learning program block
 */
class lpstats_block extends utility {
    public static function get_data($lpid) {
    	$response = new stdClass();
    	$response->data = self::get_lpstats($lpid);

    	return $response;
    }

    /**
     * Get LP stats details
     * @param  [int] $lpid Learning Program Id
     * @return [object] Learning Program stats
     */
    public static function get_lpstats($lpid) {
    	global $DB;
    	$lp = $DB->get_record("wdm_learning_program", array("id" => $lpid), "courses");
    	$lpenrolment = $DB->get_records("wdm_learning_program_enrol", array("learningprogramid" => $lpid), "userid");
    	$courses = json_decode($lp->courses);

    	$lpstats = new stdClass();

    	$completedusers = array();
    	foreach ($courses as $courseid) {
    		$course = get_course($courseid);
    		$lpstats->labels[] = $course->shortname;

    		$completed = 0;
    		foreach ($lpenrolment as $enrol) {
    			$completion = self::get_course_completion_info($course, $enrol->userid);

    			if ($completion["progresspercentage"] == 100) {
    				if (!in_array($enrol->userid, $completedusers)) {
    					array_push($completedusers, $enrol->userid);
    				}
    				$completed++;
    			}
    		}
    		$lpstats->data[] = $completed;
    	}

    	// No users are completd any courses
    	$lpstats->labels[] = "None of the above";
    	$lpstats->data[] = count($lpenrolment) - count($completedusers);

    	return $lpstats;
    }

    /**
     * Get Lp Stats Users Data
     * @param [int] $lpid Learning Program Id
     * @param [int] $cohortid Cohort Id
     * @return [array] LP stats Users Data
     */
    public static function get_lpstats_usersdata($lpid, $cohortid) {
        global $DB;

        $lp = $DB->get_record("wdm_learning_program", array("id" => $lpid), "courses");
        $lpenrolment = $DB->get_records("wdm_learning_program_enrol", array("learningprogramid" => $lpid), "userid");
        $courses = json_decode($lp->courses);

        $lpinfo = new stdClass();
        $lpinfo->courses = array();
        $lpinfo->users = array();
        $lpinfo->coursecount = count($courses) + 1;
        $flag = true;

        foreach ($lpenrolment as $enrol) {
            if ($cohortid) {
                $cohorts = cohort_get_user_cohorts($enrol->userid);
                if (!array_key_exists($cohortid, $cohorts)) {
                    continue;
                }
            }

            $user = core_user::get_user($enrol->userid, "id, firstname, lastname, email");

            $userinfo = new stdClass();
            $avgprogress = 0;
            $userinfo->name = $user->firstname . " " . $user->lastname;
            $userinfo->email = $user->email;
            $userinfo->enrolled = date("d M y", $enrol->timeenroled);
            $userinfo->lastaccess = $enrol->lastaccess ? date("d M y", $enrol->lastaccess) : get_string("notyet", "report_elucidsitereport");

            $userinfo->grade = 0;
            $userinfo->progress = array();

            foreach ($courses as $courseid) {
                $course = get_course($courseid);

                if (!$DB->record_exists("course", array("id" => $courseid))) {
                    continue;
                }

                $completion = self::get_course_completion_info($course, $enrol->userid);

                $gradereport = self::get_grades($course->id, $user->id);

                if (isset($gradereport->finalgrade)) {
                    $userinfo->grade += $gradereport->finalgrade;
                }

                if (isset($completion["progresspercentage"])) {
                    $userinfo->progress[] = $completion["progresspercentage"] . "%";
                    $avgprogress += $completion["progresspercentage"];
                } else {
                    $userinfo->progress[] = "NA";
                }

                if ($flag) {
                    $lpinfo->courses[] = $course;
                }

            }

            if ($lpinfo->coursecount > 1) {
                $userinfo->avgprogress = number_format($avgprogress / ($lpinfo->coursecount - 1)) . "%";   
            } else {
                $userinfo->avgprogress = 0;
            }

            $userinfo->grade = number_format($userinfo->grade, 2);
            $lpinfo->users[] = $userinfo;
            $flag = false;
        }

        return $lpinfo;
    }

    /**
     * Get header for export data actvive users
     * @return [array] Array of headers of exportable data
     */
    public static function get_header_block() {
        $header = array(
            get_string("coursename", "report_elucidsitereport"),
            get_string("lpname", "report_elucidsitereport"),
            get_string("coursecompletedusers", "report_elucidsitereport")
        );

        return $header;
    }

    /**
     * Get header for export data Lp stats page
     * @return [array] Array of headers of exportable data
     */
    public static function get_header_report() {
        $header = array(
            get_string("name", "report_elucidsitereport"),
            get_string("email", "report_elucidsitereport"),
            get_string("enrolled", "report_elucidsitereport"),
            get_string("lastaccess", "report_elucidsitereport"),
            get_string("grade", "report_elucidsitereport")
        );

        return $header;
    }

    /**
     * Get Exportable data for LP Stats Block
     * @param  [int] $filter Lp ID
     * @return [array] Array of LP Stats
     */
    public static function get_exportable_data_block($filter) {
        global $DB;

        $lpstats = self::get_lpstats($filter);
        $lp = $DB->get_record("wdm_learning_program", array("id" => $filter), "name");

        $export = array();
        $export[] = self::get_header_block();
        foreach($lpstats->labels as $key => $label) {
            $export[] = array(
                $label,
                $lp->name,
                $lpstats->data[$key]
            );
        }
        return $export;
    }

    /**
     * Get Exportable data for LP Stats Page
     * @param  [int] $filter Lp ID
     * @return [array] Array of LP Stats
     */
    public static function get_exportable_data_report($filter) {
        $cohortid = optional_param("cohortid", 0, PARAM_INT);
        $lpstats = self::get_lpstats_usersdata($filter, $cohortid);

        $header = self::get_header_report();
        foreach ($lpstats->courses as $course) {
            $header[] = $course->shortname;
        }

        $export = array();
        $export[] = $header;
        foreach($lpstats->users as $user) {
            $data = array();
            $data[] = $user->name;
            $data[] = $user->email;
            $data[] = $user->enrolled;
            $data[] = $user->lastaccess;
            $data[] = $user->grade;
            foreach ($user->progress as $progress) {
                $data[] = $progress;
            }
            $export[] = $data;
        }
        return $export;
    }
}
