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

defined('MOODLE_INTERNAL') || die();

use stdClass;
use context_system;
use context_helper;
use context_course;

require_once($CFG->dirroot . '/local/edwiserreports/classes/constants.php');

/**
 * Abstract class for reports_block
 */
class block_base {
    /**
     * Prepare layout
     * @var Object
     */
    public $layout;

    /**
     * Block object
     * @var Object
     */
    public $block;

    /**
     * Constructor to prepate data
     * @param Integer $blockid Block id
     */
    public function __construct($blockid = false) {
        $context = context_system::instance();

        $this->layout = new stdClass();
        $this->layout->sesskey = sesskey();
        $this->layout->extraclasses = '';
        $this->layout->infoicon = $this->image_icon('info');
        $this->layout->contextid = $context->id;
        $this->layout->caneditadv = false;
        $this->layout->region = 'block';
        $this->block = new stdClass();
        if (is_siteadmin()) {
            $this->layout->upgradelink = UPGRADE_URL;
        }

        if ($blockid) {
            $this->blockid = $blockid;
        }
    }

    /**
     * Get current user from authentication or global variable.
     *
     * @return int User id.
     */
    public function get_current_user() {
        global $USER;
        $secret = optional_param('secret', null, PARAM_TEXT);
        if ($secret !== null) {
            $authentication = new \local_edwiserreports\controller\authentication();
            return $authentication->get_user($secret);
        }

        return $USER->id;
    }

    /**
     * Create blocks data
     * @param Array $params Parameters
     */
    public function get_data($params = false) {
        debugging('extend the reports_block class and add get_data function');
    }

    /**
     * Preapre layout for each block
     */
    public function get_layout() {
        debugging('extend the reports_block class and add get_layout function');
    }

    /**
     * Create blocks data
     * @param  String $templatename Template name to render
     * @param  Object $context      Context object
     * @return String               HTML content
     */
    public function render_block($templatename, $context = array()) {
        // @codingStandardsIgnoreStart
        global $PAGE;

        $base = new \plugin_renderer_base($PAGE, RENDERER_TARGET_GENERAL);
        // @codingStandardsIgnoreEnd
        return $base->render_from_template('local_edwiserreports/' . $templatename, $context);
    }

    /**
     * Generate cache key for blocks
     * @param  String $blockname Block name
     * @param  Int    $id        Id
     * @param  Int    $cohortid  Cohort id
     * @return String            Cache key
     */
    public function generate_cache_key($blockname, $id, $cohortid = 0) {
        return $blockname . "-" . $id . "-" . $cohortid;
    }

    /**
     * Set block size
     * @param Object $block Block name
     */
    public function set_block_size($block) {
        $prefname = 'pref_' . $block->classname;
        if ($block->classname == 'customreportsblock') {
            $prefname .= '-' . $block->id;
        }

        $sizes = array();
        if ($prefrences = get_user_preferences($prefname)) {
            $blockdata = json_decode($prefrences, true);
            $position = $blockdata['position'];
            $sizes[LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW] = $blockdata[LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW];
            $sizes[LOCAL_SITEREPORT_BLOCK_TABLET_VIEW] = $blockdata[LOCAL_SITEREPORT_BLOCK_TABLET_VIEW];
        } else {
            $blockdata = json_decode($block->blockdata, true);
            $position = get_config('local_edwiserreports', $block->blockname . 'position');
            $position = $position ? $position : $blockdata['position'];
            $desktopview = get_config('local_edwiserreports', $block->blockname . 'desktopsize');
            $sizes[LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW] = $desktopview ? $desktopview : $blockdata['desktopview'];
            $tabletview = get_config('local_edwiserreports', $block->blockname . 'tabletsize');
            $sizes[LOCAL_SITEREPORT_BLOCK_TABLET_VIEW] = $tabletview ? $tabletview : $blockdata['tabletview'];
        }

        $devicecolclass = array(
            LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => 'col-lg-',
            LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => 'col-md-'
        );

        foreach ($sizes as $media => $size) {
            switch($size) {
                case LOCAL_SITEREPORT_BLOCK_LARGE:
                    $this->layout->extraclasses .= $devicecolclass[$media] . '12 ';
                    break;
                case LOCAL_SITEREPORT_BLOCK_MEDIUM:
                    $this->layout->extraclasses .= $devicecolclass[$media] . '6 ';
                    break;
                case LOCAL_SITEREPORT_BLOCK_SMALL:
                    $this->layout->extraclasses .= $devicecolclass[$media] . '4 ';
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * Get block position
     * @param Array $pref Preference
     */
    public function get_block_position($pref) {
        $position = $pref['position'];
    }

    /**
     * Set block edit capabilities for each block
     * @param  String $blockname Block name
     * @return Bool              false If not supported
     */
    public function set_block_edit_capabilities($blockname) {
        global $DB, $USER;

        if (!isset($USER->editing)) {
            return false;
        }

        // If user is not editing.
        if (!$USER->editing) {
            return false;
        }

        $block = \local_edwiserreports\utility::get_reportsblock_by_name($blockname);
        if (!$block) {
            return false;
        }

        $pref = \local_edwiserreports\utility::get_reportsblock_preferences($block);

        $this->layout->hidden = isset($pref["hidden"]) ? $pref["hidden"] : 0;

        $context = context_system::instance();
        if (strpos($blockname, 'customreportsblock') === false) {
            // Based on capability show the edit button
            // If user dont have capability to see the block.
            $this->layout->caneditadv = has_capability('report/edwiserreports_' . $blockname . ':editadvance', $context);
        } else {
            $this->layout->caneditadv = has_capability('report/edwiserreports_customreports:manage', $context);
        }

        // If have capability to edit.
        $this->layout->editopt = true;
    }

    /**
     * Filter courses based on visibility and capability.
     *
     * @param array $courses        Courses to be filtered
     * @param array $visiblecourses Array to store filtered courses
     * @param int   $userid         Current user id
     *
     * @return void
     */
    public function filter_courses($courses, &$visiblecourses, $userid) {
        // Preload contexts and check visibility.
        foreach ($courses as $course) {
            context_helper::preload_from_record($course);
            if ($course->visible) {
                if (!$context = context_course::instance($course->id)) {
                    continue;
                }
                // Check if user can view course.
                if (!has_capability('moodle/course:viewhiddencourses', $context, $userid)) {
                    continue;
                }
                // Skip duplicate course.
                if (isset($visiblecourses[$course->id])) {
                    continue;
                }
                $visiblecourses[$course->id] = $course;
            }
        }
    }


        /**
     * Get block data download link list for dropdown.
     * @param  bool  $graphical If true then return graphical export option
     * @param  bool  $email     If true then add email option in dropdown.
     * @return array
     */
    public function get_block_download_options($graphical = false, $email = true) {
        // If block is graphical then export options to export block image.
        if ($graphical == true) {
            $return = [[
                'name' => 'pdfimage',
                'label' => get_string('exporttopdf', 'local_edwiserreports'),
                'type' => 'button'
            ], [
                'name' => 'png',
                'label' => get_string('exporttopng', 'local_edwiserreports'),
                'type' => 'button'
            ], [
                'name' => 'jpeg',
                'label' => get_string('exporttojpeg', 'local_edwiserreports'),
                'type' => 'button'
            ], [
                'name' => 'svg',
                'label' => get_string('exporttosvg', 'local_edwiserreports'),
                'type' => 'button'
            ]];
            if ($email == true) {
                $return[] = [
                    'name' => 'email',
                    'label' => get_string('sendoveremail', 'local_edwiserreports'),
                    'type' => 'button',
                    'graphical' => 'graphical'
                ];
            }
            return $return;
        }

        // Tabular report.
        $return = [[
            'name' => 'pdf',
            'label' => get_string('exporttopdf', 'local_edwiserreports'),
            'type' => 'submit'
        ], [
            'name' => 'csv',
            'label' => get_string('exporttocsv', 'local_edwiserreports'),
            'type' => 'submit'
        ], [
            'name' => 'excel',
            'label' => get_string('exporttoexcel', 'local_edwiserreports'),
            'type' => 'submit'
        ]];
        if ($email == true) {
            $return[] = [
                'name' => 'email',
                'label' => get_string('sendoveremail', 'local_edwiserreports'),
                'type' => 'button'
            ];
        }
        return $return;
    }



    /**
     * Get date range for timeperiod.
     * @param String $timeperiod Timeperiod
     */
    public function get_old_date_range($timeperiod, $startdate, $enddate) {
        $oldenddate = $startdate - 86400;

        // Switch between timeperiod.
        switch ($timeperiod) {
            case 'last7days':
            case 'weekly':
                $days = LOCAL_SITEREPORT_WEEKLY_DAYS - 1;
                break;
            case 'monthly':
                $oldenddate = strtotime('last day of previous month', $startdate);
                $days = $oldenddate / 86400 - strtotime('first day of this month', $oldenddate) / 86400;
                break;
            case 'yearly':
                // Yearly days.
                // Ex. Date is 1960-04-31. Then period will be from 1958-04-01 to 1959-03-31.
                // Ex. Date is 1960-05-01. Then period will be from 1959-04-01 to 1960-03-31.
                $month = date('m', $startdate);
                $year = date('Y', $startdate);
                if ($month < 4) {
                    $year--;
                }
                $oldenddate = strtotime("$year-03-31") + 86400;
                $days = ($oldenddate / 86400) - (strtotime(($year - 1) . "-04-01") / 86400) - 1;
                break;
            default:
                $days = round(($enddate - $startdate) / 86400);
                break;
        }

        // Calculating startdate.
        $oldstartdate = $oldenddate - ($days * 86400);

        // Returning startdate and enddate.
        return [$oldstartdate, $oldenddate, $days];
    }

    /**
     * Get date range for timeperiod.
     * @param String $timeperiod Timeperiod
     */
    public function get_date_range($timeperiod) {

        // Default enddate.
        $enddate = floor(strtotime('yesterday') / 86400 + 1) * 86400;

        // Switch between timeperiod.
        switch ($timeperiod) {
            case 'last7days':
                // Last 7 days. Except today.
                $enddate = floor(strtotime('yesterday') / 86400 + 1) * 86400;
                $days = LOCAL_SITEREPORT_WEEKLY_DAYS - 1;
                break;
            case 'weekly':
                // Weekly days. From Last Week. Sunday to Saturday.
                $enddate = floor(strtotime('last saturday') / 86400 + 1) * 86400;
                $days = LOCAL_SITEREPORT_WEEKLY_DAYS - 1;
                break;
            case 'monthly':
                // Monthly days. Last Months 1st day to last day.
                $enddate = strtotime('last day of previous month');
                $days = $enddate / 86400 - strtotime('first day of previous month') / 86400;
                break;
            case 'yearly':
                // Yearly days.
                // Ex. Date is 1960-04-31. Then period will be from 1958-04-01 to 1959-03-31.
                // Ex. Date is 1960-05-01. Then period will be from 1959-04-01 to 1960-03-31.
                $month = date('m');
                $year = date('Y');
                if ($month < 4) {
                    $year--;
                }
                $enddate = strtotime("$year-03-31") + 86400;
                $days = ($enddate / 86400) - (strtotime(($year - 1) . "-04-01") / 86400) - 1;
                break;
            default:
                // Explode dates from custom date filter.
                $dates = explode(" to ", $timeperiod);
                if (count($dates) == 2) {
                    $startdate = strtotime($dates[0] . " 00:00:00") + 86400;
                    $enddate = strtotime($dates[1] . " 23:59:59");
                }

                // If it has correct startdat and end date then count xlabel.
                if (isset($startdate) && isset($enddate)) {
                    $days = round(($enddate - $startdate) / LOCAL_SITEREPORT_ONEDAY);
                } else {
                    $days = LOCAL_SITEREPORT_WEEKLY_DAYS; // Default one week.
                }
                break;
        }

        // Calculating startdate.
        $startdate = $enddate - ($days * 86400);

        // Returning startdate and enddate.
        return [$startdate, $enddate, $days];
    }

    /**
     * Get cohorts list.
     * @param  bool     $disabled   If true, return disabled cohorts.
     * @return array                Cohort list.
     */
    public function get_cohorts($disabled = false) {
        global $DB;
        if (!$DB->get_records('cohort')) {
            return false;
        }
        $cohorts = [
            'values' => [
                [
                    'id' => 0,
                    'name' => get_string('allcohorts', 'local_edwiserreports')
                ]
            ]
        ];
        if ($disabled) {
            $cohorts['disabled'] = true;
            $cohorts['availableinprolink'] = get_string('availableinprolink', 'local_edwiserreports', UPGRADE_URL);
        }
        return $cohorts;
    }

    /**
     * Check whether group filter can be shown.
     * If groups are present in the site then returning blank option
     * with message: 'Please select course'
     *
     * @return mixed boolean/array
     */
    public function get_default_group_filter($disabled = false) {
        global $DB;
        $sql = "SELECT DISTINCT(g.id), g.courseid, g.name
                  FROM {groups} g
                  JOIN {groups_members} gm ON g.id = gm.groupid
        ";
        if (!$DB->get_records_sql($sql)) {
            return false;
        }
        $groups = [
            'groups' =>
            [[
                'id' => 0,
                'name' => get_string('allgroups', 'local_edwiserreports')
            ]]
        ];
        if ($disabled) {
            $groups['disabled'] = true;
            $groups['availableinprolink'] = get_string('availableinprolink', 'local_edwiserreports', UPGRADE_URL);
        }
        return $groups;
    }

    /**
     * Get courses of categories where user is category manager or course creator.
     *
     * @param int $userid Current user id
     *
     * @return array
     */
    public function get_category_manager_creator_courses($userid) {
        global $DB;

        $allcats = array_keys(\core_course_category::make_categories_list());
        $allowedcat = array();
        $usercats = array();

        $categories = $DB->get_records_sql(
            "SELECT cc.*
               FROM {role_assignments} ra
               JOIN {role} r ON ra.roleid = r.id AND (r.archetype = 'manager' OR r.archetype = 'coursecreator')
               JOIN {context} ctx ON ra.contextid = ctx.id AND ctx.contextlevel = :contextlevel
               JOIN {course_categories} cc ON ctx.instanceid = cc.id
              WHERE ra.userid = :userid",
        array('userid' => $userid, 'contextlevel' => CONTEXT_COURSECAT));
        foreach ($categories as $categories) {
            $nested = $DB->get_records_sql(
                "SELECT * FROM {course_categories} WHERE path LIKE ? OR path LIKE ?",
                array('%/' . $categories->id, '%/' . $categories->id . '/%')
            );
            foreach ($nested as $n) {
                $usercats[$n->id] = true;
            }
        }

        $allowedcat = array_intersect($allcats, array_keys($usercats));

        // Temporary table for storing the course ids.
        $catstable = utility::create_temp_table('tmp_bb_cats', $allowedcat);

        $courses = $DB->get_records_sql(
            "SELECT c.*
            FROM {{$catstable}} ct
            JOIN {course} c ON ct.tempid = c.category"
        );

        // Droppping cats table.
        utility::drop_temp_table($catstable);

        // Get manager courses.
        $managercourses = $DB->get_records_sql(
            "SELECT c.*
               FROM {role_assignments} ra
               JOIN {role} r ON ra.roleid = r.id AND (r.archetype = 'manager' OR r.archetype = 'coursecreator')
               JOIN {context} ctx ON ra.contextid = ctx.id AND ctx.contextlevel = :contextlevel
               JOIN {course} c ON ctx.instanceid = c.id
              WHERE ra.userid = :userid",
        array('userid' => $userid, 'contextlevel' => CONTEXT_COURSE));

        foreach ($managercourses as $id => $course) {
            $courses[$id] = $course;
        }

        return $courses;
    }

    /**
     * Get users courses based on user role.
     * Site Admin/Manager - All courses.
     * Category Manager/Category Creator/Teacher/Editing Teacher - Enrolled courses.
     *
     * @param int $userid User id
     *
     * @return array
     */
    public function get_courses_of_user($userid = null) {
        global $USER;
        if ($userid == null) {
            $userid = $USER->id;
        }

        // Admin or Manager.
        if (is_siteadmin($userid) || has_capability('moodle/site:configview', context_system::instance(), $userid)) {
            return get_courses();
        }

        $visiblecourses = [];

        // Manager or creator courses.
        $managercourses = $this->get_category_manager_creator_courses($userid);
        $this->filter_courses($managercourses, $visiblecourses, $userid);
        if (count($visiblecourses) > 0) {
            return $visiblecourses;
        }

        // Enrolled users courses.
        $allcourses = enrol_get_all_users_courses($userid);
        $this->filter_courses($allcourses, $visiblecourses, $userid);

        return $visiblecourses;
    }

    /**
     * Get courses based on cohort.
     *
     * @param int $cohortid Cohort id
     * @param int $userid   User id
     *
     * @return array
     */
    public function get_courses_of_cohort_and_user($cohortid, $userid = null) {
        global $USER, $DB, $COURSE;
        if ($userid == null) {
            $userid = $USER->id;
        }

        // Get courses.
        $allcourses = $this->get_courses_of_user($userid);

        // Remove site course.
        unset($allcourses[$COURSE->id]);

        if ($cohortid) {
            // Temporary course table.
            $coursetable = utility::create_temp_table('tmp_bb_c', array_keys($allcourses));

            $fields = implode(', ', [
                'c.id',
                'c.category',
                'c.sortorder',
                'c.fullname',
                'c.shortname',
                'c.idnumber',
                'c.summaryformat',
                'c.format',
                'c.showgrades',
                'c.newsitems',
                'c.startdate',
                'c.enddate',
                'c.relativedatesmode',
                'c.marker',
                'c.maxbytes',
                'c.legacyfiles',
                'c.showreports',
                'c.visible',
                'c.visibleold',
                'c.downloadcontent',
                'c.groupmode',
                'c.groupmodeforce',
                'c.defaultgroupingid',
                'c.lang',
                'c.calendartype',
                'c.theme',
                'c.timecreated',
                'c.timemodified',
                'c.requested',
                'c.enablecompletion',
                'c.completionnotify',
                'c.cacherev',
                'c.originalcourseid',
                'c.showactivitydates',
                'c.showcompletionconditions'
            ]);

            $sql = "SELECT DISTINCT $fields
                    FROM {cohort} cht
                    JOIN {cohort_members} cm ON cht.id = cm.cohortid AND cm.cohortid = :cohortid
                    JOIN {role_assignments} ra ON cm.userid = ra.userid
                    JOIN {context} ctx ON ra.contextid = ctx.id AND ctx.contextlevel = :contextlevel
                    JOIN {course} c ON ctx.instanceid = c.id
                    JOIN {{$coursetable}} ct ON ct.tempid = ctx.instanceid";
            $param = [
                'cohortid' => $cohortid,
                'contextlevel' => CONTEXT_COURSE
            ];

            $allcourses = $DB->get_records_sql($sql, $param);

            // Drop temp table.
            utility::drop_temp_table($coursetable);
        }

        // Allowed courses.
        $visiblecourses = [];

        $this->filter_courses($allcourses, $visiblecourses, $userid);

        return $visiblecourses;
    }

    /**
     * Get users list based on cohort, course, and group.
     *
     * @param int $cohortid Cohort id
     * @param int $courseid Course id
     * @param int $groupid  Group id
     * @param int $userid   User id
     *
     * @return array
     */
    public function get_user_from_cohort_course_group($cohortid, $courseid, $groupid, $userid = null) {
        global $USER, $DB;
        if ($userid == null) {
            $userid = $USER->id;
        }

        $fields = 'u.id, ' . $DB->sql_fullname("u.firstname", "u.lastname") . ' AS fullname';

        // Use utility method if group is selected.
        if ($groupid != 0) {
            return \local_edwiserreports\utility::get_enrolled_students($courseid, false, $cohortid, $groupid, $fields);
        }

        $params = [
            'contextlevel' => CONTEXT_COURSE,
            'archetype' => 'student'
        ];

        // Cohort join.
        $cohortjoin = "";
        if ($cohortid != 0) {
            $cohortjoin = "JOIN {cohort_members} cm ON cm.userid = u.id AND cm.cohortid = :cohortid";
            $params['cohortid'] = $cohortid;
        }

        // Course join.
        if ($courseid != 0) {
            $coursejoin = " AND c.id = :courseid";
            $params['courseid'] = $courseid;
        } else {
            // Get courses.
            $courses = $this->get_courses_of_user($userid);

            // Creating temporary table.
            $coursetable = utility::create_temp_table('tmp_bb_c', array_keys($courses));

            $coursejoin = "JOIN {{$coursetable}} ct ON ct.tempid = ctx.instanceid";
        }

        $sql = "SELECT DISTINCT $fields
                FROM {role_assignments} ra
                JOIN {role} r ON ra.roleid = r.id AND r.archetype = :archetype
                JOIN {user} u ON ra.userid = u.id
                JOIN {context} ctx ON ra.contextid = ctx.id AND ctx.contextlevel = :contextlevel
                JOIN {course} c ON ctx.instanceid = c.id
                $coursejoin
                $cohortjoin";

        $users = $DB->get_records_sql($sql, $params);

        if ($courseid == 0) {
            // Drop temp table.
            utility::drop_temp_table($coursetable);
        }

        return $users;
    }

    /**
     * Get users from courses who are enrolled as student.
     *
     * @param int   $user    Accessing User id
     * @param mixed $courses Courses list
     *
     * @return array
     */
    public function get_users_of_course($course) {
        global $DB;
        // Admin or Manager.

        $params = [
            'contextlevel' => CONTEXT_COURSE,
            'archetype' => 'student',
            'course' => $course
        ];

        $fullname = $DB->sql_fullname("u.firstname", "u.lastname");

        $sql = "SELECT DISTINCT u.id, $fullname fullname
                  FROM {context} ctx
                  JOIN {role_assignments} ra ON ctx.id = ra.contextid
                  JOIN {role} r ON ra.roleid = r.id
                  JOIN {user} u ON ra.userid = u.id
                 WHERE ctx.contextlevel = :contextlevel
                   AND ctx.instanceid = :course
                   AND r.archetype = :archetype
                   AND u.confirmed = 1";
        $users = $DB->get_records_sql($sql, $params);

        return $users;
    }

    /**
     * Get users from courses who are enrolled as student.
     *
     * @param int   $user    Accessing User id
     * @param mixed $courses Courses list
     *
     * @return array
     */
    public function get_users_of_courses($userid, $courses) {
        global $DB;
        // Admin or Manager.

        $params = [
            'contextlevel' => CONTEXT_COURSE,
            'archetype' => 'student'
        ];

        $fullname = $DB->sql_fullname("u.firstname", "u.lastname");
        if (is_siteadmin($userid) || has_capability('moodle/site:configview', context_system::instance(), $userid)) {
            $sql = "SELECT DISTINCT u.id, $fullname fullname
                  FROM {context} ctx
                  JOIN {role_assignments} ra ON ctx.id = ra.contextid
                  JOIN {role} r ON ra.roleid = r.id
                  JOIN {user} u ON ra.userid = u.id
                 WHERE ctx.contextlevel = :contextlevel
                   AND r.archetype = :archetype
                   AND u.confirmed = 1";
            return $DB->get_records_sql($sql, $params);
        }

        // Temporary course table.
        $coursetable = utility::create_temp_table('tmp_bb_c', array_keys($courses));

        $sql = "SELECT DISTINCT u.id, $fullname fullname
                  FROM {{$coursetable}} c
                  JOIN {context} ctx ON c.tempid = ctx.instanceid
                  JOIN {role_assignments} ra ON ctx.id = ra.contextid
                  JOIN {role} r ON ra.roleid = r.id
                  JOIN {user} u ON ra.userid = u.id
                 WHERE ctx.contextlevel = :contextlevel
                   AND r.archetype = :archetype
                   AND u.confirmed = 1";
        $users = $DB->get_records_sql($sql, $params);

        // Droppping course table.
        utility::drop_temp_table($coursetable);

        return $users;
    }

    /**
     * Default method which will return empty postfix.
     * If any block has additional name content for export file
     * then override this method.
     *
     * @param string $filter
     *
     * @return string
     */
    public function get_exportable_data_block_file_postfix($filter) {
        return '';
    }

    /**
     * Get download link list for dropdown.
     * @param   bool    $graph  If graph is true then return graph export options.
     * @return  array
     */
    public function get_block_download_links($graph = false) {
        $exports = [[
            'name' => 'pdf',
            'label' => get_string('exporttopdf', 'local_edwiserreports'),
            'type' => 'submit',
            'btnclass' => 'text-left'
        ], [
            'name' => 'info',
            'label' => get_string('availableinpro', 'local_edwiserreports', UPGRADE_URL),
            'type' => 'button',
            'pro' => true,
            'class' => 'disabled text-center',
            'btnclass' => 'text-white theme-1-bg'
        ]];
        if ($graph == true) {
            $exports = array_merge($exports, [[
                'name' => 'png',
                'label' => get_string('exporttopng', 'local_edwiserreports'),
                'type' => 'button',
                'class' => 'disabled',
                'btnclass' => 'text-left'
            ], [
                'name' => 'jpeg',
                'label' => get_string('exporttojpeg', 'local_edwiserreports'),
                'type' => 'button',
                'class' => 'disabled',
                'btnclass' => 'text-left'
            ], [
                'name' => 'svg',
                'label' => get_string('exporttosvg', 'local_edwiserreports'),
                'type' => 'button',
                'class' => 'disabled',
                'btnclass' => 'text-left'
            ]]);
        } else {
            $exports = array_merge($exports, [[
                'name' => 'csv',
                'label' => get_string('exporttocsv', 'local_edwiserreports'),
                'type' => 'button',
                'class' => 'disabled',
                'btnclass' => 'text-left'
            ], [
                'name' => 'excel',
                'label' => get_string('exporttoexcel', 'local_edwiserreports'),
                'type' => 'button',
                'class' => 'disabled',
                'btnclass' => 'text-left'
            ]]);
        }
        $exports[] = [
            'name' => 'email',
            'label' => get_string('sendoveremail', 'local_edwiserreports'),
            'type' => 'button',
            'class' => 'disabled',
            'btnclass' => 'text-left'
        ];
        return $exports;
    }

    /**
     * Get svg content.
     *
     * @return string
     */
    public function image_icon($type) {
        global $CFG;
        $image = file_get_contents($CFG->dirroot . '/local/edwiserreports/pix/' . $type . '.svg');
        return $image;
    }
}
