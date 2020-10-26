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
 * Class Course Access Block. To get the data related to active users block.
 */
class courseaccess_block extends utility {
    /**
     * Get Data for Course Access
     * @param  int    $courseid Course id
     * @return object           Response for Course Access
     */
    public static function get_data($courseid) {
        $response = new stdClass();
        $response->data = self::get_courseaccess_data($courseid);
        return $response;
    }

    /**
     * Get Course Access data
     * @param  int   $courseid Course id
     * @return array           Array of users with course Access
     */
    public static function get_courseaccess_data($courseid) {
        global $DB;

        $coursecontext = context_course::instance($courseid);
        $enrolledstudents = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');
        $course = get_course($courseid);
        $timenow = time();

        $userscompletion = array();
        // Can be optimized.
        foreach ($enrolledstudents as $user) {
            $enrolsql = "SELECT *
            FROM {user_enrolments} ue
            JOIN {enrol} e ON (e.id = ue.enrolid AND e.courseid = :courseid)
            JOIN {user} u ON u.id = ue.userid
            WHERE ue.userid = :userid AND u.deleted = 0";

            $params = array('courseid' => $courseid, 'userid' => $user->id);
            $enrolinfo = $DB->get_record_sql($enrolsql, $params);

            $completion = self::get_course_completion_info($course, $user->id);

            if (empty($completion)) {
                $progressper = "NA";
            } else {
                $progressper = $completion["progresspercentage"] . "%";
            }

            $visits = self::get_visits_by_users($courseid, $user->id);

            if (empty($visits)) {
                $lastvisits = get_string("never");
            } else {
                $lastvisits = format_time($timenow - array_values((array) $visits)[0]->timecreated);
            }

            $completioninfo = new stdClass();
            $completioninfo->username = fullname($user);
            $completioninfo->useremail = $user->email;
            $completioninfo->lastvists = $lastvisits;
            $completioninfo->visitscount = count(self::get_visits_by_users($courseid, $user->id));
            $completioninfo->enrolledon = date("d M Y", $enrolinfo->timemodified);
            $completioninfo->completion = $progressper;
            $userscompletion[] = $completioninfo;
        }
        return $userscompletion;
    }
}
