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
use stdClass;
use context_course;

/**
 * Class Acive Users Block. To get the data related to active users block.
 */
class todaysactivityblock extends block_base {
    /**
     * Preapre layout for each block
     * @return object Layout object
     */
    public function get_layout() {

        // Layout related data.
        $this->layout->id = 'todaysactivityblock';
        $this->layout->name = get_string('todaysactivityheader', 'local_edwiserreports');
        $this->layout->info = get_string('todaysactivityblockhelp', 'local_edwiserreports');
        $this->layout->filters = '<input class="btn btn-sm dropdown-toggle input-group-addon"';
        $this->layout->filters .= 'id="flatpickrCalender" placeholder="' .
        get_string('selectdate', 'local_edwiserreports') .
        '" data-input/>';

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
        $response->data = self::get_todaysactivity($date);
        return $response;
    }

    /**
     * Get Todays Activity information
     * @param [string] $date Date filter in proprtdat format
     * @return [array] Array of todays activities information
     */
    public static function get_todaysactivity($date) {
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
        $total = 0;
        // Enrolments.
        $enrollmentsql = "SELECT * FROM {user_enrolments}
            WHERE timecreated >= :starttime
            AND timecreated < :endtime";
        $params['starttime'] = $starttime;
        $params['endtime'] = $endtime;
        $enrollments = $DB->get_records_sql($enrollmentsql, $params);
        $todaysactivity["enrollments"] = count($enrollments);
        $total += count($enrollments);

        // Activity Completion.
        $activitycompletionsql = "SELECT * FROM {course_modules_completion}
            WHERE timemodified >= :starttime
            AND timemodified < :endtime";
        $activitycompletions = $DB->get_records_sql($activitycompletionsql, $params);
        $todaysactivity["activitycompletions"] = count($activitycompletions);
        $total += count($activitycompletions);

        // Course Completion.
        $coursecompletionsql = "SELECT * FROM {course_completions}
            WHERE timecompleted >= :starttime
            AND timecompleted < :endtime";
        $coursecompletions = $DB->get_records_sql($coursecompletionsql, $params);
        $todaysactivity["coursecompletions"] = count($coursecompletions);
        $total += count($coursecompletions);

        // Registration.
        $registrationssql = "SELECT * FROM {user}
            WHERE timecreated >= :starttime
            AND timecreated < :endtime";
        $registrations = $DB->get_records_sql($registrationssql, $params);
        $todaysactivity["registrations"] = count($registrations);
        $total += count($registrations);

        // Visits.
        $visitsssql = "SELECT DISTINCT userid
            FROM {logstore_standard_log}
            WHERE timecreated >= :starttime
            AND timecreated < :endtime"; // Remove guest users.
        $visits = $DB->get_records_sql($visitsssql, $params);
        $todaysactivity["visits"] = count($visits);
        $total += count($visits);

        $starttimehour = $starttime;
        $endtimehour = $starttime + 60 * 60;
        $todaysactivity["visitshour"] = array();
        $params = array();
        $params['starttime'] = $starttimehour;
        $params['endtime'] = $endtimehour;
        do {
            $visitshour = $DB->get_records_sql($visitsssql, $params);
            $todaysactivity["visitshour"][] = count($visitshour);
            $starttimehour = $endtimehour;
            $endtimehour = $endtimehour + 60 * 60;
            $params = array();
            $params['starttime'] = $starttimehour;
            $params['endtime'] = $endtimehour;
        } while ($starttimehour < $endtime);

        $todaysactivity["onlinelearners"] = $todaysactivity["onlineteachers"] = 0;

        // Capability 'moodle/course:ignoreavailabilityrestrictions' - is allowed to only teachers.
        $teacherscap = "moodle/course:ignoreavailabilityrestrictions";

        // Capability 'moodle/course:isincompletionreports' - is allowed to only students.
        $learnerscap = "moodle/course:isincompletionreports";
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
        $todaysactivity["totalusers"] = $total;
        return $todaysactivity;
    }
}
