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
 * Reports block external apis
 *
 * @package     local_edwiserreports
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserreports\external;

defined('MOODLE_INTERNAL') || die();

use external_function_parameters;
use external_value;
use stdClass;

require_once($CFG->libdir.'/externallib.php');
/**
 * Trait implementing the external function local_edwiserreports_delete_custom_report.
 */
trait delete_custom_report {

    /**
     * Describes the structure of parameters for the function.
     *
     * @return external_function_parameters
     */
    public static function delete_custom_report_parameters() {
        return new external_function_parameters(
            array (
                'params' => new external_value(PARAM_RAW, 'Prameters', 'local_edwiserreports')
            )
        );
    }

    /**
     * Get Custom Reports data
     *
     * @param  string $params Plugin name
     * @return object             Configurations
     */
    public static function delete_custom_report($params) {
        global $DB;

        $params = json_decode($params);
        $reportsid = $params->reportsid;
        $reportsname = 'customreportsblock-' . $reportsid;

        $ret = $DB->delete_records('edwreports_custom_reports', array('id' => $reportsid));
        if ($ret) {
            $reportscompare = $DB->sql_compare_text('name');
            $cmpsql = $reportscompare . " LIKE '" . $reportsname . "'";
            $DB->delete_records_select('user_preferences', $cmpsql);
            $DB->delete_records_select('config_plugins', $cmpsql);

            $reportscompare = $DB->sql_compare_text('blockname');
            $cmpsql = $reportscompare . " LIKE '" . $reportsname . "'";
            $DB->delete_records_select('edwreports_schedemails', $cmpsql);
        }

        $successmsg = get_string('deletesuccess', 'local_edwiserreports');
        $failmsg = get_string('deletefailed', 'local_edwiserreports');
        $response = array();
        $response["success"] = $ret;
        $response["message"] = $ret ? $successmsg : $failmsg;
        return $response;
    }

    /**
     * Describes the structure of the function return value.
     *
     * @return external_single_structure
     */
    public static function delete_custom_report_returns() {
        return new \external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, 'Status', null),
                'message' => new external_value(PARAM_TEXT, 'Message', '')
            )
        );
    }
}
