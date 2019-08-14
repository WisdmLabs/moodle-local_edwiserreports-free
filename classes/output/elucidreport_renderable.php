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
            $export->lpstatslink = new moodle_url($CFG->wwwroot."/report/elucidsitereport/lpstats.php");
        }

        $export->exportlinks = get_block_exportlinks($downloadurl, $data);

        return $export;
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
        $output->exportlink = get_exportlinks($downloadurl, "report", "activeusers", "all");
        $output->userfilters = get_userfilters();
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
        $output->exportlink = get_exportlinks($downloadurl, "report", "activeusers");
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
        foreach ($customcerts as $customcert) {
            $course = get_course($customcert->course);
            $customcert->coursename = $course->shortname;
        }
        $output->certificates = array_values($customcerts);
        $downloadurl = $CFG->wwwroot."/report/elucidsitereport/download.php";
        $output->exportlink = get_exportlinks($downloadurl, "report", "certificates", "1");
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
        $output->exportlink = get_exportlinks($downloadurl, "report", "f2fsession", "1");
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
        global $DB;

        $output = new stdClass();
        $output->sesskey = sesskey();
        $output->lps = \report_elucidsitereport\utility::get_lps();
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
        global $DB;

        $output = new stdClass();
        $output->sesskey = sesskey();
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
        global $DB;

        $output = new stdClass();
        $output->sesskey = sesskey();
        return $output;
    }
}
