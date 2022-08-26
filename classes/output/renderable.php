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
use local_edwiserreports\certificatesblock;
use local_edwiserreports\controller\authentication;
use local_edwiserreports\courseprogressblock;

$files = scandir($CFG->dirroot . "/local/edwiserreports/classes/blocks/");
unset($files[0]);
unset($files[1]);
foreach ($files as $file) {
    require_once($CFG->dirroot . "/local/edwiserreports/classes/blocks/" . $file);
}

require_once($CFG->dirroot."/local/edwiserreports/lib.php");
require_once($CFG->dirroot."/local/edwiserreports/classes/report_blocks.php");
require_once($CFG->dirroot."/local/edwiserreports/locallib.php");

/**
 * Elucid report renderable.
 */
class edwiserreports_renderable implements renderable, templatable {
    /**
     * Function to export the renderer data in a format that is suitable for a
     * edit mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */
    public function export_for_template(renderer_base $output) {
        global $CFG, $PAGE, $USER;

        user_preference_allow_ajax_update('local_edwiserreports_insights_order', PARAM_TEXT);

        $output = null;
        $export = new stdClass();
        $authentication = new authentication();

        $export->secret = $authentication->get_secret_key($USER->id);
        $context = context_system::instance();

        $export->downloadurl = new moodle_url("/local/edwiserreports/download.php");

        // Prepare reports blocks.
        $reportblocks = \local_edwiserreports\utility::get_reports_block();
        $reportblocks = new \local_edwiserreports\report_blocks($reportblocks);
        $export->blocks = $reportblocks->get_report_blocks();

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

        // Top insights.
        $insights = new \local_edwiserreports\insights\insight();
        $export->topinsights = $insights->get_insights();
        if ($CFG->branch > 311) {
            $export->setactive = true;
            $export->activeurl = new moodle_url("/local/edwiserreports/index.php");
        }

        $export->showwhatsnew = get_config('local_edwiserreports', 'showwhatsnew') == true;
        return $export;
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

        $output->pageheader = get_string("activeusersheader", "local_edwiserreports");

        if ($cohortfilter = local_edwiserreports_get_cohort_filter()) {
            $output->cohortfilters = $cohortfilter;
        }

        // Add export icons to export array.
        $output->export = \local_edwiserreports\utility::get_export_icons([
            "id" => "activeusersblock",
            "region" => "report",
            "downloadurl" => new moodle_url("/local/edwiserreports/download.php")
        ]);

        $output->searchicon = \local_edwiserreports\utility::image_icon('actions/search');
        $output->placeholder = get_string('searchdate', 'local_edwiserreports');
        $output->length = [10, 25, 50, 100];
        if ($CFG->branch > 311) {
            $output->setactive = true;
            $output->activeurl = new moodle_url("/local/edwiserreports/index.php");
        }
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

        // Add export icons to export array.
        $output->export = \local_edwiserreports\utility::get_export_icons(array(
            "id" => "courseprogressblock",
            "region" => "report",
            "downloadurl" => $CFG->wwwroot . "/local/edwiserreports/download.php"
        ));

        $output->pageheader = get_string("coursereportsheader", "local_edwiserreports");

        $output->backurl = $CFG->wwwroot."/local/edwiserreports/index.php";

        if ($cohortfilter = local_edwiserreports_get_cohort_filter()) {
            $output->cohortfilters = $cohortfilter;
        }

        $output->groups = (new courseprogressblock())->get_default_group_filter(true);
        $output->searchicon = \local_edwiserreports\utility::image_icon('actions/search');
        $output->placeholder = get_string('searchcourse', 'local_edwiserreports');
        $output->length = [10, 25, 50, 100];
        if ($CFG->branch > 311) {
            $output->setactive = true;
            $output->activeurl = new moodle_url("/local/edwiserreports/index.php");
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
        global $CFG, $DB, $USER;

        $output = new stdClass();

        $authentication = new authentication();
        $output->secret = $authentication->get_secret_key($USER->id);

        $customcerts = $DB->get_records("customcert", array());

        $output->pageheader = get_string("certificatestats", "local_edwiserreports");

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

            // Add export icons to export array.
            $output->export = \local_edwiserreports\utility::get_export_icons(array(
                "id" => "certificatesblock",
                "region" => "report",
                "downloadurl" => $CFG->wwwroot . "/local/edwiserreports/download.php",
                "filter" => $firstcertid
            ));
            $output->cohort = (new certificatesblock())->get_cohorts(true);
            $output->searchicon = \local_edwiserreports\utility::image_icon('actions/search');
            $output->placeholder = get_string('searchuser', 'local_edwiserreports');
            $output->length = [10, 25, 50, 100];
        }
        if ($CFG->branch > 311) {
            $output->setactive = true;
            $output->activeurl = new moodle_url("/local/edwiserreports/index.php");
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
        $course = get_course($courseid);
        $output = new stdClass();
        $output->sesskey = sesskey();

        if ($cohortfilter = local_edwiserreports_get_cohort_filter()) {
            $output->cohortfilters = $cohortfilter;
        }

        // Add export icons to export array.
        $output->export = \local_edwiserreports\utility::get_export_icons(array(
            "id" => "completionblock",
            "region" => "report",
            "downloadurl" => $CFG->wwwroot . "/local/edwiserreports/download.php",
            "filter" => $courseid
        ));

        $output->pageheader = get_string("completionheader", "local_edwiserreports", array('coursename' => $course->fullname));

        if ($url = optional_param('backurl', '', PARAM_URL)) {
            $output->backurl = $url;
        } else {
            $output->backurl = $CFG->wwwroot."/course/view.php?id=".$courseid;
        }

        $output->searchicon = \local_edwiserreports\utility::image_icon('actions/search');
        $output->placeholder = get_string('searchuser', 'local_edwiserreports');
        $output->length = [10, 25, 50, 100];
        if ($CFG->branch > 311) {
            $output->setactive = true;
            $output->activeurl = new moodle_url("/local/edwiserreports/completion.php", array('courseid' => $course->id));
        }
        return $output;
    }
}
