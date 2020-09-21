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

/**
 * Check whether the plugin is available or not
 * this will return true is plugin is available
 * @param  [string] $plugintype Plugin type to check
 * @param  [string] $puginname Plugin Name
 * @return boolean Return boolean
 */

defined('MOODLE_INTERNAL') || die();

use \report_elucidsitereport;

require_once($CFG->dirroot."/report/elucidsitereport/locallib.php");

/**
 * Get Users List Fragments for diffrent pages
 * @param [array] $args Array of arguments
 * @return [string] HTML table
 */
function report_elucidsitereport_output_fragment_userslist($args) {
    $response = null;
    $page = clean_param($args["page"], PARAM_TEXT);
    $cohortid = clean_param($args["cohortid"], PARAM_TEXT);

    switch ($page) {
        case "activeusers":
            $filter = clean_param($args['filter'], PARAM_TEXT);
            $action = clean_param($args['action'], PARAM_TEXT);

            $response = \report_elucidsitereport\active_users_block::get_userslist_table($filter, $action, $cohortid);
            break;

        case "courseprogress":
            $courseid = clean_param($args['courseid'], PARAM_TEXT);
            $minval = clean_param($args['minval'], PARAM_TEXT);
            $maxval = clean_param($args['maxval'], PARAM_TEXT);

            $response = \report_elucidsitereport\course_progress_block::get_userslist_table($courseid, $minval, $maxval, $cohortid);
            break;
        case "courseengage":
            $courseid = clean_param($args['courseid'], PARAM_TEXT);
            $action   = clean_param($args['action'], PARAM_TEXT);

            $response = \report_elucidsitereport\courseengage_block::get_userslist_table($courseid, $action, $cohortid);
            break;
    }

    return $response;
}

/**
 * Get Learning Program stats fragment
 * @param [array] $args Array of arguments
 * @return [string] HTML table
 */
function report_elucidsitereport_output_fragment_lpstats($args) {
    global $DB;
    $lpid = clean_param($args["lpid"], PARAM_TEXT);
    $cohortid = clean_param($args["cohortid"], PARAM_TEXT);

    return json_encode(\report_elucidsitereport\lpstats_block::get_lpstats_usersdata($lpid, $cohortid));
}

require_once("$CFG->libdir/formslib.php");

/**
 * Email Dialog form to send report via email
 */
class email_dialog_form extends moodleform {
    /**
     * The constructor function calls the abstract function definition() and it will then
     * process and clean and attempt to validate incoming data.
     *
     * It will call your custom validate method to validate data and will also check any rules
     * you have specified in definition using addRule
     *
     * The name of the form (id attribute of the form) is automatically generated depending on
     * the name you gave the class extending moodleform. You should call your class something
     * like
     *
     * @param mixed $action the action attribute for the form. If empty defaults to auto detect the
     *              current url. If a moodle_url object then outputs params as hidden variables.
     * @param mixed $customdata if your form defintion method needs access to data such as $course
     *              $cm, etc. to construct the form definition then pass it in this array. You can
     *              use globals for somethings.
     * @param string $method if you set this to anything other than 'post' then _GET and _POST will
     *               be merged and used as incoming data to the form.
     * @param string $target target frame for form submission. You will rarely use this. Don't use
     *               it if you don't need to as the target attribute is deprecated in xhtml strict.
     * @param mixed $attributes you can pass a string of html attributes here or an array.
     *               Special attribute 'data-random-ids' will randomise generated elements ids. This
     *               is necessary when there are several forms on the same page.
     * @param bool $editable
     * @param array $ajaxformdata Forms submitted via ajax, must pass their data here, instead of relying on _GET and _POST.
     */
    //@codingStandardsIgnoreStart
    public function __construct(
        $action=null,
        $customdata=null,
        $method='post',
        $target='',
        $attributes=null,
        $editable=true,
        $ajaxformdata=null
    ) {
        parent::__construct($action, $customdata, $method, $target, $attributes, $editable, $ajaxformdata);
    }
    //@codingStandardsIgnoreEnd

    // Add elements to form.
    public function definition() {
        global $CFG;

        $mform = $this->_form;
        $customdata = $this->_customdata;
        $blockname = $customdata["blockname"];

        // Email Text area.
        $mform->addElement('text', 'email',
            get_string('email', 'report_elucidsitereport'),
            array(
                'size' => '30',
                'placeholder' => get_string("emailexample", "report_elucidsitereport")
            )
        );
        $mform->setType('email', PARAM_NOTAGS);

        // Subject Text area.
        $mform->addElement('text', 'subject',
            get_string("subject", "report_elucidsitereport"),
            array(
                'size' => '30',
                'placeholder' => get_string($blockname . "exportheader", "report_elucidsitereport")
            )
        );
        $mform->setType('subject', PARAM_NOTAGS);

        // Content Text area.
        $mform->addElement('editor', 'content',
            get_string("content", "report_elucidsitereport"),
            array(
                'rows' => '5',
                'cols' => '40',
                'enable_filemanagement' => false
            )
        );
        $mform->setType('content', PARAM_RAW);
        $this->content["text"] = get_string($blockname . "exporthelp", "report_elucidsitereport");
    }
}

/**
 * Create fragment for email dialog box
 * @param  [array] $args Arguments
 * @return [string] HTML String
 */
function report_elucidsitereport_output_fragment_email_dialog($args) {
    $blockname = clean_param($args["blockname"], PARAM_TEXT);
    $form = new email_dialog_form(null, array("blockname" => $blockname));
    ob_start();
    $form->display();
    return ob_get_clean();
}

/**
 * Create schedule email dialoge box
 * @param  [type] $args [description]
 * @return [type]       [description]
 */
function report_elucidsitereport_output_fragment_schedule_email_dialog($args) {
    $formaction = clean_param($args["href"], PARAM_TEXT);
    $blockname = clean_param($args["blockname"], PARAM_TEXT);
    $region = clean_param($args["region"], PARAM_TEXT);

    // Get existing email id.
    $id = $args["id"];

    $out = html_writer::start_div("nav-tabs-horizontal", array(
        "data-plugin" => "tabs"
    ));

    $out .= html_writer::start_tag("ul", array(
        "class" => "nav nav-tabs nav-tabs-line",
        "role" => "tablist"
    ));

    // Tab 1.
    $out .= html_writer::start_tag("li", array(
        "class" => "nav-item",
        "role" => "presentation"
    ));
    $out .= html_writer::link("#scheduletab",
        get_string("schedule", "report_elucidsitereport"),
        array(
            "class" => "nav-link active",
            "data-toggle" => "tab",
            "aria-controls" => "scheduletab",
            "role" => "tab",
            "aria-selected" => "true"
        )
    );
    $out .= html_writer::end_tag("li");

    // Tab 2.
    $out .= html_writer::start_tag("li", array(
        "class" => "nav-item",
        "role" => "presentation"
    ));
    $out .= html_writer::link("#listemailstab",
        get_string("scheduledlist", "report_elucidsitereport"),
        array(
            "class" => "nav-link",
            "data-toggle" => "tab",
            "aria-controls" => "listemailstab",
            "role" => "tab",
            "aria-selected" => "true"
        )
    );
    $out .= html_writer::end_tag("li");

    $out .= html_writer::end_tag("ul");

    // Tab Content.
    $out .= html_writer::start_div("tab-content pt-20");

    // Tab Content 1.
    $out .= html_writer::div(get_schedule_emailform($id, $formaction, $blockname, $region), "tab-pane active", array(
        "id" => "scheduletab",
        "role" => "tabpanel"
    ));
    $out .= html_writer::div("", "tab-pane", array(
        "id" => "schedule",
        "role" => "tabpanel"
    ));

    // Tab Content 2.
    $out .= html_writer::div(get_schedule_emaillist(), "tab-pane", array(
        "id" => "listemailstab",
        "role" => "tabpanel"
    ));
    $out .= html_writer::div("", "tab-pane", array(
        "id" => "listemails",
        "role" => "tabpanel"
    ));
    $out .= html_writer::end_div();

    return $out;
}

/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function report_elucidsitereport_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    static $report;
    $course = $course;
    $cm = $cm;

    if ($context->contextlevel != CONTEXT_USER) {
        send_file_not_found();
    }

    $itemid = (int)array_shift($args);
    if ($itemid != 0) {
        send_file_not_found();
    }

    $relativepath = implode('/', $args);

    $fullpath = "/{$context->id}/report_elucidsitereport/$filearea/$itemid/$relativepath";

    $fs = get_file_storage();
    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        send_file_not_found();
    }
    send_stored_file($file, 0, 0, $forcedownload, $options);
}

/**
 * Get for for blocks setting
 */
function report_elucidsitereport_output_fragment_get_blocksetting_form($params) {
    global $PAGE;
    $blockname = isset($params['blockname']) ? $params['blockname'] : false;
    $component = 'report_elucidsitereport';

    if (!$blockname) {
        throw new moodle_exception('blocknameinvalid', 'error');
    }

    // Check if block is exist or not
    $block = \report_elucidsitereport\utility::get_reportsblock_by_name($blockname);

    if (!$block) {
        throw new moodle_exception('noblockfound', 'error');
    }

    // Get block preferences
    $preferences = \report_elucidsitereport\utility::get_reportsblock_preferences($block);

    // Prepare form for block editing
    $o = html_writer::start_tag('form', array('class' => 'form block-settings-form'));

    // Prepare view string
    $views = array(
        BLOCK_DESKTOP_VIEW => array(
            'key' => 'desktopview',
            'name' => get_string('desktopview', $component)
        ),
        BLOCK_TABLET_VIEW => array(
            'key' => 'tabletview',
            'name' => get_string('tabletview', $component)
        ),
        BLOCK_MOBILE_VIEW => array(
            'key' => 'mobileview',
            'name' => get_string('mobileview', $component)
        ),
    );
    foreach($views as $key => $view) {
        $o .= html_writer::start_tag('div', array('class' => 'form-group row fitem'));
        $o .= html_writer::start_tag('div', array('class' => 'col-md-6'));
        $o .= html_writer::tag('label', $view['name'], array('class' => 'col-form-label d-inline', 'for' => 'id_' . $view['key']));
        $o .= html_writer::end_tag('label');
        $o .= html_writer::end_tag('div');
        $o .= html_writer::start_tag('div', array('class' => 'col-md-6'));
        $o .= html_writer::select(array(
            BLOCK_LARGE => get_string('large', $component),
            BLOCK_MEDIUM => get_string('medium', $component),
            BLOCK_SMALL => get_string('small', $component)
        ), $view['key'], $preferences[$key], null);
        $o .= html_writer::end_tag('label');
        $o .= html_writer::end_tag('div');
        $o .= html_writer::end_tag('div');
    }

    $blocks = \report_elucidsitereport\utility::get_reports_block();
    $positions = range(1, count($blocks));
    $o .= html_writer::start_tag('div', array('class' => 'form-group row fitem'));
    $o .= html_writer::start_tag('div', array('class' => 'col-md-6'));
    $o .= html_writer::tag('label', get_string('position', $component),
            array('class' => 'col-form-label d-inline', 'for' => 'id_position'));
    $o .= html_writer::end_tag('label');
    $o .= html_writer::end_tag('div');
    $o .= html_writer::start_tag('div', array('class' => 'col-md-6'));
    $o .= html_writer::select($positions, 'position', $preferences['position'], null);
    $o .= html_writer::end_tag('label');
    $o .= html_writer::end_tag('div');
    $o .= html_writer::end_tag('div');
    $o .= html_writer::tag('button', 'Save', array('type' => 'submit', 'class' => 'btn btn-primary pull-right save-block-settings'));

    $o .= html_writer::end_tag('form');

    return $o;
}
