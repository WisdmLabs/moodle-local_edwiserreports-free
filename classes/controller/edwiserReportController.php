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
 * @package    local_edwiserreports
 * @copyright  (c) 2018 WisdmLabs (https://wisdmlabs.com/)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserreports\controller;

use moodle_url;
use context_course;

defined('MOODLE_INTERNAL') || die();

/**
 * Handles requests regarding all ajax operations.
 *
 * @package   local_edwiserreports
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edwiserReportController extends controllerAbstract {
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
        echo json_encode(\local_edwiserreports\utility::get_active_users_data($data));
    }

    public function get_activecourses_data_ajax_action() {
        echo json_encode(\local_edwiserreports\utility::get_active_courses_data());
    }

    public function get_courseprogress_graph_data_ajax_action() {
        $data = json_decode(required_param('data', PARAM_RAW));
        echo json_encode(\local_edwiserreports\utility::get_course_progress_data($data));
    }

    public function get_certificates_data_ajax_action() {
        $data = json_decode(optional_param('data', false, PARAM_RAW));
        echo json_encode(\local_edwiserreports\utility::get_certificates_data($data));
    }

    public function get_liveusers_data_ajax_action() {
        echo json_encode(\local_edwiserreports\utility::get_liveusers_data());
    }

    public function get_siteaccess_data_ajax_action() {
        echo json_encode(\local_edwiserreports\utility::get_siteaccess_data());
    }

    public function get_todaysactivity_data_ajax_action() {
        $data = json_decode(required_param('data', PARAM_RAW));
        echo json_encode(\local_edwiserreports\utility::get_todaysactivity_data($data));
    }

    public function get_inactiveusers_data_ajax_action() {
        $data = json_decode(required_param('data', PARAM_RAW));
        echo json_encode(\local_edwiserreports\utility::get_inactiveusers_data($data));
    }

    public function get_courseengage_data_ajax_action() {
        $cohortid = json_decode(optional_param('cohortid', 0, PARAM_RAW));
        echo json_encode(\local_edwiserreports\utility::get_courseengage_data($cohortid));
    }

    public function get_completion_data_ajax_action() {
        $data = json_decode(required_param('data', PARAM_RAW));
        echo json_encode(\local_edwiserreports\utility::get_completion_data($data));
    }

    public function get_courseanalytics_data_ajax_action() {
        $data = json_decode(required_param('data', PARAM_RAW));
        echo json_encode(\local_edwiserreports\utility::get_courseanalytics_data($data));
    }

    public function get_scheduled_emails_ajax_action() {
        $data = json_decode(required_param('data', PARAM_RAW));
        echo json_encode(\local_edwiserreports\utility::get_scheduled_emails($data));
    }

    public function get_scheduled_email_detail_ajax_action() {
        $data = json_decode(required_param('data', PARAM_RAW));
        echo json_encode(\local_edwiserreports\utility::get_scheduled_email_details($data));
    }

    public function delete_scheduled_email_ajax_action() {
        $data = json_decode(required_param('data', PARAM_RAW));
        echo json_encode(\local_edwiserreports\utility::delete_scheduled_email($data));
    }

    public function change_scheduled_email_status_ajax_action() {
        $data = json_decode(required_param('data', PARAM_RAW));
        echo json_encode(\local_edwiserreports\utility::change_scheduled_email_status($data));
    }

    /**
     * Get course reports selectors
     * @return string selectors data
     */
    public function get_customreport_selectors_ajax_action() {
        // Get filters.
        $filter = json_decode(required_param('filter', PARAM_RAW));

        // Response for ajax action.
        echo json_encode(\local_edwiserreports\utility::get_customreport_selectors($filter));
    }

    public function get_customqueryreport_data_ajax_action() {
        // Get data.
        $data = json_decode(required_param('data', PARAM_RAW));

        // Response for ajax action.
        echo json_encode(\local_edwiserreports\utility::get_lp_courses($data->lpids));
    }

    /**
     * Get custom query cohort users
     */
    public function get_customqueryreport_cohort_users_ajax_action() {
        // Get data.
        $data = json_decode(required_param('data', PARAM_RAW));

        // Response for ajax action.
        echo json_encode(\local_edwiserreports\utility::get_cohort_users($data->cohortids));
    }

    /**
     * Get custom query cohort users
     */
    public function set_block_preferences_ajax_action() {
        // Get data.
        $data = json_decode(required_param('data', PARAM_RAW));

        // Response for ajax action.
        echo json_encode(\local_edwiserreports\utility::set_block_preferences($data));
    }

    /**
     * Set custom query cohort users
     */
    public function set_block_capability_ajax_action() {
        // Get data.
        $data = json_decode(required_param('data', PARAM_RAW));

        // Response for ajax action.
        echo json_encode(\local_edwiserreports\utility::set_block_capability($data));
    }

    /**
     * Hide block
     */
    public function toggle_hide_block_ajax_action() {
        // Get data.
        $data = json_decode(required_param('data', PARAM_RAW));

        // Response for ajax action.
        echo json_encode(\local_edwiserreports\utility::toggle_hide_block($data));
    }
}
