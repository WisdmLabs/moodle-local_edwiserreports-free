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

namespace local_edwiserreports\output;

defined('MOODLE_INTERNAL') || die();

use moodle_url;
use renderable;
use renderer_base;
use stdClass;
use templatable;
use context_system;

require_once($CFG->dirroot."/local/edwiserreports/lib.php");
require_once($CFG->dirroot."/local/edwiserreports/classes/report_blocks.php");
require_once($CFG->dirroot."/local/edwiserreports/locallib.php");

/**
 * Elucid report renderable.
 */
class elucidreport_renderable implements renderable, templatable {
    /**
     * Function to export the renderer data in a format that is suitable for a
     * edit mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */
    public function export_for_template(renderer_base $output) {
        global $CFG, $PAGE, $USER;

        $output = null;
        $export = new stdClass();
        $context = context_system::instance();

        // Prepare reports blocks.
        $reportblocks = \local_edwiserreports\utility::get_reports_block();
        $reportblocks = new \local_edwiserreports\report_blocks($reportblocks);
        $export->blocks = $reportblocks->get_report_blocks();

        // Todo: Remove below code.
        $export->downloadurl = $CFG->wwwroot."/local/edwiserreports/download.php";

        $export->sesskey = sesskey();
        $export->timenow = date("Y-m-d", time());
        $export->courses = \local_edwiserreports\utility::get_courses();

        $export->hascustomcertpluign = local_edwiserreports_has_plugin("mod", "customcert");

        if ($export->hascustomcertpluign) {
            $PAGE->requires->js_call_amd('local_edwiserreports/block_certificatestats', 'init');
            $export->certificateslink = new moodle_url($CFG->wwwroot."/local/edwiserreports/certificates.php");
        }

        $export->editing = isset($USER->editing) ? $USER->editing : 0;
        $export->canmanagecustomreports = has_capability('report/edwiserreports_customreports:manage', $context);
        $export->customreportseditlink = new moodle_url($CFG->wwwroot."/local/edwiserreports/customreportedit.php");

        return $export;
    }

    /**
     * Get all reports field to prepare sql query
     * @return array Array of all available field
     */
    public static function get_report_fields() {
        $userfields = array(
            array(
                'key' => 'username',
                'value' => get_string('username', 'local_edwiserreports'),
                'dbkey' => 'u.username',
                'disbaled' => true
            ),
            array('key' => 'email', 'value' => get_string('useremail', 'local_edwiserreports'), 'dbkey' => 'u.email'),
            array('key' => 'firstname', 'value' => get_string('firstname', 'local_edwiserreports'), 'dbkey' => 'u.firstname'),
            array('key' => 'lastname', 'value' => get_string('lastname', 'local_edwiserreports'), 'dbkey' => 'u.lastname')
        );
        $coursefields = array(
            array(
                'key' => 'coursename',
                'value' => get_string('course', 'local_edwiserreports'),
                'dbkey' => 'CONCAT(\'"\', c.fullname, \'"\')',
                'disbaled' => true
            ),
            array(
                'key' => 'coursecategory',
                'value' => get_string('coursecategory', 'local_edwiserreports'),
                'dbkey' => 'ctg.name'
            ),
            array(
                'key' => 'courseenroldate',
                'value' => get_string('courseenroldate', 'local_edwiserreports'),
                'dbkey' => 'FROM_UNIXTIME(ra.timemodified, "%D %M %Y")'
            ),
            array(
                'key' => 'courseprogress',
                'value' => get_string('courseprogress', 'local_edwiserreports'),
                'dbkey' => 'ec.progress'
            ),
            array(
                'key' => 'completionstatus',
                'value' => get_string('course_completion_status', 'local_edwiserreports'),
                'dbkey' => '(CASE ec.progress WHEN 100 THEN "Completed" ELSE "In Progress" END)'
            ),
            array(
                'key' => 'activitiescompleted',
                'value' => get_string('activitiescompleted', 'local_edwiserreports'),
                'dbkey' => 'LENGTH(ec.completedmodules) - LENGTH(REPLACE(ec.completedmodules, ",", "")) + 1'
            ),
            array(
                'key' => 'incompletedactivities',
                'value' => get_string('incompletedactivities', 'local_edwiserreports'),
                'dbkey' => 'ec.totalmodules - (LENGTH(ec.completedmodules) - LENGTH(REPLACE(ec.completedmodules, ",", "")) + 1)'
            ),
            array(
                'key' => 'totalactivities',
                'value' => get_string('totalactivities', 'local_edwiserreports'),
                'dbkey' => 'ec.totalmodules'
            ),
            array(
                'key' => 'completiontime',
                'value' => get_string('completiontime', 'local_edwiserreports'),
                'dbkey' => 'FROM_UNIXTIME(ec.completiontime, "%D %M %Y")'
            ),
            array(
                'key' => 'coursestartdate',
                'value' => get_string('coursestartdate', 'local_edwiserreports'),
                'dbkey' => 'FROM_UNIXTIME(c.startdate, "%D %M %Y")'
            ),
            array(
                'key' => 'courseenddate',
                'value' => get_string('courseenddate', 'local_edwiserreports'),
                'dbkey' => '(CASE c.enddate WHEN 0 THEN "Never" ELSE FROM_UNIXTIME(c.enddate, "%D %M %Y") END)'
            ),
        );
        $lpfields = array(
            array(
                'key' => 'lpname',
                'value' => get_string('lpname', 'local_edwiserreports'),
                'dbkey' => 'lp.name',
                'disbaled' => true
            ),
            array(
                'key' => 'lpenroldate',
                'value' => get_string('lpenroldate', 'local_edwiserreports'),
                'dbkey' => 'FROM_UNIXTIME(lpe.timeenroled, "%D %M %Y")'
            ),
            array(
                'key' => 'lpstartdate',
                'value' => get_string('lpstartdate', 'local_edwiserreports'),
                'dbkey' => 'FROM_UNIXTIME(lp.timestart, "%D %M %Y")'
            ),
            array(
                'key' => 'lpenddate',
                'value' => get_string('lpenddate', 'local_edwiserreports'),
                'dbkey' => '(CASE lp.timeend WHEN 0 THEN "Never" ELSE FROM_UNIXTIME(lp.timeend, "%D %M %Y") END)'
            ),
            array(
                'key' => 'lpduration',
                'value' => get_string('lpduration', 'local_edwiserreports'),
                'dbkey' => 'lp.durationtime'
            ),
            array(
                'key' => 'lpcompletion',
                'value' => get_string('lpcompletion', 'local_edwiserreports'),
                'dbkey' => 'FROM_UNIXTIME(lpe.completed, "%D %M %Y")'
            ),
        );

        $activityfields = array(
            array(
                'key' => 'activityname',
                'value' => get_string('activityname', 'local_edwiserreports'),
                'dbkey' => 'q.name',
                'disbaled' => true
            ),
            array('key' => 'grade', 'value' => get_string('grade', 'local_edwiserreports'), 'dbkey' => 'ROUND(qg.grade, 2)'),
            array(
                'key' => 'totalgrade',
                'value' => get_string('totalgrade', 'local_edwiserreports'),
                'dbkey' => 'ROUND(q.grade, 2)'
            ),
            array('key' => 'status', 'value' => get_string('status', 'local_edwiserreports'), 'dbkey' => 'qa.state'),
            array('key' => 'attempt', 'value' => get_string('attempt', 'local_edwiserreports'), 'dbkey' => 'qa.attempt'),
            array(
                'key' => 'attemptstart',
                'value' => get_string('attemptstart', 'local_edwiserreports'),
                'dbkey' => 'FROM_UNIXTIME(qa.timestart, "%D %M %Y %h:%i:%m")'
            ),
            array(
                'key' => 'attemptfinish',
                'value' => get_string('attemptfinish', 'local_edwiserreports'),
                'dbkey' => 'FROM_UNIXTIME(qa.timefinish, "%D %M %Y %h:%i:%m")'
            ),
        );

        // Check if leraning hours plugin is present.
        if (local_edwiserreports_has_plugin('report', 'learning_hours')) {
            $dbkey = '(CASE WHEN ulh.totalhours THEN CONCAT(FLOOR(ulh.totalhours/60), "h ", ';
            $dbkey .= 'RPAD(MOD(ROUND(ulh.totalhours), 60), 2, "00"), "m")ELSE"00h 00m"END)';
            $coursefields[] = array(
                'key' => 'learninghours',
                'value' => get_string('learninghours', 'local_edwiserreports'),
                'dbkey' => $dbkey
            );
        }

        $fields['userfields']   = $userfields;
        $fields['coursefields'] = $coursefields;
        $fields['lpfields']     = $lpfields;
        $fields['activityfields'] = $activityfields;
        return $fields;
    }
}

/**
 * Active users page renderables.
 */
class activeusers_renderable implements renderable, templatable {
    /**
     * Function to export the renderer data in a format that is suitable for a
     * edit mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */
    public function export_for_template(renderer_base $output) {
        global $CFG;

        require_once($CFG->dirroot . '/local/edwiserreports/classes/blocks/activeusersblock.php');
        $output = new stdClass();
        $output->sesskey = sesskey();

        $output->backurl = $CFG->wwwroot."/local/edwiserreports/index.php";

        if ($cohortfilter = local_edwiserreports_get_cohort_filter()) {
            $output->cohortfilters = $cohortfilter;
        }

        $output->export = array(
            "id" => "activeusersblock",
            "region" => "report",
            "downloadurl" => $CFG->wwwroot . "/local/edwiserreports/download.php"
        );
        return $output;
    }
}

/**
 * Course report renderables.
 */
class coursereport_renderable implements renderable, templatable {
    /**
     * Function to export the renderer data in a format that is suitable for a
     * edit mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */
    public function export_for_template(renderer_base $output) {
        global $CFG;
        $output = new stdClass();
        $output->sesskey = sesskey();

        $output->courseprogressexport = array(
            "id" => "courseprogressblock",
            "region" => "report",
            "downloadurl" => $CFG->wwwroot . "/local/edwiserreports/download.php"
        );

        $output->courseengageexport = array(
            "id" => "courseengageblock",
            "region" => "report",
            "downloadurl" => $CFG->wwwroot . "/local/edwiserreports/download.php"
        );

        $output->backurl = $CFG->wwwroot."/local/edwiserreports/index.php";

        if ($cohortfilter = local_edwiserreports_get_cohort_filter()) {
            $output->cohortfilters = $cohortfilter;
        }

        return $output;
    }
}

/**
 * Certificate renderable.
 */
class certificates_renderable implements renderable, templatable {
    /**
     * Function to export the renderer data in a format that is suitable for a
     * edit mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */
    public function export_for_template(renderer_base $output) {
        global $CFG, $DB;

        $output = new stdClass();
        $output->sesskey = sesskey();
        $customcerts = $DB->get_records("customcert", array());
        $output->backurl = $CFG->wwwroot."/local/edwiserreports/index.php";

        if (!empty($customcerts)) {
            $output->hascertificates = true;
            $firstcertid = 0;
            foreach ($customcerts as $customcert) {
                if (!$firstcertid) {
                    $firstcertid = $customcert->id;
                }
                $course = get_course($customcert->course);
                $customcert->coursename = $course->shortname;
            }
            $output->certificates = array_values($customcerts);
            $output->certexport = array(
                "id" => "certificatesblock",
                "region" => "report",
                "downloadurl" => $CFG->wwwroot . "/local/edwiserreports/download.php",
                "filter" => $firstcertid
            );
        }
        return $output;
    }
}

/**
 * Completion renderables.
 */
class completion_renderable implements renderable, templatable {
    /**
     * Function to export the renderer data in a format that is suitable for a
     * edit mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */
    public function export_for_template(renderer_base $output) {
        global $CFG;

        $courseid = required_param("courseid", PARAM_INT);
        $output = new stdClass();
        $output->sesskey = sesskey();

        if ($cohortfilter = local_edwiserreports_get_cohort_filter()) {
            $output->cohortfilters = $cohortfilter;
        }

        $output->completionexport = array(
            "id" => "completionblock",
            "region" => "report",
            "downloadurl" => $CFG->wwwroot . "/local/edwiserreports/download.php",
            "filter" => $courseid
        );

        return $output;
    }
}
