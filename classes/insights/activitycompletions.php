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
 * Insight cards logic for activitycompletions insight.
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
 * Trait for activitycompletions
 */
trait activitycompletions {

    /**
     * Get activities completed by current user in given period
     *
     * @param int    $startdate   Start date.
     * @param int    $enddate     End date.
     * @param string $coursetable Course table.
     *
     * @return int
     */
    private function get_activitycompletions($startdate, $enddate, $coursetable) {
        global $DB;
        $sql = "SELECT COUNT(cmc.completionstate)
                FROM {course_modules_completion} cmc
                JOIN {course_modules} cm ON cm.id = cmc.coursemoduleid
                JOIN {{$coursetable}} c ON c.tempid = cm.course
                WHERE cmc.completionstate = 1
                AND cmc.timemodified >= :startdate
                AND cmc.timemodified <= :enddate";
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
    public function get_activitycompletions_data(
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

        $activitiescompleted = $this->get_activitycompletions($startdate, $enddate, $coursetable);
        $oldactivitiescompleted = $this->get_activitycompletions($oldstartdate, $oldenddate, $coursetable);

        // Drop temporary table.
        utility::drop_temp_table($coursetable);

        return [$activitiescompleted, $oldactivitiescompleted];
    }
}
