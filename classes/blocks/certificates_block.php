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
use cache;

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

        // Get response from cache
        $cache = cache::make('report_elucidsitereport', 'certificates');
        if (!$response = $cache->get('response')) {
            $response->data->customcerts = self::get_certificate_list();

            // Set cache for certificate response
            $cache->set('response', $response);
        }

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

        $sqlcm = "SELECT cm.id FROM {course_modules} cm
            JOIN {modules} m ON m.id = cm.module
            WHERE cm.course = ? AND cm.instance = ? AND m.name = ?";
        foreach ($customcert as $certificate) {
            $course = get_course($certificate->course);
            $coursecontext = context_course::instance($course->id);
            $cm = $DB->get_record_sql($sqlcm, array(
                $certificate->course,
                $certificate->id,
                "customcert"
            ));

            $modulecontext = context_module::instance($cm->id);
            $enrolledusers = get_enrolled_users($coursecontext);

            $issued = $DB->get_records('customcert_issues', array('customcertid' => $certificate->id));

            // Number of perople who can view certificates
            $notawareded = 0;
            foreach($enrolledusers as $user) {
                $canmanage = has_capability('mod/customcert:manage', $modulecontext, $user);
                // These people can manage the certificates
                if ($canmanage) {
                    continue;
                }
                // These people can only view the certificates
                $awarded = false;
                foreach ($issued as $issue) {
                    if ($issue->userid === $user->id) {
                        $awarded = true;
                    }
                }

                if (!$awarded) {
                    $notawareded++;
                }
            }

            $certificates[] = array(
                "id" => $certificate->id,
                "name" => $certificate->name,
                "coursename" => $course->fullname,
                "issued" => count($issued),
                "notissued" => $notawareded
            );
        }

        return $certificates;
    }

    /**
     * Get a certificates details for certificate page
     * @return [object] Certifcates details object
     */
    public static function get_issued_users($certid, $cohortid = false) {
        global $DB;

        $cache = cache::make('report_elucidsitereport', 'certificates');

        $cachekey = "certificates-userslist-" . $certid . "-";
        if ($cohortid) {
            $cachekey .= $cohortid;
        } else {
            $cachekey .= "all";
        }

        // Get certificates details from cache
        if (!$issuedcert = $cache->get($cachekey)) {
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

            // Set cache for issued certificates
            $cache->set($cachekey, $issuedcert);
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
        if ($grade) {
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
        $certinfo->grade = number_format($gradeval, 2);
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
        $cohortid = optional_param("cohortid", 0, PARAM_INT);
        $record = self::get_issued_users($certid, $cohortid);

        foreach($record->data as $key => $user) {
            $user->courseprogress = strip_tags($user->courseprogress);
            $users[$key] = array_values((array) $user);
        }

        $out = array_merge(array(
            self::get_headers_report()
        ), $users);
        return $out;
    }
}