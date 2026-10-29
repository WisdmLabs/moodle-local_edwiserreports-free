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
 * Insight cards logic for totalcoursesenrolled insight.
 *
 * @package     local_edwiserreports
 * @copyright   2022 wisdmlabs <support@wisdmlabs.com>
 * @author      Yogesh Shirsath
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserreports\insights;

use local_edwiserreports\block_base;
use context_helper;
use context_system;
use context_course;

/**
 * Trait for totalcoursesenrolled
 */
trait totalcoursesenrolled {

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
    public function get_totalcoursesenrolled_data(
        $startdate,
        $enddate,
        $oldstartdate,
        $oldenddate
    ) {
        $blockbase = new block_base();
        $userid = $blockbase->get_current_user();
        // Admin or Manager.
        if (is_siteadmin($userid) || has_capability('moodle/site:configview', context_system::instance(), $userid)) {
            $courses = get_courses();
            unset($courses[SITEID]);
        } else {
            $courses = enrol_get_all_users_courses($userid);
        }

        $count = 0;
        // Preload contexts and check visibility.
        foreach ($courses as $id => $course) {
            context_helper::preload_from_record($course);
            if (!$course->visible) {
                unset($courses[$id]);
                continue;
            }
            $count++;
        }

        return [$count, $count];
    }
}
