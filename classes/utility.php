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

use stdClass;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . "/report/elucidsitereport/classes/blocks/active_users_block.php");
/**
 * Utilty class to add all utility function
 * to perform in the eLucid report plugin
 */
class utility {
    public static function get_active_users_data($data) {
        if (isset($data->filter)) {
            $filter = $data->filter;
        } else {
            $filter = 'weekly'; // Default filter
        }
        return \report_elucidsitereport\active_users_block::get_active_users_graph_data($filter);
    }
}