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
 * eLucid Report
 * @package    report_elucidsitereport
 * @copyright  (c) 2018 WisdmLabs (https://wisdmlabs.com/)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_elucidsitereport\controller;

use moodle_url;
use context_course;

// use report_elucidsitereport\utility;
defined('MOODLE_INTERNAL') || die();

/**
 * Handles requests regarding all ajax operations.
 *
 * @package   report_elucidsitereport
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class elucidsitereportController extends controllerAbstract
{
    /**
     * Do any security checks needed for the passed action
     *
     * @param string $action
     */
    public function require_capability($action) {
        $action = $action;
    }

    public function get_activeusers_graph_data_ajax_action() {
        $data = json_decode(required_param('data', PARAM_RAW));
        echo json_encode(\report_elucidsitereport\utility::get_active_users_data($data));
    }

    public function get_activecourses_data_ajax_action() {
        echo json_encode(\report_elucidsitereport\utility::get_active_courses_data($data));
    }

    public function get_courseprogress_graph_data_ajax_action() {
        $data = json_decode(required_param('data', PARAM_RAW));
        echo json_encode(\report_elucidsitereport\utility::get_course_progress_data($data));
    }

    public function get_f2fsession_data_ajax_action() {
        echo json_encode(\report_elucidsitereport\utility::get_f2fsessiondata_data());
    }

    public function get_certificates_data_ajax_action() {
        echo json_encode(\report_elucidsitereport\utility::get_certificates_data());
    }

    public function get_liveusers_data_ajax_action() {
        echo json_encode(\report_elucidsitereport\utility::get_liveusers_data());
    }

    public function get_siteaccess_data_ajax_action() {
        echo json_encode(\report_elucidsitereport\utility::get_siteaccess_data());
    }

    public function get_todaysactivity_data_ajax_action() {
        echo json_encode(\report_elucidsitereport\utility::get_todaysactivity_data());
    }

    public function get_lpstats_data_ajax_action() {
        $data = json_decode(required_param('data', PARAM_RAW));
        echo json_encode(\report_elucidsitereport\utility::get_lpstats_data($data));
    }

    public function get_inactiveusers_data_ajax_action() {
        $data = json_decode(required_param('data', PARAM_RAW));
        echo json_encode(\report_elucidsitereport\utility::get_inactiveusers_data($data));
    }
}
