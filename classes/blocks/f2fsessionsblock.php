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

namespace local_edwiserreports;

defined('MOODLE_INTERNAL') || die();

use stdClass;
use context_module;
use html_writer;
use core_user;
use moodle_url;

require_once($CFG->dirroot . '/local/edwiserreports/locallib.php');

/**
 * Class f2fsession Block
 * To get the data related to active users block
 */
class f2fsessionsblock extends block_base {
    /**
     * Preapre layout for active courses block
     */
    public function get_layout() {
        global $CFG;

        // Layout related data.
        $this->layout->id = 'f2fsessionsblock';
        $this->layout->name = get_string('f2fsessionsheader', 'local_edwiserreports');
        $this->layout->info = get_string('f2fsessionsblockhelp', 'local_edwiserreports');
        $this->layout->morelink = new moodle_url($CFG->wwwroot . "/local/edwiserreports/f2fsessions.php");
        $this->layout->hasdownloadlink = true;

        // Block related data.
        $this->block = new stdClass();

        // Add block view in layout.
        $this->layout->blockview = $this->render_block('f2fsessionsblock', $this->block);

        // Return blocks layout.
        return $this->layout;
    }

    /**
     * Get data for F2F session block
     */
    public function get_data($params = false) {
        $cohortid = isset($params->cohortid) ? $params->cohortid : false;
        $response = new stdClass();
        $response->data = new stdClass();
        $response->data->f2fmodules = self::get_f2fmodules($cohortid);
        return $response;
    }

    /**
     * Get face to face activities
     * @return array Array of all available face to face activities
     */
    public static function get_f2fmodules($cohortid = false) {
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
            $f2fsessions = self::get_f2fsessions($facetoface, $cohortid);

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
    public static function get_f2fsessions($facetoface, $cohortid) {
        global $CFG, $DB, $USER;

        require_once($CFG->dirroot . '/mod/facetoface/lib.php');

        // TODO: This needs to check the location.
        $location = '';
        $sessionid = false;
        $sessionwaitlisted = false;
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
                    $sessiondata = self::get_session_data($session, $sessiondate, $cohortid);
                    // Add custom fields to sessiondata.
                    $customdata = $DB->get_records(
                        'facetoface_session_data',
                        array('sessionid' => $session->id),
                        '',
                        'fieldid, data'
                        );

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
                    } else if ($sessionwaitlisted) { // Waitlist Not scheduled.
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
        if (!empty($sessions)) {
            $f2fsessions->previous = array_merge($previousarray, $upcomingarray);
            $f2fsessions->overallattendend = $overallattendend;
            $f2fsessions->overallsignups = $overallsignups;
        } else {
            $f2fsessions->previous = array();
            $f2fsessions->overallattendend = array();
            $f2fsessions->overallsignups = array();
        }
        return $f2fsessions;
    }

    /**
     * Get the required data from F2F Sessions
     * @param [object] $facetoface Face to face detail
     * @param [object] $f2fsession Face to face session detail
     * @return [object] Face to Face data
     */
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

    /**
     * Get the F2F Session data
     * @param [object] $session Session data
     * @param [string] $sessiondate Session Date
     * @return [object] Face to Face data
     */
    public static function get_session_data($session, $sessiondate, $cohortid) {
        global $CFG;
        require_once($CFG->dirroot . '/mod/facetoface/lib.php');
        $attendees = facetoface_get_attendees($session->id);
        $attended = 0;
        $waitlisted = 0;
        $declined = 0;
        $confirmed = 0;
        $status = "";
        foreach ($attendees as $key => $attendee) {
            if ($cohortid) {
                $cohorts = cohort_get_user_cohorts($attendee->id);
                if (!array_key_exists($cohortid, $cohorts)) {
                    unset($attendees[$key]);
                    continue;
                }
            }

            switch ($attendee->statuscode) {
                case MDL_F2F_STATUS_FULLY_ATTENDED:
                case MDL_F2F_STATUS_PARTILOCAL_SITEREPORT_ALLY_ATTENDED:
                    $attended++;
                    $status = html_writer::span(
                        get_string("attended", "local_edwiserreports"),
                        "badge badge-round badge-success"
                    );
                    break;
                case MDL_F2F_STATUS_WAITLISTED:
                case MDL_F2F_STATUS_REQUESTED:
                    $waitlisted++;
                    $status = html_writer::span(
                        get_string("requested", "local_edwiserreports"),
                        "badge badge-round badge-warning"
                    );
                    break;
                case MDL_F2F_STATUS_DECLINED:
                case MDL_F2F_STATUS_SESSION_CANCELLED:
                    $declined++;
                    $status = html_writer::span(
                        get_string("canceled", "local_edwiserreports"),
                        "badge badge-round badge-danger"
                    );
                    break;
                case MDL_F2F_STATUS_APPROVED:
                    $confirmed++;
                    $status = html_writer::span(
                        get_string("approved", "local_edwiserreports"),
                        "badge badge-round badge-dark"
                    );
                    break;
                default:
                    $status = html_writer::span(
                        get_string("booked", "local_edwiserreports"),
                        "badge badge-round badge-primary"
                    );

            }
            $attendee->status = $status;
        }

        $canceledusers = self::get_canceled_sessionsdata($session->id, $cohortid);
        $allsignups = array_merge($attendees, $canceledusers);

        $sessiondata = new stdClass();
        $sessiondata->id = $session->id;
        $sessiondata->sessionid = $session->id."-".$sessiondate->timestart;
        $sessiondata->date = date("d M y", $sessiondate->timestart);
        $sessiondata->time = date("h:i A", $sessiondate->timestart);
        $sessiondata->signups = count($attendees);
        $sessiondata->attendend = $attended;
        $sessiondata->waitlisted = $waitlisted;
        $sessiondata->declined = $declined;
        $sessiondata->confirmed = $confirmed;
        $sessiondata->canceled = count($canceledusers);
        $sessiondata->users = array_values($allsignups);
        $downloadurl = $CFG->wwwroot."/local/edwiserreports/download.php";
        $sessiondata->exportlink->isindividual = true;
        return $sessiondata;
    }

    /**
     * Get Canceled Session Data
     * @param  [int] $sessionid Session Id
     * @param  [int] $cohortid Cohort Id
     * @return [array] Array of Canceled Session
     */
    public static function get_canceled_sessionsdata($sessionid, $cohortid) {
        global $DB;
        $sql = "SELECT ss.* FROM {facetoface_signups_status} ss
                JOIN {facetoface_signups} fs
                ON fs.id = ss.signupid
                JOIN {facetoface_sessions_dates} sd
                ON sd.sessionid = fs.sessionid
                WHERE ss.statuscode = :statuscode
                AND sd.sessionid = :sessionid";
        $params = array(
            "statuscode" => MDL_F2F_STATUS_USER_CANCELLED,
            "sessionid" => $sessionid
        );
        $records = $DB->get_records_sql($sql, $params);

        $canceled = array();
        foreach ($records as $record) {
            if ($cohortid) {
                $cohorts = cohort_get_user_cohorts($record->createdby);
                if (!array_key_exists($cohortid, $cohorts)) {
                    continue;
                }
            }

            $user = core_user::get_user($record->createdby, "id, firstname, lastname");
            $user->status = html_writer::span(
                get_string("canceled", "local_edwiserreports"),
                "badge badge-round badge-danger"
            );
            $user->reason = $record->note;
            $canceled[] = $user;
        }

        return $canceled;
    }

    /**
     * Get header for f2fsessions block
     * @return [array] Array of header f2fsessions block
     */
    public static function get_headers_report($filter = false) {
        if ($filter) {
            $header = array (
                get_string("username", "local_edwiserreports"),
                get_string("status", "local_edwiserreports"),
                get_string("reason", "local_edwiserreports")
            );
        } else {
            $header = array(
                get_string("date", "local_edwiserreports"),
                get_string("time", "local_edwiserreports"),
                get_string("name", "local_edwiserreports"),
                get_string("coursename", "local_edwiserreports"),
                get_string("signups", "local_edwiserreports"),
                get_string("attendees", "local_edwiserreports"),
                get_string("waitlist", "local_edwiserreports"),
                get_string("declined", "local_edwiserreports"),
                get_string("confirmed", "local_edwiserreports")
            );
        }

        return $header;
    }


    /**
     * Get exportable data for f2fsessions block
     * @return [array] Array f2fsessions information
     */
    public static function get_exportable_data_block() {
        $export = array();
        $export[] = self::get_headers_report();

        $modules = self::get_f2fmodules();
        foreach ($modules as $module) {
            $data = new stdClass();

            $data->name = $module->name;
            $data->coursename = $module->coursename;
            foreach ($module->sessions as $session) {
                $data->date = $session->date;
                $data->time = $session->time;
                $data->signups = $session->signups;
                $data->attendend = $session->attendend;
                $data->waitlisted = $session->waitlisted;
                $data->declined = $session->declined;
                $data->confirmed = $session->confirmed;
                $export[] = array_values((array)$data);
            }
        }
        return $export;
    }

    /**
     * Get exportable data for f2fsession block
     * @param [string] $filter Session Id
     * @return [array] Array certificates information
     */
    public static function get_exportable_data_report($filter) {
        $cohortid = optional_param("cohortid", 0, PARAM_INT);
        $export = array();
        $modules = self::get_f2fmodules($cohortid);
        $export[] = self::get_headers_report($filter);
        foreach ($modules as $module) {
            if ($filter) {
                $data = self::get_exportable_data_report_users($module, $filter);
            } else {
                $data = self::get_exportable_data_report_sessions($module);
            }
            $export = array_merge($export, $data);
        }
        return $export;
    }

    /**
     * Get Exportable data of users in a session
     * @param  [object] $module Session Module Object
     * @param  [string] $filter Session Id
     * @return [array] Users array of exportable data
     */
    public static function get_exportable_data_report_users($module, $filter) {
        $dataarray = array();
        foreach ($module->sessions as $session) {
            if ($session->sessionid !== $filter) {
                continue;
            }
            foreach ($session->users as $user) {
                $data = new stdClass();
                $data->fullname = $user->firstname . " " . $user->lastname;
                $data->status = strip_tags($user->status);
                $dataarray[] = array_values((array) $data);

                if (isset($user->reason)) {
                    $data->reason = $user->reason;
                }
            }
        }
        return $dataarray;
    }

    /**
     * Get Exportable data of session
     * @param  [object] $module Session Module Object
     * @return [array] Array of sessions
     */
    public static function get_exportable_data_report_sessions($module) {
        $dataarray = array();
        $data = new stdClass();
        $data->name = $module->name;
        $data->coursename = $module->coursename;
        foreach ($module->sessions as $session) {
            $data->date = $session->date;
            $data->time = $session->time;
            $data->signups = $session->signups;
            $data->attendend = $session->attendend;
            $data->waitlisted = $session->waitlisted;
            $data->declined = $session->declined;
            $data->confirmed = $session->confirmed;
            $dataarray[] = array_values((array)$data);
        }
        return $dataarray;
    }
}
