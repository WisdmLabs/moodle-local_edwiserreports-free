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
 * Trait implementing the external function local_edwiserreports_save_customreports_data.
 */
trait save_customreports_data {

    /**
     * Describes the structure of parameters for the function.
     *
     * @return external_function_parameters
     */
    public static function save_customreports_data_parameters() {
        return new external_function_parameters(
            array (
                'params' => new external_value(PARAM_RAW, 'Prameters', 'local_edwiserreports')
            )
        );
    }

    /**
     * Save Custom Reports data
     *
     * @param  string $params Plugin name
     * @return object             Configurations
     */
    public static function save_customreports_data($params) {
        global $DB, $USER;
        $params = json_decode($params);
        $timenow = time();
        $table = 'edwreports_custom_reports';
        $response = array(
            "success" => true,
            "reportsid" => 0,
            "errormsg" => ''
        );

        $customreports = new stdClass();
        $customreports->fullname = $params->reportname;
        $customreports->shortname = $params->reportshortname;
        $customreports->createdby = $USER->id;
        $params->querydata->downloadenable = $params->downloadenable;
        $customreports->data = json_encode($params->querydata);

        if ($DB->record_exists($table, array('shortname' => $customreports->shortname))) {
            $response["success"] = false;
            $response["errormsg"] = get_string('shortnameexist', 'local_edwiserreports');
        } else {
            // If id is present then update the records.
            if ($params->id) {
                $reportsid = $customreports->id = $params->id;
                $customreports->timemodified = $timenow;
                $DB->update_record($table, $customreports);
            } else {
                $customreports->timecreated = $timenow;
                $customreports->timemodified = 0;
                $reportsid = $DB->insert_record($table, $customreports);
            }
            $response["reportsid"] = $reportsid;
        }

        return $response;
    }

    /**
     * Describes the structure of the function return value.
     *
     * @return external_single_structure
     */
    public static function save_customreports_data_returns() {
        return new \external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, 'Status', null),
                'reportsid' => new external_value(PARAM_INT, 'Custom Reports Id', 0),
                'errormsg' => new external_value(PARAM_TEXT, 'ERROR message if any', '')
            )
        );
    }
}
