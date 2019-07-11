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
}