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
use context_system;

require_once($CFG->libdir.'/externallib.php');
/**
 * Trait implementing the external function local_edwiserreports_complete_edwiserreports_installation.
 */
trait complete_edwiserreports_installation {

    /**
     * Describes the structure of parameters for the function.
     *
     * @return external_function_parameters
     */
    public static function complete_edwiserreports_installation_parameters() {
        return new external_function_parameters(
            array ()
        );
    }

    /**
     * Complete edwiser report installation
     *
     * @return object Configuration
     */
    public static function complete_edwiserreports_installation() {
        $res = new stdClass();

        $configs = get_config('local_edwiserreports');
        if (!$configs && !$configs->edwiserreportsinstallation) {
            $res->success = false;
            $res->value  = '';
            return $res;
        }

        $context = context_system::instance();
        $capchanged = false;
        foreach ($configs as $key => $value) {
            if (strpos($key, 'roleallow') === false) {
                continue;
            }

            $blockname = str_replace('roleallow', '', $key);
            $allowroleids = explode(',', $value);
            $roles = get_all_roles();

            foreach (array_keys($roles) as $roleid) {
                $capname = 'report/edwiserreports_' . $blockname . 'block:view';
                if (is_array($allowroleids) && in_array($roleid, $allowroleids)) {
                    assign_capability($capname, CAP_ALLOW, $roleid, $context->id, true);
                } else {
                    assign_capability($capname, CAP_INHERIT, $roleid, $context->id, true);
                }
            }

            $capchanged = true;
        }

        if ($capchanged) {
            set_config('edwiserreportsinstallation', false, 'local_edwiserreports');
            $res->success = true;
        } else {
            $res->success = false;
        }
        $res->value  = '';
        return $res;
    }
    /**
     * Describes the structure of the function return value.
     *
     * @return external_single_structure
     */
    public static function complete_edwiserreports_installation_returns() {
        return new \external_single_structure(
            array(
                'success' => new external_value(PARAM_RAW, 'Success Status', null),
                'value' => new external_value(PARAM_RAW, 'Config Value', 0)
            )
        );
    }
}
