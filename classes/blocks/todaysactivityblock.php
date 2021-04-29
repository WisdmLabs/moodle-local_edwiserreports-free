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

defined('MOODLE_INTERNAL') or die;

use stdClass;
use context_course;

/**
 * Class Acive Users Block. To get the data related to active users block.
 */
class todaysactivityblock extends block_base {
    /**
     * Preapre layout for each block
     * @return Object Layout object
     */
    public function get_layout() {

        // Layout related data.
        $this->layout->id = 'todaysactivityblock';
        $this->layout->name = get_string('todaysactivityheader', 'local_edwiserreports');
        $this->layout->info = get_string('todaysactivityblockhelp', 'local_edwiserreports');
        $this->layout->filters = '<div class="flatpickr-wrapper">';
        $this->layout->filters .= '<input class="btn btn-sm dropdown-toggle input-group-addon"';
        $this->layout->filters .= 'id="flatpickrCalender" placeholder="' .
        get_string('selectdate', 'local_edwiserreports') .
        '" data-input/></div>';

        // Block related data.
        $this->block = new stdClass();

        // Add block view in layout.
        $this->layout->blockview = $this->render_block('todaysactivityblock', $this->block);

        // Set block edit capabilities.
        $this->set_block_edit_capabilities($this->layout->id);

        // Return blocks layout.
        return $this->layout;
    }

    /**
     * Get todays activity data
     * @param Object $params Parameters
     */
    public function get_data($params = false) {
        $date = isset($params->date) ? $params->date : false;
        $response = new stdClass();
        $response->data = $this->get_todaysactivity($date);
        return $response;
    }

    /**
     * Get todays enrolments
     * @param  Integer $starttime Start Time
     * @param  Integer $endtime   End Time
     * @return Integer            Todays Course Enrolment Count
     */
    public function count_user_enrolments($starttime, $endtime) {
        global $DB;

        $select = "timecreated >= $starttime AND timecreated < $endtime";
        return $DB->count_records_select('user_enrolments', $select);
    }

    /**
     * Get todays module completion count
     * @param  Integer $starttime Start Time
     * @param  Integer $endtime   End Time
     * @return Integer            Todays Module Completion Count
     */
    public function count_module_completions($starttime, $endtime) {
        global $DB;

        $select = "timemodified >= $starttime AND timemodified < $endtime";
        return $DB->count_records_select('course_modules_completion', $select);
    }

    /**
     * Get todays course completion count
     * @param  Integer $starttime Start Time
     * @param  Integer $endtime   End Time
     * @return Integer            Todays Course Completion Count
     */
    public function count_course_completions($starttime, $endtime) {
        global $DB;

        $select = "completiontime >= $starttime
                   AND completiontime < $endtime
                   AND progress = 100";
        return $DB->count_records_select('edwreports_course_progress', $select);
    }

    /**
     * Get todays registrations count
     * @param  Integer $starttime Start Time
     * @param  Integer $endtime   End Time
     * @return Integer            Todays Registration Count
     */
    public function count_registrations_completions($starttime, $endtime) {
        global $DB;

        $select = "timecreated >= $starttime
                   AND timecreated < $endtime";
        return $DB->count_records_select('user', $select);
    }

    /**
     * Get todays site visit count
     * @param  Integer $starttime Start Time
     * @param  Integer $endtime   End Time
     * @return Integer            Todays Site Visits Count
     */
    public function count_site_visits($starttime, $endtime) {
        global $DB;

        $visitsssql = "SELECT DISTINCT userid
            FROM {logstore_standard_log}
            WHERE timecreated >= :starttime
            AND timecreated < :endtime
            AND userid < 1";
        $params = array(
            'starttime' => $starttime,
            'endtime' => $endtime
        );
        $visits = $DB->get_records_sql($visitsssql, $params);

        return count($visits);
    }

    /**
     * Get visits in every hours
     * @param  Integer $starttime Start Time
     * @param  Integer $endtime   End Time
     * @return Integer            Get Visits in Every Hours
     */
    public function get_visits_in_hours($starttime, $endtime) {
        global $DB;

        $starttimehour = $starttime;
        $endtimehour = $starttime + 60 * 60;

        $visitsssql = "SELECT DISTINCT userid
            FROM {logstore_standard_log}
            WHERE timecreated >= :starttime
            AND timecreated < :endtime";
        $params = array(
            'starttime' => $starttimehour,
            'endtime' => $endtimehour
        );

        $visitshour = array();
        do {
            $visitshour[] = count($DB->get_records_sql($visitsssql, $params));
            $starttimehour = $endtimehour;
            $endtimehour = $endtimehour + 60 * 60;
            $params['starttime'] = $starttimehour;
            $params['endtime'] = $endtimehour;
        } while ($starttimehour < $endtime);

        return $visitshour;
    }

    /**
     * Get Todays Activity information
     * @param  String $date Date filter in proprtdat format
     * @return Array        Array of todays activities information
     */
    public function get_todaysactivity($date) {
        global $DB;

        // Set time according to the filter.
        if ($date) {
            $starttime = strtotime($date);
            $endtime = $starttime + 24 * 60 * 60;
        } else {
            $endtime = time();
            $starttime = strtotime(date("Ymd", $endtime));
        }

        $todaysactivity = array();
        $todaysactivity["enrollments"] = $this->count_user_enrolments($starttime, $endtime);
        $todaysactivity["activitycompletions"] = $this->count_module_completions($starttime, $endtime);
        $todaysactivity["coursecompletions"] = $this->count_course_completions($starttime, $endtime);
        $todaysactivity["registrations"] = $this->count_registrations_completions($starttime, $endtime);
        $todaysactivity["visits"] = $this->count_site_visits($starttime, $endtime);
        $todaysactivity["visitshour"] = $this->get_visits_in_hours($starttime, $endtime);

        $todaysactivity["onlinelearners"] = $todaysactivity["onlineteachers"] = 0;

        // Capability 'moodle/course:ignoreavailabilityrestrictions' - is allowed to only teachers.
        $teacherscap = "moodle/course:ignoreavailabilityrestrictions";

        // Capability 'moodle/course:isincompletionreports' - is allowed to only students.
        $learnerscap = "moodle/course:isincompletionreports";

        $visitsssql = "SELECT DISTINCT userid
            FROM {logstore_standard_log}
            WHERE timecreated >= :starttime
            AND timecreated < :endtime";
        $params = array(
            'starttime' => $starttime,
            'endtime' => $endtime
        );
        $visits = $DB->get_records_sql($visitsssql, $params);
        foreach ($visits as $user) {
            $isteacher = $islearner = false;
            $courses = enrol_get_users_courses($user->userid);
            foreach ($courses as $course) {
                $coursecontext = context_course::instance($course->id);
                $isteacher = has_capability($teacherscap, $coursecontext, $user->userid);
                $islearner = has_capability($learnerscap, $coursecontext, $user->userid);
            }

            if ($isteacher) {
                $todaysactivity["onlineteachers"]++;
            }

            if ($islearner) {
                $todaysactivity["onlinelearners"]++;
            }
        }
        return $todaysactivity;
    }
}
