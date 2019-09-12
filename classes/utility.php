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

use completion_info;
use context_course;
use MoodleQuickForm;
use progress;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . "/completion/classes/progress.php");
require_once($CFG->dirroot . "/cohort/lib.php");
require_once($CFG->dirroot . "/report/elucidsitereport/classes/blocks/active_users_block.php");
require_once($CFG->dirroot . "/report/elucidsitereport/classes/blocks/active_courses_block.php");
require_once($CFG->dirroot . "/report/elucidsitereport/classes/blocks/course_progress_block.php");
require_once($CFG->dirroot . "/report/elucidsitereport/classes/blocks/f2fsession_block.php");
require_once($CFG->dirroot . "/report/elucidsitereport/classes/blocks/certificates_block.php");
require_once($CFG->dirroot . "/report/elucidsitereport/classes/blocks/liveusers_block.php");
require_once($CFG->dirroot . "/report/elucidsitereport/classes/blocks/siteaccess_block.php");
require_once($CFG->dirroot . "/report/elucidsitereport/classes/blocks/todaysactivity_block.php");
require_once($CFG->dirroot . "/report/elucidsitereport/classes/blocks/lpstats_block.php");
require_once($CFG->dirroot . "/report/elucidsitereport/classes/blocks/inactiveusers_block.php");
require_once($CFG->dirroot . "/report/elucidsitereport/classes/blocks/courseengage_block.php");
require_once($CFG->dirroot . "/report/elucidsitereport/classes/blocks/completion_block.php");
require_once($CFG->dirroot . "/report/elucidsitereport/classes/blocks/courseanalytics_block.php");

/**
 * Utilty class to add all utility function
 * to perform in the eLucid report plugin
 */
class utility {
    public static function get_active_users_data($data) {
        if (isset($data->filter)) {
            $filter = $data->filter;
        } else {
            $filter = 'weekly'; // Default filter
        }

        if (isset($data->cohortid)) {
            $cohortid = $data->cohortid;
        } else {
            $cohortid = 0; // Default Cohort ID
        }
        return \report_elucidsitereport\active_users_block::get_data($filter, $cohortid);
    }

    /**
     * Get Course Progress data for Course Progress Page
     * @param  [object] Consist of filters 
     * @return [array] Course Progress array
     */
    public static function get_course_progress_data($data) {
        if ($data->courseid == "all") {
            return \report_elucidsitereport\course_progress_block::get_courselist($data->cohortid);
        }
        return \report_elucidsitereport\course_progress_block::get_data($data->courseid);
    }

    public static function get_active_courses_data() {
        return \report_elucidsitereport\active_courses_block::get_data();
    }

    /**
     * Get Face to Face session data
     * @param [string] $data Data for external service
     */
    public static function get_f2fsessiondata_data($data) {
        if (isset($data->cohortid)) {
            $cohortid = $data->cohortid;
        } else {
            $cohortid = 0;
        }

        return \report_elucidsitereport\f2fsession_block::get_data($cohortid);
    }

    public static function get_certificates_data($data) {
        if (isset($data->certificateid)) {
            return \report_elucidsitereport\certificates_block::get_issued_users($data->certificateid, $data->cohortid);
        }
        return \report_elucidsitereport\certificates_block::get_data();
    }

    public static function get_liveusers_data() {
        return \report_elucidsitereport\liveusers_block::get_data();
    }

    public static function get_siteaccess_data() {
        return \report_elucidsitereport\siteaccess_block::get_data();
    }

    public static function get_todaysactivity_data($data) {
        if (isset($data->date)) {
            $date = $data->date;
        } else {
            $date = false;
        }
        return \report_elucidsitereport\todaysactivity_block::get_data($date);
    }

    public static function get_lpstats_data($data) {
        return \report_elucidsitereport\lpstats_block::get_data($data->lpid);
    }

    public static function get_courseengage_data($cohortid) {
        return \report_elucidsitereport\courseengage_block::get_data($cohortid);
    }

    public static function get_inactiveusers_data($data) {
        if (isset($data->filter)) {
            $filter = $data->filter;
        } else {
            $filter = 'never'; // Default filter
        }
        return \report_elucidsitereport\inactiveusers_block::get_data($filter);
    }

    /** 
     * Get Course Completion Data
     * @param [string] $data Data to get Course Completion detail
     */
    public static function get_completion_data($data) {
        if ($data->cohortid) {
            $cohortid = $data->cohortid;
        } else {
            $cohortid = 0;
        }
        return \report_elucidsitereport\completion_block::get_data($data->courseid, $cohortid);
    }

    public static function get_courseanalytics_data($data) {
        if (isset($data->cohortid)) {
            $cohortid = $data->cohortid;
        } else {
            $cohortid = 0;
        }
        return \report_elucidsitereport\courseanalytics_block::get_data($data->courseid, $cohortid);
    }

    /** Generate Course Filter for course progress block
     * @param [bool] $all Get course with no enrolment as well
     * @return [array] Array of courses
     */
    public static function get_courses($all = false) {
        global $DB;
        $fields = "id, fullname, shortname";
        $records = $DB->get_records('course', array(), '', $fields);

        $courses = array();
        foreach ($records as $course) {
            if ($course->id == 1) {
                continue;
            }
            $coursecontext = context_course::instance($course->id);
            // Get only students
            $enrolledstudents = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');
            if (!$all && count($enrolledstudents) == 0) {
                continue;
            }
            $courses[] = $course;
        }
        return $courses;
    }

    /* Generate Learning Program Filter for course progress block
     * @return String HTML form with select and search box
     */
    public static function get_lps() {
        global $DB;
        $fields = "id, name, shortname, courses";
        $form = new MoodleQuickForm('learningprogram', 'post', '#');
        $records = $DB->get_records('wdm_learning_program', array(), '', $fields);

        $lps = array();
        foreach ($records as $lp) {
            /* If there in no courses available */
            if (empty(json_decode($lp->courses))) {
                continue;
            }

            /* If there in no userss available */
            $lpenrolment = $DB->get_records("wdm_learning_program_enrol", array("learningprogramid" => $lp->id), "userid");
            if (empty($lpenrolment)) {
                continue;
            }

            $lps[] = array(
                "id" => $lp->id,
                "fullname" => $lp->name
            );
        }

        return $lps;
    }

    /**
     * Get All Course Completion
     * @return [type] [description]
     */
    public static function get_course_completions() {
        global $DB;

        $sql = "SELECT CONCAT(mc.userid, '-', m.course),
            mc.userid, m.course,(COUNT(mc.userid)/
            (SELECT COUNT(*) FROM {course_modules}
            WHERE completion = m.completion
            AND course = m.course)) AS 'progress'
            FROM {course_modules} m, {course_modules_completion} mc
            WHERE m.id=mc.coursemoduleid
            AND mc.completionstate = :completionstatus
            AND m.completion > :completion
            GROUP BY mc.userid, m.course";
        $params = array(
            "completion" => 0,
            "completionstatus" => true
        );

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Get Course Completions by users
     * @return [type] [description]
     */
    public static function get_course_completion($courseid) {
        global $DB;

        $sql = "SELECT mc.userid, m.course,(COUNT(mc.userid)/
            (SELECT COUNT(*) FROM {course_modules}
            WHERE completion = m.completion
            AND course = m.course)) AS 'progress'
            FROM {course_modules} m, {course_modules_completion} mc
            WHERE m.id=mc.coursemoduleid
            AND mc.completionstate = :completionstatus
            AND m.completion > :completion
            AND m.course = :courseid
            GROUP BY mc.userid";

        $params = array(
            "completion" => 0,
            "completionstatus" => true,
            "courseid" => $courseid
        );

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Get Course Completion Information about a course
     * @param [object] $course Course Object
     * @param [int] $userid User Id
     * @return [array] Array of completion information
     */
    public static function get_course_completion_info($course = false, $userid = false) {
        global $COURSE, $USER;
        if (!$course) {
            $course = $COURSE;
        }

        if (!$userid) {
            $userid = $USER->id;
        }

        $completioninfo = array();
        $coursecontext = context_course::instance($course->id);
        if (is_enrolled($coursecontext, $userid)) {
            $completion = new completion_info($course);

            if ($completion->is_enabled()) {
                $percentage = \core_completion\progress::get_course_progress_percentage($course, $userid);
                $modules = $completion->get_activities();
                $completioninfo['totalactivities'] = count($modules);
                $completioninfo['completedactivities'] = 0;
                if (!is_null($percentage)) {
                    $percentage = floor($percentage);
                    if ($percentage == 100) {
                        $completioninfo['progresspercentage'] = 100;
                        $completioninfo['completedactivities'] = count($modules);
                    } else if ($percentage > 0 && $percentage < 100) {
                        $completioninfo['progresspercentage'] = $percentage;
                        foreach ($modules as $module) {
                            $data = $completion->get_data($module, false, $userid);
                            if ($data->completionstate) {
                                $completioninfo['completedactivities']++;
                            }
                        }
                    } else {
                        $completioninfo['progresspercentage'] = 0;
                    }
                } else {
                    $completioninfo['progresspercentage'] = 0;
                }
            }
        }
        return $completioninfo;
    }

    /**
     * Get Course Completion Time For Users
     * @param  [int] $courseid Course Id
     * @param  [int] $userid User Id
     * @return [int] Completion Time
     */
    public static function get_time_completion($courseid = false, $userid = false) {
        global $COURSE, $DB, $USER;

        if (!$courseid) {
            $courseid = $COURSE->id;
        }

        if (!$userid) {
            $userid = $USER->id;
        }

        $params = array(
            "userid" => $userid,
            "course" => $courseid
        );
        $completion = $DB->get_record("course_completions", $params);

        // If completion then return time completed
        if ($completion && $completion->timecompleted) {
            return $completion->timecompleted;
        }

        // If not completed then return false
        return NULL;
    }

    /**
     * Get Course Grade of a user
     * @param  [int] $courseid Course Id
     * @param  [int] $userid User Id
     * @return [object] Grade Report
     */
    public static function get_grades($courseid = false, $userid = false) {
        global $COURSE, $DB, $USER;

        if (!$userid) {
            $userid = $USER->id;
        }

        if (!$courseid) {
            $coueseid = $COURSE->id;
        }

        // please note that we must fetch all grade_grades fields if we want to construct grade_grade object from it!
        $gradesql = "SELECT g.id, g.finalgrade
            FROM {grade_items} gi, {grade_grades} g
            WHERE g.itemid = gi.id
            AND gi.courseid = :courseid
            AND g.userid = :userid
            AND gi.itemtype = 'course'";

        $params = array('courseid'=>$courseid, 'userid' => $userid);
        $gradereport = $DB->get_record_sql($gradesql, $params);

        return $gradereport;
    }

    /**
     * Get Users who visited the Course
     * @param [int] $courseid Course ID to get all visits
     * @return [array] Array of Users ID who visited the course
     */
    public static function get_course_visites($courseid, $cohortid) {
        global $DB;

        $params = array(
            "courseid" => $courseid,
            "action" => "viewed"
        );

        if ($cohortid) {
            $params["cohortid"] = $cohortid;
            $sql = "SELECT DISTINCT l.userid
                FROM {logstore_standard_log} l
                JOIN {cohort_members} cm
                ON l.userid = cm.userid
                JOIN {user} u
                ON u.id = l.userid
                WHERE cm.cohortid = :cohortid
                AND l.action = :action
                AND l.courseid = :courseid
                AND u.delete = 0";
        } else {
            $sql = "SELECT DISTINCT l.userid
                FROM {logstore_standard_log} l
                JOIN {user} u
                ON u.id = l.userid
                WHERE l.action = :action
                AND l.courseid = :courseid
                AND u.deleted = 0";
        }

        $records = $DB->get_records_sql($sql, $params);
        return $records;
    }

    /**
     * Get Users Who have complted atleast one activity in a course
     * @param [object] $courseid Course ID
     * @param [array] $users Enrolled Users
     * @return [array] Array of Users ID who have completed a activity 
     */
    public static function users_completed_a_module($course, $users, $cohortid) {
        $records = array();

        foreach($users as $user) {
            /* If cohort filter is there then get only users from cohort */
            if ($cohortid) {
                $cohorts = cohort_get_user_cohorts($user->id);
                if (!array_key_exists($cohortid, $cohorts)) {
                    continue;
                }
            }

            $completion = self::get_course_completion_info($course, $user->id);
            if ($completion["completedactivities"] > 0) {
                $records[] = $user;
            }
        }

        return $records;
    }

    /**
     * Get Users Who have complted half activities in a course
     * @param [object] $courseid Course ID
     * @param [array] $users Enrolled Users
     * @return [array] Array of Users ID who have completed half activities 
     */
    public static function users_completed_half_modules($course, $users, $cohortid) {
        $records = array();
        foreach($users as $user) {
            /* If cohort filter is there then get only users from cohort */
            if ($cohortid) {
                $cohorts = cohort_get_user_cohorts($user->id);
                if (!array_key_exists($cohortid, $cohorts)) {
                    continue;
                }
            }

            $completion = self::get_course_completion_info($course, $user->id);
            if ($completion["progresspercentage"] > 50) {
                $records[] = $user;
            }
        }

        return $records;
    }

    /**
     * Get Users Who have complted all activities in a course
     * @param [object] $courseid Course ID
     * @param [array] $users Enrolled Users
     * @return [array] Array of Users ID who have completed all activities 
     */
    public static function users_completed_all_module($course, $users, $cohortid) {
        $records = array();
        foreach($users as $user) {
            /* If cohort filter is there then get only users from cohort */
            if ($cohortid) {
                $cohorts = cohort_get_user_cohorts($user->id);
                if (!array_key_exists($cohortid, $cohorts)) {
                    continue;
                }
            }

            $completion = self::get_course_completion_info($course, $user->id);
            if ($completion["progresspercentage"] == 100) {
                $records[] = $user;
            }
        }

        return $records;
    }

    /**
     * Get Course visited by a users
     * @param [int] $courseid Course ID
     * @param [string] $userid User ID
     * @return [int] Count of visits by this users
     */
    public static function get_visits_by_users($courseid, $userid) {
        global $DB;
        
        $table = "logstore_standard_log";
        $records = $DB->get_records($table,
            array(
                "action" => "viewed",
                "courseid" => $courseid,
                "userid" => $userid
            ),
            "timecreated DESC"
        );
        return $records;
    }
}