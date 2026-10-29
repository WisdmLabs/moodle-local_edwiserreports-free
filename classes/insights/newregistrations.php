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
 * Insight cards logic for newregistrations insight.
 *
 * @package     local_edwiserreports
 * @copyright   2022 wisdmlabs <support@wisdmlabs.com>
 * @author      Yogesh Shirsath
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserreports\insights;

/**
 * Trait for newregistrations
 */
trait newregistrations {

    /**
     * Get new registration insight data
     *
     * @param int   $startdate      Start date.
     * @param int   $enddate        End date.
     * @param int   $oldstartdate   Old start date.
     * @param int   $oldenddate     Old end date.
     *
     * @return array
     */
    public function get_newregistrations_data(
        $startdate,
        $enddate,
        $oldstartdate,
        $oldenddate
    ) {
        global $DB;
        $sql = "SELECT COUNT(id)
                FROM {user}
                WHERE FLOOR(timecreated / 86400) >= ?
                AND FLOOR(timecreated / 86400) <= ?";

        $currentregistrations = $DB->get_field_sql($sql, [$startdate, $enddate]);
        $oldregistrations = $DB->get_field_sql($sql, [$oldstartdate, $oldenddate]);

        return [$currentregistrations, $oldregistrations];
    }
}
