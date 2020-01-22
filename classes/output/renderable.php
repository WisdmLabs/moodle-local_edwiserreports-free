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

namespace report_elucidsitereport\output;

defined('MOODLE_INTERNAL') || die();

use moodle_url;
use renderable;
use renderer_base;
use stdClass;
use templatable;

require_once $CFG->dirroot."/report/elucidsitereport/lib.php";
require_once $CFG->dirroot."/report/elucidsitereport/classes/blocks/active_users_block.php";
require_once $CFG->dirroot."/report/elucidsitereport/classes/blocks/course_progress_block.php";
require_once $CFG->dirroot."/report/elucidsitereport/classes/reporting_manager.php";
require_once $CFG->dirroot."/report/elucidsitereport/locallib.php";

class elucidreport_renderable implements renderable, templatable {
    /**
     * Function to export the renderer data in a format that is suitable for a
     * edit mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */
    public function export_for_template(renderer_base $output) {
        global $CFG, $PAGE;
        $output = null;
        $export = new stdClass();
        $export->sesskey = sesskey();
        $export->timenow = date("Y-m-d", time());
        $export->courses = \report_elucidsitereport\utility::get_courses();
        $export->isreportingmanager = false;
        // Create reporting manager instance
        $rpm = \report_elucidsitereport\reporting_manager::get_instance();
        if ($rpm->isrpm) {
            $export->isreportingmanager = true;
        }
        // Blocks
        $blocks = array(
            'activeusers' => get_string('activeusersheader', 'report_elucidsitereport'),
            'courseprogress' => get_string('courseprogress', 'report_elucidsitereport'),
            'activecourses' => get_string('activecoursesheader', 'report_elucidsitereport'),
            'certificatestats' => get_string('certificatestats', 'report_elucidsitereport'),
            'realtimeusers' => get_string('realtimeusers', 'report_elucidsitereport'),
            'f2fsessions' => get_string('f2fsessionsheader', 'report_elucidsitereport'),
            'accessinfo' => get_string('accessinfo', 'report_elucidsitereport'),
            'lpstats' => get_string('lpstatsheader', 'report_elucidsitereport'),
            'todaysactivity' => get_string('todaysactivityheader', 'report_elucidsitereport'),
            'inactiveusers' => get_string('inactiveusers', 'report_elucidsitereport'),
        );
        // Get Reporting Manager hidden blocks
        $added_blocks = isset($CFG->ed_reporting_manager_blocks) ? unserialize($CFG->ed_reporting_manager_blocks) : array();
        // If block is hidden then add true because we have added a not condition in mustache file
        foreach ($blocks as $key => $value) {
            if (in_array($key, $added_blocks)) {
                $export->$key = true;
            } else {
                $export->$key = false;
            }
        }

        $downloadurl = $CFG->wwwroot."/report/elucidsitereport/download.php";
        $data = new stdClass();
        if (!empty($export->courses)) {
            $export->hascourses = true;
            $data->firstcourseid = $export->courses[0]->id;
        }

        $export->hasf2fpluign = has_plugin("mod", "facetoface");
        $export->activeuserslink = new moodle_url($CFG->wwwroot."/report/elucidsitereport/activeusers.php");
        $export->courseprogresslink = new moodle_url($CFG->wwwroot."/report/elucidsitereport/coursereport.php");

        if ($export->hasf2fpluign) {
            $PAGE->requires->js_call_amd('report_elucidsitereport/block_f2fsessions', 'init');
            $export->f2fsessionlink = new moodle_url($CFG->wwwroot."/report/elucidsitereport/f2fsessions.php");
        }
        $export->hascustomcertpluign = has_plugin("mod", "customcert");

        if ($export->hascustomcertpluign) {
            $PAGE->requires->js_call_amd('report_elucidsitereport/block_certificatestats', 'init');
            $export->certificateslink = new moodle_url($CFG->wwwroot."/report/elucidsitereport/certificates.php");
        }

        $export->haslppluign = has_plugin("local", "learning_program");

        if ($export->haslppluign) {
            $export->lps = \report_elucidsitereport\utility::get_lps();
            if (!empty($export->lps)) {
                $export->haslps = true;
                $data->firstlpid = $export->lps[0]["id"];
            }
            $export->lpstatslink = new moodle_url($CFG->wwwroot."/report/elucidsitereport/lpstats.php");
        }
        // Custom Query Report
        $rpmgrs = $rpm->get_all_reporting_managers();
        $export->rpmgrs = $rpmgrs;
        $export->exportlinks = get_block_exportlinks($downloadurl, $data);
        $export->reportfields = elucidreport_renderable::get_report_fields();
        $export->downloadurl = $downloadurl;

        return $export;
    }
    public static function get_report_fields() {
        $userfields = array(
            array('key' => 'username', 'value'=>get_string('username', 'report_elucidsitereport'), 'dbkey' => 'u.username', 'disbaled' => true),
            array('key' => 'email', 'value'=>get_string('useremail', 'report_elucidsitereport'), 'dbkey' => 'u.email'),
            array('key' => 'firstname', 'value'=>get_string('firstname', 'report_elucidsitereport'), 'dbkey' => 'u.firstname'),
            array('key' => 'lastname', 'value'=>get_string('lastname', 'report_elucidsitereport'), 'dbkey' => 'u.lastname')
        );
        $coursefields = array(
            array('key' => 'coursename', 'value'=>get_string('course', 'report_elucidsitereport'), 'dbkey' => 'c.fullname', 'disbaled' => true),
            array('key' => 'coursecategory', 'value'=>get_string('coursecategory', 'report_elucidsitereport'), 'dbkey' => 'ctg.name'),
            array('key' => 'courseenroldate', 'value'=>get_string('courseenroldate', 'report_elucidsitereport'), 'dbkey' => 'FROM_UNIXTIME(ra.timemodified, "%D %M %Y")'),
            array('key' => 'courseprogress', 'value'=>get_string('courseprogress', 'report_elucidsitereport'), 'dbkey' => 'ec.progress'),
            array('key' => 'completionstatus', 'value'=>get_string('course_completion_status', 'report_elucidsitereport'), 'dbkey' => '(CASE ec.progress WHEN 100 THEN "Completed" ELSE "In Progress" END)'),
            array('key' => 'activitiescompleted', 'value'=>get_string('activitiescompleted', 'report_elucidsitereport'), 'dbkey' => 'LENGTH(ec.completedmodules) - LENGTH(REPLACE(ec.completedmodules, ",", "")) + 1'),
            array('key' => 'incompletedactivities', 'value'=>get_string('incompletedactivities', 'report_elucidsitereport'), 'dbkey' => 'ec.totalmodules - (LENGTH(ec.completedmodules) - LENGTH(REPLACE(ec.completedmodules, ",", "")) + 1)'),
            array('key' => 'totalactivities', 'value'=>get_string('totalactivities', 'report_elucidsitereport'), 'dbkey' => 'ec.totalmodules'),
            array('key' => 'completiontime', 'value'=>get_string('completiontime', 'report_elucidsitereport'), 'dbkey' => 'FROM_UNIXTIME(ec.completiontime, "%D %M %Y")'),
            array('key' => 'coursestartdate', 'value'=>get_string('coursestartdate', 'report_elucidsitereport'), 'dbkey' => 'FROM_UNIXTIME(c.startdate, "%D %M %Y")'),
            array('key' => 'courseenddate', 'value'=>get_string('courseenddate', 'report_elucidsitereport'), 'dbkey' => '(CASE c.enddate WHEN 0 THEN "Never" ELSE FROM_UNIXTIME(c.enddate, "%D %M %Y") END)'),
        );
        $lpfields = array(
            array('key' => 'lpname', 'value'=>get_string('lpname', 'report_elucidsitereport'), 'dbkey' => 'lp.name', 'disbaled' => true),
            array('key' => 'lpenroldate', 'value'=>get_string('lpenroldate', 'report_elucidsitereport'), 'dbkey' => 'FROM_UNIXTIME(lpe.timeenroled, "%D %M %Y")'),
            array('key' => 'lpstartdate', 'value'=>get_string('lpstartdate', 'report_elucidsitereport'), 'dbkey' => 'FROM_UNIXTIME(lp.timestart, "%D %M %Y")'),
            array('key' => 'lpenddate', 'value'=>get_string('lpenddate', 'report_elucidsitereport'), 'dbkey' => '(CASE lp.timeend WHEN 0 THEN "Never" ELSE FROM_UNIXTIME(lp.timeend, "%D %M %Y") END)'),
            array('key' => 'lpduration', 'value'=>get_string('lpduration', 'report_elucidsitereport'), 'dbkey' => 'lp.durationtime'),
            array('key' => 'lpcompletion', 'value'=>get_string('lpcompletion', 'report_elucidsitereport'), 'dbkey' => 'FROM_UNIXTIME(lpe.completed, "%D %M %Y")'),
        );
        $rpmfields = array();
        // Create reporting manager instance
        $rpm = \report_elucidsitereport\reporting_manager::get_instance();
        if (!$rpm->isrpm) {
            $rpmfields = array(
                array('key' => 'rpmname', 'value'=>get_string('rpmname', 'report_elucidsitereport'), 'dbkey' => 'CONCAT(rpm.firstname, " ", rpm.lastname)'),
            );
        }
        $fields['userfields']   = $userfields;
        $fields['coursefields'] = $coursefields;
        $fields['lpfields']     = $lpfields;
        $fields['rpmfields']    = $rpmfields;
        return $fields;
    }
}


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
        $output = new stdClass();
        $output->sesskey = sesskey();

        $downloadurl = $CFG->wwwroot."/report/elucidsitereport/download.php";
        $output->exportlink = get_exportlinks($downloadurl, "report", "activeusers", "weekly", 0);
        $output->userfilters = get_userfilters(false, true, true);
        $output->backurl = new moodle_url($CFG->wwwroot."/report/elucidsitereport/index.php");
        return $output;
    }
}

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

        $downloadurl = $CFG->wwwroot."/report/elucidsitereport/download.php";
        $output->exportlink = new stdClass();
        $output->exportlink->courseprogress = get_exportlinks($downloadurl, "report", "courseprogress", false, 0);
        $output->exportlink->courseengage = get_exportlinks($downloadurl, "report", "courseengage", false, 0);
        $output->userfilters = get_userfilters(false, true, false);
        $output->backurl = new moodle_url($CFG->wwwroot."/report/elucidsitereport/index.php");
        return $output;
    }
}

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
        $params = array();
		// $customcerts = $DB->get_records("customcert", array());
        // Create reporting manager instance
        $rpm = \report_elucidsitereport\reporting_manager::get_instance();
        $sql = "SELECT DISTINCT(c.id), c.course FROM {customcert} c
                JOIN {customcert_issues} ci ON ci.customcertid = c.id WHERE ci.userid ".$rpm->insql;
        $params = array_merge($params, $rpm->inparams);
        $customcerts = $DB->get_records_sql($sql, $params);
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
            $downloadurl = $CFG->wwwroot."/report/elucidsitereport/download.php";
            $output->exportlink = get_exportlinks($downloadurl, "report", "certificates", $firstcertid, 0);
		}
        $output->userfilters = get_userfilters(false, true, false);
        $output->backurl = new moodle_url($CFG->wwwroot."/report/elucidsitereport/index.php");
        return $output;
    }
}

class f2fsessions_renderable implements renderable, templatable {
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
        $downloadurl = $CFG->wwwroot."/report/elucidsitereport/download.php";
        $output->exportlink = get_exportlinks($downloadurl, "report", "f2fsession", false, 0);
        $output->userfilters = get_userfilters(false, true, false);
        $output->backurl = new moodle_url($CFG->wwwroot."/report/elucidsitereport/index.php");
        return $output;
    }
}

class lpstats_renderable implements renderable, templatable {
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
        $output->lps = \report_elucidsitereport\utility::get_lps();

        if (!empty($output->lps)) {
            $output->haslps = true;
            $downloadurl = $CFG->wwwroot."/report/elucidsitereport/download.php";
            $output->exportlink = get_exportlinks($downloadurl, "report", "lpstats", $output->lps[0]["id"], 0);
            $output->userfilters = get_userfilters(false, true, false);
        }
        $output->backurl = new moodle_url($CFG->wwwroot."/report/elucidsitereport/index.php");
        $output->lpexportdetailed = true;
        return $output;
    }
}

class completion_renderable implements renderable, templatable {
    /**
     * Function to export the renderer data in a format that is suitable for a
     * edit mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */
    public function export_for_template(renderer_base $output) {
        global $CFG, $DB;

        $courseid = required_param("courseid", PARAM_INT);
        $output = new stdClass();
        $output->sesskey = sesskey();
        $downloadurl = $CFG->wwwroot."/report/elucidsitereport/download.php";
        $output->exportlink = get_exportlinks($downloadurl, "report", "completion", $courseid, 0);
        $output->userfilters = get_userfilters(false, true, false);
        $output->backurl = new moodle_url($CFG->wwwroot."/report/elucidsitereport/index.php");
        return $output;
    }
}

class courseanalytics_renderable implements renderable, templatable {
    /**
     * Function to export the renderer data in a format that is suitable for a
     * edit mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */
    public function export_for_template(renderer_base $output) {
        global $CFG, $DB;

        $courseid = required_param("courseid", PARAM_INT);
        $output = new stdClass();
        $output->sesskey = sesskey();
        $downloadurl = $CFG->wwwroot."/report/elucidsitereport/download.php";
        $output->visitsexportlink = get_exportlinks($downloadurl, "report", "courseanalytics", $courseid, 0, "visits");
        $output->enrolmentexportlink = get_exportlinks($downloadurl, "report", "courseanalytics", $courseid, 0, "enrolment");
        $output->completionexportlink = get_exportlinks($downloadurl, "report", "courseanalytics", $courseid, 0, "completion");
        $output->userfilters = get_userfilters(false, true, false);
        $output->backurl = new moodle_url($CFG->wwwroot."/report/elucidsitereport/index.php");
        return $output;
    }
}


class customqueryreport_renderable {
    /**
     * Function to export the renderer data in a format that is suitable for a
     * edit mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */
    public function export_for_template() {
        global $CFG, $DB;

        $output = new stdClass();
        $output->sesskey = sesskey();
        $output->temp = "This is temp";
        return $output;
    }
}