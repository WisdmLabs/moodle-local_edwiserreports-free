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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . "/completion/classes/progress.php");
require_once($CFG->dirroot . "/cohort/lib.php");
require_once($CFG->libdir."/csvlib.class.php");
require_once($CFG->dirroot . '/local/edwiserreports/classes/constants.php');

$files = scandir($CFG->dirroot . "/local/edwiserreports/classes/blocks/");
unset($files[0]);
unset($files[1]);
foreach ($files as $file) {
    require_once($CFG->dirroot . "/local/edwiserreports/classes/blocks/" . $file);
}
require_once($CFG->dirroot . "/local/edwiserreports/locallib.php");

use stdClass;
use completion_info;
use context_course;
use html_writer;
use xmldb_table;
use core_course_category;
use context_system;
use moodle_url;

/**
 * Utilty class to add all utility function to perform in the eLucid report plugin.
 */
class utility {
    /**
     * Get active users data for active users blocks
     * @param  Object $data Data Object
     * @return Array        Active users data
     */
    public static function get_active_users_data($data) {
        global $CFG;

        // Require block file.
        require_once($CFG->dirroot . '/local/edwiserreports/classes/blocks/activeusersblock.php');

        $activeusersblock = new \local_edwiserreports\activeusersblock($data);
        return $activeusersblock->get_data($data);
    }

    /**
     * Get Course Progress data for Course Progress Page
     * @param  Object $data Consist of filters
     * @return Array        Course Progress array
     */
    public static function get_course_progress_data($data) {
        global $CFG;

        require_once($CFG->dirroot . '/local/edwiserreports/classes/blocks/courseprogressblock.php');

        $data = json_decode(required_param('data', PARAM_RAW));

        $courseprogress = new \local_edwiserreports\courseprogressblock();
        if ($data->courseid == "all") {
            return array("data" => $courseprogress->get_courselist($data->cohortid));
        }
        return $courseprogress->get_data($data);
    }

    /**
     * Get active courses data
     *
     * @return Array
     */
    public static function get_active_courses_data() {
        global $CFG;

        require_once($CFG->dirroot . '/local/edwiserreports/classes/blocks/activecoursesblock.php');

        $activecourses = new \local_edwiserreports\activecoursesblock();
        return $activecourses->get_data(false);
    }

    /**
     * Get certificates data
     *
     * @param  Object $data Filter data
     * @return Array
     */
    public static function get_certificates_data($data) {
        global $CFG;
        require_once($CFG->dirroot . '/local/edwiserreports/classes/blocks/certificatesblock.php');

        $certificatesblock = new \local_edwiserreports\certificatesblock();
        if (isset($data->certificateid)) {
            $cohort = isset($data->cohort) ? $data->cohort : false;
            return $certificatesblock->get_issued_users($data->certificateid, $cohort);
        }
        return $certificatesblock->get_data();
    }

    /**
     * Get live users data
     *
     * @return Array
     */
    public static function get_liveusers_data() {
        global $CFG;
        require_once($CFG->dirroot . '/local/edwiserreports/classes/blocks/liveusersblock.php');

        $liveusers = new \local_edwiserreports\liveusersblock();
        return $liveusers->get_data();
    }

    /**
     * Get site access data
     *
     * @return Array
     */
    public static function get_siteaccess_data() {
        global $CFG;
        require_once($CFG->dirroot . '/local/edwiserreports/classes/blocks/liveusersblock.php');

        $siteaccess = new \local_edwiserreports\siteaccessblock();
        return $siteaccess->get_data();
    }

    /**
     * Get todays activity data
     *
     * @param  Object $data Filter data
     * @return Array
     */
    public static function get_todaysactivity_data($data) {
        global $CFG;
        require_once($CFG->dirroot . '/local/edwiserreports/classes/blocks/todaysactivityblock.php');

        $todaysactivityblock = new \local_edwiserreports\todaysactivityblock();
        return $todaysactivityblock->get_data($data);
    }

    /**
     * Get course engage data
     *
     * @param  Object $cohortid Cohort id
     * @return Array
     */
    public static function get_courseengage_data($cohortid) {
        return \local_edwiserreports\courseengageblock::get_data($cohortid);
    }

    /**
     * Get inactive users data
     *
     * @param  Object $data Filter data
     * @return Array
     */
    public static function get_inactiveusers_data($data) {
        global $CFG;
        require_once($CFG->dirroot . '/local/edwiserreports/classes/blocks/inactiveusersblock.php');

        $inactiveusers = new \local_edwiserreports\inactiveusersblock();
        return $inactiveusers->get_data($data);
    }

    /**
     * Get Course Completion Data
     * @param  Object $data Data to get Course Completion detail
     * @return Object
     */
    public static function get_completion_data($data) {
        if ($data->cohortid) {
            $cohortid = $data->cohortid;
        } else {
            $cohortid = 0;
        }
        return \local_edwiserreports\completionblock::get_data($data->courseid, $cohortid);
    }

    /**
     * Get course analytics data
     *
     * @param  Object $data Data object
     * @return Object
     */
    public static function get_courseanalytics_data($data) {
        if (isset($data->cohortid)) {
            $cohortid = $data->cohortid;
        } else {
            $cohortid = 0;
        }
        return \local_edwiserreports\courseanalytics_block::get_data($data->courseid, $cohortid);
    }

    /**
     * Get data for grade graph.
     *
     * @param Object $data Data object
     *
     * @return Object
     */
    public static function get_grade_graph_data($data) {
        $grade = new \local_edwiserreports\gradeblock();
        return $grade->get_graph_data($data->filter);
    }

    /** Generate Course Filter for course progress block
     * @param  Bool  $all Get course with no enrolment as well
     * @return Array      Array of courses
     */
    public static function get_courses($all = false) {
        global $DB;

        // Get records for courses.
        $courses = $DB->get_records('course', array(), '', '*');
        foreach (array_keys($courses) as $courseid) {
            $enrolledstudents = self::get_enrolled_students($courseid);
            if ($courseid == 1 || empty($enrolledstudents)) {
                unset($courses[$courseid]);
            }
        }
        return array_values($courses);
    }

    /**
     * Generate Learning Program Filter for course progress block
     * @return array HTML form with select and search box
     */
    public static function get_lps() {
        global $DB;
        $fields = "DISTINCT(lp.id) as id, lp.name as fullname, lp.shortname,
                lp.programid, lp.visible, lp.selfenrolment, lp.description,
                lp.featureimage, lp.courses, lp.duration, lp.durationtime,
                lp.coursesequenceenable, lp.timecreated, lp.timemodified,
                lp.timestart, lp.timeend";

        $sql = "SELECT ".$fields."
                FROM {wdm_learning_program} lp
                JOIN {wdm_learning_program_enrol} lpen
                ON lp.id = lpen.learningprogramid";
        $records = $DB->get_records_sql($sql);
        $lps = array();

        $lps = array();
        foreach ($records as $lp) {
            /* If there in no courses available */
            if (empty(json_decode($lp->courses))) {
                continue;
            }

            /* If there in no userss available */
            $lpenrolment = $DB->get_records("wdm_learning_program_enrol", array("learningprogramid" => $lp->id), "userid");

            $lps[] = (array) $lp;
        }

        return $lps;
    }

    /**
     * Get All Course Completion
     * @return Array Course completions records
     */
    public static function get_course_completions() {
        global $DB;

        $sql = "SELECT CONCAT(CONCAT(mc.userid, '-'), m.course),
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
     * @param  Int   $courseid Course Id
     * @return Array           Array of course completion
     */
    public static function get_course_completion($courseid) {
        global $DB;

        // Return course completion from report completion table.
        return $DB->get_records_sql("SELECT userid, progress as completion
                                       FROM {edwreports_course_progress}
                                      WHERE courseid = :courseid", array('courseid' => $courseid));
    }

    /**
     * Get Course Completion Information about a course
     * @param  Object  $course Course Object
     * @param  Integer $userid User Id
     * @return Array           Array of completion information
     */
    public static function get_course_completion_info($course = false, $userid = false) {
        global $COURSE, $USER;
        if (!$course) {
            $course = $COURSE;
        }

        if (!$userid) {
            $userid = $USER->id;
        }

        // Default completions is 0.
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
     * Get Course Grade of a user
     * @param  Integer $courseid Course Id
     * @param  Integer $userid   User Id
     * @return Object            Grade Report
     */
    public static function get_grades($courseid = false, $userid = false) {
        global $COURSE, $DB, $USER;

        if (!$userid) {
            $userid = $USER->id;
        }

        if (!$courseid) {
            $coueseid = $COURSE->id;
        }

        // Please note that we must fetch all grade_grades fields if we want to construct grade_grade object from it!
        $gradesql = "SELECT g.id, gi.grademax, g.finalgrade
            FROM {grade_items} gi, {grade_grades} g
            WHERE g.itemid = gi.id
            AND gi.courseid = :courseid
            AND g.userid = :userid
            AND gi.itemtype = 'course'";

        $params = array('courseid' => $courseid, 'userid' => $userid);
        $gradereport = $DB->get_record_sql($gradesql, $params);

        return $gradereport;
    }

    /**
     * Get Course visited by a users
     * @param  Integer $courseid Course ID
     * @param  String  $userid   User ID
     * @return array           Count of visits by this users
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

    /**
     * Get Scheduled emails Tabs
     *
     * @param  Object $data Mail data
     * @return Object       Response
     */
    public static function get_scheduled_emails($data) {
        $response = new stdClass();
        $response->error = false;

        if (!$blockname = isset($data->blockname) ? $data->blockname : false) {
            $response->error = true;
        }

        if (!$component = isset($data->region) ? $data->region : false) {
            $response->error = true;
        }

        if ($blockname && $component) {
            $response->data = array();
            $params = array(
                'blockname' => $blockname,
                'component' => $component
            );
            $response->data = self::local_edwiserreports_get_schedule_emaillist($params);
        }
        return $response;
    }


    /**
     * Get Scheduled email list
     * @param  Array $params Parameters
     * @return Array         Emails
     */
    public static function local_edwiserreports_get_schedule_emaillist($params) {
        global $DB;

        $emails = array();
        $cmpblocknamesql = $DB->sql_compare_text('blockname');
        $cmpcomponentsql = $DB->sql_compare_text('component');

        $sql = "SELECT * FROM {edwreports_schedemails}
            WHERE $cmpblocknamesql LIKE :blockname
            AND $cmpcomponentsql LIKE :component";

        $rec = $DB->get_record_sql($sql, $params);
        // If data is not an array.
        if ($rec && ($emaildata = json_decode($rec->emaildata)) && is_array($emaildata)) {
            // If everythings is ok then.
            foreach ($emaildata as $key => $emailinfo) {
                $data = array();
                $data["esrname"] = $emailinfo->esrname;
                $data["esrnextrun"] = date("d M y", $emailinfo->esrnextrun);
                $data["esrfrequency"] = $emailinfo->esrfrequency;
                $data["esrmanage"] = local_edwiserreports_create_manage_icons_for_emaillist(
                    $key,
                    $rec->blockname,
                    $rec->component,
                    $emailinfo->esremailenable
                );
                $emails[] = $data;
            }
        }

        return $emails;
    }

    /**
     * Get Shceduled email details by id
     * @param  Object $data Data for email
     * @return Object       Response
     */
    public static function get_scheduled_email_details($data) {
        global $DB;

        $params = array(
            "blockname" => $data->blockname,
            "component" => $data->region
        );

        $response = new stdClass();
        $blockcompare = $DB->sql_compare_text('blockname');
        $componentcompare = $DB->sql_compare_text('component');
        $sql = "SELECT * FROM {edwreports_schedemails}
            WHERE $blockcompare LIKE :blockname
            AND $componentcompare LIKE :component";
        $records = $DB->get_record_sql($sql, $params);
        if (!$records) {
            $response->error = true;
            $response->errormsg = get_string('recordnotfound', 'local_edwiserreports');
        } else if (!$emaildata = json_decode($records->emaildata)) { // If it dosent have email data.
            $response->error = true;
            $response->errormsg = get_string('jsondecodefailed', 'local_edwiserreports');
        } else if (!is_array($emaildata)) { // If dta is not an array.
            $response->error = true;
            $response->errormsg = get_string('emaildataisnotasarray', 'local_edwiserreports');
        } else if (!isset($emaildata[$data->id])) {
            $response->error = true;
            $response->errormsg = get_string('sceduledemailnotexist', 'local_edwiserreports');
        } else {
            $response->error = false;
            $emaildata[$data->id]->esrid = $data->id;
            $response->data = $emaildata[$data->id];
        }
        return $response;
    }

    /**
     * Delete Shceduled emails
     * @param  [object] $data paramters to delete scheduled emails
     */
    public static function delete_scheduled_email($data) {
        global $DB;

        // Get data from table.
        $table = "edwreports_schedemails";
        $blockcompare = $DB->sql_compare_text('blockname');
        $componentcompare = $DB->sql_compare_text('component');
        $sql = "SELECT * FROM {edwreports_schedemails}
            WHERE $blockcompare LIKE :blockname
            AND $componentcompare LIKE :component";
        $params = array(
            "blockname" => $data->blockname,
            "component" => $data->region
        );

        $response = new stdClass();
        if (!$rec = $DB->get_record_sql($sql, $params)) {
            $response->error = true;
            $response->errormsg = get_string('recordnotfound', 'local_edwiserreports');
        } else if (!$emaildata = json_decode($rec->emaildata)) { // If it dosent have email data.
            $response->error = true;
            $response->errormsg = get_string('jsondecodefailed', 'local_edwiserreports');
        } else if (!is_array($emaildata)) { // If dta is not an array.
            $response->error = true;
            $response->errormsg = get_string('emaildataisnotasarray', 'local_edwiserreports');
        } else if (!isset($emaildata[$data->id])) {
            $response->error = true;
            $response->errormsg = get_string('sceduledemailnotexist', 'local_edwiserreports');
        } else {
            $response->error = false;
            unset($emaildata[$data->id]);
            $rec->emaildata = json_encode(array_values($emaildata));
            // Updating the record.
            $DB->update_record($table, $rec);
        }
        return $response;
    }

    /**
     * Change Shceduled emails
     * @param  Object $data paramters to delete scheduled emails
     * @return Object
     */
    public static function change_scheduled_email_status($data) {
        global $DB;

        // Get data from table.
        $table = "edwreports_schedemails";
        $blockcompare = $DB->sql_compare_text('blockname');
        $componentcompare = $DB->sql_compare_text('component');
        $sql = "SELECT * FROM {edwreports_schedemails}
                WHERE $blockcompare LIKE :blockname
                AND $componentcompare LIKE :component";
        $params = array(
            "blockname" => $data->blockname,
            "component" => $data->region
        );

        $response = new stdClass();
        if (!$rec = $DB->get_record_sql($sql, $params)) {
            $response->error = true;
            $response->errormsg = get_string('recordnotfound', 'local_edwiserreports');
            return $response;
        } else if (!$emaildata = json_decode($rec->emaildata)) { // If it dosent have email data.
            $response->error = true;
            $response->errormsg = get_string('jsondecodefailed', 'local_edwiserreports');
            return $response;
        } else if (!is_array($emaildata)) { // If dta is not an array.
            $response->error = true;
            $response->errormsg = get_string('emaildataisnotasarray', 'local_edwiserreports');
            return $response;
        } else if (!isset($emaildata[$data->id])) {
            $response->error = true;
            $response->errormsg = get_string('sceduledemailnotexist', 'local_edwiserreports');
            return $response;
        } else {
            $response->error = false;
            if ($status = $emaildata[$data->id]->esremailenable) {
                $emaildata[$data->id]->esremailenable = false;
                $response->successmsg = get_string('scheduledemaildisbled', 'local_edwiserreports');
            } else {
                $emaildata[$data->id]->esremailenable = true;
                $response->successmsg = get_string('scheduledemailenabled', 'local_edwiserreports');
            }
            $rec->emaildata = json_encode(array_values($emaildata));
            // Updating the record.
            $DB->update_record($table, $rec);
        }

        return $response;
    }

    /**
     * Get custom report selectors
     * @param  Object $filter Filter parameter
     * @return Object
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

        // Return courses.
        $response = new stdClass();
        $response->data = array_values($selectors);
        return $response;
    }

    /**
     * Get custom selectors for lps
     * @return Array Learning program array
     */
    private static function get_customreport_lp_selectors() {
        global $DB;

        // Get all learning programs.
        $lps = self::get_lps();

        // Prepare lp related data.
        $response = array();
        foreach ($lps as $key => $lp) {
            $res = new stdClass();
            $res->fullname = $lp['name'];
            $res->shortname = $lp['shortname'];

            // Prepare selector checkbox to select courses.
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

            // If duration is set in learning program.
            if ($lp->duration) {
                // Get duration time.
                $res->duration = format_time($lp['durationtime']);

                // Set starttime and endtime not applicable.
                $res->startdate = get_string('na', 'local_edwiserreports');
                $res->enddate = get_string('na', 'local_edwiserreports');
            } else {
                // Get learning programs startdate in redable format.
                $res->startdate = date('d-M-Y', $lp['timestart']);

                // Get learning programs end date in readable format.
                if ($lp['timeend']) {
                    $res->enddate = date('d-M-Y', $lp['timeend']);
                } else {
                    $res->enddate = get_string('never');
                }

                // Set duration not applicable.
                $res->duration = get_string('na', 'local_edwiserreports');
            }

            $response[] = $res;
        }

        // Return response.
        return $response;
    }

    /**
     * Get custom course selectors
     * @return Array Courses Array
     */
    private static function get_customreport_course_selectors() {
        // Get all courses.
        $courses = self::get_courses();

        // Prepare course related data.
        $response = array();
        foreach ($courses as $key => $course) {
            // Skip system course.
            if ($course->id == 1) {
                continue;
            }

            // Prepare response object.
            $res = new stdClass();
            $res->fullname = $course->fullname;
            $res->shortname = $course->shortname;

            // Prepare selector checkbox to select courses.
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

            // Get course startdate in redable format.
            $res->startdate = date('d-M-Y', $course->startdate);

            // Get course end date in readable format.
            if ($course->enddate) {
                $res->enddate = date('d-M-Y', $course->enddate);
            } else {
                $res->enddate = get_string('never');
            }

            // Get category.
            $category = core_course_category::get($course->category);
            $res->category = $category->get_formatted_name();

            $response[] = $res;
        }

        // Return courses.
        return $response;
    }

    /**
     * Get course enrolment information
     * @param  Integer        $courseid Course Id
     * @param  Integer        $userid   User Id
     * @return stdClass|false           enrolment information
     */
    public static function get_course_enrolment_info($courseid, $userid) {
        global $DB;
        $sql = "SELECT ue.*, e.enrol FROM {user_enrolments} ue
            JOIN {enrol} e ON e.id = ue.enrolid
            WHERE ue.userid = :uid AND e.courseid = :cid LIMIT 1";
        return $DB->get_record_sql($sql, array('uid' => $userid, 'cid' => $courseid));
    }
    /**
     * Get lp courses
     * @param  Array $lpids lp ids
     * @return Array        Courses
     */
    public static function get_lp_courses($lpids) {
        global $DB;
        if (in_array(0, $lpids) || empty($lpids)) {
            return self::get_courses();
        }
        list($insql, $inparams) = $DB->get_in_or_equal($lpids, SQL_PARAMS_NAMED, 'param', true);
        $sql = 'SELECT courses FROM {wdm_learning_program} WHERE id '.$insql;
        $lpcourses = array_values($DB->get_records_sql($sql, $inparams));
        $catids = array_map(function($o) {
            return $o->courses;
        }, $lpcourses);
        $coursesarr = array();
        if (!empty($catids)) {
            foreach ($catids as $catid) {
                foreach (json_decode($catid) as $cid) {
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
     * Get learning program students
     * @param  Int   $lpid Lp Id
     * @return Array       Array of users
     */
    public static function get_lp_students($lpid) {
        global $DB;

        // Prepare parameters.
        $params = array(
            'lpid' => $lpid,
            'roleid' => 0
        );

        // SQL to get leraning program records.
        $sql = "SELECT * FROM {wdm_learning_program_enrol}
                WHERE learningprogramid = :lpid
                AND roleid = :roleid";

        // Return all erolments.
        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Get all cohort based users
     * @param  array $cohortids Cohort Ids
     * @return array            Users array
     */
    public static function get_cohort_users($cohortids) {
        global $DB;

        if (in_array(0, $cohortids)) {
            $cohorts = local_edwiserreports_get_cohort_filter();
            if (isset($cohorts->values) && !empty($cohorts->values)) {
                $cohortids = array_column($cohorts->values, 'id');
            }
        }

        $params = array();

        $cohortjoinsql = '';
        $insql = '';
        if (!empty($cohortids)) {
            list($insql, $inparams) = $DB->get_in_or_equal($cohortids);
            $cohortjoinsql = "JOIN {cohort_members} co ON co.userid = u.id";
            $insql = " AND co.cohortid $insql ";
            $params = $inparams;
        }

        // Get all users.
        $sql = "SELECT DISTINCT(u.id), CONCAT(CONCAT(u.firstname, ' '), u.lastname) as fullname
                FROM {user} u
                $cohortjoinsql
                WHERE u.deleted = 0
                AND u.confirmed = 1
                AND u.id > 1 $insql
                ORDER BY fullname ASC";

        return array(
            'users' => array_values($DB->get_records_sql($sql, $params))
        );
    }

    /**
     * Get all available modules for reports
     * @return Array Modules array
     */
    public static function get_available_reports_modules() {
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
     * @return Array Modules array
     */
    public static function get_reports_block() {
        global $DB;
        $defaultreports = $DB->get_records('edwreports_blocks');
        $customreports = $DB->get_records('edwreports_custom_reports', array('enabledesktop' => 1));
        $cbposition = count($defaultreports);

        foreach ($customreports as $customreport) {
            $report = new stdClass();
            $report->id = $customreport->id;
            $report->classname = 'customreportsblock';
            $report->blockname = $customreport->shortname;
            $pref = new stdClass();
            $pref->desktopview = LOCAL_SITEREPORT_BLOCK_LARGE;
            $pref->tabletview = LOCAL_SITEREPORT_BLOCK_LARGE;
            $pref->position = $cbposition;
            $report->blockdata = json_encode($pref);
            $defaultreports[] = $report;
        }

        return $defaultreports;
    }

    /**
     * Set block preferences
     * @param  Object $data Data
     * @return Array
     */
    public static function set_block_preferences($data) {
        // Get all blocks.
        $blocks = self::get_reports_block();

        // Get preference of current block.
        $currentblock = self::get_reportsblock_by_name($data->blockname);
        $currentpref = self::get_reportsblock_preferences($currentblock);

        // For each blocks change preferences.
        foreach ($blocks as $key => $block) {
            $blockname = '';
            $pref = self::get_reportsblock_preferences($block);
            $prefname = 'pref_' . $block->classname;
            if ($block->classname == 'customreportsblock') {
                $blockname = 'customreportsblock-' . $block->id;
                $prefname .= '-' . $block->id;
            }

            if ($block->classname == $data->blockname || $blockname == $data->blockname) {
                $pref = $data;
            } else if ($currentpref['position'] == $data->position) {
                continue;
            } else {
                if ($pref['position'] >= $data->position && $pref['position'] < $currentpref['position']) {
                    $pref['position']++;
                } else if ($pref['position'] <= $data->position && $pref['position'] > $currentpref['position']) {
                    $pref['position']--;
                }
            }

            // Set block Preference.
            set_user_preference($prefname, json_encode($pref));
        }

        return array(
            "success" => true
        );
    }

    /**
     * Rearrage block with preferences.
     * @param Array $blocks Blocks
     */
    public static function rearrange_block_with_preferences(&$blocks) {
        $newblocks = array();
        foreach ($blocks as $block) {
            $pref = self::get_reportsblock_preferences($block);
            while (isset($newblocks[$pref['position']])) {
                $pref['position']++;
            }
            $newblocks[$pref['position']] = $block;
        }

        ksort($newblocks);
        $blocks = $newblocks;
    }

    /**
     * Get enrolled students in course
     * @param  Integer     $courseid Course Id
     * @param  Object|Bool $context  Context
     * @param  Integer     $cohortid Cohort Id
     * @param  Integer     $groupid  Group Id
     * @param  String      $fields   User table fields
     * @return Array                 Array of users
     */
    public static function get_enrolled_students($courseid, $context = false, $cohortid = 0, $groupid = 0, $fields = "u.*") {
        global $DB;
        if (!$context) {
            // Get default course context.
            $context = context_course::instance($courseid);
        }

        list($esql, $params) = get_enrolled_sql(
            $context,
            'moodle/course:isincompletionreports',
            $groupid,
            false);

        $cohortsql = "";
        if ($cohortid) {
            $params["cohortid"] = $cohortid;
            $cohortsql = "JOIN {cohort_members} ctmr ON u.id = ctmr.userid AND ctmr.cohortid = :cohortid
                            JOIN {cohort} cht ON ctmr.cohortid = cht.id AND cht.visible = 1";
        }

        $sql = "SELECT $fields
                FROM {user} u
                $cohortsql
                JOIN ($esql) je ON je.id = u.id
                WHERE u.deleted = 0";

        list($sort, $sortparams) = users_order_by_sql('u');
        $sql = "$sql ORDER BY $sort";
        $params = array_merge($params, $sortparams);

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Get reports blocks detailed by it name
     * @param String $blockname Block Name
     */
    public static function get_reportsblock_by_name($blockname) {
        global $DB;

        $block = new stdClass();
        if (strpos($blockname, 'customreportsblock') !== false) {
            $block = self::get_custom_report_block($blockname);
        } else {
            $block = $DB->get_record('edwreports_blocks', array('classname' => $blockname));
        }

        return $block;
    }

    /**
     * Get custom report block
     * @param String $blockname Block Name
     */
    public static function get_custom_report_block($blockname) {
        global $DB;
        $customreports = $DB->get_records('edwreports_custom_reports', array('enabledesktop' => 1), '', 'id');

        $params = explode('-', $blockname);
        $classname = isset($params[0]) ? $params[0] : '';
        $blockid = isset($params[1]) ? $params[1] : '';
        $crcount = array_search($blockid, array_keys($customreports));

        $block = $DB->get_record('edwreports_custom_reports', array('id' => $blockid));
        $block->blockname = $block->shortname;
        $pref = new stdClass();
        $pref->desktopview = LOCAL_SITEREPORT_BLOCK_LARGE;
        $pref->tabletview = LOCAL_SITEREPORT_BLOCK_LARGE;
        $pref->position = $DB->count_records('edwreports_blocks') + $crcount;
        $block->blockdata = json_encode($pref);
        $block->classname = 'customreportsblock';

        return $block;
    }

    /**
     * Get reports blocks detailed by it name
     * @param Object $block Block
     */
    public static function get_reportsblock_preferences($block) {
        $prefname = 'pref_' . $block->classname;
        if ($block->classname == 'customreportsblock') {
            $prefname .= '-' . $block->id;
        }

        if ($prefrences = get_user_preferences($prefname)) {
            $blockdata = json_decode($prefrences, true);
            $position = $blockdata['position'];
            $desktopview = $blockdata[LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW];
            $tabletview = $blockdata[LOCAL_SITEREPORT_BLOCK_TABLET_VIEW];
        } else {
            $blockdata = json_decode($block->blockdata, true);
            $position = get_config('local_edwiserreports', $block->blockname . 'position');
            $position = $position !== false ? $position : $blockdata['position'];
            $desktopview = get_config('local_edwiserreports', $block->blockname . 'desktopsize');
            $desktopview = $desktopview ? $desktopview : $blockdata['desktopview'];
            $tabletview = get_config('local_edwiserreports', $block->blockname . 'tabletsize');
            $tabletview = $tabletview ? $tabletview : $blockdata['tabletview'];
        }

        // Set default preference.
        $preferences = array();
        $preferences[LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW] = $desktopview;
        $preferences[LOCAL_SITEREPORT_BLOCK_TABLET_VIEW] = $tabletview;
        $preferences['position'] = $position;
        $preferences['hidden'] = isset($blockdata["hidden"]) ? $blockdata["hidden"] : 0;

        return $preferences;
    }

    /**
     * Allow users preferences to save remotly
     */
    public static function allow_update_userpreferences_remotly() {
        global $USER;

        $blocks = self::get_reports_block();
        foreach ($blocks as $block) {
            $USER->ajax_updatable_user_prefs['pref_' . $block->classname] = true;
        }
    }

    /**
     * Get blocks capabilities
     * @param  Object $block Block Data
     * @return Array
     */
    public static function get_blocks_capability($block) {
        $context = context_system::instance();

        $capabilitychoices = array();
        // Prepare the list of capabilities to choose from.
        if ($block->classname == 'customreportsblock') {
            $capname = 'report/edwiserreports_customreportsblock-' . $block->id . ':view';
            $capabilitychoices[$capname] = $block->fullname;
        } else {
            foreach ($context->get_capabilities() as $cap) {
                if (strpos($cap->name, 'report/edwiserreports_' . $block->classname) !== false) {
                    $strkey = str_replace(array('report/edwiserreports_', ':'), array('', ''), $cap->name);
                    $capabilitychoices[$cap->name] = get_string($strkey, 'local_edwiserreports');
                }
            }
        }

        return $capabilitychoices;
    }

    /**
     * Set blocks capabilities
     * @param  Object $data Block Data
     * @return Array
     */
    public static function set_block_capability($data) {
        global $DB;

        $context = context_system::instance();
        $blockname = $data->blockname;
        $capability = $data->capabilities;
        unset($data->blockname);
        unset($data->capabilities);

        $permissionconst = array(
             'inherit' => CAP_INHERIT,
             'allow' => CAP_ALLOW,
             'prevent' => CAP_PREVENT,
             'prohibit' => CAP_PROHIBIT
        );

        $config = array();
        if ($configstr = get_config('local_edwiserreports', str_replace('block', 'roleallow', $blockname))) {
            $config = explode(',', $configstr);
        }

        foreach ($data as $rolename => $permission) {
            $role = $DB->get_record('role', array('shortname' => $rolename));
            if (!$role) {
                continue;
            }

            if (strpos($blockname, 'customreportsblock') === false) {
                assign_capability($capability, $permissionconst[$permission], $role->id, $context->id, true);
            }

            if ($permissionconst[$permission] === CAP_ALLOW) {
                if (!in_array($role->id, $config)) {
                    $config[] = $role->id;
                }
            } else {
                if (($key = array_search($role->id, $config)) !== false) {
                    unset($config[$key]);
                }
            }
        }

        $config = implode(',', $config);
        set_config(str_replace('block', 'roleallow', $blockname), $config, 'local_edwiserreports');

        return array(
            "success" => true
        );
    }

    /**
     * Set blocks capabilities
     * @param  Object $data Block Data
     * @return Array
     */
    public static function toggle_hide_block($data) {
        $blockname = $data->blockname;
        $hidden = $data->hidden;

        $block = self::get_reportsblock_by_name($blockname);
        if (!$block) {
            return array(
                "error" => true,
                "errormsg" => "blocknotfound"
            );
        }

        $hide = $hidden ? 0 : 1;
        $pref = self::get_reportsblock_preferences($block);
        $pref['hidden'] = $hide;

        // Set block Preference.
        set_user_preference('pref_' . $blockname, json_encode($pref));

        return array(
            "success" => true
        );
    }

    /**
     * Get role capability from context
     * @param  Object $capcontext Capability Context
     * @param  Object $role       Role Object
     * @param  String $blockname  Block name
     * @return Int
     */
    public static function get_rolecap_from_context($capcontext, $role, $blockname) {
        global $CFG;

        if (strpos($blockname, 'customreportsblock') !== false) {
            $params = explode('-', $blockname);
            $classname = isset($params[0]) ? $params[0] : '';
            $blockid = isset($params[1]) ? $params[1] : '';
            $configstr = get_config('local_edwiserreports', str_replace('block', 'roleallow' , $blockname));
            if ($configstr) {
                $config = explode(',', $configstr);
                $rolecap = in_array($role->id, $config) ? CAP_ALLOW : CAP_INHERIT;
            } else {
                if ($role->archetype == 'manager' || $role->archetype == 'coursecreator') {
                    $rolecap = CAP_ALLOW;
                } else {
                    $rolecap = CAP_INHERIT;
                }
            }
        } else {
            $rolecap = $capcontext->rolecapabilities[$role->id];
        }

        return $rolecap;
    }

    /**
     * Create temporary table to join ids with table
     * @param  String $tablename Name of table
     * @param  Array $ids       Id array
     */
    public static function create_temp_table($tablename, $ids) {
        global $DB;

        $blockbase = new block_base();

        // User id for unique temp table for individual user.
        $userid = $blockbase->get_current_user();

        $tablename = $tablename . '_' . $userid;

        $dbman = $DB->get_manager();

        $table = new xmldb_table($tablename);
        $table->add_field('id', XMLDB_TYPE_INTEGER, 10, null, true, true);
        $table->add_field('tempid', XMLDB_TYPE_INTEGER, 10, null, true);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        self::drop_temp_table($tablename);

        $dbman->create_temp_table($table);

        foreach ($ids as $id) {
            $DB->insert_record($tablename, (object)[
                'tempid' => $id
            ]);
        }

        return $tablename;
    }

    /**
     * Delete temporary created table
     * @param  String $tablename Table name
     */
    public static function drop_temp_table($tablename) {
        global $DB;

        $dbman = $DB->get_manager();

        $table = new xmldb_table($tablename);

        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }
    }

    /**
     * Get active theme.
     *
     * @return int
     */
    public static function get_active_theme() {
        return 0;
    }

    /**
     * Load color themes as variable and css classes on page.
     *
     * @return void
     */
    public static function load_color_pallets() {
        global $PAGE;
        $theme = self::get_active_theme();
        $PAGE->requires->data_for_js('edwiser_reports_color_themes', LOCAL_EDWISERREPORTS_COLOR_THEMES[$theme]);
        $PAGE->requires->css(new moodle_url('/local/edwiserreports/styles/color-themes.php', array('theme' => $theme)));
    }

    /**
     * Get all exports icons
     * If options is set then return options with icons.
     * Else return icons array.
     *
     * @param Array $options Array options to add export icons
     * @return Array
     */
    public static function get_export_icons($options = null) {
        if ($options == null) {
            $options = [];
        }
        $options['pdf'] = self::image_icon('export/pdf');
        $options['csv'] = self::image_icon('export/csv');
        $options['xls'] = self::image_icon('export/xls');
        return $options;
    }

    /**
     * Get svg content.
     *
     * @return string
     */
    public static function image_icon($type) {
        global $CFG;
        $image = file_get_contents($CFG->dirroot . '/local/edwiserreports/pix/' . $type . '.svg');
        return $image;
    }

    public static function get_default_capabilities() {
        global $CFG;
        require_once($CFG->dirroot . '/local/edwiserreports/db/access.php');
        foreach ($capabilities as $key => $capability) {
            $capabilities[$key] = $capability['archetypes'];
        }
        return $capabilities;
    }
}
