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
 * Insight cards logic for coursecompleted insight.
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
 * Trait for coursecompleted
 */
trait coursecompleted {

    /**
     * Get completions data in given period.
     *
     * @param int $startdate Start date timestamp.
     * @param int $enddate   End date timestamp.
     * @param int $userid    User id.
     *
     * @return int
     */
    private function get_users_completions($startdate, $enddate, $userid) {
        global $DB;
        $sql = "SELECT COUNT(ecp.completiontime)
                  FROM {edwreports_course_progress} ecp
                  WHERE FLOOR(ecp.completiontime / 86400) >= :starttime
                    AND FLOOR(ecp.completiontime / 86400) <= :endtime
                    AND ecp.userid = :userid";
        $params = array(
            'starttime' => $startdate,
            'endtime' => $enddate,
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
    public function get_coursecompleted_data(
        $startdate,
        $enddate,
        $oldstartdate,
        $oldenddate
    ) {
        $blockbase = new block_base();
        $userid = $blockbase->get_current_user();

        $currentcompletions = $this->get_users_completions($startdate, $enddate, $userid);
        $oldcompletions = $this->get_users_completions($oldstartdate, $oldenddate, $userid);

        return [$currentcompletions, $oldcompletions];
    }
}
