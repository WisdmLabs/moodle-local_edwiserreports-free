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
 * Insight cards logic for activitiescompleted insight.
 *
 * @package     local_edwiserreports
 * @copyright   2022 wisdmlabs <support@wisdmlabs.com>
 * @author      Yogesh Shirsath
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserreports\insights;

use local_edwiserreports\block_base;

/**
 * Trait for activitiescompleted
 */
trait activitiescompleted {

    /**
     * Get activities completed by current user in given period
     *
     * @param int $startdate Start date.
     * @param int $enddate   End date.
     * @param int $userid    User id.
     *
     * @return void
     */
    private function get_users_activities_completed($startdate, $enddate, $userid) {
        global $DB;
        $sql = "SELECT COUNT(cmc.completionstate)
                FROM {course_modules_completion} cmc
                WHERE cmc.completionstate = 1
                AND FLOOR(cmc.timemodified / 86400) >= :startdate
                AND FLOOR(cmc.timemodified / 86400) <= :enddate
                AND cmc.userid = :userid";
        $params = array(
            'startdate' => $startdate,
            'enddate' => $enddate,
            'userid' => $userid
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
    public function get_activitiescompleted_data(
        $startdate,
        $enddate,
        $oldstartdate,
        $oldenddate
    ) {
        $blockbase = new block_base();
        $userid = $blockbase->get_current_user();

        $activitiescompleted = $this->get_users_activities_completed($startdate, $enddate, $userid);
        $oldactivitiescompleted = $this->get_users_activities_completed($oldstartdate, $oldenddate, $userid);

        return [$activitiescompleted, $oldactivitiescompleted];
    }
}
