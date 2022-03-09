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

namespace local_edwiserreports;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . '/formslib.php');

use html_writer;
use moodleform;

// Form to select start and end date ranges and session time.
class old_log_form extends moodleform {

    public function definition() {
        global $DB;

        $mform = & $this->_form;

        $mform->addElement('html', html_writer::tag(
            'p',
            get_string('oldloginfo', 'local_edwiserreports'),
            array('class' => 'px-2 mx-5')
        ));

        $firstlogs = $DB->get_record_sql('SELECT id, userid, timecreated
                                             FROM {logstore_standard_log}
                                            ORDER BY timecreated ASC', [], IGNORE_MULTIPLE);

        // Convert the year stored in the DB as gregorian to that used by the calendar type.
        $start = date('Y', $firstlogs->timecreated);
        $end = date('Y', time());

        $attributes = array(
            'startyear' => $start,
            'stopyear'  => $end
        );

        $mform->addElement('date_selector', 'mintime', get_string('oldlogmintime', 'local_edwiserreports'), $attributes);
        $mform->addHelpButton('mintime', 'oldlogmintime', 'local_edwiserreports');

        $mform->addElement('duration', 'limit', get_string('oldloglimit', 'local_edwiserreports'), array(
            'units' => [1, MINSECS, HOURSECS]
        ));
        $mform->addHelpButton('limit', 'oldloglimit', 'local_edwiserreports');

        // Buttons.
        $this->add_action_buttons(false, get_string('fetch', 'local_edwiserreports'));
    }

}
