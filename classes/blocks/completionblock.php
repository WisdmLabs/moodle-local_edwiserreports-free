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

use local_edwiserreports\utility;
use context_course;
use html_writer;
use stdClass;

/**
 * Class Course Completion Block. To get the data related to active users block.
 */
class completionblock {
    /**
     * Get Data for Course Completion
     * @param  int    $courseid Course Id
     * @param  int    $cohortid Cohort Id
     * @return object           Response for Course Completion
     */
    public static function get_data($courseid, $cohortid) {
        $response = new stdClass();
        $response->data = self::get_completions($courseid, $cohortid);
        return $response;
    }

    /**
     * Get Course Completion data
     * @param  int   $courseid Course Id
     * @param  int   $cohortid Cohort Id
     * @return array           Array of users with course Completion
     */
    public static function get_completions($courseid, $cohortid, $isexportdata = 0) {
        global $DB, $USER;
        $timenow = time();

        $rtl = get_string('thisdirection', 'langconfig') == 'rtl' ? 1 : 0;
        
        // Get user timezone for consistent date formatting (matches browser timezone in JavaScript)
        $usertimezone = \core_date::get_user_timezone($USER);

        // Get only enrolled students.
        $enrolledstudents = utility::get_enrolled_students($courseid, false, $cohortid);
        $course = get_course($courseid);

        $usertable = utility::create_temp_table("cc_u2", array_keys($enrolledstudents));

        $fullname = $DB->sql_fullname("u.firstname", "u.lastname");
        $sql = " SELECT u.id, u.email, $fullname fullname, e.enrol, ue.timecreated enrolledon, ecp.progress,
                        ecp.completiontime, (gg.finalgrade / gg.rawgrademax * 100) grade, logs.visits, logs.lastvisit
                   FROM {course} c
                   JOIN {enrol} e ON c.id = e.courseid
                   JOIN {user_enrolments} ue ON e.id = ue.enrolid
                   JOIN {{$usertable}} ut ON ue.userid = ut.tempid
                   JOIN {user} u ON ut.tempid = u.id
                   JOIN {edwreports_course_progress} ecp ON c.id = ecp.courseid AND u.id = ecp.userid
                   JOIN {grade_items} gi ON c.id = gi.courseid AND gi.itemtype = :itemtype
                   LEFT JOIN {grade_grades} gg ON gi.id = gg.itemid AND u.id = gg.userid
                   LEFT JOIN (SELECT lsl.userid, COUNT(lsl.courseid) visits, MAX(lsl.timecreated) lastvisit
                           FROM {logstore_standard_log} lsl
                          WHERE lsl.courseid = :courseid
                            AND lsl.action = :action
                          GROUP BY lsl.userid
                          ) logs ON u.id = logs.userid
                  WHERE c.id = :course
                    AND u.deleted = :deleted";
        $params = [
            'course' => $courseid,
            'courseid' => $courseid,
            'deleted' => 0,
            'itemtype' => 'course',
            'action' => 'viewed'
        ];
        $users = $DB->get_records_sql($sql, $params);

        $notyet = get_string("notyet", "local_edwiserreports");
        $never = get_string("never");

        $userscompletion = array();
        foreach ($users as $key => $user) {
            $completioninfo = new stdClass();
            $completioninfo->username = html_writer::div(
                $user->fullname, '',
                array (
                    "data-toggle" => "tooltip",
                    "title" => $user->email
                )
            );
            $completioninfo->enrolledon = $rtl ? date("Y M d", $user->enrolledon) : date("d M Y", $user->enrolledon);
            $completioninfo->enrolltype = $user->enrol;
            $completioninfo->noofvisits = empty($user->visits) ? 0 : $user->visits;
            $completioninfo->completion = empty($user->progress) ? "NA" : round($user->progress) . '%';
            $completioninfo->compleiontime = empty($user->completiontime) ?
                                            $notyet : ( $rtl ? date("Y M d", $user->completiontime) : date("d M Y", $user->completiontime));
            $completioninfo->grade = isset($user->grade) ? round($user->grade, 2) . '%' : '0%';
            // $completioninfo->lastaccess = empty($user->lastvisit) ? 0 : format_time($timenow - $user->lastvisit);
            // $completioninfo->lastaccess = empty($user->lastvisit) ? 0 : ($rtl ? date('A i:h Y M d', $user->lastvisit) : date('d M Y h:i A', $user->lastvisit));
            $completioninfo->lastaccess = empty($user->lastvisit) ? 0 : $user->lastvisit;
            if ($isexportdata) {
                // Format to match JavaScript table display exactly
                // JavaScript LTR: "d MMM yyyy hh:mm TT" → "7 Jan 2026<br>02:25 PM"
                // JavaScript RTL: "TT mm:hh yyyy MMM d" → "2026 Jan 7<br>PM 02:25"
                if (empty($user->lastvisit)) {
                    $completioninfo->lastaccess = 0;
                } else {
                    // Use user timezone to match JavaScript browser timezone behavior
                    $dt = new \DateTime('@' . $user->lastvisit);
                    $dt->setTimezone(new \DateTimeZone($usertimezone));
                    
                    // Get month abbreviation (Jan, Feb, etc.) to match JavaScript MMM format
                    $monthnames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    $day = $dt->format('j'); // Day without leading zero (1-31)
                    $month = $monthnames[(int)$dt->format('n') - 1]; // Month abbreviation
                    $year = $dt->format('Y'); // 4-digit year
                    $hour = $dt->format('h'); // 12-hour format with leading zero (01-12)
                    $minute = $dt->format('i'); // Minutes with leading zero (00-59)
                    $ampm = $dt->format('A'); // AM/PM
                    
                    if ($rtl) {
                        // RTL format matching JavaScript: "TT mm:hh yyyy MMM d"
                        // Output: "2026 Jan 7<br>PM 02:25"
                        $completioninfo->lastaccess = $year . ' ' . $month . ' ' . $day . '<br>' . $ampm . ' ' . $minute . ':' . $hour;
                    } else {
                        // LTR format matching JavaScript: "d MMM yyyy hh:mm TT"
                        // Output: "7 Jan 2026<br>02:25 PM"
                        $completioninfo->lastaccess = $day . ' ' . $month . ' ' . $year . '<br>' . $hour . ':' . $minute . ' ' . $ampm;
                    }
                }
            }
            $userscompletion[] = $completioninfo;
            unset($users[$key]);
        }

        // DROP userstable
        utility::drop_temp_table($usertable);
        return $userscompletion;
    }

    /**
     * Get export header string
     * @return array Header array
     */
    public static function get_header() {
        return array(
            get_string("fullname", "local_edwiserreports"),
            get_string("enrolledon", "local_edwiserreports"),
            get_string("enrolltype", "local_edwiserreports"),
            get_string("noofvisits", "local_edwiserreports"),
            get_string("coursecompletion", "local_edwiserreports"),
            get_string("completiontime", "local_edwiserreports"),
            get_string("grade", "local_edwiserreports"),
            get_string("lastaccess", "local_edwiserreports")
        );
    }

    /**
     * Get Exportable data for Course Completion Page
     * @param  int    $filter     Course id
     * @param  bool   $filterdata If enabled then filter data
     * @return array              Array of LP Stats
     */
    public static function get_exportable_data_report($filter, $filterdata = true) {
        $cohortid = optional_param("cohortid", 0, PARAM_INT);
        $rtl = optional_param("rtl", 0, PARAM_INT);

        $completions = self::get_completions($filter, optional_param('cohortid', 0, PARAM_INT), 1);

        $export = array();
        $export[] = $rtl ? array_reverse(self::get_header()) : self::get_header();
        foreach ($completions as $completion) {
            $completion->username = strip_tags($completion->username);
            $data = array_values((array)$completion);
            $export[] = $rtl ? array_reverse($data) : $data;
        }
        return $export;
    }
}
