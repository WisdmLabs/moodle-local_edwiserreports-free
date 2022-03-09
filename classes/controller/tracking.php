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
 * This class has methods for time tracking.
 *
 * @package     local_edwiserreports
 * @category    controller
 * @copyright   2021 wisdmlabs <support@wisdmlabs.com>
 * @author      Yogesh Shirsath
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserreports\controller;

defined('MOODLE_INTERNAL') || die;

// Default frequency for time update is 5 minutes.
define('DEFAULT_FREQUENCY', 300);
define('DEFAULT_TIMESLOT', 300);

use stdClass;

class tracking {

    /**
     * Class instance
     *
     * @var tracking
     */
    private static $instance = null;

    /**
     * Context id of page for tracking.
     *
     * @var int
     */
    private $contextid = null;

    /**
     * Class constructor
     *
     * @param int $contextid Context id
     */
    private function __construct($contextid) {
        $this->contextid = $contextid;
    }

    /**
     * Method for singletone class
     *
     * @param int $contextid Context id
     *
     * @return tracking
     */
    public static function instance($contextid) {
        if (self::$instance == null) {
            self::$instance = new self($contextid);
        }
        return self::$instance;
    }

    /**
     * Get frequency to track user time.
     *
     * @return int
     */
    public function get_frequency() {
        $frequency = get_config('local_edwiserreports', 'trackfrequency');
        if (!$frequency) {
            $frequency = DEFAULT_FREQUENCY;
        }
        return $frequency;
    }

    /**
     * Get minimum time slot for time tracking window
     *
     * @return int
     */
    public function get_minimum_timeslot() {
        // This setting is commented for now
        // and going to reintroduce in future update.
        // $timeslot = get_config('local_edwiserreports', 'tracktimeslot');
        // if (!$timeslot) {
            // $timeslot = DEFAULT_TIMESLOT;
        // }
        // return $timeslot;
        return DEFAULT_TIMESLOT;
    }

    /**
     * Get activity_log table row id for tracking.
     *
     * @return int
     */
    public function get_tracking_details() {
        global $DB, $USER;
        $id = null;
        $time = time();
        $context = $DB->get_record('context', array('id' => $this->contextid));
        $track = new stdClass;
        $track->datecreated = floor($time / 86400);
        $track->userid = $USER->id;
        $track->course = 0;
        $track->activity = 0;
        $track->timestart = $time;
        $track->timespent = 0;
        $track->timetocomplete = 0;
        switch($context->contextlevel) {
            case CONTEXT_COURSE:
                $track->course = $context->instanceid;
                break;
            case CONTEXT_MODULE:
                $cm = $DB->get_record('course_modules', array('id' => $context->instanceid));
                $track->course = $cm->course;
                $track->activity = $cm->id;
                break;
        }
        $sql = "SELECT id
                    FROM {edwreports_activity_log}
                    WHERE datecreated = :todate
                    AND userid = :userid
                    AND course = :course
                    AND activity = :activity
                    AND timestart >= :timestart
                    ORDER BY timestart DESC";
        if ($existingtrack = $DB->get_record_sql($sql, array(
            'todate' => $track->datecreated,
            'userid' => $track->userid,
            'course' => $track->course,
            'activity' => $track->activity,
            'timestart' => $track->timestart - $this->get_minimum_timeslot(),
        ), IGNORE_MULTIPLE)) {
            $id = $existingtrack->id;
        } else {
            $id = $DB->insert_record('edwreports_activity_log', $track);
        }
        return $id;
    }

    /**
     * Update time details in edwreports_activity_log table
     *
     * @param int $id        Tracking id
     * @param int $frequency Time to add in record
     *
     * @return bool
     */
    public static function update_time($id, $frequency) {
        global $DB;
        $sql = "UPDATE {edwreports_activity_log}
                   SET timespent = timespent + :frequency
                 WHERE id = :id";
        return $DB->execute($sql, array(
            'id' => $id,
            'frequency' => $frequency
        ));
    }
}
