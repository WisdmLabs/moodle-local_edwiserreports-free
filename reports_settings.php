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
 * Web services admin UI forms
 *
 * @package   webservice
 * @copyright 2009 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir . '/formslib.php');
require_once('lib.php');
// Restrict normal user to access this page.
admin_externalpage_setup('elucidsitereport_settings');

// Require Login.
require_login();
$context = context_system::instance();
$PAGE->set_pagelayout('admin');
$PAGE->set_context($context);
$PAGE->set_url('/report/elucidsitereport/reports_settings.php');
$PAGE->set_title(get_string('pluginname', 'report_elucidsitereport'));

class report_blocks_form extends moodleform {

    public function definition() {

        $mform = $this->_form;

        $mform->addElement('header', 'addfunction', get_string('rpmblocks', 'report_elucidsitereport'));
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
        // Get previously added blocks.
        $options = array(
           'noselectionstring' => get_string('notselected', 'report_elucidsitereport'),
           'multiple' => true,
        );
        $mform->addElement('autocomplete', 'rpmblocks', get_string('selectblocks', 'report_elucidsitereport'), $blocks, $options);
        // Set previously added blocks as default.
        $this->add_action_buttons(true, get_string('addblocks', 'report_elucidsitereport'));
    }
}

// Create a form for showing blocks in multselect box.
$mform = new report_blocks_form();
// Form processing and displaying is done here.
if ($mform->is_cancelled()) {
    // Handle form cancel operation, if cancel button is present on form.
    redirect(new moodle_url('/report/elucidsitereport/reports_settings.php'));
} else if ($formdata = $mform->get_data()) {
    // In this case you process validated data. $mform->get_data() returns data posted in form.
    save_settings_form_data($formdata);
}
$addedblocks = isset($CFG->ed_reporting_manager_blocks) ? unserialize($CFG->ed_reporting_manager_blocks) : array();
$mform->set_data([
    'rpmblocks' => $addedblocks
]);

// OUTPUT edit/create form.
echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
