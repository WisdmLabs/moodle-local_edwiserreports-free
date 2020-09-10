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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . "/completion/classes/progress.php");
require_once($CFG->dirroot . "/cohort/lib.php");
require_once($CFG->libdir."/csvlib.class.php");
require_once($CFG->dirroot . "/report/elucidsitereport/classes/blocks/activecoursesblock.php");
require_once($CFG->dirroot . "/report/elucidsitereport/classes/blocks/f2fsessionsblock.php");
require_once($CFG->dirroot . "/report/elucidsitereport/classes/blocks/certificatesblock.php");
require_once($CFG->dirroot . "/report/elucidsitereport/classes/blocks/liveusersblock.php");
require_once($CFG->dirroot . "/report/elucidsitereport/classes/blocks/siteaccess_block.php");
require_once($CFG->dirroot . "/report/elucidsitereport/classes/blocks/todaysactivity_block.php");
require_once($CFG->dirroot . "/report/elucidsitereport/classes/blocks/lpstats_block.php");
require_once($CFG->dirroot . "/report/elucidsitereport/classes/blocks/inactiveusers_block.php");
require_once($CFG->dirroot . "/report/elucidsitereport/classes/blocks/courseengage_block.php");
require_once($CFG->dirroot . "/report/elucidsitereport/classes/blocks/completion_block.php");
require_once($CFG->dirroot . "/report/elucidsitereport/classes/blocks/courseanalytics_block.php");

use stdClass;
use completion_info;
use context_course;
use MoodleQuickForm;
use progress;
use html_writer;
use csv_export_writer;
use core_user;
use core_course_category;

/**
 * Utilty class to add all utility function
 * to perform in the eLucid report plugin
 */
class utility {
    /**
     * Get active users data for active users blocks
     */
    public static function get_active_users_data($data) {
        global $CFG;

        require_once($CFG->dirroot . '/report/elucidsitereport/classes/blocks/activeusersblock.php');

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

        $activeusersblock =  new \report_elucidsitereport\activeusersblock($filter);
        return $activeusersblock->get_data($filter, $cohortid);
    }

    /**
     * Get Course Progress data for Course Progress Page
     * @param  [object] Consist of filters 
     * @return [array] Course Progress array
     */
    public static function get_course_progress_data($data) {
        global $CFG;

        require_once($CFG->dirroot . '/report/elucidsitereport/classes/blocks/courseprogressblock.php');

        $data = json_decode(required_param('data', PARAM_RAW));

        $courseprogress = new \report_elucidsitereport\courseprogressblock();
        if ($data->courseid == "all") {
            return $courseprogress->get_courselist($data->cohortid);
        }
        return $courseprogress->get_data($data);
    }

    public static function get_active_courses_data() {
        global $CFG;

        require_once($CFG->dirroot . '/report/elucidsitereport/classes/blocks/activecoursesblock.php');

        $activecourses = new \report_elucidsitereport\activecoursesblock();
        return $activecourses->get_data(false);
    }

    /**
     * Get Face to Face session data
     * @param [string] $data Data for external service
     */
    public static function get_f2fsessiondata_data($data) {
        global $CFG;

        require_once($CFG->dirroot . '/report/elucidsitereport/classes/blocks/f2fsessionsblock.php');

        $f2fsession = new \report_elucidsitereport\f2fsessionsblock();
        return $f2fsession->get_data($data);
    }

    public static function get_certificates_data($data) {
        global $CFG;
        require_once($CFG->dirroot . '/report/elucidsitereport/classes/blocks/certificatesblock.php');

        $certificatesblock = new \report_elucidsitereport\certificatesblock();
        if (isset($data->certificateid)) {
            return \report_elucidsitereport\certificates_block::get_issued_users($data->certificateid, $data->cohortid);
        }
        return $certificatesblock->get_data();
    }

    public static function get_liveusers_data() {
        global $CFG;
        require_once($CFG->dirroot . '/report/elucidsitereport/classes/blocks/liveusersblock.php');

        $liveusers = new \report_elucidsitereport\liveusersblock();
        return $liveusers->get_data();
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
        $fields = "*";
        $records = $DB->get_records('course', array(), '', $fields);

        $courses = array();
        foreach ($records as $course) {
            if ($course->id == 1) {
                continue;
            }
            $coursecontext = context_course::instance($course->id);
            // Get only students
            $enrolledstudents = \report_elucidsitereport\courseprogressblock::rep_get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');
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
        $fields = "DISTINCT(lp.id) as id, lp.name as fullname, lp.shortname, lp.programid, lp.visible, lp.selfenrolment, lp.description, lp.featureimage, lp.courses, lp.duration, lp.durationtime, lp.coursesequenceenable, lp.timecreated, lp.timemodified, lp.timestart, lp.timeend";

        // $fields = "id, name, shortname, courses";
        //$form = new MoodleQuickForm('learningprogram', 'post', '#');
        // Create reporting manager instance
        $rpm = reporting_manager::get_instance();
        $sql = "SELECT ".$fields."
                FROM {wdm_learning_program} lp
                JOIN {wdm_learning_program_enrol} lpen
                ON lp.id = lpen.learningprogramid
                WHERE lpen.userid ".$rpm->insql."";
        $records = $DB->get_records_sql($sql, $rpm->inparams);
        // Create reporting manager instance
        $rpm = \report_elucidsitereport\reporting_manager::get_instance();
        $lps = array();
        // $records = $DB->get_records('wdm_learning_program', array(), '', $fields);

        $lps = array();
        $rpms = array();
        foreach ($records as $lp) {
            /* If there in no courses available */
            if (empty(json_decode($lp->courses))) {
                continue;
            }

            /* If there in no userss available */
            $lpenrolment = $DB->get_records("wdm_learning_program_enrol", array("learningprogramid" => $lp->id), "userid");
            if ($rpm->isrpm) {
                $lpusers = array();
                array_map(function($value) use (&$lpusers){
                    $lpusers[] = $value->userid;
                }, $lpenrolment);
                $rpms = array_intersect($lpusers, $rpm->rpmusers);
            }
            if (empty($lpenrolment) || ($rpm->isrpm && empty($rpms))) {
                continue;
            }

            $lps[] = (array) $lp;
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
     * @param [int] Course Id
     * @return [array] Array of course completion
     */
    public static function get_course_completion($courseid) {
        global $DB;

        // Return course completion from report completion table
        $table = "edw_course_progress";
        return $DB->get_records($table, array("courseid" => $courseid), "", "userid, progress as completion");
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

        // Default completions is 0
        $completioninfo = array(
            'totalactivities' => 0,
            'completedactivities' => 0,
            'progresspercentage' => 0
        );

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
        // Create reporting manager instance
        $rpm = reporting_manager::get_instance();
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
                AND u.deleted = 0
                AND u.id ".$rpm->insql."";
        } else {
            $sql = "SELECT DISTINCT l.userid
                FROM {logstore_standard_log} l
                JOIN {user} u
                ON u.id = l.userid
                WHERE l.action = :action
                AND l.courseid = :courseid
                AND u.deleted = 0
                AND u.id ".$rpm->insql."";
        }
        $params = array_merge($params, $rpm->inparams);
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
        global $DB;

        $records = array();
        foreach($users as $user) {
            /* If cohort filter is there then get only users from cohort */
            if ($cohortid) {
                $cohorts = cohort_get_user_cohorts($user->id);
                if (!array_key_exists($cohortid, $cohorts)) {
                    continue;
                }
            }

            $completionsql = "SELECT id, progress as completion
                FROM {edw_course_progress}
                WHERE userid = :userid
                AND courseid = :courseid
                AND progress
                BETWEEN :completionstart
                AND :completionend";

            // Calculate 50% Completion Count for Courses 
            $params = array(
                "completionstart" => 50.00,
                "completionend" => 99.99,
                "courseid" => $course->id,
                "userid" => $user->id 
            );
            $completion = $DB->get_record_sql($completionsql, $params);

            // $completion = self::get_course_completion_info($course, $user->id);
            if ($completion && $completion->completion >= 50 && $completion->completion < 100) {
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

    /* Get Scheduled emails Tabs */
    public function get_scheduled_emails($data) {
        $response = new stdClass();
        $response->error = false;
        $response->data = array();
        $response->data = self::get_schedule_emaillist();
        return $response;
    }


    /**
     * Get Scheduled email list
     * @return [type] [description]
     */
    public static function get_schedule_emaillist() {
        global $DB;

        $emails = array();
        $rec = $DB->get_records('elucidsitereport_schedemails');
        foreach($rec as $key => $val) {
            // If it dosent have email data
            if (!$emaildata = json_decode($val->emaildata)) {
                continue;
            }

            // If dta is not an array
            if (!is_array($emaildata)) {
                continue;
            }

            // If everythings is ok then
            foreach($emaildata as $key => $emailinfo) {
                $data = array();
                $data["esrtoggle"] = create_toggle_switch_for_emails(
                    $key,
                    $emailinfo->esremailenable,
                    $val->blockname,
                    $val->component
                );
                $data["esrname"] = $emailinfo->esrname;
                $data["esrnextrun"] = date("d M y", $emailinfo->esrnextrun);
                $data["esrfrequency"] = $emailinfo->esrfrequency;
                $data["esrcomponent"] = $val->blockname;
                $data["esrmanage"] = create_manage_icons_for_emaillist(
                    $key,
                    $val->blockname,
                    $val->component,
                    $emailinfo->esremailenable
                );
                $emails[] = $data;
            }
        }
        return $emails;
    }

    /**
     * Get Shceduled email details by id
     * @return [type] [description]
     */
    public static function get_scheduled_email_details($data) {
        global $DB;

        // Get data from table
        $table = "elucidsitereport_schedemails";
        $sql = "SELECT * FROM {elucidsitereport_schedemails}
            WHERE blockname = :blockname
            AND component = :component";
        $params = array(
            "blockname" => $data->blockname,
            "component" => $data->region
        );

        $response = new stdClass();
        if (!$rec = $DB->get_record_sql($sql, $params)) {
            $response->error = true;
            $response->errormsg = "Record not found";
            return $response;
        }

        // If it dosent have email data
        if (!$emaildata = json_decode($rec->emaildata)) {
            $response->error = true;
            $response->errormsg = "Json decode failed";
            return $response;
        }

        // If dta is not an array
        if (!is_array($emaildata)) {
            $response->error = true;
            $response->errormsg = "Email data is not an array";
            return $response;
        }

        if (!isset($emaildata[$data->id])) {
            $response->error = true;
            $response->errormsg = "Schedule email not exist";
            return $response;
        }

        $response->error = false;
        $emaildata[$data->id]->esrid = $data->id;
        $response->data = $emaildata[$data->id];
        return $response;
    }

    /**
     * Delete Shceduled emails
     * @param  [object] $data paramters to delete scheduled emails
     */
    public static function delete_scheduled_email($data) {
        global $DB;

        // Get data from table
        $table = "elucidsitereport_schedemails";
        $sql = "SELECT * FROM {elucidsitereport_schedemails}
            WHERE blockname = :blockname
            AND component = :component";
        $params = array(
            "blockname" => $data->blockname,
            "component" => $data->region
        );

        $response = new stdClass();
        if (!$rec = $DB->get_record_sql($sql, $params)) {
            $response->error = true;
            $response->errormsg = "Record not found";
            return $response;
        }

        // If it dosent have email data
        if (!$emaildata = json_decode($rec->emaildata)) {
            $response->error = true;
            $response->errormsg = "Json decode failed";
            return $response;
        }

        // If dta is not an array
        if (!is_array($emaildata)) {
            $response->error = true;
            $response->errormsg = "Email data is not an array";
            return $response;
        }

        if (!isset($emaildata[$data->id])) {
            $response->error = true;
            $response->errormsg = "Schedule email not exist";
            return $response;
        }

        $response->error = false;
        unset($emaildata[$data->id]);

        $rec->emaildata = json_encode(array_values($emaildata));

        // Updating the record
        $DB->update_record($table, $rec);
        return $response;
    }

    /**
     * Change Shceduled emails
     * @param  [object] $data paramters to delete scheduled emails
     */
    public static function change_scheduled_email_status($data) {
        global $DB;

        // Get data from table
        $table = "elucidsitereport_schedemails";
        $sql = "SELECT * FROM {elucidsitereport_schedemails}
            WHERE blockname = :blockname
            AND component = :component";
        $params = array(
            "blockname" => $data->blockname,
            "component" => $data->region
        );

        $response = new stdClass();
        if (!$rec = $DB->get_record_sql($sql, $params)) {
            $response->error = true;
            $response->errormsg = "Record not found";
            return $response;
        }

        // If it dosent have email data
        if (!$emaildata = json_decode($rec->emaildata)) {
            $response->error = true;
            $response->errormsg = "Json decode failed";
            return $response;
        }

        // If dta is not an array
        if (!is_array($emaildata)) {
            $response->error = true;
            $response->errormsg = "Email data is not an array";
            return $response;
        }

        if (!isset($emaildata[$data->id])) {
            $response->error = true;
            $response->errormsg = "Schedule email not exist";
            return $response;
        }

        $response->error = false;
        if ($status = $emaildata[$data->id]->esremailenable) {
            $emaildata[$data->id]->esremailenable = false;
        } else {
            $emaildata[$data->id]->esremailenable = true;
        }

        $rec->emaildata = json_encode(array_values($emaildata));

        // Updating the record
        $DB->update_record($table, $rec);
        return $response;
    }

    /**
     * Get custom report selectors
     * @param  [type] $filter [description]
     * @return [type]         [description]
     */
    public static function get_customreport_selectors($filter) {
        global $DB;

        switch($filter->type) {
            case 'lps' :
                $selectors = self::get_customreport_lp_selectors();;
                break;
            case 'courses' :
            default :
                $selectors = self::get_customreport_course_selectors();
        }

        // Return courses
        $response = new stdClass();
        $response->data = array_values($selectors);
        return $response;
    }

    /**
     * Get custom selectors for lps
     * @return array Learning program array
     */
    private static function get_customreport_lp_selectors() {
        global $DB;

        // Get all learning programs
        $lps = self::get_lps();

        // Prepare lp related data
        $response = array();
        foreach ($lps as $key => $lp) {
            $res = new stdClass();
            $res->fullname = $lp['name'];
            $res->shortname = $lp['shortname'];

            // Prepare selector checkbox to select courses
            $res->select = html_writer::start_tag('span',
                array("class" => "checkbox-custom"));
            $res->select .= html_writer::tag('input', '',
                array(
                    "type" => "checkbox",
                    "name" => "customReportSelect-" . $lp['id'],
                    "data-id" => $lp['id']
                )
            );
            $res->select .= html_writer::tag('label', '',
                array(
                    "class" => "selectorCheckbox-" . $lp['id'],
                    "for" => "customReportSelect-" . $lp['id']
                )
            );
            $res->select .= html_writer::end_tag('span');

            // If duration is set in learning program
            if ($lp->duration) {
                // Get duration time
                $res->duration = format_time($lp['durationtime']);

                // Set starttime and endtime not applicable
                $res->startdate = get_string('na', 'report_elucidsitereport');
                $res->enddate = get_string('na', 'report_elucidsitereport');
            } else {
                // Get learning programs startdate in redable format
                $res->startdate = date('d-M-Y', $lp['timestart']);

                // Get learning programs end date in readable format
                if ($lp['timeend']) {
                    $res->enddate = date('d-M-Y', $lp['timeend']);
                } else {
                    $res->enddate = get_string('never');
                }

                // Set duration not applicable
                $res->duration = get_string('na', 'report_elucidsitereport');
            }

            $response[] = $res;
        }

        // Return response
        return $response;
    }

    /**
     * Get custom course selectors
     * @return array Courses Array
     */
    private static function get_customreport_course_selectors() {
        // Get all courses
        $courses = \report_elucidsitereport\utility::get_courses();

        // Prepare course related data
        $response = array();
        foreach($courses as $key => $course) {
            // Skip system course
            if ($course->id == 1) {
                continue;
            }

            // Prepare response object
            $res = new stdClass();
            $res->fullname = $course->fullname;
            $res->shortname = $course->shortname;

            // Prepare selector checkbox to select courses
            $res->select = html_writer::start_tag('span',
                array("class" => "checkbox-custom"));
            $res->select .= html_writer::tag('input', '',
                array(
                    "type" => "checkbox",
                    "name" => "customReportSelect-" . $course->id,
                    "data-id" => $course->id
                )
            );
            $res->select .= html_writer::tag('label', '',
                array(
                    "class" => "selectorCheckbox-" . $course->id,
                    "for" => "customReportSelect-" . $course->id
                )
            );
            $res->select .= html_writer::end_tag('span');

            // Get course startdate in redable format
            $res->startdate = date('d-M-Y', $course->startdate);

            // Get course end date in readable format
            if ($course->enddate) {
                $res->enddate = date('d-M-Y', $course->enddate);
            } else {
                $res->enddate = get_string('never');
            }

            // Get category
            $category = core_course_category::get($course->category);
            $res->category = $category->get_formatted_name();

            $response[] = $res;
        }

        // Return courses
        return $response;
    }

    /**
     * Get course enrolment information
     * @param  int $courseid Course Id
     * @param  int $userid User Id
     * @return stdClass|false enrolment information
     */
    public static function get_course_enrolment_info($courseid, $userid) {
        global $DB;
        $sql = "SELECT ue.*, e.enrol FROM {user_enrolments} ue
            JOIN {enrol} e ON e.id = ue.enrolid
            WHERE ue.userid = :uid AND e.courseid = :cid LIMIT 1";
        return $DB->get_record_sql($sql, array('uid' => $userid, 'cid' => $courseid));
    }
    /**
     * [get_lp_courses description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public static function get_lp_courses($lpids) {
        global $DB;
        if (in_array(0, $lpids) || empty($lpids)) {
            return \report_elucidsitereport\utility::get_courses();
        }
        list($insql, $inparams) = $DB->get_in_or_equal($lpids, SQL_PARAMS_NAMED, 'param', true);
        $sql = 'SELECT courses FROM {wdm_learning_program} WHERE id '.$insql;
        $lpcourses = array_values($DB->get_records_sql($sql, $inparams));
        $catIds = array_map(create_function('$o', 'return $o->courses;'), $lpcourses);
        $coursesarr = array();
        if (!empty($catIds)) {
            foreach ($catIds as $catId) {
                foreach (json_decode($catId) as $cid) {
                    if ($course = $DB->get_record('course', array('id' => $cid))) {
                        $courseinfo = new \stdClass();
                        $courseinfo->id = $course->id;
                        $courseinfo->fullname = $course->fullname;
                        array_push($coursesarr, $courseinfo);
                    }
                }
            }
        }
        return $coursesarr;
    }

    /**
     * Get reporting managers related data for
     * courses and leraning programs
     * @param  [object] $data Data object
     * @return [array]        Courses and Leraning Programs
     */
    public static function get_rpm_data($data) {
        global $DB;
        
        // Get all users
        $cohorts = self::get_cohort_users($data->cohortids, $data->rpmids);
        $userinfo = $cohorts['users'];

        if (in_array(0, $data->rpmids) || empty($data->rpmids)) {
            $courses = \report_elucidsitereport\utility::get_courses();
            $lps = \report_elucidsitereport\utility::get_lps();
            return array('courses' => $courses, 'lps' => $lps, 'users' => $userinfo);
        }
        list($insql, $inparams) = $DB->get_in_or_equal($data->rpmids, SQL_PARAMS_NAMED, 'param', true);
        // Query to get users of reporting manager
        $sql = "SELECT userid FROM {user_info_data} WHERE data ".$insql;
        $users = $DB->get_records_sql($sql, $inparams);

        // If there is no users for this reporting managers
        if (empty($users)) {
            return array('courses' => array(), 'lps' => array(), 'users' => array());
        }

        $rpmusers = array_keys($users);
        $lps = array();
        $courses = array();
        list($insql, $inparams) = $DB->get_in_or_equal($rpmusers, SQL_PARAMS_NAMED, 'param', true);
        $sql = "SELECT DISTINCT(lp.id) as id, lp.name as fullname FROM {wdm_learning_program} lp
        JOIN {wdm_learning_program_enrol} lpe ON lpe.learningprogramid = lp.id
        WHERE lpe.userid ".$insql;
        
        $records = $DB->get_records_sql($sql, $inparams);
        $lpIds = array_keys($records);
        $lps = array_values($records);
        // $lpIds = array_map(create_function('$o', 'return $o->id;'), $lps);
        if (!empty($lpIds)) {
            $courses = \report_elucidsitereport\utility::get_lp_courses($lpIds);
        }

        return array('courses' => $courses, 'lps' =>$lps, 'users' => $userinfo);
    }

    /**
     * Get learning program students
     * @param  $lpid   Lp Id
     * @return [array] Array of users
     */
    public static function get_lp_students($lpid) {
        global $DB;

        // Reporting manager object
        $rpm = reporting_manager::get_instance();

        // Prepare parameters
        $params = array(
            'lpid' => $lpid,
            'roleid' => 0
        );

        // SQL to get leraning program records
        $sql = "SELECT * FROM {wdm_learning_program_enrol}
                WHERE learningprogramid = :lpid
                AND roleid = :roleid
                AND userid " . $rpm->insql;

        // Merge params for reporting manager
        $params = array_merge($params, $rpm->inparams);

        // Return all erolments
        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Get all cohort based users
     * @param  array    $cohortids  Cohort Ids
     * @return array                Users array
     */
    public static function get_cohort_users($cohortids, $rpmids) {
        global $DB;

        if (in_array(0, $cohortids)) {
            $cohorts = get_cohort_filter();
            if (isset($cohorts->values) && !empty($cohorts->values)) {
                $cohortids = array_column($cohorts->values, 'id');
            }
        }

        $rpmdb = '';
        $params = array();
        $rpmparams = array();
        if (!empty($rpmids)) {
            // Create reporting manager instance
            $rpm = \report_elucidsitereport\reporting_manager::get_instance();
            $students = $rpm->get_all_reporting_managers_students($rpmids);
            if (!empty($students)) {
                list($rpmdb, $rpmparams) = $DB->get_in_or_equal($students);
            }
        }

        $cohortjoinsql = '';
        $insql = '';
        if (!empty($cohortids)) {
            list($insql, $inparams) = $DB->get_in_or_equal($cohortids);
            $cohortjoinsql = "JOIN {cohort_members} co ON co.userid = u.id";
            $insql = " AND co.cohortid $insql ";
            $params = array_merge($rpmparams, $inparams);
        }

        // Get all users
        $sql = "SELECT DISTINCT(u.id), CONCAT(u.firstname, ' ', u.lastname) as fullname
                FROM {user} u
                $cohortjoinsql
                WHERE u.id $rpmdb
                AND u.deleted = false
                AND u.confirmed = true
                AND u.id > 1 $insql
                ORDER BY fullname ASC";

        return array(
            'users' => array_values($DB->get_records_sql($sql, $params))
        );
    }

    /**
     * Get all available modules for reports
     * @return array Modules array
     */
    public static function get_available_reports_modules () {
        global $DB;
        $availablemod = array(
            'quiz'
        );

        list($insql, $inparams) = $DB->get_in_or_equal($availablemod);
        $sql = "SELECT id, name FROM {modules}
                WHERE name $insql";

        return array_values($DB->get_records_sql($sql, $inparams));
    }

    /**
     * Get all available modules for reports
     * @return array Modules array
     */
    public static function get_reports_block() {
        // TODO: Temp preparation of block remove after done
        $activeusersblock = new stdClass();
        $activeusersblock->classname = 'activeusersblock';
        $courseprogressblock = new stdClass();
        $courseprogressblock->classname = 'courseprogressblock';
        $activecoursesblock = new stdClass();
        $activecoursesblock->classname = 'activecoursesblock';
        $certificatesblock = new stdClass();
        $certificatesblock->classname = 'certificatesblock';
        $liveusersblock = new stdClass();
        $liveusersblock->classname = 'liveusersblock';
        $f2fsessionsblock = new stdClass();
        $f2fsessionsblock->classname = 'f2fsessionsblock';
        $reportblocks = array(
            'activeusers' => $activeusersblock,
            'courseprogress' => $courseprogressblock,
            'activecourses' => $activecoursesblock,
            'certificates' => $certificatesblock,
            'liveusers' => $liveusersblock,
            'f2fsessions' => $f2fsessionsblock
        );

        return $reportblocks;
    }
}