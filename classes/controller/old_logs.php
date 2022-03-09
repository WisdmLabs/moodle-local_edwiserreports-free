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

define('LIMIT_NUM', 100);

use context_course;
use stdClass;

/**
 * This class contains method to transform Moodle logs to Edwiser Reports Logs.
 */
class old_logs {

    /**
     * Highlight current course of which data we are calculating.
     *
     * @param int $id Course id
     */
    public function highlight_current_progress($id, $count, $total) {
        $overallwidth = $count / $total * 100;
        echo "<script>
            if (document.querySelector('[data-course-id]:not(.d-none)') != null) {
                document.querySelector('[data-course-id]:not(.d-none)').classList.add('d-none');
            }
            document.querySelector('[data-course-id=\"" . $id . "\"]').classList.remove('d-none');
            document.getElementById('overall-progress').setAttribute('aria-valuenow', " . $overallwidth . ");
            document.getElementById('overall-progress').style.width = '" . $overallwidth . "%';
            document.getElementById('overall-progress').innerText = '" . $count . '/' . $total . "';
        </script>";
        flush();
    }

    /**
     * Get time spent track record object so we can insert in db.
     *
     * @param object $event Event object
     *
     * @return stdClass
     */
    public function get_timespent_track($event) {
        $track = new stdClass;
        $track->datecreated = round($event->timecreated / 86400);
        $track->userid = $event->userid;
        $track->course = 0;
        $track->activity = 0;
        $track->timestart = $event->timecreated;
        $track->timespent = 0;
        $track->timetocomplete = 0;
        switch($event->contextlevel) {
            case CONTEXT_COURSE:
                $track->course = $event->courseid;
                break;
            case CONTEXT_MODULE:
                $track->course = $event->courseid;
                $track->activity = $event->contextinstanceid;
                break;
        }
        return $track;
    }

    /**
     * Get logs using course, user and mintime.
     *
     * @param int $course       Course id
     * @param int $user         User id
     * @param int $mintime      Minimum time period
     * @param int $limitfrom    Starting offset of records
     *
     * @return array Logs
     */
    private function get_logs($course, $user, $mintime, $limitfrom) {
        global $DB;
        $where = 'courseid = :courseid AND userid = :userid AND timecreated >= :mintime';
        $params = array(
            'courseid' => $course,
            'userid' => $user,
            'mintime' => $mintime
        );
        return $DB->get_records_select('logstore_standard_log', $where, $params, 'timecreated ASC', '*', $limitfrom, LIMIT_NUM);
    }

    /**
     * Get logs count using course, user and mintime.
     *
     * @param int $course       Course id
     * @param int $user         User id
     * @param int $mintime      Minimum time period
     *
     * @return int Logs count
     */
    private function get_logs_count($course, $user, $mintime) {
        global $DB;
        $where = 'courseid = :courseid AND userid = :userid AND timecreated >= :mintime';
        $params = array(
            'courseid' => $course,
            'userid' => $user,
            'mintime' => $mintime
        );
        return $DB->count_records_select('logstore_standard_log', $where, $params);
    }

    /**
     * Process users old logs and transform to Edwiser Reports Logs.
     *
     * @param bool  $run      If true then old data will be process.
     * @param array $courses  Courses list to process data.
     * @param int   $mintime  Starting period of log time.
     * @param int   $timeslot Time slot between clicks.
     *
     * @return void
     */
    private function process_users_old_logs($courseid, $userid, $mintime, $timeslot, $percent, $increment, $progress) {
        global $DB;
        $count = $this->get_logs_count($courseid, $userid, $mintime);
        if ($count == 0) {
            return;
        }
        $offset = 0;
        $logs = $this->get_logs($courseid, $userid, $mintime, $offset);
        $offset += 100;
        if (empty($logs)) {
            return;
        }
        $previous = $this->get_timespent_track(array_shift($logs));
        $tracks = [];
        $innerpercent = $percent;
        $innerincrement = $increment / ($count / LIMIT_NUM);
        while (true) {
            if (empty($logs)) {
                break;
            }
            foreach ($logs as $log) {
                $track = $this->get_timespent_track($log);
                $difference = $track->timestart - $previous->timestart;
                if ($difference > 0 && $difference < $timeslot) {
                    $previous->timespent = $difference;
                    $tracks[] = $previous;
                }
                $previous = $track;
            }
            $logs = $this->get_logs($courseid, $userid, $mintime, $offset);
            $offset += 100;
            $innerpercent += $innerincrement;
            if ($innerpercent > $percent + $increment) {
                $innerpercent = $percent + $increment;
            }
            if (!empty($tracks)) {
                $DB->insert_records('edwreports_activity_log', $tracks);
                $tracks = [];
            }
            $progress->update_progress($innerpercent, 3);
        }
    }

    /**
     * Update user completions using activity log and completion time
     *
     * @param int $userid       User id
     * @param int $course       Course id
     * @param int $activity     Activity id
     * @param int $timemodified Time when activity is completed
     *
     */
    private function update_user_completions($userid, $course, $activity, $timemodified) {
        global $DB;
        $params = [
            'userid' => $userid,
            'course' => $course,
            'activity' => $activity,
            'timemodified' => $timemodified
        ];
        $select = "SELECT sum(" . $DB->sql_cast_char2int("timespent") . ") timespent";
        $remainingsql = " FROM {edwreports_activity_log}
                    WHERE userid = :userid
                    AND course = :course
                    AND activity = :activity
                    AND timestart <= :timemodified";
        $record = $DB->get_record_sql($select . $remainingsql, $params);
        if ($record->timespent != null) {
            $select = 'SELECT *';
            $log = $DB->get_record_sql($select . $remainingsql . ' ORDER BY timestart DESC', $params, IGNORE_MULTIPLE);
            if ($log != false) {
                $log->timetocomplete = $record->timespent;
                $DB->update_record('edwreports_activity_log', $log);
            }
        }
    }

    /**
     * Process activity completion of users
     *
     * @param int $courseid Course id
     * @param int $userid   User id
     * @param int $mintime  Starting period of completion
     *
     */
    private function process_users_completions($courseid, $userid, $mintime) {
        global $DB;
        $sql = "SELECT cmc.id id,
                       cmc.userid userid,
                       cm.course course,
                       cmc.coursemoduleid activity,
                       cmc.timemodified timemodified
                  FROM {course_modules_completion} cmc
                  JOIN {course_modules} cm ON cmc.coursemoduleid = cm.id
                 WHERE cm.course = :course
                   AND cmc.userid = :user
                   AND cmc.timemodified > :mintime
                   AND cmc.completionstate <> 0";
        $params = [
            'course' => $courseid,
            'user' => $userid,
            'mintime' => $mintime
        ];
        $completions = $DB->get_records_sql($sql, $params);
        if (!empty($completions)) {
            foreach ($completions as $completion) {
                $this->update_user_completions(
                    $completion->userid,
                    $completion->course,
                    $completion->activity,
                    $completion->timemodified
                );
            }
        }
    }

    /**
     * Process old logs from logstore_stadard_log table.
     *
     * @param bool  $run      If true then old data will be process.
     * @param array $courses  Courses list to process data.
     * @param int   $mintime  Starting period of log time.
     * @param int   $timeslot Time slot between clicks.
     */
    public function process_old_logs($run, $courses, $mintime, $timeslot) {
        global $DB;
        if ($run === false) {
            return;
        }
        $DB->execute('TRUNCATE TABLE {edwreports_activity_log}');
        $total = count($courses);
        $count = 0;
        foreach ($courses as $course) {
            $context = context_course::instance($course->id);
            $progress = new progress('course-' . $course->id);
            $this->highlight_current_progress($course->id, ++$count, $total);
            if ($course->id == 1) {
                $users = $DB->get_records('user');
            } else {
                $users = get_enrolled_users($context);
            }
            if (empty($users)) {
                $progress->end_progress(100);
                unset($progress);
                continue;
            }
            $percent = 0;
            $increment = 100 / count($users);
            foreach ($users as $user) {
                $this->process_users_old_logs(
                    $course->id,
                    $user->id,
                    $mintime,
                    $timeslot,
                    $percent,
                    $increment,
                    $progress
                );
                $this->process_users_completions(
                    $course->id,
                    $user->id,
                    $mintime
                );
                $percent += $increment;
                $progress->update_progress($percent);
            }
            $progress->end_progress(100);
        }
        echo "<script>
            document.querySelector('#continue').parentElement.classList.remove('d-none');
            if (document.querySelector('[data-course-id]:not(.d-none)') != null) {
                document.querySelector('[data-course-id]:not(.d-none)').classList.add('d-none');
            }
        </script>";
    }
}
