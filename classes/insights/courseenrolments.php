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
 * Insight cards logic for courseenrolments insight.
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
 * Trait for courseenrolments
 */
trait courseenrolments {

    /**
     * Get enrolments data in given period.
     *
     * @param int $startdate Start date timestamp.
     * @param int $enddate   End date timestamp.
     *
     * @return int
     */
    private function get_enrolments($startdate, $enddate, $coursetable) {
        global $DB;

        $sum = 0;
        $sql = "SELECT COUNT(DISTINCT(CONCAT(CONCAT(l.courseid, '-'), l.relateduserid))) as usercount
                  FROM {logstore_standard_log} l
                  JOIN {{$coursetable}} ct ON l.courseid = ct.tempid
                 WHERE l.eventname = :eventname
                   AND l.action = :actionname
                   AND l.timecreated >= :starttime
                   AND l.timecreated < :endtime
                 GROUP BY FLOOR(l.timecreated/86400)";
        $params = array(
            "eventname" => '\core\event\user_enrolment_created',
            "actionname" => "created",
            'starttime' => $startdate,
            'endtime' => $enddate
        );
        $enrolments = $DB->get_recordset_sql($sql, $params);

        if ($enrolments->valid()) {
            foreach ($enrolments as $enrolment) {
                $sum += $enrolment->usercount;
            }
        }
        return $sum;
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
    public function get_courseenrolments_data(
        $startdate,
        $enddate,
        $oldstartdate,
        $oldenddate
    ) {
        $blockbase = new block_base();
        $userid = $blockbase->get_current_user();
        $courses = $blockbase->get_courses_of_user($userid);
        // Temporary course table.
        $coursetable = utility::create_temp_table('tmp_insight_courses', array_keys($courses));

        $currentenrolments = $this->get_enrolments($startdate, $enddate, $coursetable);
        $oldenrolments = $this->get_enrolments($oldstartdate, $oldenddate, $coursetable);

        // Drop temporary table.
        utility::drop_temp_table($coursetable);

        return [$currentenrolments, $oldenrolments];
    }
}
