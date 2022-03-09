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
 * Insight cards logic for timespentonsite insight.
 *
 * @package     local_edwiserreports
 * @copyright   2022 wisdmlabs <support@wisdmlabs.com>
 * @author      Yogesh Shirsath
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserreports\insights;

use local_edwiserreports\block_base;

/**
 * Trait for timespentonsite
 */
trait timespentonsite {

    /**
     * Get user's timespent on site in give time period.
     *
     * @param int $startdate Start date.
     * @param int $enddate   End date.
     * @param int $userid    User id.
     *
     * @return void
     */
    private function get_users_timespent_on_site($startdate, $enddate, $userid) {
        global $DB;
        $sql = "SELECT SUM(timespent)
                FROM {edwreports_activity_log}
                WHERE datecreated >= :startdate
                  AND datecreated <= :enddate
                  AND userid = :userid";
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
    public function get_timespentonsite_data(
        $startdate,
        $enddate,
        $oldstartdate,
        $oldenddate
    ) {
        $blockbase = new block_base();
        $userid = $blockbase->get_current_user();

        $timespentonsite = $this->get_users_timespent_on_site(floor($startdate / 86400), floor($enddate / 86400), $userid);
        $oldtimespentonsite = $this->get_users_timespent_on_site(floor($oldstartdate / 86400), floor($oldenddate / 86400), $userid);

        return [$timespentonsite, $oldtimespentonsite];
    }
}
