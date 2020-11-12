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

/**
 * Check whether the plugin is available or not
 * this will return true is plugin is available
 * @param  [string] $plugintype Plugin type to check
 * @param  [string] $puginname Plugin Name
 * @return boolean Return boolean
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot."/local/edwiserreports/locallib.php");

/**
 * Get Users List Fragments for diffrent pages
 * @param [array] $args Array of arguments
 * @return [string] HTML table
 */
function local_edwiserreports_output_fragment_userslist($args) {
    global $CFG;

    $response = null;
    $page = clean_param($args["page"], PARAM_TEXT);
    $cohortid = clean_param($args["cohortid"], PARAM_TEXT);

    switch ($page) {
        case "activeusers":
            require_once($CFG->dirroot . '/local/edwiserreports/classes/blocks/activeusersblock.php');
            $filter = clean_param($args['filter'], PARAM_TEXT);
            $action = clean_param($args['action'], PARAM_TEXT);

            $response = \local_edwiserreports\activeusersblock::get_userslist_table($filter, $action, $cohortid);
            break;

        case "courseprogress":
            require_once($CFG->dirroot . '/local/edwiserreports/classes/blocks/courseprogressblock.php');
            $courseid = clean_param($args['courseid'], PARAM_TEXT);
            $minval = clean_param($args['minval'], PARAM_TEXT);
            $maxval = clean_param($args['maxval'], PARAM_TEXT);

            $response = \local_edwiserreports\courseprogressblock::get_userslist_table($courseid, $minval, $maxval, $cohortid);
            break;
        case "courseengage":
            require_once($CFG->dirroot . '/local/edwiserreports/classes/blocks/courseengageblock.php');
            $courseid = clean_param($args['courseid'], PARAM_TEXT);
            $action   = clean_param($args['action'], PARAM_TEXT);

            $response = \local_edwiserreports\courseengageblock::get_userslist_table($courseid, $action, $cohortid);
            break;
    }

    return $response;
}

require_once("$CFG->libdir/formslib.php");

/**
 * Email Dialog form to send report via email
 */
class local_edwiserreports_email_dialog_form extends moodleform {
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
    public function __construct(
        $action = null,
        $customdata = null,
        $method = 'post',
        $target = '',
        $attributes = null,
        $editable = true,
        $ajaxformdata = null
    ) {
        parent::__construct($action, $customdata, $method, $target, $attributes, $editable, $ajaxformdata);
    }

    /**
     * Add elements to form.
     */
    public function definition() {
        $mform = $this->_form;
        $customdata = $this->_customdata;
        $blockname = $customdata["blockname"];

        // Email Text area.
        $mform->addElement('text', 'email',
            get_string('email', 'local_edwiserreports'),
            array(
                'size' => '30',
                'placeholder' => get_string("emailexample", "local_edwiserreports")
            )
        );
        $mform->setType('email', PARAM_NOTAGS);

        // Subject Text area.
        $mform->addElement('text', 'subject',
            get_string("subject", "local_edwiserreports"),
            array(
                'size' => '30',
                'placeholder' => get_string($blockname . "exportheader", "local_edwiserreports")
            )
        );
        $mform->setType('subject', PARAM_NOTAGS);

        // Content Text area.
        $mform->addElement('editor', 'content',
            get_string("content", "local_edwiserreports"),
            array(
                'rows' => '5',
                'cols' => '40',
                'enable_filemanagement' => false
            )
        );
        $mform->setType('content', PARAM_RAW);
        $this->content["text"] = get_string($blockname . "exporthelp", "local_edwiserreports");
    }
}

/**
 * Create fragment for email dialog box
 * @param  [array] $args Arguments
 * @return [string] HTML String
 */
function local_edwiserreports_output_fragment_email_dialog($args) {
    $blockname = clean_param($args["blockname"], PARAM_TEXT);
    $form = new local_edwiserreports_email_dialog_form(null, array("blockname" => $blockname));
    ob_start();
    $form->display();
    return ob_get_clean();
}

/**
 * Create schedule email dialoge box
 * @param  [type] $args [description]
 * @return [type]       [description]
 */
function local_edwiserreports_output_fragment_schedule_email_dialog($args) {
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
        get_string("schedule", "local_edwiserreports"),
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
        get_string("scheduledlist", "local_edwiserreports"),
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
    $out .= html_writer::div(local_edwiserreports_get_schedule_emailform($id, $formaction, $blockname, $region),
        "tab-pane active",
        array(
            "id" => "scheduletab",
            "role" => "tabpanel"
        )
    );
    $out .= html_writer::div("", "tab-pane", array(
        "id" => "schedule",
        "role" => "tabpanel"
    ));

    // Tab Content 2.
    $out .= html_writer::div(local_edwiserreports_get_schedule_emaillist(), "tab-pane", array(
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
function local_edwiserreports_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
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

    $fullpath = "/{$context->id}/local_edwiserreports/$filearea/$itemid/$relativepath";

    $fs = get_file_storage();
    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        send_file_not_found();
    }
    send_stored_file($file, 0, 0, $forcedownload, $options);
}

/**
 * Get for for blocks setting
 * @param array $params Fragment parameters
 */
function local_edwiserreports_output_fragment_get_blocksetting_form($params) {
    $blockname = isset($params['blockname']) ? $params['blockname'] : false;
    $component = 'local_edwiserreports';

    if (!$blockname) {
        throw new moodle_exception('blocknameinvalid', 'error');
    }

    // Check if block is exist or not.
    $block = \local_edwiserreports\utility::get_reportsblock_by_name($blockname);

    if (!$block) {
        throw new moodle_exception('noblockfound', 'error');
    }

    // Get block preferences.
    $preferences = \local_edwiserreports\utility::get_reportsblock_preferences($block);

    // Prepare form for block editing.
    $o = html_writer::start_tag('form', array('class' => 'form block-settings-form'));

    // Prepare view string.
    $views = array(
        LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => array(
            'key' => 'desktopview',
            'name' => get_string('desktopview', $component)
        ),
        LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => array(
            'key' => 'tabletview',
            'name' => get_string('tabletview', $component)
        )
    );
    foreach ($views as $key => $view) {
        $voption = array(
            LOCAL_SITEREPORT_BLOCK_LARGE => get_string('large', $component),
            LOCAL_SITEREPORT_BLOCK_MEDIUM => get_string('medium', $component)
        );

        if (LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW == $key) {
            $voption[LOCAL_SITEREPORT_BLOCK_SMALL] = get_string('small', $component);
        }
        $o .= html_writer::start_tag('div', array('class' => 'form-group row fitem'));
        $o .= html_writer::start_tag('div', array('class' => 'col-md-6'));
        $o .= html_writer::tag('label', $view['name'], array('class' => 'col-form-label d-inline', 'for' => 'id_' . $view['key']));
        $o .= html_writer::end_tag('label');
        $o .= html_writer::end_tag('div');
        $o .= html_writer::start_tag('div', array('class' => 'col-md-6'));
        $o .= html_writer::select($voption, $view['key'], $preferences[$key], null);
        $o .= html_writer::end_tag('label');
        $o .= html_writer::end_tag('div');
        $o .= html_writer::end_tag('div');
    }

    $blocks = \local_edwiserreports\utility::get_reports_block();
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
    $o .= html_writer::tag(
        'button',
        'Save',
        array('type' => 'submit', 'class' => 'btn btn-primary pull-right save-block-settings')
    );

    $o .= html_writer::end_tag('form');

    return $o;
}

/**
 * Get for for blocks capabilty from
 * @param array $block Fragment parameter data object
 */
function local_edwiserreports_output_fragment_get_blockscap_form($block) {
    global $CFG, $PAGE;

    $blockname = isset($block['blockname']) ? $block['blockname'] : false;
    $component = 'local_edwiserreports';

    if (!$blockname) {
        throw new moodle_exception('blocknameinvalid', 'error');
    }

    // Check if block is exist or not.
    $block = \local_edwiserreports\utility::get_reportsblock_by_name($blockname);

    if (!$block) {
        throw new moodle_exception('noblockfound', 'error');
    }

    // Get block capabilities.
    $capabilities = \local_edwiserreports\utility::get_blocks_capability($block);
    $capvalues = array_values($capabilities);

    // Prepare form for block editing.
    $o = html_writer::start_tag('form', array('class' => 'form block-cap-form'));

    $o .= html_writer::start_tag('div', array('class' => 'form-group row fitem'));
    $o .= html_writer::start_tag('div', array('class' => 'col-md-3'));
    $o .= html_writer::tag('label',
        get_string('capabilties', $component),
        array('class' => 'col-form-label d-inline', 'for' => 'id_capabilities')
    );
    $o .= html_writer::end_tag('label');
    $o .= html_writer::end_tag('div');
    $o .= html_writer::start_tag('div', array('class' => 'col-md-9'));
    $o .= html_writer::select($capabilities, 'capabilities', $capvalues[0], null);
    $o .= html_writer::end_tag('label');
    $o .= html_writer::end_tag('div');
    $o .= html_writer::end_tag('div');

    $o .= html_writer::start_div('clearfix path-admin-tool-capability overflow-scroll col-12 cap-overview');
    $data = array();
    $data['capvalue'] = array_search($capvalues[0], $capabilities);
    $o .= local_edwiserreports_output_fragment_block_overview_display($data);
    $o .= html_writer::end_div();
    $o .= html_writer::tag('button', 'Save', array('type' => 'submit', 'class' => 'btn btn-primary pull-right save-block-caps'));

    $o .= html_writer::end_tag('form');

    return $o;
}

/**
 * Render blocks capability view
 * @param array $data Fragment parameter array
 */
function local_edwiserreports_output_fragment_block_overview_display($data) {
    global $CFG;

    require_once($CFG->dirroot . '/admin/tool/capability/locallib.php');

    $context = context_system::instance();
    $strpermissions = array(
        CAP_INHERIT => new lang_string('inherit', 'role'),
        CAP_ALLOW => new lang_string('allow', 'role'),
        CAP_PREVENT => new lang_string('prevent', 'role'),
        CAP_PROHIBIT => new lang_string('prohibit', 'role')
    );
    $permissionclasses = array(
        CAP_INHERIT => 'inherit',
        CAP_ALLOW => 'allow',
        CAP_PREVENT => 'prevent',
        CAP_PROHIBIT => 'prohibit',
    );

    $o = html_writer::start_tag('table', array('class' => 'comparisontable w-full'));
    $o .= html_writer::start_tag('thead');
    $o .= html_writer::start_tag('tr');
    // Prepare data in same loop.
    $d = html_writer::start_tag('tbody');
    $d .= html_writer::start_tag('tr');

    // Get capability context.
    $roles = role_fix_names(get_all_roles($context));
    $capabilitycontext = tool_capability_calculate_role_data($data['capvalue'], $roles);
    foreach ($roles as $roleid => $role) {
        $o .= '<th><div><a href="javascript:void(0)">' . $role->localname . '</a></div></th>';

        $rolecap = $capabilitycontext[$context->id]->rolecapabilities[$role->id];
        $permission = isset($rolecap) ? $rolecap : CAP_INHERIT;
        $d .= '<td class="switch-capability ' . $permissionclasses[$permission] . '" data-permission="' . $permission . '">';
        $d .= '<label>' . $strpermissions[$permission] . '</label>';

        foreach ($permissionclasses as $key => $class) {
            $checked = '';
            if ($key == $permission) {
                $checked = 'checked';
            }
            $d .= '<input class="d-none" type="radio" name="' . $role->shortname .'" ';
            $d .= 'value="' . $class . '" data-strpermission="' . $strpermissions[$key] . '"';
            $d .= 'data-permissionclass="' . $class . '"' . $checked . '>';
        }

        $d .= '</td>';
    }

    $d .= html_writer::end_tag('tr');
    $d .= html_writer::end_tag('tbody');

    $o .= html_writer::end_tag('tr');
    $o .= html_writer::end_tag('thead');
    $o .= $d;
    $o .= html_writer::end_tag('table');

    return $o;
}

/**
 * Adding learning program link in sidebar
 * @param navigation_node $nav navigation node
 */
function local_edwiserreports_extend_navigation(navigation_node $nav) {
    global $CFG, $PAGE, $COURSE;

    // Check if users is logged in to extend navigation.
    if (!isloggedin()) {
        return;
    }

    $hasblocks = is_block_present_indashboard();

    // During the installation save the capability.
    $PAGE->requires->js_call_amd('local_edwiserreports/install', 'init');

    if ($hasblocks) {
        $icon = new pix_icon('i/stats', '');

        $node = $nav->add(
            get_string('reportsandanalytics', 'local_edwiserreports'),
            new moodle_url($CFG->wwwroot . '/local/edwiserreports/index.php'),
            navigation_node::TYPE_CUSTOM,
            'reportsandanalytics',
            'reportsandanalytics',
            $icon
        );
        $node->showinflatnavigation = true;
    }
    $iscompletionpage = strpos($PAGE->url, '/local/edwiserreports/completion.php');
    if ($PAGE->pagelayout !== 'course' && $PAGE->pagelayout !== 'incourse' && !$iscompletionpage) {
        return true;
    }

    if (!has_capability('moodle/course:viewhiddencourses', context_course::instance($COURSE->id))) {
        return;
    }

    $icon = new pix_icon('i/report', '');

    $node = $nav->add(
        get_string('completionreports', 'local_edwiserreports'),
        new moodle_url($CFG->wwwroot . '/local/edwiserreports/completion.php', array('courseid' => $COURSE->id)),
        navigation_node::TYPE_CUSTOM,
        'completionreports',
        'completionreports',
        $icon
    );
    $node->showinflatnavigation = true;
}

/**
 * Get default block settings
 */
function get_default_block_settings() {
    // Return defautl block settings.
    return array(
        'activeusers' => array(
            'classname' => 'activeusersblock',
            'position' => 0,
            LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE,
            LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE,
            LOCAL_SITEREPORT_BLOCK_MOBILE_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE
        ),
        'courseprogress' => array(
            'classname' => 'courseprogressblock',
            'position' => 1,
            LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => LOCAL_SITEREPORT_BLOCK_MEDIUM,
            LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE,
            LOCAL_SITEREPORT_BLOCK_MOBILE_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE
        ),
        'activecourses' => array(
            'classname' => 'activecoursesblock',
            'position' => 2,
            LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => LOCAL_SITEREPORT_BLOCK_MEDIUM,
            LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE,
            LOCAL_SITEREPORT_BLOCK_MOBILE_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE
        ),
        'certificates' => array(
            'classname' => 'certificatesblock',
            'position' => 3,
            LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => LOCAL_SITEREPORT_BLOCK_MEDIUM,
            LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE,
            LOCAL_SITEREPORT_BLOCK_MOBILE_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE
        ),
        'liveusers' => array(
            'classname' => 'liveusersblock',
            'position' => 4,
            LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => LOCAL_SITEREPORT_BLOCK_MEDIUM,
            LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE,
            LOCAL_SITEREPORT_BLOCK_MOBILE_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE
        ),
        'siteaccess' => array(
            'classname' => 'siteaccessblock',
            'position' => 5,
            LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => LOCAL_SITEREPORT_BLOCK_MEDIUM,
            LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE,
            LOCAL_SITEREPORT_BLOCK_MOBILE_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE
        ),
        'todaysactivity' => array(
            'classname' => 'todaysactivityblock',
            'position' => 6,
            LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => LOCAL_SITEREPORT_BLOCK_MEDIUM,
            LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE,
            LOCAL_SITEREPORT_BLOCK_MOBILE_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE
        ),
        'inactiveusers' => array(
            'classname' => 'inactiveusersblock',
            'position' => 7,
            LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => LOCAL_SITEREPORT_BLOCK_MEDIUM,
            LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE,
            LOCAL_SITEREPORT_BLOCK_MOBILE_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE
        )
    );
}
