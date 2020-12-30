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
 * @copyright   2020 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserreports\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use stdClass;
use templatable;
use context_system;
use context_coursecat;

/**
 * Elucid report renderable.
 */
class custom_reports_block implements renderable, templatable {
    /**
     * Function to export the renderer data in a format that is suitable for a
     * edit mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */
    public function export_for_template(renderer_base $output) {
        global $DB, $CFG;

        require_once($CFG->dirroot . "/cohort/lib.php");

        $output = null;
        $export = new stdClass();

        $syscontext = context_system::instance();
        $cohortobj = cohort_get_cohorts($syscontext->id);
        $cohorts = $cohortobj['cohorts'];

        $categories = $DB->get_records('course_categories', null, 'id');
        foreach ($categories as $category) {
            $catcontext = context_coursecat::instance($category->id);
            $cohortobj = cohort_get_cohorts($catcontext->id);
            $cohorts = array_merge($cohorts, $cohortobj["cohorts"]);
        }
        $export->cohorts = $cohorts;
        $courses = get_courses();

        // Remove system course.
        unset($courses[1]);
        $export->courses = array_values($courses);
        $export->fields = array(
            array (
                'key' => 'user',
                'title' => get_string('userfields', 'local_edwiserreports'),
                'fieldsarray' => $this->get_custom_report_user_fields()
            ),
            array (
                'key' => 'course',
                'title' => get_string('coursefields', 'local_edwiserreports'),
                'fieldsarray' => $this->get_custom_report_course_fields()
            )
        );

        return $export;
    }

    /**
     * Get custom reports users fields
     * @return array  Users Field for custom reports
     */
    public function get_custom_report_user_fields() {
        $userfields = array(
            array(
                'id' => 'username',
                'text' => get_string('username', 'local_edwiserreports'),
                'dbkey' => 'u.username',
                'disbaled' => true
            ),
            array('id' => 'email', 'text' => get_string('useremail', 'local_edwiserreports'), 'dbkey' => 'u.email'),
            array('id' => 'firstname', 'text' => get_string('firstname', 'local_edwiserreports'), 'dbkey' => 'u.firstname'),
            array('id' => 'lastname', 'text' => get_string('lastname', 'local_edwiserreports'), 'dbkey' => 'u.lastname')
        );
        return $userfields;
    }

    /**
     * Get custom reports course fields
     * @return array  Course Field for custom reports
     */
    public function get_custom_report_course_fields() {
        $coursefields = array(
            array(
                'id' => 'coursename',
                'text' => get_string('course', 'local_edwiserreports'),
                'dbkey' => 'CONCAT(\'"\', c.fullname, \'"\')',
                'disbaled' => true
            ),
            array(
                'id' => 'coursecategory',
                'text' => get_string('coursecategory', 'local_edwiserreports'),
                'dbkey' => 'ctg.name'
            ),
            array(
                'id' => 'courseenroldate',
                'text' => get_string('courseenroldate', 'local_edwiserreports'),
                'dbkey' => 'FROM_UNIXTIME(ra.timemodified, "%D %M %Y")'
            ),
            array(
                'id' => 'courseprogress',
                'text' => get_string('courseprogress', 'local_edwiserreports'),
                'dbkey' => 'ec.progress'
            ),
            array(
                'id' => 'completionstatus',
                'text' => get_string('course_completion_status', 'local_edwiserreports'),
                'dbkey' => '(CASE ec.progress WHEN 100 THEN "Completed" ELSE "In Progress" END)'
            ),
            array(
                'id' => 'activitiescompleted',
                'text' => get_string('activitiescompleted', 'local_edwiserreports'),
                'dbkey' => 'LENGTH(ec.completedmodules) - LENGTH(REPLACE(ec.completedmodules, ",", "")) + 1'
            ),
            array(
                'id' => 'incompletedactivities',
                'text' => get_string('incompletedactivities', 'local_edwiserreports'),
                'dbkey' => 'ec.totalmodules - (LENGTH(ec.completedmodules) - LENGTH(REPLACE(ec.completedmodules, ",", "")) + 1)'
            ),
            array(
                'id' => 'totalactivities',
                'text' => get_string('totalactivities', 'local_edwiserreports'),
                'dbkey' => 'ec.totalmodules'
            ),
            array(
                'id' => 'completiontime',
                'text' => get_string('completiontime', 'local_edwiserreports'),
                'dbkey' => 'FROM_UNIXTIME(ec.completiontime, "%D %M %Y")'
            ),
            array(
                'id' => 'coursestartdate',
                'text' => get_string('coursestartdate', 'local_edwiserreports'),
                'dbkey' => 'FROM_UNIXTIME(c.startdate, "%D %M %Y")'
            ),
            array(
                'id' => 'courseenddate',
                'text' => get_string('courseenddate', 'local_edwiserreports'),
                'dbkey' => '(CASE c.enddate WHEN 0 THEN "Never" ELSE FROM_UNIXTIME(c.enddate, "%D %M %Y") END)'
            ),
        );
        return $coursefields;
    }

    /**
     * Get custom reports users fields
     * @return array  Users Field for custom reports
     */
    public function get_custom_report_activity_fields() {
        $activityfields = array(
            array(
                'id' => 'activityname',
                'text' => get_string('activityname', 'local_edwiserreports'),
                'dbkey' => 'q.name',
                'disbaled' => true
            ),
            array('id' => 'grade', 'text' => get_string('grade', 'local_edwiserreports'), 'dbkey' => 'ROUND(qg.grade, 2)'),
            array(
                'id' => 'totalgrade',
                'text' => get_string('totalgrade', 'local_edwiserreports'),
                'dbkey' => 'ROUND(q.grade, 2)'
            ),
            array('id' => 'status', 'text' => get_string('status', 'local_edwiserreports'), 'dbkey' => 'qa.state'),
            array('id' => 'attempt', 'text' => get_string('attempt', 'local_edwiserreports'), 'dbkey' => 'qa.attempt'),
            array(
                'id' => 'attemptstart',
                'text' => get_string('attemptstart', 'local_edwiserreports'),
                'dbkey' => 'FROM_UNIXTIME(qa.timestart, "%D %M %Y %h:%i:%m")'
            ),
            array(
                'id' => 'attemptfinish',
                'text' => get_string('attemptfinish', 'local_edwiserreports'),
                'dbkey' => 'FROM_UNIXTIME(qa.timefinish, "%D %M %Y %h:%i:%m")'
            ),
        );
        return $activityfields;
    }
}
