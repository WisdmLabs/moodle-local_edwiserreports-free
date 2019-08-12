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
use context_course;
use html_writer;

/**
 * Class Course Completion Block
 * To get the data related to active users block
 */
class completion_block extends utility {
    /**
     * Get Data for Course Completion
     * @return [object] Response for Course Completion
     */
    public static function get_data($courseid) {
        $response = new stdClass();
        $response->data = self::get_completion_data($courseid);
        return $response;
    }

    /**
     * Get Course Completion data
     * @return [array] Array of users with course Completion 
     */
    public static function get_completion_data($courseid) {
        global $DB;
        $timenow = time();

        $enrolsql = "SELECT *
            FROM {user_enrolments} ue
            JOIN {enrol} e ON (e.id = ue.enrolid AND e.courseid = :courseid)
            JOIN {user} u ON u.id = ue.userid
            WHERE ue.userid = :userid AND u.deleted = 0";

        $coursecontext = context_course::instance($courseid);
        $enrolledstudents = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');
        $course = get_course($courseid);

        $userscompletion = array();
        foreach ($enrolledstudents as $user) {
            $params = array('courseid'=>$courseid, 'userid' => $user->id);
            $enrolinfo = $DB->get_record_sql($enrolsql, $params);

            $completion = self::get_course_completion_info($course, $user->id);

            if (empty($completion)) {
                $progressper = "NA";
            } else {
                $progressper = $completion["progresspercentage"] . "%";
            }

            $completioninfo = new stdClass();
            $completioninfo->username = html_writer::div(
                fullname($user), '',
                array (
                    "data-toggle" => "tooltip",
                    "title" => $user->email
                )
            );

            $visits = self::get_visits_by_users($courseid, $user->id);

            if (empty($visits)) {
                $lastvisits = get_string("never");
            } else {
                $lastvisits = format_time($timenow - array_values($visits)[0]->timecreated);
            }

            $gradeval = 0;
            $grade = self::get_grades($courseid, $user->id);
            if (isset($grade->finalgrade)) {
                $gradeval = $grade->finalgrade;
            }

            $completioninfo->enrolledon = date("d M Y", $enrolinfo->timemodified);
            $completioninfo->enrolltype = $enrolinfo->enrol;
            $completioninfo->noofvisits = count(self::get_visits_by_users($courseid, $user->id));
            $completioninfo->completion = $progressper;
            $completioninfo->compleiontime = self::get_timecompleted($courseid, $user->id);
            $completioninfo->grade = $gradeval;
            $completioninfo->lastaccess = $lastvisits;
            $userscompletion[] = $completioninfo;
        }
        return $userscompletion;
    }

    public static function get_timecompleted($courseid, $userid) {
        global $DB;

        $coursecompletion = $DB->get_record("course_completions", array(
            "userid" => $user->id,
            "course" => $course->id
        ));

        if (isset($coursecompletion) && $coursecompletion->timecompleted) {
            return $coursecompletion->timecompleted;
        } else {
            return get_string("notyet", "report_elucidsitereport");
        }
    }
}