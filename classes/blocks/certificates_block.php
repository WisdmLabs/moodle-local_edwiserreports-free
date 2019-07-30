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
use context_course;
use core_user;
use html_writer;

require_once($CFG->dirroot.'/grade/report/grader/lib.php');

/**
 * Class Certifictes Block
 * To get the data for certificates
 */
class certificates_block extends utility {
    public static function get_data() {
        $response = new stdClass();
        $response->data = new stdClass();
        $response->data->customcerts = self::get_certificates();
        return $response;
    }

    public static function get_certificates() {
        global $DB;

        $certificates = array();
        $customcert = $DB->get_records("customcert", array());

        foreach ($customcert as $certificate) {
            $course = get_course($certificate->course);
            $cm = $DB->get_record("course_modules", array(
                "course" => $certificate->course,
                "instance" => $certificate->id
            ));

            $context = context_module::instance($cm->id);
            $enrolledusers = get_enrolled_users(context_course::instance($course->id));
            $canmanage = has_capability('mod/customcert:manage', $context);

            $cangetcertificates = 0;
            for ($i = 0; $i < count($enrolledusers); $i++) {
                if ($canmanage) {
                    continue;
                }
                $cangetcertificates++;
            }

            $issued = $DB->get_records('customcert_issues', array('customcertid' => $certificate->id));

            $certificates[] = array(
                "id" => $certificate->id,
                "name" => $certificate->name,
                "coursename" => $course->fullname,
                "issued" => count($issued),
                "notissued" => $cangetcertificates = count($issued)
            );
        }

        return $certificates;
    }

    public static function get_certificate($certid) {
        global $DB;
        $certificate = $DB->get_record("customcert", array("id" => $certid));
        $course = get_course($certificate->course);
        $coursecontext = context_course::instance($certificate->course);
        $issued = $DB->get_records('customcert_issues', array('customcertid' => $certid));

        $sql = "SELECT *
                FROM {user_enrolments} ue
                JOIN {enrol} e ON (e.id = ue.enrolid AND e.courseid = :courseid)
                JOIN {user} u ON u.id = ue.userid
                WHERE ue.userid = :userid AND u.deleted = 0";

        // please note that we must fetch all grade_grades fields if we want to construct grade_grade object from it!
        $sql = "SELECT g.*
                  FROM {grade_items} gi,
                       {grade_grades} g
                 WHERE g.itemid = gi.id
                 AND gi.courseid = :courseid
                 AND g.userid = :userid
                 AND gi.itemtype = 'course'";

        $issuedcert = array();
        foreach ($issued as $issue) {
            $certinfo = array();
            $user = core_user::get_user($issue->userid);

            $params = array('courseid'=>$course->id, 'userid' => $issue->userid);
            $gradeval = 0;
            $grade = $DB->get_record_sql($sql, $params);
            if (!$grade) {
                $gradeval = $grade->finalgrade;
            }

            $enrolment = $DB->get_record_sql($sql, $params);

            $enrolmentdate = get_string("notenrolled", "report_elucidsitereport");
            $progressper = 0;
            if ($enrolment) {
                $enrolmentdate = date("d M y", $enrolment->timemodified);
                $completion = self::get_course_completion_info($course, $user->id);

                if (isset($completion[progresspercentage])) {
                    $progressper = $completion[progresspercentage];
                }
            }

            $courseprogresshtml = html_writer::div(
                html_writer::span(
                    "$progressper %",
                    "pie-progress-number"
                ),
                "pie-progress pie-progress-xs",
                array(
                    "role" => "progressbar",
                    "data-goal" => "$progressper",
                    "aria-valuenow" => "$progressper",
                    "data-barcolor" => "#28c0de",
                    "aria-valuemin" => "0",
                    "aria-valuemax" => "100"
                )
            );

            $certinfo = array(
                "username" => fullname($user),
                "email" => $user->email,
                "issuedate" => date("d M y", $issue->timecreated),
                "dateenrolled" => $enrolmentdate,
                "grade" => number_format($grade->finalgrade, 2),
                "courseprogress" => $courseprogresshtml
            );
            $issuedcert[] = array_values($certinfo);
        }

        return $issuedcert;
    }
}