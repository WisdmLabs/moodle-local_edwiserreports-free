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
 * Insight cards logic for activeusers insight.
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
 * Trait for activeusers
 */
trait activeusers {

    /**
     * Get active users in given period.
     *
     * @param int    $startdate  Start date.
     * @param int    $enddate    End date.
     * @param string $userstable Temporary users table
     *
     * @return int
     */
    private function get_activeusers($startdate, $enddate, $userstable) {
        global $DB;

        $sql = "SELECT DISTINCT l.userid
                  FROM {logstore_standard_log} l
                  JOIN {{$userstable}} ut ON l.userid = ut.tempid
                 WHERE l.action = :action
                   AND FLOOR(l.timecreated / 86400) >= :starttime
                   AND FLOOR(l.timecreated / 86400) <= :endtime
                   AND l.userid > 1";
        $params = array(
            'action' => 'viewed',
            'starttime' => $startdate,
            'endtime' => $enddate
        );

        $users = $DB->get_records_sql($sql, $params);
        return count($users);
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
    public function get_activeusers_data(
        $startdate,
        $enddate,
        $oldstartdate,
        $oldenddate
    ) {

        $blockbase = new block_base();

        $users = $blockbase->get_user_from_cohort_course_group(0, 0, 0, $blockbase->get_current_user());
        // Temporary users table.
        $userstable = utility::create_temp_table('tmp_i_au', array_keys($users));

        $currentactive = $this->get_activeusers($startdate, $enddate, $userstable);
        $oldactive = $this->get_activeusers($oldstartdate, $oldenddate, $userstable);

        // Drop temporary table.
        utility::drop_temp_table($userstable);

        return [$currentactive, $oldactive];
    }
}
