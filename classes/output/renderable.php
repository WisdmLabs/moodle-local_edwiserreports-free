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
 * @package     local_sitereport
 * @category    admin
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_sitereport\output;

defined('MOODLE_INTERNAL') || die();

use moodle_url;
use renderable;
use renderer_base;
use stdClass;
use templatable;
use context_system;

require_once($CFG->dirroot."/local/sitereport/lib.php");
require_once($CFG->dirroot."/local/sitereport/classes/report_blocks.php");
require_once($CFG->dirroot."/local/sitereport/locallib.php");

class elucidreport_renderable implements renderable, templatable {
    /**
     * Function to export the renderer data in a format that is suitable for a
     * edit mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */
    public function export_for_template(renderer_base $output) {
        global $CFG, $DB, $PAGE;

        // Get system context.
        $context = context_system::instance();

        $output = null;
        $export = new stdClass();

        // Prepare reports blocks.
        $reportblocks = \local_sitereport\utility::get_reports_block();
        $reportblocks = new \local_sitereport\report_blocks($reportblocks);
        $export->blocks = $reportblocks->get_report_blocks();

        // Todo: Remove below code.
        $export->downloadurl = $CFG->wwwroot."/local/sitereport/download.php";

        $export->sesskey = sesskey();
        $export->timenow = date("Y-m-d", time());
        $export->courses = \local_sitereport\utility::get_courses();

        // Blocks.
        $blocks = array(
            'activeusers' => get_string('activeusersheader', 'local_sitereport'),
            'courseprogress' => get_string('courseprogress', 'local_sitereport'),
            'activecourses' => get_string('activecoursesheader', 'local_sitereport'),
            'certificatestats' => get_string('certificatestats', 'local_sitereport'),
            'realtimeusers' => get_string('realtimeusers', 'local_sitereport'),
            'f2fsessions' => get_string('f2fsessionsheader', 'local_sitereport'),
            'accessinfo' => get_string('accessinfo', 'local_sitereport'),
            'lpstats' => get_string('lpstatsheader', 'local_sitereport'),
            'todaysactivity' => get_string('todaysactivityheader', 'local_sitereport'),
            'inactiveusers' => get_string('inactiveusers', 'local_sitereport'),
        );
        // Get Reporting Manager hidden blocks.
        $addedblocks = isset($CFG->ed_reporting_manager_blocks) ? unserialize($CFG->ed_reporting_manager_blocks) : array();
        // If block is hidden then add true because we have added a not condition in mustache file.
        foreach ($blocks as $key => $value) {
            if (in_array($key, $addedblocks)) {
                $export->$key = true;
            } else {
                $export->$key = false;
            }
        }

        $downloadurl = $CFG->wwwroot."/local/sitereport/download.php";
        $data = new stdClass();
        if (!empty($export->courses)) {
            $export->hascourses = true;
            $data->firstcourseid = $export->courses[0]->id;
        }

        $export->hasf2fpluign = local_sitereport_has_plugin("mod", "facetoface");
        $export->activeuserslink = new moodle_url($CFG->wwwroot."/local/sitereport/activeusers.php");
        $export->courseprogresslink = new moodle_url($CFG->wwwroot."/local/sitereport/coursereport.php");

        if ($export->hasf2fpluign) {
            $PAGE->requires->js_call_amd('local_sitereport/block_f2fsessions', 'init');
            $export->f2fsessionlink = new moodle_url($CFG->wwwroot."/local/sitereport/f2fsessions.php");
        }
        $export->hascustomcertpluign = local_sitereport_has_plugin("mod", "customcert");

        if ($export->hascustomcertpluign) {
            $PAGE->requires->js_call_amd('local_sitereport/block_certificatestats', 'init');
            $export->certificateslink = new moodle_url($CFG->wwwroot."/local/sitereport/certificates.php");
        }

        $export->haslppluign = local_sitereport_has_plugin("local", "learning_program");

        if ($export->haslppluign) {
            $export->lps = \local_sitereport\utility::get_lps();
            if (!empty($export->lps)) {
                $export->haslps = true;
                $data->firstlpid = $export->lps[0]["id"];
            }
            $export->lpstatslink = new moodle_url($CFG->wwwroot."/local/sitereport/lpstats.php");
        }

        // Get all cohort filters.
        $cohorts = local_sitereport_get_cohort_filter();
        $export->hascohorts = false;
        if (isset($cohorts->values) && !empty($cohorts->values)) {
            $export->cohorts = $cohorts->values;
            $export->hascohorts = true;
        }

        $cohortjoinsql = '';
        if ($export->hascohorts) {
            $cohortjoinsql = 'JOIN {cohort_members} co ON co.userid = u.id';
        }

        // Get all users.
        $sql = "SELECT DISTINCT(u.id), CONCAT(u.firstname, ' ', u.lastname) as fullname
                FROM {user} u
                $cohortjoinsql
                WHERE u.deleted = :deleted
                AND u.confirmed = :confirmed
                AND u.id > 1
                ORDER BY fullname ASC";
        $params = array(
            'deleted' => false,
            'confirmed' => true
        );
        $params = array(
            'deleted' => false,
            'confirmed' => true
        );

        $export->users = array_values($DB->get_records_sql($sql, $params));

        $export->modules = \local_sitereport\utility::get_available_reports_modules();

        $export->exportlinks = local_sitereport_get_block_exportlinks($downloadurl, $data);
        $export->reportfields = self::get_report_fields();
        $export->downloadurl = $downloadurl;

        return $export;
    }

    /**
     * Get all reports field to prepare sql query
     * @return [Array] Array of all available field
     */
    public static function get_report_fields() {
        $userfields = array(
            array(
                'key' => 'username',
                'value' => get_string('username', 'local_sitereport'),
                'dbkey' => 'u.username',
                'disbaled' => true
            ),
            array('key' => 'email', 'value' => get_string('useremail', 'local_sitereport'), 'dbkey' => 'u.email'),
            array('key' => 'firstname', 'value' => get_string('firstname', 'local_sitereport'), 'dbkey' => 'u.firstname'),
            array('key' => 'lastname', 'value' => get_string('lastname', 'local_sitereport'), 'dbkey' => 'u.lastname')
        );
        $coursefields = array(
            array(
                'key' => 'coursename',
                'value' => get_string('course', 'local_sitereport'),
                'dbkey' => 'CONCAT(\'"\', c.fullname, \'"\')',
                'disbaled' => true
            ),
            array(
                'key' => 'coursecategory',
                'value' => get_string('coursecategory', 'local_sitereport'),
                'dbkey' => 'ctg.name'
            ),
            array(
                'key' => 'courseenroldate',
                'value' => get_string('courseenroldate', 'local_sitereport'),
                'dbkey' => 'FROM_UNIXTIME(ra.timemodified, "%D %M %Y")'
            ),
            array(
                'key' => 'courseprogress',
                'value' => get_string('courseprogress', 'local_sitereport'),
                'dbkey' => 'ec.progress'
            ),
            array(
                'key' => 'completionstatus',
                'value' => get_string('course_completion_status', 'local_sitereport'),
                'dbkey' => '(CASE ec.progress WHEN 100 THEN "Completed" ELSE "In Progress" END)'
            ),
            array(
                'key' => 'activitiescompleted',
                'value' => get_string('activitiescompleted', 'local_sitereport'),
                'dbkey' => 'LENGTH(ec.completedmodules) - LENGTH(REPLACE(ec.completedmodules, ",", "")) + 1'
            ),
            array(
                'key' => 'incompletedactivities',
                'value' => get_string('incompletedactivities', 'local_sitereport'),
                'dbkey' => 'ec.totalmodules - (LENGTH(ec.completedmodules) - LENGTH(REPLACE(ec.completedmodules, ",", "")) + 1)'
            ),
            array(
                'key' => 'totalactivities',
                'value' => get_string('totalactivities', 'local_sitereport'),
                'dbkey' => 'ec.totalmodules'
            ),
            array(
                'key' => 'completiontime',
                'value' => get_string('completiontime', 'local_sitereport'),
                'dbkey' => 'FROM_UNIXTIME(ec.completiontime, "%D %M %Y")'
            ),
            array(
                'key' => 'coursestartdate',
                'value' => get_string('coursestartdate', 'local_sitereport'),
                'dbkey' => 'FROM_UNIXTIME(c.startdate, "%D %M %Y")'
            ),
            array(
                'key' => 'courseenddate',
                'value' => get_string('courseenddate', 'local_sitereport'),
                'dbkey' => '(CASE c.enddate WHEN 0 THEN "Never" ELSE FROM_UNIXTIME(c.enddate, "%D %M %Y") END)'
            ),
        );
        $lpfields = array(
            array(
                'key' => 'lpname',
                'value' => get_string('lpname', 'local_sitereport'),
                'dbkey' => 'lp.name',
                'disbaled' => true
            ),
            array(
                'key' => 'lpenroldate',
                'value' => get_string('lpenroldate', 'local_sitereport'),
                'dbkey' => 'FROM_UNIXTIME(lpe.timeenroled, "%D %M %Y")'
            ),
            array(
                'key' => 'lpstartdate',
                'value' => get_string('lpstartdate', 'local_sitereport'),
                'dbkey' => 'FROM_UNIXTIME(lp.timestart, "%D %M %Y")'
            ),
            array(
                'key' => 'lpenddate',
                'value' => get_string('lpenddate', 'local_sitereport'),
                'dbkey' => '(CASE lp.timeend WHEN 0 THEN "Never" ELSE FROM_UNIXTIME(lp.timeend, "%D %M %Y") END)'
            ),
            array(
                'key' => 'lpduration',
                'value' => get_string('lpduration', 'local_sitereport'),
                'dbkey' => 'lp.durationtime'
            ),
            array(
                'key' => 'lpcompletion',
                'value' => get_string('lpcompletion', 'local_sitereport'),
                'dbkey' => 'FROM_UNIXTIME(lpe.completed, "%D %M %Y")'
            ),
        );

        $activityfields = array(
            array(
                'key' => 'activityname',
                'value' => get_string('activityname', 'local_sitereport'),
                'dbkey' => 'q.name',
                'disbaled' => true
            ),
            array('key' => 'grade', 'value' => get_string('grade', 'local_sitereport'), 'dbkey' => 'ROUND(qg.grade, 2)'),
            array(
                'key' => 'totalgrade',
                'value' => get_string('totalgrade', 'local_sitereport'),
                'dbkey' => 'ROUND(q.grade, 2)'
            ),
            array('key' => 'status', 'value' => get_string('status', 'local_sitereport'), 'dbkey' => 'qa.state'),
            array('key' => 'attempt', 'value' => get_string('attempt', 'local_sitereport'), 'dbkey' => 'qa.attempt'),
            array(
                'key' => 'attemptstart',
                'value' => get_string('attemptstart', 'local_sitereport'),
                'dbkey' => 'FROM_UNIXTIME(qa.timestart, "%D %M %Y %h:%i:%m")'
            ),
            array(
                'key' => 'attemptfinish',
                'value' => get_string('attemptfinish', 'local_sitereport'),
                'dbkey' => 'FROM_UNIXTIME(qa.timefinish, "%D %M %Y %h:%i:%m")'
            ),
        );

        // Check if leraning hours plugin is present.
        if (local_sitereport_has_plugin('report', 'learning_hours')) {
            $coursefields[] = array(
                'key' => 'learninghours',
                'value' => get_string('learninghours', 'local_sitereport'),
                'dbkey' => '(CASE WHEN ulh.totalhours THEN
                             CONCAT(
                                FLOOR(ulh.totalhours/60), "h ",
                                      RPAD(MOD(ROUND(ulh.totalhours), 60), 2, "00"), "m")
                             ELSE
                                "00h 00m"
                             END)'
            );
        }

        $fields['userfields']   = $userfields;
        $fields['coursefields'] = $coursefields;
        $fields['lpfields']     = $lpfields;
        $fields['activityfields'] = $activityfields;
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

        $downloadurl = $CFG->wwwroot."/local/sitereport/download.php";
        $output->exportlink = local_sitereport_get_exportlinks($downloadurl, "report", "activeusers", "weekly", 0);
        $output->userfilters = local_sitereport_get_userfilters(false, true, true);
        $output->backurl = new moodle_url($CFG->wwwroot."/local/sitereport/index.php");
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

        $downloadurl = $CFG->wwwroot."/local/sitereport/download.php";
        $output->exportlink = new stdClass();
        $output->exportlink->courseprogress = local_sitereport_get_exportlinks($downloadurl, "report", "courseprogress", false, 0);
        $output->exportlink->courseengage = local_sitereport_get_exportlinks($downloadurl, "report", "courseengage", false, 0);
        $output->userfilters = local_sitereport_get_userfilters(false, true, false);
        $output->backurl = new moodle_url($CFG->wwwroot."/local/sitereport/index.php");
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
        $customcerts = $DB->get_records("customcert", array());

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
            $downloadurl = $CFG->wwwroot."/local/sitereport/download.php";
            $output->exportlink = local_sitereport_get_exportlinks($downloadurl, "report", "certificates", $firstcertid, 0);
        }
        $output->userfilters = local_sitereport_get_userfilters(false, true, false);
        $output->backurl = new moodle_url($CFG->wwwroot."/local/sitereport/index.php");
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
        $downloadurl = $CFG->wwwroot."/local/sitereport/download.php";
        $output->exportlink = local_sitereport_get_exportlinks($downloadurl, "report", "f2fsession", false, 0);
        $output->userfilters = local_sitereport_get_userfilters(false, true, false);
        $output->backurl = new moodle_url($CFG->wwwroot."/local/sitereport/index.php");
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
        $output->lps = \local_sitereport\utility::get_lps();

        if (!empty($output->lps)) {
            $output->haslps = true;
            $downloadurl = $CFG->wwwroot."/local/sitereport/download.php";
            $output->exportlink = local_sitereport_get_exportlinks($downloadurl, "report", "lpstats", $output->lps[0]["id"], 0);
            $output->userfilters = local_sitereport_get_userfilters(false, true, false);
        }
        $output->backurl = new moodle_url($CFG->wwwroot."/local/sitereport/index.php");
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
        $downloadurl = $CFG->wwwroot."/local/sitereport/download.php";
        $output->exportlink = local_sitereport_get_exportlinks($downloadurl, "report", "completion", $courseid, 0);
        $output->userfilters = local_sitereport_get_userfilters(false, true, false);
        $output->backurl = new moodle_url($CFG->wwwroot."/local/sitereport/index.php");
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
        $downloadurl = $CFG->wwwroot."/local/sitereport/download.php";
        $output->visitsexportlink = local_sitereport_get_exportlinks(
                $downloadurl,
                "report",
                "courseanalytics",
                $courseid,
                0,
                "visits");
        $output->enrolmentexportlink = local_sitereport_get_exportlinks(
            $downloadurl,
            "report",
            "courseanalytics",
            $courseid,
            0,
            "enrolment");
        $output->completionexportlink = local_sitereport_get_exportlinks(
            $downloadurl,
            "report",
            "courseanalytics",
            $courseid,
            0,
            "completion");
        $output->userfilters = local_sitereport_get_userfilters(false, true, false);
        $output->backurl = new moodle_url($CFG->wwwroot."/local/sitereport/index.php");
        return $output;
    }
}
