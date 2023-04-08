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

use html_writer;
use moodle_url;
use renderable;
use renderer_base;
use stdClass;
use templatable;
use context_system;
use local_edwiserreports\certificatesblock;
use local_edwiserreports\controller\authentication;
use local_edwiserreports\courseprogressblock;
use local_edwiserreports\controller\navigation;

$files = scandir($CFG->dirroot . "/local/edwiserreports/classes/blocks/");
unset($files[0]);
unset($files[1]);
foreach ($files as $file) {
    require_once($CFG->dirroot . "/local/edwiserreports/classes/blocks/" . $file);
}

require_once($CFG->dirroot . "/local/edwiserreports/lib.php");
require_once($CFG->dirroot . "/local/edwiserreports/classes/reports/base.php");
require_once($CFG->dirroot . "/local/edwiserreports/classes/report_blocks.php");
require_once($CFG->dirroot . "/local/edwiserreports/locallib.php");

/**
 * Elucid report renderable.
 */
class edwiserreports_renderable implements renderable, templatable {

    /**
     * Show black friday container on the top of page.
     *
     * @return sdtClass
     */
    public function get_black_friday_sale_ribbon() {
        $now = time();
        $bfs = new stdClass;
        $bfs->logolarge = new moodle_url('/local/edwiserreports/pix/logolarge.png');
        switch (true) {
            case $now >= 1667520000 && $now <= 1669161600;
                if (get_user_preferences('local_edwiserreports_bfs_pre_hide', false)) {
                    $bfs->show = false;
                    break;
                }
                $bfs->show = true;
                $bfs->title = "Play a Game with Edwiser to get ahead of the Black Friday Sale!";
                $bfs->description = "Spin the Wheel to Win Free Access or Discounts on our Premium Moodle theme & plugins";
                $bfs->action = "Spin and Win";
                $bfs->cta = "https://edwiser.org/edwiser-black-friday-giveaway/?utm_source=giveaway&utm_medium=spinthewheel&utm_campaign=bfcm22";
                $bfs->graphics = new moodle_url('/local/edwiserreports/pix/bfs/spin_graphics.png');
                $bfs->close = 'local_edwiserreports_bfs_pre_hide';
                user_preference_allow_ajax_update('local_edwiserreports_bfs_pre_hide', PARAM_BOOL);
                break;
            case $now >= 1669334400 && $now <= 1669766400;
                if (get_user_preferences('local_edwiserreports_bfs_after_hide', false)) {
                    $bfs->show = false;
                    break;
                }
                $bfs->show = true;
                $bfs->title = "You're missing out on in-depth course & learner performance analysis!";
                $bfs->description = "Bridge the gap with a complete reporting solution this Black Friday Season!<br>
                                        Get special discounts on Edwiser Reports Pro!";
                $bfs->action = "Upgrade Now";
                $bfs->cta = "https://edwiser.org/reports/?utm_source=freeplugin&utm_medium=banner&utm_campaign=bfcm22";
                $bfs->graphics = new moodle_url('/local/edwiserreports/pix/bfs/jackpot_graphics.png');
                $bfs->close = 'local_edwiserreports_bfs_after_hide';
                user_preference_allow_ajax_update('local_edwiserreports_bfs_after_hide', PARAM_BOOL);
                break;
            default:
                $bfs->show = false;
                break;
        }
        return $bfs;
    }
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
        if (is_siteadmin()) {
            $bfs = $this->get_black_friday_sale_ribbon();
            if ($bfs->show) {
                $export->bfs = $bfs;
            }
        }

        // Prepare reports blocks.
        $reportblocks = \local_edwiserreports\utility::get_reports_block();
        $reportblocks = new \local_edwiserreports\report_blocks($reportblocks);
        $export->blocks = $reportblocks->get_report_blocks();

        $export->navigation = navigation::instance()->get_navigation('overview');

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

        $output->navigation = navigation::instance()->get_navigation('other');

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

        $output->navigation = navigation::instance()->get_navigation('course');

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

        $output->breadcrumb = array(
            'items' => array(
                array(
                    'item' => get_string('coursereports', 'local_edwiserreports')
                )
            )
        );

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

        $output->navigation = navigation::instance()->get_navigation('other');

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
        global $CFG, $USER;

        $output = new stdClass();

        $courseid = optional_param("courseid", 0, PARAM_INT);

        $courses = (new \local_edwiserreports\block_base)->get_courses_of_user($USER->id);
        unset($courses[SITEID]);

        if ($courseid == 0) {
            $courseid = reset($courses)->id;
        }

        // Invalid course.
        if (!isset($courses[$courseid])) {
            throw new \moodle_exception('invalidcourse', 'core_error');
        }

        $courses[$courseid]->selected = true;

        $course = $courses[$courseid];

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

        $output->navigation = navigation::instance()->get_navigation('course');

        if ($url = optional_param('backurl', '', PARAM_URL)) {
            $output->backurl = $url;
        } else {
            $output->backurl = $CFG->wwwroot."/course/view.php?id=".$courseid;
        }

        $output->courses = array_values($courses);
        $output->searchicon = \local_edwiserreports\utility::image_icon('actions/search');
        $output->placeholder = get_string('searchuser', 'local_edwiserreports');
        $output->length = [10, 25, 50, 100];
        if ($CFG->branch > 311) {
            $output->setactive = true;
            $output->activeurl = new moodle_url("/local/edwiserreports/completion.php", array('courseid' => $course->id));
        }

        $output->breadcrumb = array(
            'items' => array(
                array(
                    'item' => html_writer::link(
                        new moodle_url(
                            "/local/edwiserreports/allcoursessummary.php",
                        ),
                        get_string('allcoursessummary', 'local_edwiserreports'),
                        array(
                            'style' => 'margin-left: 0.5rem;'
                        )
                    )
                ),
                array(
                    'item' => get_string('coursecompletion', 'local_edwiserreports')
                )
            )
        );

        return $output;
    }
}



/**
 * All courses summary renderables.
 */
class allcoursessummary_renderable implements renderable, templatable {
    /**
     * Function to export the renderer data in a format that is suitable for a
     * edit mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */
    public function export_for_template(renderer_base $output) {
        global $CFG, $USER;
        require_once($CFG->dirroot . '/local/edwiserreports/classes/reports/allcoursessummary.php');

        $allcoursessummary = new \local_edwiserreports\reports\allcoursessummary();
        $authentication = new authentication();
        $output = new stdClass();

        if ($allcoursessummary->can_edit_report_capability('allcoursessummary')) {
            $output->canedit = true;
            $output->capdata = [
                'contextid' => context_system::instance()->id,
                'reportname' => 'allcoursessummary'
            ];
        }

        // Show license notice.
        // $output->notice = (new license())->get_license_notice();

        // Secret key.
        $output->secret = $authentication->get_secret_key($USER->id);

        // Add export icons to export array.
        $output->export = array(
            "id" => "allcoursessummary",
            "region" => "report",
            "downloadlinks" => $allcoursessummary->bb->get_block_download_options(),
            "downloadurl" => $CFG->wwwroot . "/local/edwiserreports/download.php",
            "filter" => json_encode([
                "cohort" => 0,
                "group" => 0,
                "exclude" => [],
                "enrolment" => 'all'
            ])
        );

        // Header navigation.
        $output->navigation = navigation::instance()->get_navigation('course');
        $output->showdaterange = true;
        $output->showdatefilter = true;

        $output->pageheader = get_string("allcoursessummarypro", "local_edwiserreports");
        $output->breadcrumb = $allcoursessummary->get_breadcrumb();
        $output->calendar = file_get_contents($CFG->dirroot . '/local/edwiserreports/pix/calendar.svg');
        $filters = $allcoursessummary->get_filter();

        // Cohort filter.
        if (isset($filters['cohorts'])) {
            $output->cohortfilters = $filters['cohorts'];
        }

        // Groups to show on grade page.
        if (isset($filters['groups'])) {
            $output->groups = $filters['groups'];
        }

        $output->searchicon = \local_edwiserreports\utility::image_icon('actions/search');
        $output->placeholder = get_string('searchcourse', 'local_edwiserreports');
        $output->length = [10, 25, 50, 100];
        if ($CFG->branch > 311) {
            $output->setactive = true;
            $output->activeurl = new moodle_url("/local/edwiserreports/index.php");
        }

        $image = file_get_contents($CFG->dirroot . '/local/edwiserreports/pix/lock.svg');
        $upgradelink = '';
        if (is_siteadmin($USER->id)) {
            $upgradelink = UPGRADE_URL;
        }
        $output->pro = $image;
        $output->upgradelink = $upgradelink;

        return $output;
    }
}


/**
 * Course Activity Completion page renderables.
 */
class courseactivitycompletion_renderable implements renderable, templatable {
    /**
     * Function to export the renderer data in a format that is suitable for a
     * edit mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */
    public function export_for_template(renderer_base $output) {
        global $USER, $CFG;

        $courseactivitycompletion = new \local_edwiserreports\reports\courseactivitycompletion();
        $authentication = new authentication();
        $output = new stdClass();

        // Show license notice.
        // $output->notice = (new license())->get_license_notice();

        // Secret key.
        $output->secret = $authentication->get_secret_key($USER->id);
        if ($courseactivitycompletion->can_edit_report_capability('courseactivitycompletion')) {
            $output->canedit = true;
            $output->capdata = [
                'contextid' => context_system::instance()->id,
                'reportname' => 'courseactivitycompletion'
            ];
        }

        // Selected Course.
        $activecourse = optional_param('course', 0, PARAM_INT);

        // Selected Module.
        $activecm = optional_param('cm', 0, PARAM_INT);

        // Course to show on grade page.
        $filter = $courseactivitycompletion->get_filter($activecourse, $activecm);
        $output->coursecats = $filter['coursecategories'];
        $output->cms = $filter['cms'];
        $output->groups = $filter['groups'];

        // Header navigation.
        $output->pageheader = get_string("courseactivitycompletion", "local_edwiserreports");
        $output->showdaterange = true;
        $output->navigation = navigation::instance()->get_navigation('course');
        $output->breadcrumb = $courseactivitycompletion->get_breadcrumb();
        $output->calendar = file_get_contents($CFG->dirroot . '/local/edwiserreports/pix/calendar.svg');

        // Table filter context.
        $output->searchicon = \local_edwiserreports\utility::image_icon('actions/search');
        $output->placeholder = get_string('searchuser', 'local_edwiserreports');
        $output->length = [10, 25, 50, 100];

        // Add export icons to export array.
        $output->export = array(
            "id" => "courseactivitycompletion",
            "region" => "report",
            "downloadlinks" => $courseactivitycompletion->bb->get_block_download_options(),
            "downloadurl" => $CFG->wwwroot . "/local/edwiserreports/download.php",
            "filter" => json_encode([
                "course" => $filter['activecourse'],
                "cm" => $filter['activecm'],
                "group" => 0,
                "enrolment" => "all"
            ])
        );

        if ($CFG->branch > 311) {
            $output->setactive = true;
            $output->activeurl = new moodle_url("/local/edwiserreports/index.php");
        }

        $filters = new stdClass();
        $filters->course = $filter['activecourse'];
        $filters->cm = $filter['activecm'];
        $output->summarycard = $courseactivitycompletion->get_summary_data($filters);

        $image = file_get_contents($CFG->dirroot . '/local/edwiserreports/pix/lock.svg');
        $upgradelink = '';
        if (is_siteadmin($USER->id)) {
            $upgradelink = UPGRADE_URL;
        }
        $output->pro = $image;
        $output->upgradelink = $upgradelink;

        return $output;
    }
}

/**
 * Course activities summary page renderables.
 */
class courseactivitiessummary_renderable implements renderable, templatable {
    /**
     * Function to export the renderer data in a format that is suitable for a
     * edit mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */
    public function export_for_template(renderer_base $output) {
        global $USER, $CFG;
        require_once($CFG->dirroot . '/local/edwiserreports/classes/reports/courseactivitiessummary.php');

        $courseactivitiessummary = new \local_edwiserreports\reports\courseactivitiessummary();
        $authentication = new authentication();
        $output = new stdClass();

        // Show license notice.
        // $output->notice = (new license())->get_license_notice();

        // Secret key.
        $output->secret = $authentication->get_secret_key($USER->id);
        if ($courseactivitiessummary->can_edit_report_capability('courseactivitiessummary')) {
            $output->canedit = true;
            $output->capdata = [
                'contextid' => context_system::instance()->id,
                'reportname' => 'courseactivitiessummary'
            ];
        }

        // Courses.
        $activecourse = optional_param('course', 0, PARAM_INT);

        // Course to show on grade page.
        $filter = $courseactivitiessummary->get_filter($activecourse);
        // $output->coursecats = $filter['coursecategories'];
        $output->sections = $filter['sections'];
        $output->modules = $filter['modules'];
        $output->groups = $filter['groups'];

        // Header navigation.
        $output->pageheader = get_string("courseactivitiessummary", "local_edwiserreports");
        $output->showdaterange = true;
        $output->navigation = navigation::instance()->get_navigation('course');
        $output->breadcrumb = $courseactivitiessummary->get_breadcrumb();
        $output->calendar = file_get_contents($CFG->dirroot . '/local/edwiserreports/pix/calendar.svg');

        // Table filter context.
        $output->searchicon = \local_edwiserreports\utility::image_icon('actions/search');
        $output->placeholder = get_string('searchactivity', 'local_edwiserreports');
        $output->length = [10, 25, 50, 100];

        // Add export icons to export array.
        $output->export = array(
            "id" => "courseactivitiessummary",
            "region" => "report",
            "downloadlinks" => $courseactivitiessummary->bb->get_block_download_options(),
            "downloadurl" => $CFG->wwwroot . "/local/edwiserreports/download.php",
            "filter" => json_encode([
                "course" => $filter['activecourse'],
                "section" => 0,
                "module" => "all",
                "group" => 0,
                "enrolment" => "all",
                "exclude" => []
            ])
        );

        if ($CFG->branch > 311) {
            $output->setactive = true;
            $output->activeurl = new moodle_url("/local/edwiserreports/index.php");
        }


        $filters = new stdClass();
        $filters->course = $filter['activecourse'];
        $output->summarycard = $courseactivitiessummary->get_summary_data($filters);

        $image = file_get_contents($CFG->dirroot . '/local/edwiserreports/pix/lock.svg');
        $upgradelink = '';
        if (is_siteadmin($USER->id)) {
            $upgradelink = UPGRADE_URL;
        }
        $output->pro = $image;
        $output->upgradelink = $upgradelink;

        return $output;
    }
}

/**
 * Learner course progress page renderables.
 */
class learnercourseactivities_renderable implements renderable, templatable {

    /**
     * Function to export the renderer data in a format that is suitable for a
     * edit mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */
    public function export_for_template(renderer_base $output) {
        global $USER, $CFG;

        $learnercourseactivities = new \local_edwiserreports\reports\learnercourseactivities();
        $authentication = new authentication();
        $output = new stdClass();

        // Show license notice.
        // $output->notice = (new license())->get_license_notice();

        // Secret key.
        $output->secret = $authentication->get_secret_key($USER->id);

        if ($learnercourseactivities->can_edit_report_capability('learnercourseactivities')) {
            $output->canedit = true;
            $output->capdata = [
                'contextid' => context_system::instance()->id,
                'reportname' => 'learnercourseactivities'
            ];
        }

        // Active course.
        $activecourse = optional_param('course', 0, PARAM_INT);

        // Active learner.
        $activelearner = optional_param('learner', 0, PARAM_INT);

        // Course to show on grade page.
        $filter = $learnercourseactivities->get_filter($activecourse, $activelearner);
        $output->coursecats = $filter['coursecategories'];
        $output->students = $filter['students'];
        $output->sections = $filter['sections'];
        $output->modules = $filter['modules'];

        // Header navigation.
        $output->pageheader = get_string("learnercourseactivities", "local_edwiserreports");
        $output->navigation = navigation::instance()->get_navigation('learners');
        $output->breadcrumb = $learnercourseactivities->get_breadcrumb();
        $output->calendar = file_get_contents($CFG->dirroot . '/local/edwiserreports/pix/calendar.svg');

        // Table filter context.
        $output->searchicon = \local_edwiserreports\utility::image_icon('actions/search');
        $output->placeholder = get_string('searchactivity', 'local_edwiserreports');
        $output->length = [10, 25, 50, 100];

        // Add export icons to export array.
        $output->export = array(
            "id" => "learnercourseactivities",
            "region" => "report",
            "downloadlinks" => $learnercourseactivities->bb->get_block_download_options(),
            "downloadurl" => $CFG->wwwroot . "/local/edwiserreports/download.php",
            "filter" => json_encode([
                "course" => $filter['activecourse'],
                "learner" => $filter['activelearner'],
                "section" => 0,
                "module" => "all",
                "completion" => "all"
            ])
        );

        if ($CFG->branch > 311) {
            $output->setactive = true;
            $output->activeurl = new moodle_url("/local/edwiserreports/index.php");
        }

        $filters = new stdClass();
        $filters->course = $filter['activecourse'];
        $filters->learner = $filter['activelearner'];
        $output->summarycard = $learnercourseactivities->get_summary_data($filters);

        $image = file_get_contents($CFG->dirroot . '/local/edwiserreports/pix/lock.svg');
        $upgradelink = '';
        if (is_siteadmin($USER->id)) {
            $upgradelink = UPGRADE_URL;
        }
        $output->pro = $image;
        $output->upgradelink = $upgradelink;

        return $output;
    }
}


/**
 * Learner course progress page renderables.
 */
class learnercourseprogress_renderable implements renderable, templatable {

    /**
     * If this is true then course progress will be shown of current user only.
     *
     * @var bool
     */
    public $learner;

    /**
     * Constructor
     *
     * @param boolean $learner True if current user is learner
     */
    public function __construct($learner = true) {
        $this->learner = $learner;
    }
    /**
     * Function to export the renderer data in a format that is suitable for a
     * edit mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */
    public function export_for_template(renderer_base $output) {
        global $USER, $CFG;

        $learnercourseprogress = new \local_edwiserreports\reports\learnercourseprogress();
        $authentication = new authentication();
        $output = new stdClass();

        // Show license notice.
        // $output->notice = (new license())->get_license_notice();

        // Secret key.
        $output->secret = $authentication->get_secret_key($USER->id);

        if (!$this->learner) {
            $activelearner = optional_param('learner', 0, PARAM_INT);

            // Course to show on grade page.
            $filter = $learnercourseprogress->get_filter($activelearner);

            $activelearner = $filter['activelearner'];
            $output->students = $filter['learners'];

            // Add export icons to export array.
            $output->export = array(
                "id" => "learnercourseprogress",
                "region" => "report",
                "downloadlinks" => $learnercourseprogress->bb->get_block_download_options(),
                "downloadurl" => $CFG->wwwroot . "/local/edwiserreports/download.php",
                "filter" => json_encode([
                    "learner" => $activelearner,
                    "enrolment" => 'all'
                ])
            );

            if ($learnercourseprogress->can_edit_report_capability('learnercourseprogress')) {
                $output->canedit = true;
                $output->capdata = [
                    'contextid' => context_system::instance()->id,
                    'reportname' => 'learnercourseprogress'
                ];
            }
        }

        $output->learner = $this->learner;

        // Header navigation.
        $output->pageheader = get_string("learnercourseprogress", "local_edwiserreports");
        $output->showdaterange = true;
        $output->navigation = navigation::instance()->get_navigation('learners');
        $output->breadcrumb = $learnercourseprogress->get_breadcrumb();
        $output->calendar = file_get_contents($CFG->dirroot . '/local/edwiserreports/pix/calendar.svg');

        // Table filter context.
        $output->searchicon = \local_edwiserreports\utility::image_icon('actions/search');
        $output->placeholder = get_string('searchcourse', 'local_edwiserreports');
        $output->length = [10, 25, 50, 100];

        if ($CFG->branch > 311) {
            $output->setactive = true;
            $output->activeurl = new moodle_url("/local/edwiserreports/index.php");
        }

        $filters = new stdClass();
        // $filters->course = $filter['activecourse'];
        $filters->learner = $filter['activelearner'];
        $output->summarycard = $learnercourseprogress->get_summary_data($filters);

        $image = file_get_contents($CFG->dirroot . '/local/edwiserreports/pix/lock.svg');
        $upgradelink = '';
        if (is_siteadmin($USER->id)) {
            $upgradelink = UPGRADE_URL;
        }
        $output->pro = $image;
        $output->upgradelink = $upgradelink;

        return $output;
    }
}


/**
 * Student engagement renderables.
 */
class studentengagement_renderable implements renderable, templatable {
    /**
     * Function to export the renderer data in a format that is suitable for a
     * edit mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */
    public function export_for_template(renderer_base $output) {
        global $USER, $CFG;

        // Getting secret key for service authentication.
        $authentication = new authentication();

        $studentengagement = new \local_edwiserreports\reports\studentengagement();
        $output = new stdClass();

        // Show license notice.
        // $output->notice = (new license())->get_license_notice();

        // Secret key.
        $output->secret = $authentication->get_secret_key($USER->id);

        // Courses for filter.
        $output->courses = $studentengagement->get_studentengagement_courses();

        // Fetch cohort filters.
        if ($cohortfilters = local_edwiserreports_get_cohort_filter()) {
            $output->cohortfilters = $cohortfilters;
        }

        // Groups to show on grade page.
        if ($groups = $studentengagement->get_default_group_filter()) {
            $output->groups = $groups;
        }

        // Header navigation.
        $output->navigation = navigation::instance()->get_navigation('learners');
        $output->showdaterange = true;
        $output->pageheader = get_string('alllearnersummary', 'local_edwiserreports');
        $output->breadcrumb = $studentengagement->get_breadcrumb();
        $output->calendar = file_get_contents($CFG->dirroot . '/local/edwiserreports/pix/calendar.svg');

        $output->searchicon = \local_edwiserreports\utility::image_icon('actions/search');
        $output->placeholder = get_string('searchuser', 'local_edwiserreports');
        $output->length = [10, 25, 50, 100];
        if ($CFG->branch > 311) {
            $output->setactive = true;
            $output->activeurl = new moodle_url("/local/edwiserreports/index.php");
        }

        $filters = new stdClass();
        $output->summarycard = $studentengagement->get_summary_data($filters);

        $image = file_get_contents($CFG->dirroot . '/local/edwiserreports/pix/lock.svg');
        $upgradelink = '';
        if (is_siteadmin($USER->id)) {
            $upgradelink = UPGRADE_URL;
        }
        $output->pro = $image;
        $output->upgradelink = $upgradelink;

        return $output;
    }
}
