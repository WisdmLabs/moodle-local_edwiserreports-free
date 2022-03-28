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
 * Insight cards logic for timespentoncourses insight.
 *
 * @package     local_edwiserreports
 * @copyright   2022 wisdmlabs <support@wisdmlabs.com>
 * @author      Yogesh Shirsath
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserreports\insights;

use local_edwiserreports\block_base;
use local_edwiserreports\utility;

/**
 * Trait for timespentoncourses
 */
trait timespentoncourses {

    /**
     * Get students timespent on courses in given period.
     *
     * @param int    $startdate   Start date.
     * @param int    $enddate     End date.
     * @param string $coursetable Course table.
     *
     * @return int
     */
    private function get_timespent_on_courses($startdate, $enddate, $coursetable) {
        global $DB;

        $sql = "SELECT SUM(eal.timespent)
                FROM {edwreports_activity_log} eal
                JOIN {{$coursetable}} c ON eal.course = c.id
                WHERE eal.datecreated >= :startdate
                  AND eal.datecreated <= :enddate";
        $params = array(
            'startdate' => $startdate,
            'enddate' => $enddate
        );
        return $DB->get_field_sql($sql, $params);
    }

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
    public function get_timespentoncourses_data(
        $startdate,
        $enddate,
        $oldstartdate,
        $oldenddate
    ) {

        $blockbase = new block_base();
        $userid = $blockbase->get_current_user();
        $courses = $blockbase->get_courses_of_user($userid);
        // Temporary course table.
        $coursetable = 'tmp_insight_courses_' . $userid;
        // Creating temporary table.
        utility::create_temp_table($coursetable, array_keys($courses));

        $currenttimespent = $this->get_timespent_on_courses(floor($startdate / 86400), floor($enddate / 86400), $coursetable);
        $oldtimespent = $this->get_timespent_on_courses(floor($oldstartdate / 86400), floor($oldenddate / 86400), $coursetable);

        // Drop temporary table.
        utility::drop_temp_table($coursetable);

        return [$currenttimespent, $oldtimespent];
    }
}
