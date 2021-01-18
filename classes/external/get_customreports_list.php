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
 * Trait implementing the external function local_edwiserreports_get_customreports_data.
 */
trait get_customreports_list {

    /**
     * Describes the structure of parameters for the function.
     *
     * @return external_function_parameters
     */
    public static function get_customreports_list_parameters() {
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
    public static function get_customreports_list($params) {
        global $DB;
        $table = 'edwreports_custom_reports';
        $data = array();
        $count = 0;

        $sql = 'SELECT ecr.*, u.firstname, u.lastname
                FROM {edwreports_custom_reports} ecr
                JOIN {user} u
                ON u.id = ecr.createdby';
        $customreports = $DB->get_records_sql($sql);
        foreach ($customreports as $customreport) {
            $crdata = new stdClass();
            $crdata->sno = ++$count;
            $crdata->fullname = $customreport->fullname;
            $crdata->shortname = $customreport->shortname;
            $crdata->createdby = $customreport->firstname . ' ' . $customreport->lastname;
            $crdata->datecreated = date('d/M/Y', $customreport->timecreated);
            $crdata->managehtml = self::create_manage_html($customreport);
            $data[] = $crdata;
        }

        $response = array(
            "success" => true,
            "data" => json_encode($data)
        );

        return $response;
    }

    /**
     * Create manage HTML for custom reports.
     */
    private static function create_manage_html() {
        $querydata = json_decode($customreport->data);
        $html = '<div>
            <span class="icon">
                <input type="checkbox" id="enabledesktop-' . $customreport->id . '"
                    class="custom-field-checkbox" value="enabledesktop">
            </span>
            <a href="#">
                <i class="icon fa fa-cog text-primary"></i>
            </a>
            <a href="#">
                <i class="icon fa fa-trash text-danger"></i>
            </a>
        </div>';
        return $html;
    }

    /**
     * Describes the structure of the function return value.
     *
     * @return external_single_structure
     */
    public static function get_customreports_list_returns() {
        return new \external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, 'Status', null),
                'data' => new external_value(PARAM_RAW, 'Reports Data', 0)
            )
        );
    }
}
