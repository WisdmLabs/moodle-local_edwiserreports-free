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
 * @copyright   2021 wisdmlabs <support@wisdmlabs.com>
 * @author      Yogesh Shirsath
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserreports\external;

defined('MOODLE_INTERNAL') || die();

use external_function_parameters;
use external_value;

require_once($CFG->libdir.'/externallib.php');
/**
 * Trait implementing the external function local_edwiserreports_get_tracking_details.
 */
trait get_tracking_details {

    /**
     * Describes the structure of parameters for the function.
     *
     * @return external_function_parameters
     */
    public static function get_tracking_details_parameters() {
        return new external_function_parameters(
            array (
                'contextid' => new external_value(PARAM_INT, 'Context id', VALUE_DEFAULT, 1)
            )
        );
    }

    /**
     * Get tracking details for keep alive requests.
     *
     * @param  int    $contextid Context id
     * @return object            Tracking details
     */
    public static function get_tracking_details($contextid) {
        $tracking = \local_edwiserreports\controller\tracking::instance($contextid);
        $status = true;
        $id = null;
        $frequency = $tracking->get_frequency();
        $id = $tracking->get_tracking_details();
        return [
            'status'    => $status,
            'id'        => $id,
            'frequency' => $frequency
        ];
    }
    /**
     * Describes the structure of the function return value.
     *
     * @return external_single_structure
     */
    public static function get_tracking_details_returns() {
        return new \external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'Status'),
                'id'     => new external_value(PARAM_INT, 'Tracking id'),
                'frequency' => new external_value(PARAM_INT, 'Frequency to track user time')
            )
        );
    }
}
