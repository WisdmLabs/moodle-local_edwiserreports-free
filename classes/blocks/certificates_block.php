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
    /**
     * Get data for certificates block
     * @return [object] Response object for Certificates Block
     */
    public static function get_data() {
        $response = new stdClass();
        $response->data = new stdClass();
        $response->data->customcerts = self::get_certificate_list();
        return $response;
    }

    /**
     * Get all certificates list with details
     * for certificates block
     * @return [array] Array of Certifcates
     */
    public static function get_certificate_list() {
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

    /**
     * Get a certificates details for certificate page
     * @return [object] Certifcates details object
     */
    public static function get_issued_users($certid, $cohortid) {
        global $DB;
        $certificate = $DB->get_record("customcert", array("id" => $certid));
        $course = get_course($certificate->course);
        $issued = $DB->get_records('customcert_issues', array('customcertid' => $certid));

        $response = new stdClass();
        $issuedcert = array();
        foreach ($issued as $issue) {
            if ($cohortid) {
                $cohorts = cohort_get_user_cohorts($issue->userid);
                if (!array_key_exists($cohortid, $cohorts)) {
                    continue;
                }
            }

            $issuedcert[] = self::get_certinfo($course, $issue);
        }

        $response->data = $issuedcert;

        return $response;
    }

    /**
     * Get Certificate Information
     * @param [object] $course stdClass object of course
     * @param [object] $issued stdClass object of issued certificates
     * @return [object] Certificate information
     */
    public static function get_certinfo($course, $issue) {
        global $DB;

        $enrolsql = "SELECT *
            FROM {user_enrolments} ue
            JOIN {enrol} e ON (e.id = ue.enrolid AND e.courseid = :courseid)
            JOIN {user} u ON u.id = ue.userid
            WHERE ue.userid = :userid AND u.deleted = 0";

        $certinfo = array();
        $user = core_user::get_user($issue->userid);

        $params = array('courseid'=>$course->id, 'userid' => $issue->userid);
        $gradeval = 0;
        $grade = self::get_grades($course->id, $issue->userid);
        if (!$grade) {
            $gradeval = $grade->finalgrade;
        }

        $enrolment = $DB->get_record_sql($enrolsql, $params);
        $enrolmentdate = get_string("notenrolled", "report_elucidsitereport");
        $progressper = 0;
        if ($enrolment) {
            $enrolmentdate = date("d M y", $enrolment->timemodified);
            $completion = self::get_course_completion_info($course, $user->id);

            if (isset($completion["progresspercentage"])) {
                $progressper = $completion["progresspercentage"];
            }
        }

        /* Pie Progress for Course Progress */
        $courseprogresshtml = html_writer::div(
            html_writer::span(
                $progressper . "%",
                "pie-progress-number font-size-14"
            ),
            "pie-progress pie-progress-xs",
            array(
                "data-plugin" => "pieProgress",
                "role" => "progressbar",
                "data-goal" => "$progressper",
                "aria-valuenow" => "$progressper",
                "data-barcolor" => "#28c0de",
                "aria-valuemin" => "0",
                "aria-valuemax" => "100",
                "data-barsize" => "2",
                "data-size" => "60",
            )
        );

        /* Certificates Object */
        $certinfo = new stdClass();
        $certinfo->username = fullname($user);
        $certinfo->email = $user->email;
        $certinfo->issuedate = date("d M y", $issue->timecreated);
        $certinfo->dateenrolled = $enrolmentdate;
        $certinfo->grade = number_format($grade->finalgrade, 2);
        $certinfo->courseprogress = $courseprogresshtml;
        return $certinfo;
    }

    /**
     * Get headers for certificates block
     * @return [array] Array of headers of certificates block
     */
    public static function get_headers() {
        $headers = array(
            get_string("name", "report_elucidsitereport"),
            get_string("coursename", "report_elucidsitereport"),
            get_string("issued", "report_elucidsitereport"),
            get_string("notissued", "report_elucidsitereport")
        );
        return $headers;
    }

    /**
     * Get headers for certificates block
     * @return [array] Array of headers of certificates block
     */
    public static function get_headers_report() {
        $headers = array(
            get_string("username", "report_elucidsitereport"),
            get_string("useremail", "report_elucidsitereport"),
            get_string("dateofissue", "report_elucidsitereport"),
            get_string("dateofenrol", "report_elucidsitereport"),
            get_string("grade", "report_elucidsitereport"),
            get_string("courseprogress", "report_elucidsitereport")
        );
        return $headers;
    }

    /**
     * Get exportable data for certificatesblock
     * @return [array] Array certificates information
     */
    public static function get_exportable_data_block() {
        $certificates = self::get_certificate_list();
        foreach($certificates as $key => $certificate) {
            unset($certificate["id"]);
            $certificates[$key] = array_values($certificate);
        }

        $certificates = array_merge(
            array(self::get_headers()),
            $certificates
        );
        return $certificates;
    }

    /**
     * Get exportable data for certificates report
     * @return [array] Array certificates information
     */
    public static function get_exportable_data_report($certid) {
        $users = self::get_issued_users($certid);

        foreach($users as $c => $user) {
            foreach($user as $r => $userinfo) {
                $users[$c][$r] = strip_tags($userinfo);
            }
        }

        $out = array_merge(
            array(
                self::get_headers_report()
            ),
            $users
        );
        return $out;
    }
}