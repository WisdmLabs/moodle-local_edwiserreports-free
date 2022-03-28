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

defined('MOODLE_INTERNAL') || die();

$files = scandir($CFG->dirroot . "/local/edwiserreports/classes/blocks/");
unset($files[0]);
unset($files[1]);
foreach ($files as $file) {
    require_once($CFG->dirroot . "/local/edwiserreports/classes/blocks/" . $file);
}

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

    /**
     * Get active users graph data ajax action
     */
    public function get_activeusers_graph_data_ajax_action() {
        $data = json_decode(required_param('data', PARAM_RAW));
        echo json_encode(\local_edwiserreports\utility::get_active_users_data($data));
    }

    /**
     * Get active courses data ajax action
     */
    public function get_activecourses_data_ajax_action() {
        echo json_encode(\local_edwiserreports\utility::get_active_courses_data());
    }

    /**
     * Get course progress graph data ajax action
     */
    public function get_courseprogress_graph_data_ajax_action() {
        $data = json_decode(required_param('data', PARAM_RAW));
        echo json_encode(\local_edwiserreports\utility::get_course_progress_data($data));
    }

    /**
     * Get certificates data ajax action
     */
    public function get_certificates_data_ajax_action() {
        $data = json_decode(optional_param('data', false, PARAM_RAW));
        echo json_encode(\local_edwiserreports\utility::get_certificates_data($data));
    }

    /**
     * Get live users data ajax action
     */
    public function get_liveusers_data_ajax_action() {
        echo json_encode(\local_edwiserreports\utility::get_liveusers_data());
    }

    /**
     * Get site access data ajax action
     */
    public function get_siteaccess_data_ajax_action() {
        echo json_encode(\local_edwiserreports\utility::get_siteaccess_data());
    }

    /**
     * Get todays activity data ajax action
     */
    public function get_todaysactivity_data_ajax_action() {
        $data = json_decode(required_param('data', PARAM_RAW));
        echo json_encode(\local_edwiserreports\utility::get_todaysactivity_data($data));
    }

    /**
     * Get inactive users data ajax action
     */
    public function get_inactiveusers_data_ajax_action() {
        $data = json_decode(required_param('data', PARAM_RAW));
        echo json_encode(\local_edwiserreports\utility::get_inactiveusers_data($data));
    }

    /**
     * Get course engage data ajax action
     */
    public function get_courseengage_data_ajax_action() {
        $cohortid = json_decode(optional_param('cohortid', 0, PARAM_RAW));
        echo json_encode(\local_edwiserreports\utility::get_courseengage_data($cohortid));
    }

    /**
     * Get completion data ajax action
     */
    public function get_completion_data_ajax_action() {
        $data = json_decode(required_param('data', PARAM_RAW));
        echo json_encode(\local_edwiserreports\utility::get_completion_data($data));
    }

    /**
     * Get course analytics data ajax action
     */
    public function get_courseanalytics_data_ajax_action() {
        $data = json_decode(required_param('data', PARAM_RAW));
        echo json_encode(\local_edwiserreports\utility::get_courseanalytics_data($data));
    }

    /**
     * Get scheduled emails ajax action
     */
    public function get_scheduled_emails_ajax_action() {
        $data = json_decode(required_param('data', PARAM_RAW));
        echo json_encode(\local_edwiserreports\utility::get_scheduled_emails($data));
    }

    /**
     * Get scheduled email detail ajax action
     */
    public function get_scheduled_email_detail_ajax_action() {
        $data = json_decode(required_param('data', PARAM_RAW));
        echo json_encode(\local_edwiserreports\utility::get_scheduled_email_details($data));
    }

    /**
     * Delete scheduled mail ajax action
     */
    public function delete_scheduled_email_ajax_action() {
        $data = json_decode(required_param('data', PARAM_RAW));
        echo json_encode(\local_edwiserreports\utility::delete_scheduled_email($data));
    }

    /**
     * Change scheduled email status ajax action
     */
    public function change_scheduled_email_status_ajax_action() {
        $data = json_decode(required_param('data', PARAM_RAW));
        echo json_encode(\local_edwiserreports\utility::change_scheduled_email_status($data));
    }

    /**
     * Get course reports selectors
     */
    public function get_customreport_selectors_ajax_action() {
        // Get filters.
        $filter = json_decode(required_param('filter', PARAM_RAW));

        // Response for ajax action.
        echo json_encode(\local_edwiserreports\utility::get_customreport_selectors($filter));
    }

    /**
     * Get custom query report data ajax action
     *
     * @return void
     */
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

    /**
     * Get graph data for courseprogress on course graph of learner block.
     */
    public function get_learnercourseprogress_graph_data_ajax_action() {
        $learnercourseprogress = new \local_edwiserreports\learnercourseprogressblock();

        // Response for ajax action.
        echo json_encode($learnercourseprogress->get_data());
    }

    /**
     * Get graph data for courseprogress on course graph of learner block.
     */
    public function get_learnertimespentonsite_graph_data_ajax_action() {
        $learnertimespentonsite = new \local_edwiserreports\learnertimespentonsiteblock();

        // Response for ajax action.
        echo json_encode($learnertimespentonsite->get_data());
    }

    /**
     * Check if plugin is installed.
     *
     * @return boolean
     */
    public function is_installed_ajax_action() {
        echo json_encode([
            'installed' => get_config('local_edwiserreports', 'version') !== false
        ]);
    }

    /**
     * Get data for grade graph
     *
     * @return void
     */
    public function get_grade_graph_data_ajax_action() {
        $grade = new \local_edwiserreports\gradeblock();
        // Response for ajax action.
        echo json_encode($grade->get_graph_data());
    }

    /**
     * Get table data for Visits on Site graph.
     */
    public function get_visitsonsite_graph_data_ajax_action() {
        $visitsonsite = new \local_edwiserreports\visitsonsiteblock();

        // Response for ajax action.
        echo json_encode($visitsonsite->get_data());
    }

    /**
     * Get table data for Timespent on site graph.
     */
    public function get_timespentonsite_graph_data_ajax_action() {

        $timespentonsite = new \local_edwiserreports\timespentonsiteblock();

        // Response for ajax action.
        echo json_encode($timespentonsite->get_data());
    }

    /**
     * Get table data for Timespent on Course graph.
     */
    public function get_timespentoncourse_graph_data_ajax_action() {
        $timespentoncourse = new \local_edwiserreports\timespentoncourseblock();

        // Response for ajax action.
        echo json_encode($timespentoncourse->get_data());
    }

    /**
     * Get table data for Course activity status graph.
     */
    public function get_courseactivitystatus_graph_data_ajax_action() {
        $courseactivitystatus = new \local_edwiserreports\courseactivitystatusblock();

        // Response for ajax action.
        echo json_encode($courseactivitystatus->get_data());
    }

    /**
     * Get insight card context to render insight card.
     */
    public function get_insight_card_context_ajax_action() {
        // Get data.
        $data = json_decode(required_param('data', PARAM_RAW));

        $insight = new \local_edwiserreports\insights\insight();

        // Response for ajax action.
        echo json_encode($insight->get_card_context($data->id));
    }

    /**
     * Get insight card data to render insight.
     */
    public function get_insight_card_data_ajax_action() {
        // Get data.
        $data = json_decode(required_param('data', PARAM_RAW));

        $insight = new \local_edwiserreports\insights\insight();

        // Response for ajax action.
        echo json_encode($insight->get_card_data(
            $data->id,
            $data->filter
        ));
    }
}
