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
 * Reports abstract block will define here to which will extend for each repoers blocks
 *
 * @package     local_edwiserreports
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserreports;

defined('MOODLE_INTERNAL') || die;

use stdClass;
use moodle_url;
use cache;
use context_course;
use html_writer;
use html_table;
use core_user;

require_once($CFG->dirroot . '/local/edwiserreports/classes/block_base.php');

/**
 * Course progress block.
 */
class courseprogressblock extends block_base {
    /**
     * Get reports data for Course Progress block
     * @param  object $params Parameters
     * @return object         Response object
     */
    public function get_data($params = false) {
        $courseid = isset($params->courseid) ? $params->courseid : false;
        $cohortid = isset($params->cohortid) ? $params->cohortid : false;

        // Make cache for courseprogress block.
        $cache = cache::make("local_edwiserreports", "courseprogress");
        $cachekey = $this->generate_cache_key('courseprogress', $courseid, $cohortid);

        // If cache not set for course progress.
        if (!$response = $cache->get($cachekey)) {
            // Get all courses for dropdown.
            $course = get_course($courseid);
            $coursecontext = context_course::instance($courseid);

            // Get only students.
            $enrolledstudents = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');

            // Get response.
            $response = new stdClass();
            $response->data = self::get_completion_with_percentage($course, $enrolledstudents, $cohortid);

            // Set cache to get data for course progress.
            $cache->set($cachekey, $response);
        }

        // Return response.
        return $response;
    }

    /**
     * Preapre layout for each block
     * @return object Response object
     */
    public function get_layout() {
        global $CFG;

        // Layout related data.
        $this->layout->id = 'courseprogressblock';
        $this->layout->name = get_string('courseprogress', 'local_edwiserreports');
        $this->layout->info = get_string('courseprogressblockhelp', 'local_edwiserreports');
        $this->layout->morelink = new moodle_url($CFG->wwwroot . "/local/edwiserreports/coursereport.php");
        $this->layout->hasdownloadlink = true;
        $this->layout->filters = '';

        // Block related data.
        $this->block->courses = \local_edwiserreports\utility::get_courses();
        if (!empty($this->block->courses)) {
            $this->block->hascourses = true;
            $this->block->firstcourseid = $this->block->courses[0]->id;
        }

        // Add block view in layout.
        $this->layout->blockview = $this->render_block('courseprogressblock', $this->block);

        // Set block edit capabilities.
        $this->set_block_edit_capabilities($this->layout->id);

        // Return blocks layout.
        return $this->layout;
    }

    /**
     * Get completion with percentage
     * (0%, 20%, 40%, 60%, 80%, 100%)
     * @param  object $course   Course Object
     * @param  array  $users    Users Object
     * @param  int    $cohortid Cohort id
     * @return array            Array of completion with percentage
     */
    public static function get_completion_with_percentage($course, $users, $cohortid) {
        $completions = \local_edwiserreports\utility::get_course_completion($course->id);
        $completedusers = array(
            LOCAL_SITEREPORT_PERCENTAGE_00 => 0,
            LOCAL_SITEREPORT_PERCENTAGE_20 => 0,
            LOCAL_SITEREPORT_PERCENTAGE_40 => 0,
            LOCAL_SITEREPORT_PERCENTAGE_60 => 0,
            LOCAL_SITEREPORT_PERCENTAGE_80 => 0,
            LOCAL_SITEREPORT_PERCENTAGE_100 => 0
        );
        foreach ($users as $user) {

            /* If cohort filter is there then get only users from cohort */
            if ($cohortid) {
                $cohorts = cohort_get_user_cohorts($user->id);
                if (!array_key_exists($cohortid, $cohorts)) {
                    continue;
                }
            }
            // If not set the completion then this user is not completed.
            if (!isset($completions[$user->id])) {
                $completedusers[LOCAL_SITEREPORT_PERCENTAGE_00]++;
            } else {
                $progress = $completions[$user->id]->completion / 100;
                switch (true) {
                    case $progress == LOCAL_SITEREPORT_COURSE_COMPLETE_100PER:
                        // Completed 100% of course.
                        $completedusers[LOCAL_SITEREPORT_PERCENTAGE_100]++;
                        break;
                    case $progress >= LOCAL_SITEREPORT_COURSE_COMPLETE_80PER && $progress < LOCAL_SITEREPORT_COURSE_COMPLETE_100PER:
                        // Completed 80% of course.
                        $completedusers[LOCAL_SITEREPORT_PERCENTAGE_80]++;
                        break;
                    case $progress >= LOCAL_SITEREPORT_COURSE_COMPLETE_60PER && $progress < LOCAL_SITEREPORT_COURSE_COMPLETE_80PER:
                        // Completed 60% of course.
                        $completedusers[LOCAL_SITEREPORT_PERCENTAGE_60]++;
                        break;
                    case $progress >= LOCAL_SITEREPORT_COURSE_COMPLETE_40PER && $progress < LOCAL_SITEREPORT_COURSE_COMPLETE_60PER:
                        // Completed 40% of course.
                        $completedusers[LOCAL_SITEREPORT_PERCENTAGE_40]++;
                        break;
                    case $progress >= LOCAL_SITEREPORT_COURSE_COMPLETE_20PER && $progress < LOCAL_SITEREPORT_COURSE_COMPLETE_40PER:
                        // Completed 20% of course.
                        $completedusers[LOCAL_SITEREPORT_PERCENTAGE_20]++;
                        break;
                    default:
                        // Completed 0% of course.
                        $completedusers[LOCAL_SITEREPORT_PERCENTAGE_00]++;
                }
            }
        }
        return array_values($completedusers);
    }

    /**
     * Get headers for exportable data for blocks
     * @return [array] Array of header
     */
    public static function get_header() {
        $header = array(
            get_string("name", "local_edwiserreports"),
            get_string("email", "local_edwiserreports"),
            get_string("coursename", "local_edwiserreports"),
            get_string("completedactivity", "local_edwiserreports"),
            get_string("completions", "local_edwiserreports")
        );
        return $header;
    }

    /**
     * Get header for report page for course progress
     * @return [array] Array of headers in report
     */
    public static function get_header_report() {
        $header = array(
            get_string("coursename", "local_edwiserreports"),
            get_string("enrolled", "local_edwiserreports"),
            get_string("completed", "local_edwiserreports"),
            get_string("per100-80", "local_edwiserreports"),
            get_string("per80-60", "local_edwiserreports"),
            get_string("per60-40", "local_edwiserreports"),
            get_string("per40-20", "local_edwiserreports"),
            get_string("per20-0", "local_edwiserreports"),
        );
        return $header;
    }

    /**
     * Get Course List
     * @param  int   $cohortid Cohort ID
     * @return array           Object of course list
     */
    public function get_courselist($cohortid) {
        global $CFG;
        $courses = \local_edwiserreports\utility::get_courses(true);

        $response = array();
        foreach ($courses as $course) {
            // Generate response object for a course.
            $completed100 = 0;
            $completed80 = 0;
            $completed60 = 0;
            $completed40 = 0;
            $completed20 = 0;
            $completed00 = 0;
            $enrolments = 0;

            $res = new stdClass();

            // Assign response data.
            $res->id = $course->id;
            $res->coursename = html_writer::link(
                new moodle_url($CFG->wwwroot . "/course/view.php", array("id" => $course->id)),
                $course->fullname
            );

            // Get only enrolled student.
            $enrolledstudents = \local_edwiserreports\utility::get_enrolled_students($course->id);
            if (!count($enrolledstudents) && !is_siteadmin()) {
                continue;
            }
            // Get completions.
            $compobj = new \local_edwiserreports\completions();
            $completions = $compobj->get_course_completions($course->id);

            // For each enrolled student get completions.
            foreach ($enrolledstudents as $key => $user) {
                // If cohort filter in there then remove the users
                // who is not belongs to the cohort.
                if ($cohortid) {
                    $cohorts = cohort_get_user_cohorts($user->id);
                    if (!array_key_exists($cohortid, $cohorts)) {
                        unset($enrolledstudents[$key]);
                        continue;
                    }
                }

                // Generate $key to save completion in an array.
                if (!isset($completions[$user->id])) {
                    // Completed 0% of course.
                    $completed00++;
                } else {
                    // Calculated progress percantage.
                    $progress = $completions[$user->id]->completion;

                    // Create array based on the completion.
                    switch (true) {
                        case $progress == 100:
                            // Completed 100% of course.
                            $completed100++;
                            break;
                        case $progress >= 80 && $progress < 100:
                            // Completed 80% of course.
                            $completed80++;
                            break;
                        case $progress >= 60 && $progress < 80:
                            // Completed 60% of course.
                            $completed60++;
                            break;
                        case $progress >= 40 && $progress < 60:
                            // Completed 40% of course.
                            $completed40++;
                            break;
                        case $progress >= 20 && $progress < 40:
                            // Completed 20% of course.
                            $completed20++;
                            break;
                        default:
                            // Completed 0% of course.
                            $completed00++;
                    }
                }

                // Increament enrolment count.
                $enrolments++;
            }

            $courseid = $course->id;
            $coursename = $course->fullname;
            $res->completed100 = self::get_userlist_popup_link($courseid, $coursename, $completed100, 'completed', '100', '100');
            $res->completed80 = self::get_userlist_popup_link($courseid, $coursename, $completed80, 'completed80', '80', '100');
            $res->completed60 = self::get_userlist_popup_link($courseid, $coursename, $completed60, 'completed60', '60', '80');
            $res->completed40 = self::get_userlist_popup_link($courseid, $coursename, $completed40, 'completed40', '40', '60');
            $res->completed20 = self::get_userlist_popup_link($courseid, $coursename, $completed20, 'completed20', '20', '40');
            $res->completed00 = self::get_userlist_popup_link($courseid, $coursename, $completed00, 'incompleted', '0', '20');
            $res->enrolments = self::get_userlist_popup_link($courseid, $coursename, $enrolments, 'enrolments', '-1', '100');

            // Added response object in response array.
            $response[] = $res;
        }
        // Return response.
        return $response;
    }

    /**
     * Prepare userslistpopup link
     *
     * @param  int    $courseid   Course id
     * @param  string $coursename Course name
     * @param  string $value      Value for link
     * @param  string $action     Action for link
     * @param  int    $minval     Minimum value for popup
     * @param  int    $maxval     Maximum value for popup
     * @return string             HTML link content
     */
    public static function get_userlist_popup_link($courseid, $coursename, $value, $action, $minval, $maxval) {
        $url = new moodle_url('javascript:void(0)');
        $class = 'modal-trigger text-dark text-decoration-none';
        return html_writer::link(
            $url,
            $value,
            array(
                'class' => $class,
                'data-action' => 'enrolments',
                'data-minvalue' => $minval,
                'data-maxvalue' => $maxval,
                'data-courseid' => $courseid,
                'data-coursename' => $coursename
            )
        );
    }

    /**
     * Get Users List Table
     * @param  int    $courseid Course ID
     * @param  int    $minval   Minimum Progress Value
     * @param  int    $maxval   Maximum Progress Value
     * @param  int    $cohortid Cohort id
     * @return string           HTML content
     */
    public static function get_userslist_table($courseid, $minval, $maxval, $cohortid) {
        $table = new html_table();
        $table->head = array(
            get_string("fullname", "local_edwiserreports"),
            get_string("email", "local_edwiserreports")
        );
        $table->attributes["class"] = "modal-table table";
        $table->attributes["style"] = "min-width: 100%;";

        $data = self::get_userslist($courseid, $minval, $maxval, $cohortid);
        if (!empty($data)) {
            $table->data = $data;
        }
        return html_writer::table($table);
    }

    /**
     * Get Users list
     * @param  int   $courseid Course ID
     * @param  int   $minval   Minimum Progress Value
     * @param  int   $maxval   Maximum Progress Value
     * @param  int   $cohortid Cohort id
     * @return array           Users Data Array
     */
    public static function get_userslist($courseid, $minval, $maxval, $cohortid) {
        $course = get_course($courseid);
        $enrolledstudents = \local_edwiserreports\utility::get_enrolled_students($course->id);

        // Get completions.
        $compobj = new \local_edwiserreports\completions();
        $completions = $compobj->get_course_completions($course->id);

        $usersdata = array();
        foreach ($enrolledstudents as $enrolleduser) {
            /* If cohort filter is there then get only users from cohort */
            if ($cohortid) {
                $cohorts = cohort_get_user_cohorts($enrolleduser->id);
                if (!array_key_exists($cohortid, $cohorts)) {
                    continue;
                }
            }

            // If Completion table dont have entries then
            // set progress as zero.
            if (!isset($completions[$enrolleduser->id])) {
                $progress = 0;
            } else {
                $progress = $completions[$enrolleduser->id]->completion;
            }

            // If progress between the min and max value.
            if (($progress >= $minval && $progress < $maxval) ||
                ($progress == 100 && $maxval == 100 && $minval == 100) ||
                ($maxval == 100 && $minval == -1)) {
                $user = core_user::get_user($enrolleduser->id);
                $usersdata[] = array(
                    fullname($user),
                    $user->email
                );
            }
        }

        return $usersdata;
    }

    /**
     * Get Exportable data for Course Progress Block
     * @param  string $filter Filter to get data from specific range
     * @return array          Array of exportable data
     */
    public function get_exportable_data_block($filter) {
        $export = array();
        $export[] = self::get_header();
        $course = get_course($filter);
        $enrolledstudents = \local_edwiserreports\utility::get_enrolled_students($filter);
        foreach ($enrolledstudents as $student) {
            $completion = \local_edwiserreports\utility::get_course_completion_info($course, $student->id);
            $completed = $completion["completedactivities"] . "/" . $completion["totalactivities"];
            $export[] = array(
                fullname($student),
                $student->email,
                $course->fullname,
                $completed,
                $completion["progresspercentage"] . "%"
            );
        }

        return $export;
    }

    /**
     * Get Exportable data for Active Users Page
     * @param  string $filter Filter to get data from specific range
     * @return array          Array of exportable data
     */
    public static function get_exportable_data_report($filter) {
        $cohortid = optional_param("cohortid", 0, PARAM_INT);
        $export = array();
        $export[] = self::get_header_report();
        $courses = \local_edwiserreports\utility::get_courses();

        foreach ($courses as $key => $course) {
            $blockobj = new self();

            $params = (object) array(
                'courseid' => $course->id,
                'cohortid' => $cohortid
            );
            $courseprogress = $blockobj->get_data($params);
            $enrolledstudents = \local_edwiserreports\utility::get_enrolled_students($course->id);
            if ($cohortid) {
                foreach ($enrolledstudents as $key => $user) {
                    $cohorts = cohort_get_user_cohorts($user->id);
                    if (!array_key_exists($cohortid, $cohorts)) {
                        unset($enrolledstudents[$key]);
                    }
                }
            }

            $export[] = array_merge(
                array(
                    $course->fullname,
                    count($enrolledstudents)
                ),
                array_reverse($courseprogress->data)
            );
        }

        return $export;
    }
    /**
     * Returns list of users enrolled into course.
     *
     * @param context $context
     * @param string $withcapability
     * @param int $groupid 0 means ignore groups, USERSWITHOUTGROUP without any group and any other value limits
     * the result by group id
     * @param string $userfields requested user record fields
     * @param string $orderby
     * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
     * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
     * @param bool $onlyactive consider only active enrolments in enabled plugins and time restrictions
     * @return array of user records
     */
    public static function rep_get_enrolled_users(
        \context $context,
        $withcapability = '',
        $groupid = 0,
        $userfields = 'u.*',
        $orderby = null,
        $limitfrom = 0,
        $limitnum = 0,
        $onlyactive = false
    ) {
        global $DB;
        list($esql, $params) = get_enrolled_sql($context, $withcapability, $groupid, $onlyactive);
        $sql = "SELECT $userfields
                  FROM {user} u
                  JOIN ($esql) je ON je.id = u.id
                 WHERE u.deleted = 0";
        if ($orderby) {
            $sql = "$sql ORDER BY $orderby";
        } else {
            list($sort, $sortparams) = users_order_by_sql('u');
            $sql = "$sql ORDER BY $sort";
            $params = array_merge($params, $sortparams);
        }
        return $DB->get_records_sql($sql, $params, $limitfrom, $limitnum);
    }
}
