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
    $output = html_writer::start_tag('form', array('class' => 'form block-settings-form'));

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
        $output .= html_writer::start_tag('div', array('class' => 'form-group row fitem'));
        $output .= html_writer::start_tag('div', array('class' => 'col-md-6'));
        $labelparams = array('class' => 'col-form-label d-inline', 'for' => 'id_' . $view['key']);
        $output .= html_writer::tag('label', $view['name'], $labelparams);
        $output .= html_writer::end_tag('label');
        $output .= html_writer::end_tag('div');
        $output .= html_writer::start_tag('div', array('class' => 'col-md-6'));
        $output .= html_writer::select($voption, $view['key'], $preferences[$key], null);
        $output .= html_writer::end_tag('label');
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('div');
    }

    $blocks = \local_edwiserreports\utility::get_reports_block();
    $positions = range(1, count($blocks));
    $output .= html_writer::start_tag('div', array('class' => 'form-group row fitem'));
    $output .= html_writer::start_tag('div', array('class' => 'col-md-6'));
    $output .= html_writer::tag('label', get_string('position', $component),
            array('class' => 'col-form-label d-inline', 'for' => 'id_position'));
    $output .= html_writer::end_tag('label');
    $output .= html_writer::end_tag('div');
    $output .= html_writer::start_tag('div', array('class' => 'col-md-6'));
    $output .= html_writer::select($positions, 'position', $preferences['position'], null);
    $output .= html_writer::end_tag('label');
    $output .= html_writer::end_tag('div');
    $output .= html_writer::end_tag('div');
    $output .= html_writer::tag(
        'button',
        'Save',
        array('type' => 'submit', 'class' => 'btn theme-primary-bg text-white pull-right save-block-settings')
    );

    $output .= html_writer::end_tag('form');

    return $output;
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
    $capvalue = 'report/edwiserreports_' . $blockname . ':view';

    // Prepare form for block editing.
    $output = html_writer::start_tag('form', array('class' => 'form block-cap-form'));

    $output .= html_writer::start_tag('div', array('class' => 'form-group row fitem'));
    $output .= html_writer::start_tag('div', array('class' => 'col-md-3'));
    $output .= html_writer::tag('label',
        get_string('capabilties', $component),
        array('class' => 'col-form-label d-inline h4', 'for' => 'id_capabilities')
    );
    $output .= html_writer::end_tag('label');
    $output .= html_writer::end_tag('div');
    $output .= html_writer::start_tag('div', array('class' => 'col-md-9'));
    $output .= html_writer::tag('label', $capabilities[$capvalue], array('class' => 'col-form-label d-inline h4'));
    $output .= html_writer::select($capabilities, 'capabilities', $capvalue, null, array('class' => 'hidden h4'));
    $output .= html_writer::end_tag('label');
    $output .= html_writer::end_tag('div');
    $output .= html_writer::end_tag('div');

    $output .= html_writer::start_div('clearfix path-admin-tool-capability overflow-scroll col-12 cap-overview');
    $blockname = $block->classname . '-' . $block->id;
    $output .= local_edwiserreports_output_fragment_block_overview_display($capvalue, $blockname);
    $output .= html_writer::end_div();
    $btnparams = array('type' => 'submit', 'class' => 'btn theme-primary-bg text-white pull-right save-block-caps');
    $output .= html_writer::tag('button', 'Save', $btnparams);

    $output .= html_writer::end_tag('form');

    return $output;
}

/**
 * Fragment to generate email tabs
 * @param array $block Fragment parameter data object
 * @return string
 */
function local_edwiserreports_output_fragment_export_data_warning($args) {
    global $OUTPUT;
    $warning = clean_param($args["warning"], PARAM_RAW);
    $data = [
        'warningicon' => \local_edwiserreports\utility::image_icon('warning'),
        'warning' => get_string($warning, 'local_edwiserreports')
    ];
    return $OUTPUT->render_from_template('local_edwiserreports/exportwarning', $data);
}

/**
 * Fragment to generate email tabs
 * @param array $block Fragment parameter data object
 * @return string
 */
function local_edwiserreports_output_fragment_email_schedule_tabs($args) {
    global $OUTPUT;
    $response = null;
    $data = clean_param($args["data"], PARAM_RAW);
    $data = json_decode($data, true);
    $data['searchicon'] = \local_edwiserreports\utility::image_icon('actions/search');
    $data['warningicon'] = \local_edwiserreports\utility::image_icon('warning');
    $data['placeholder'] = get_string('search');
    $data['length'] = [10, 25, 50, 100];
    return $OUTPUT->render_from_template('local_edwiserreports/email_schedule_tabs', $data);
}
/**
 * Render blocks capability view
 * @param Array $capvalue  Fragment parameter array
 * @param Array $blockname Block name
 */
function local_edwiserreports_output_fragment_block_overview_display($capvalue, $blockname = '') {
    global $CFG;

    require_once($CFG->dirroot . '/admin/tool/capability/locallib.php');

    $context = context_system::instance();
    $strpermissions = array(
        CAP_ALLOW => new lang_string('show'),
        CAP_PREVENT => new lang_string('hide')
    );
    $permissionclasses = array(
        CAP_ALLOW => 'allow',
        CAP_PREVENT => 'prevent'
    );

    $output = html_writer::start_tag('table', array('class' => 'comparisontable w-full'));
    $output .= html_writer::start_tag('thead');
    $output .= html_writer::start_tag('tr');

    // Prepare data in same loop.
    $data = html_writer::start_tag('tbody');
    $data .= html_writer::start_tag('tr');
    if (stripos($blockname, 'customreportsblock') !== false) {
        $defaultcaps = \local_edwiserreports\utility::get_default_capabilities()['report/edwiserreports_customreports:manage'];
    } else {
        // Default capability.
        $defaultcaps = \local_edwiserreports\utility::get_default_capabilities()[$capvalue];
    }

    // Get capability context.
    $roles = role_fix_names(get_all_roles($context));
    $capabilitycontext = tool_capability_calculate_role_data($capvalue, $roles);
    $warnings = [];
    foreach ($roles as $role) {
        $rolecap = \local_edwiserreports\utility::get_rolecap_from_context($capabilitycontext[$context->id], $role, $blockname);

        if ($rolecap == CAP_INHERIT && isset($defaultcaps[$role->archetype])) {
            $rolecap = $defaultcaps[$role->archetype];
        }
        if (!isset($defaultcaps[$role->archetype])) {
            if ($rolecap == CAP_ALLOW) {
                $warnings[] = $role->localname;
            } else {
                continue;
            }
        }
        $output .= '<th><div><a href="javascript:void(0)">' . $role->localname . '</a></div></th>';

        $permission = $rolecap > 0 ? CAP_ALLOW : CAP_PREVENT;
        $data .= '<td class="switch-capability ' . $permissionclasses[$permission] . '" data-permission="' . $permission . '">';
        $data .= '<label>' . $strpermissions[$permission] . '</label>';
        foreach ($permissionclasses as $key => $class) {
            $checked = '';
            if ($key == $permission) {
                $checked = 'checked';
            }
            $data .= '<input class="d-none" type="radio" name="' . $role->shortname .'" ';
            $data .= 'value="' . $class . '" data-strpermission="' . $strpermissions[$key] . '"';
            $data .= 'data-permissionclass="' . $class . '"' . $checked . '>';
        }

        $data .= '</td>';
    }

    $data .= html_writer::end_tag('tr');
    $data .= html_writer::end_tag('tbody');

    $output .= html_writer::end_tag('tr');
    $output .= html_writer::end_tag('thead');
    $output .= $data;
    $output .= html_writer::end_tag('table');

    $warning = '';
    if (!empty($warnings)) {
        $warning = html_writer::start_tag('div');
        $warning .= html_writer::tag('div', get_string('warning'), array('class' => 'text-warning h3'));
        $warning .= html_writer::tag(
            'label',
            get_string('permissionwarning', 'local_edwiserreports')
        );
        $warning .= html_writer::start_tag('ul');
        foreach ($warnings as $role) {
            $warning .= html_writer::tag('li', $role);
        }
        $warning .= html_writer::end_tag('ul');
        $warning .= html_writer::end_tag('div');
    }
    return $warning . $output;
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

    // If url is not set.
    if (!$PAGE->has_set_url()) {
        return true;
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
 * Delete orphan block records from db which is removed from default block list.
 *
 * @return void
 */
function local_edwiserreports_process_orphan_blocks() {
    global $DB;
    $defaultblocks = local_edwiserreports_get_default_block_settings();
    $installedblocks = $DB->get_records('edwreports_blocks');
    foreach ($installedblocks as $block) {
        if (!array_key_exists($block->blockname, $defaultblocks)) {
            $DB->delete_records('edwreports_blocks', array('id' => $block->id));
        }
    }
}

/**
 * Adding new/missing block records in edwreports_blocks table.
 *
 * @return bool
 */
function local_edwiserreports_process_block_creation() {
    global $DB;

    // All Default blocks.
    $defaultblocks = local_edwiserreports_get_default_block_settings();

    // Create each block.
    $blocks = array();
    $index = -1;
    foreach ($defaultblocks as $key => $block) {
        $index++;
        if ($DB->record_exists('edwreports_blocks', array('blockname' => $key))) {
            unset($defaultblocks[$key]);
            continue;
        }
    }

    foreach ($defaultblocks as $key => $block) {
        $blockdata = new stdClass();
        $blockdata->blockname = $key;
        $blockdata->classname = $block['classname'];
        $blockdata->blocktype = LOCAL_SITEREPORT_BLOCK_TYPE_DEFAULT;
        $blockdata->blockdata = json_encode((object) array(
            LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => $block[LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW],
            LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => $block[LOCAL_SITEREPORT_BLOCK_TABLET_VIEW],
            'position' => $index++
        ));
        $blockdata->timecreated = time();
        $blocks[] = $blockdata;
    }

    $status = $DB->insert_records('edwreports_blocks', $blocks);

    local_edwiserreports_process_orphan_blocks();
    return $status;
}

/**
 * Get default block settings
 *
 * @return Array
 */
function local_edwiserreports_get_default_block_settings() {
    // Return default block settings.
    $index = 0;
    $blocks = array(
        'activeusers' => array(
            'classname' => 'activeusersblock',
            'position' => $index++,
            LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => LOCAL_SITEREPORT_BLOCK_MEDIUM,
            LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE
        ),
        'visitsonsite' => array(
            'classname' => 'visitsonsiteblock',
            'position' => $index++,
            LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => LOCAL_SITEREPORT_BLOCK_MEDIUM,
            LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE
        ),
        'timespentonsite' => array(
            'classname' => 'timespentonsiteblock',
            'position' => $index++,
            LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => LOCAL_SITEREPORT_BLOCK_MEDIUM,
            LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE
        ),
        'timespentoncourse' => array(
            'classname' => 'timespentoncourseblock',
            'position' => $index++,
            LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => LOCAL_SITEREPORT_BLOCK_MEDIUM,
            LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE
        ),
        'courseactivitystatus' => array(
            'classname' => 'courseactivitystatusblock',
            'position' => $index++,
            LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => LOCAL_SITEREPORT_BLOCK_MEDIUM,
            LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE
        ),
        'learnertimespentonsite' => array(
            'classname' => 'learnertimespentonsiteblock',
            'position' => $index++,
            LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => LOCAL_SITEREPORT_BLOCK_MEDIUM,
            LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE
        ),
        'courseprogress' => array(
            'classname' => 'courseprogressblock',
            'position' => $index++,
            LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => LOCAL_SITEREPORT_BLOCK_MEDIUM,
            LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE
        ),
        'activecourses' => array(
            'classname' => 'activecoursesblock',
            'position' => $index++,
            LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => LOCAL_SITEREPORT_BLOCK_MEDIUM,
            LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE
        ),
        'certificates' => array(
            'classname' => 'certificatesblock',
            'position' => $index++,
            LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => LOCAL_SITEREPORT_BLOCK_MEDIUM,
            LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE
        ),
        'liveusers' => array(
            'classname' => 'liveusersblock',
            'position' => $index++,
            LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => LOCAL_SITEREPORT_BLOCK_MEDIUM,
            LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE
        ),
        'siteaccess' => array(
            'classname' => 'siteaccessblock',
            'position' => $index++,
            LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => LOCAL_SITEREPORT_BLOCK_MEDIUM,
            LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE
        ),
        'todaysactivity' => array(
            'classname' => 'todaysactivityblock',
            'position' => $index++,
            LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => LOCAL_SITEREPORT_BLOCK_MEDIUM,
            LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE
        ),
        'inactiveusers' => array(
            'classname' => 'inactiveusersblock',
            'position' => $index++,
            LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => LOCAL_SITEREPORT_BLOCK_MEDIUM,
            LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE
        ),
        'grade' => array(
            'classname' => 'gradeblock',
            'position' => $index++,
            LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => LOCAL_SITEREPORT_BLOCK_MEDIUM,
            LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE
        ),
        'learnercourseprogress' => array(
            'classname' => 'learnercourseprogressblock',
            'position' => $index++,
            LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => LOCAL_SITEREPORT_BLOCK_MEDIUM,
            LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE
        )
    );

    return $blocks;
}
