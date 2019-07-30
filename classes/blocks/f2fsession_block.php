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

namespace report_elucidsitereport;

defined('MOODLE_INTERNAL') || die();

use stdClass;
use context_module;

require_once($CFG->dirroot . '/mod/facetoface/lib.php');

/**
 * Class f2fsession Block
 * To get the data related to active users block
 */
class f2fsession_block extends utility {
    public static function get_data() {
        $response = new stdClass();
        $response->data = new stdClass();
        $response->data->f2fmodules = self::get_f2fmodules();
        return $response;
    }

    /**
     * Get face to face activities
     * @return array Array of all available face to face activities
     */
    public static function get_f2fmodules() {
        global $CFG, $DB, $USER;

        $count = 0;
        $f2fmodules = array();
        $f2factivities = $DB->get_records('facetoface', array());
        foreach ($f2factivities as $facetoface) {
            if (!$course = $DB->get_record('course', array('id' => $facetoface->course))) {
                print_error('error:coursemisconfigured', 'facetoface');
            }

            if (!$cm = get_coursemodule_from_instance('facetoface', $facetoface->id, $course->id)) {
                print_error('error:incorrectcoursemoduleid', 'facetoface');
            }

            $facetoface->coursename = $course->shortname;
            $f2fsessions = self::get_f2fsessions($facetoface);

            $facetoface->sessions[] = $f2fsessions;
            if (count($f2fsessions->previous)) {
                $f2fmodules[] = self::get_facetoface_data($facetoface, $f2fsessions);
            }
        }

        return $f2fmodules;
    }

    /**
     * Get all sesions a face to face activity
     * @param  object $facetoface Face to face activity object
     * @return array Array of all sessions
     */
    public static function get_f2fsessions($facetoface) {
        global $CFG, $DB, $USER;
        // $locations = get_locations($facetoface->id);

        // TODO: This needs to check the location
        $location = '';
        $sessionid = false;
        $timenow = time();
        $overallattendend = 0;
        $overallsignups = 0;

        $f2fsessions = new stdClass();
        $bookedsession = null;
        if ($submissions = facetoface_get_user_submissions($facetoface->id, $USER->id)) {
            $submission = array_shift($submissions);
            $bookedsession = $submission;
        }

        $upcomingarray = array();
        $previousarray = array();
        $inprogressarray = array();
        $upcomingtbdarray = array();
        if ($sessions = facetoface_get_sessions($facetoface->id, $location)) {
            foreach ($sessions as $session) {
                $sessiondata = array();
                foreach ($session->sessiondates as $sessiondate) {
                    $sessiondata = self::get_session_data($session, $sessiondate);

                    // Add custom fields to sessiondata.
                    $customdata = $DB->get_records('facetoface_session_data', array('sessionid' => $session->id), '', 'fieldid, data');

                    // Is session waitlisted.
                    if (!$session->datetimeknown) {
                        $sessionwaitlisted = true;
                    } else {
                        foreach ($customdata as $data) {
                            $field = $DB->get_record('facetoface_session_field', array('id' => $data->fieldid));
                            if ($field->shortname == 'venue') {
                                $sessiondata->signupvenue = $data->data;
                            }
                        }
                    }

                    // Check if session is started.
                    $sessionstarted = facetoface_has_session_started($session, $timenow);
                    $sessionended = false;
                    if ($session->datetimeknown && $sessionstarted && facetoface_is_session_in_progress($session, $timenow)) {
                        $sessionstarted = true;
                    } else if ($session->datetimeknown && $sessionstarted) {
                        $sessionended = true;
                    }

                    // Put the row in the right table.
                    if ($sessionended) {
                        $previousarray[] = $sessiondata;
                    } else if ($sessionstarted) {
                        $inprogressarray[] = $sessiondata;
                    } else if ($sessionwaitlisted) { // Waitlist Not scheduled
                        $upcomingtbdarray[] = $sessiondata;
                    } else { // Normal scheduled session.
                        $upcomingarray[] = $sessiondata;
                    }
                }

                if (isset($sessiondata->signups)) {
                    $overallattendend += $sessiondata->attendend;
                    $overallsignups += $sessiondata->signups;
                }
            }
        }

        // $f2fsessions->upcoming = $upcomingarray;
        $f2fsessions->previous = $previousarray;
        $f2fsessions->overallattendend = $overallattendend;
        $f2fsessions->overallsignups = $overallsignups;
        // $f2fsessions->notscheduled = $upcomingtbdarray;
        // $f2fsessions->bookedsession = $bookedarray;
        // $f2fsessions->sessionid = $sessionid;
        // $f2fsessions->bookedsession = $bookedsession;
        return $f2fsessions;
    }

    public static function get_facetoface_data($facetoface, $f2fsession) {
        $f2fmodule = new stdClass();
        $f2fmodule->id = $facetoface->id;
        $f2fmodule->name = $facetoface->name;
        $f2fmodule->coursename = get_course($facetoface->course)->shortname;
        $f2fmodule->overallsignups = $f2fsession->overallsignups;
        $f2fmodule->overallattendend = $f2fsession->overallattendend;
        $f2fmodule->sessions = $f2fsession->previous;
        return $f2fmodule;
    }

    public static function get_session_data($session, $sessiondate) {
        $attendees = facetoface_get_attendees($session->id);
        $attended = 0;
        foreach ($attendees as $attendee) {
            if ($attendee->statuscode >= 90) {
                $attended++;
            }
        }

        $sessiondata = new stdClass();
        $sessiondata->id = $session->id;
        $sessiondata->date = strtoupper(date("d M y", $sessiondate->timestart));
        $sessiondata->time = date("h:i A", $sessiondate->timestart);
        $sessiondata->signups = count($attendees);
        $sessiondata->attendend = $attended;
        return $sessiondata;
    }
}