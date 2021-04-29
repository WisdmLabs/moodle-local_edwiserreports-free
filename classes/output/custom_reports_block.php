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
use moodle_url;

/**
 * Elucid report renderable.
 */
class custom_reports_block implements renderable, templatable {
    /**
     * Constructor to create custom reports edit page
     * @param Integer $reportsid Reports ID
     */
    public function __construct($reportsid = 0) {
        $this->reportsid = $reportsid;
    }

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

        $selectedfield = array();
        $selectedcourses = array("0");
        $selectedcohorts = array("0");
        if ($export->reportsid = $this->reportsid) {
            $customreport = $DB->get_record('edwreports_custom_reports', array('id' => $this->reportsid));
            $export->fullname = $customreport->fullname;
            $export->shortname = $customreport->shortname;
            $reportsdata = json_decode($customreport->data);
            $export->downloadenable = $reportsdata->downloadenable ? true : false;
            $export->enabledesktop = $customreport->enabledesktop ? true : false;
            $selectedfield = $reportsdata->selectedfield;
            $selectedcourses = $reportsdata->courses;
            $selectedcohorts = $reportsdata->cohorts;
        }

        $syscontext = context_system::instance();
        $cohortobj = cohort_get_all_cohorts(0, 0);
        $cohorts = $cohortobj['cohorts'];

        $categories = $DB->get_records('course_categories', null, 'id');
        foreach ($categories as $category) {
            $catcontext = context_coursecat::instance($category->id);
            $cohortobj = cohort_get_cohorts($catcontext->id);
            $cohorts = array_merge($cohorts, $cohortobj["cohorts"]);
        }
        $export->cohorts = $cohorts;
        $export->isediting = $this->reportsid ? true : false;
        $url = '/local/edwiserreports/customreportedit.php';
        $export->createnewlink = new moodle_url($url, array('create' => true));

        // Select courses and cohorts.
        $courses = get_courses();
        $export->selectedcourses = json_encode($selectedcourses);
        $export->selectedcohorts = json_encode($selectedcohorts);

        // Remove system course.
        unset($courses[1]);
        $export->courses = array_values($courses);
        $export->fields = array(
            array (
                'key' => 'user',
                'title' => get_string('userfields', 'local_edwiserreports'),
                'fieldsarray' => $this->get_custom_report_user_fields($selectedfield)
            ),
            array (
                'key' => 'course',
                'title' => get_string('coursefields', 'local_edwiserreports'),
                'fieldsarray' => $this->get_custom_report_course_fields($selectedfield)
            )
        );

        return $export;
    }

    /**
     * Get custom reports users fields
     * @param  Array $selectedfield Selected report fields
     * @return Array                Users Field for custom reports
     */
    public function get_custom_report_user_fields($selectedfield = array()) {
        $userfields = array(
            array(
                'id' => 'username',
                'text' => get_string('username', 'local_edwiserreports'),
                'dbkey' => 'u.username',
                'disbaled' => true,
                'selected' => in_array('username', $selectedfield)
            ),
            array(
                'id' => 'email',
                'text' => get_string('useremail', 'local_edwiserreports'),
                'dbkey' => 'u.email',
                'selected' => in_array('email', $selectedfield)
            ),
            array(
                'id' => 'firstname',
                'text' => get_string('firstname', 'local_edwiserreports'),
                'dbkey' => 'u.firstname',
                'selected' => in_array('firstname', $selectedfield)
            ),
            array(
                'id' => 'lastname',
                'text' => get_string('lastname', 'local_edwiserreports'),
                'dbkey' => 'u.lastname',
                'selected' => in_array('lastname', $selectedfield)
            )
        );
        return $userfields;
    }

    /**
     * Get custom reports course fields
     * @param  Array $selectedfield Selected report fields
     * @return Array                Course Field for custom reports
     */
    public function get_custom_report_course_fields($selectedfield = array()) {
        $coursefields = array(
            array(
                'id' => 'coursename',
                'text' => get_string('coursename', 'local_edwiserreports'),
                'dbkey' => 'c.fullname',
                'disbaled' => true,
                'selected' => in_array('coursename', $selectedfield)
            ),
            array(
                'id' => 'coursecategory',
                'text' => get_string('coursecategory', 'local_edwiserreports'),
                'dbkey' => 'ctg.name',
                'selected' => in_array('coursecategory', $selectedfield)
            ),
            array(
                'id' => 'courseenroldate',
                'text' => get_string('courseenroldate', 'local_edwiserreports'),
                'dbkey' => 'ra.timemodified',
                'selected' => in_array('courseenroldate', $selectedfield),
                'resultfunc' => function($value) {
                    return $value ? date('d M Y', $value) : get_string('na', 'local_edwiserreports');
                }
            ),
            array(
                'id' => 'courseprogress',
                'text' => get_string('courseprogress', 'local_edwiserreports'),
                'dbkey' => 'ec.progress',
                'selected' => in_array('courseprogress', $selectedfield),
                'resultfunc' => function($value) {
                    return $value . '%';
                }
            ),
            array(
                'id' => 'completionstatus',
                'text' => get_string('course_completion_status', 'local_edwiserreports'),
                'dbkey' => 'ec.progress',
                'selected' => in_array('completionstatus', $selectedfield),
                'resultfunc' => function($value) {
                    $ret = get_string('inprogress', 'local_edwiserreports');
                    if ($value == 100) {
                        $ret = get_string('completed', 'local_edwiserreports');
                    }
                    return $ret;
                }
            ),
            array(
                'id' => 'activitiescompleted',
                'text' => get_string('activitiescompleted', 'local_edwiserreports'),
                'dbkey' => 'ec.completedmodules',
                'selected' => in_array('activitiescompleted', $selectedfield),
                'resultfunc' => function($value) {
                    $ret = 0;
                    if ($value) {
                        $ret = count(explode(',', $value));
                    }
                    return $ret;
                }
            ),
            array(
                'id' => 'totalactivities',
                'text' => get_string('totalactivities', 'local_edwiserreports'),
                'dbkey' => 'ec.totalmodules',
                'selected' => in_array('totalactivities', $selectedfield)
            ),
            array(
                'id' => 'completiontime',
                'text' => get_string('completiontime', 'local_edwiserreports'),
                'dbkey' => 'ec.completiontime',
                'selected' => in_array('completiontime', $selectedfield),
                'resultfunc' => function($value) {
                    return $value ? date('d M Y', $value) : get_string('na', 'local_edwiserreports');
                }
            ),
            array(
                'id' => 'coursestartdate',
                'text' => get_string('coursestartdate', 'local_edwiserreports'),
                'dbkey' => 'c.startdate',
                'selected' => in_array('coursestartdate', $selectedfield),
                'resultfunc' => function($value) {
                    return $value ? date('d M Y', $value) : get_string('na', 'local_edwiserreports');
                }
            ),
            array(
                'id' => 'courseenddate',
                'text' => get_string('courseenddate', 'local_edwiserreports'),
                'dbkey' => 'c.enddate',
                'selected' => in_array('courseenddate', $selectedfield),
                'resultfunc' => function($value) {
                    return $value ? date('d M Y', $value) : get_string('na', 'local_edwiserreports');
                }
            ),
            array(
                'id' => 'courseformat',
                'text' => get_string('courseformat', 'local_edwiserreports'),
                'dbkey' => 'cfo.format',
                'selected' => in_array('courseformat', $selectedfield),
                'resultfunc' => function($value) {
                    return get_string('pluginname', 'format_' . $value);
                }
            ),
            array(
                'id' => 'completionenable',
                'text' => get_string('completionenable', 'local_edwiserreports'),
                'dbkey' => 'ec.criteria',
                'selected' => in_array('completionenable', $selectedfield),
                'resultfunc' => function($value) {
                    return $value ? get_string('yes', 'moodle') : get_string('no', 'moodle');
                }
            ),
            array(
                'id' => 'guestaccess',
                'text' => get_string('guestaccess', 'local_edwiserreports'),
                'dbkey' => 'e.enrol',
                'selected' => in_array('guestaccess', $selectedfield),
                'resultfunc' => function($value) {
                    return $value == 'guest' ? get_string('yes', 'moodle') : get_string('no', 'moodle');
                }
            )
        );
        return $coursefields;
    }

    /**
     * Get custom reports users fields
     * @return Array Users Field for custom reports
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
