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

use stdClass;
use context_system;
use context_helper;
use context_course;

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
        if (is_siteadmin() || has_capability('moodle/site:configview', context_system::instance())) {
            return get_courses();
        }

        $visiblecourses = [];

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
            $coursetable = utility::create_temp_table('tmp_blockbase_courses', array_keys($allcourses));

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
            $coursetable = utility::create_temp_table('tmp_blockbase_courses', array_keys($courses));

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
        $coursetable = utility::create_temp_table('tmp_blockbase_courses', array_keys($courses));

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
     *
     * @return array
     */
    public function get_block_download_links() {
        return [[
            'name' => 'pdf',
            'label' => get_string('exporttopdf', 'local_edwiserreports'),
            'type' => 'button'
        ], [
            'name' => 'csv',
            'label' => get_string('exporttocsv', 'local_edwiserreports'),
            'type' => 'button'
        ], [
            'name' => 'excel',
            'label' => get_string('exporttoexcel', 'local_edwiserreports'),
            'type' => 'button'
        ], [
            'name' => 'email',
            'label' => get_string('sendoveremail', 'local_edwiserreports'),
            'type' => 'button'
        ]];
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
