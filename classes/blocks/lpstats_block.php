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

    public function get_lpstats($lpid) {
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
}
