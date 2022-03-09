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
 * Plugin administration pages are defined here.
 *
 * @package     local_edwiserreports
 * @category    admin
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserreports\task;

defined('MOODLE_INTERNAL') || die;

use local_edwiserreports\controller\progress;

require_once($CFG->dirroot . "/local/edwiserreports/classes/constants.php");

/**
 * Scheduled Task to pre-calculate student engagement task data.
 */
class studentengagement_data extends \core\task\scheduled_task {

    /**
     * Object to show progress of task
     * @var \local_edwiserreports\task\progress
     */
    private $progress;

    /**
     * Type of summary records we saves.
     *
     * @var array
     */
    private $summarytype;

    /**
     * Getter for $disabled.
     * @return bool
     */
    public function get_disabled() {
        $disabled = isset($this->disabled) ? $this->disabled : false;
        return !get_config('local_edwiserreports', 'precalculated') || $disabled;
    }

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('studentengagementtask', 'local_edwiserreports');
    }

    /**
     * Constructor
     */
    public function __construct() {
        // Types of summary.
        $this->summarytype = [
            'visits' => 'studentengagement-visits',
            'timespent' => 'studentengagement-timespent',
            'submissions' => 'studentengagement-courseactivity-submissions',
            'completions' => 'studentengagement-courseactivity-completions'
        ];
    }

    /**
     * Get last data calculation date.
     *
     * @param  string $type Type of data key
     * @return int
     */
    private function get_last_calculation_time($type = '') {
        global $DB;

        // Get last calculation date from.
        $params = [''];
        $sql = 'SELECT datecreated
                  FROM {edwreports_summary_detailed}
                 ';
        if ($type != '' && isset($this->summarytype[$type])) {
            $sql .= 'WHERE ' . $DB->sql_compare_text('datakey', 255) . ' = ' . $DB->sql_compare_text(":datakey", 255);
            $params['datakey'] = $this->summarytype[$type];
        }
        $sql != ' ORDER BY datecreated asc';
        $lastcalculation = $DB->get_records_sql($sql, $params, 0, 1);
        if (!empty($lastcalculation)) {
            return reset($lastcalculation)->datecreated;
        }

        // Get first log of activity_log table.
        $firstlog = $DB->get_records_sql('SELECT datecreated from {edwreports_activity_log} ORDER BY datecreated asc', null, 0, 1);
        if (!empty($firstlog)) {
            return reset($firstlog)->datecreated;
        }
        return floor(time() / 86400);
    }

    /**
     * Calculate visits and timespent on course per day based on type.
     *
     * @param int    $lastcalculation Last calculate day
     * @param int    $today           Today's date
     * @param string $type            Type of calculation
     */
    private function calculate_summary_detailed($lastcalculation, $today, $type) {
        global $DB;

        $params = [
            'start' => $lastcalculation,
            'end' => $today,
            'contextlevel' => CONTEXT_COURSE,
            'archetype' => 'student'
        ];

        switch ($type) {
            case 'visits':
                $select = "al.datecreated, al.course, count(al.id) visits";
                break;
            case 'timespent':
                $select = "al.datecreated, al.course, sum(" . $DB->sql_cast_char2int("al.timespent") . ") timespent";
                break;
        }

        $sql = "SELECT DISTINCT course id
                  FROM {edwreports_activity_log}
                 WHERE datecreated >= :start
                   AND datecreated <= :end
                 ORDER BY id";
        $courses = $DB->get_records_sql($sql, $params);
        $count = count($courses);
        if ($count < 1) {
            return;
        }
        $this->progress = new progress('summary-' . $type);
        $this->progress->start_progress();
        $progress = 0;
        $increament = 100 / $count;
        foreach ($courses as $course) {
            $progress += $increament;
            $this->progress->update_progress($progress);
            $params['course'] = $course->id;
            switch ($course->id) {
                case 0:
                case 1;
                    $sql = "SELECT $select
                              FROM {edwreports_activity_log} al
                             WHERE al.datecreated >= :start
                               AND al.datecreated <= :end
                               AND al.userid <> 0
                               AND al.course = :course
                             GROUP BY al.datecreated, al.course
                             ORDER BY al.datecreated ASC";
                    break;
                default;
                    $params['course1'] = $course->id;
                    $sql = "SELECT $select
                              FROM {edwreports_activity_log} al
                             WHERE al.datecreated >= :start
                               AND al.datecreated <= :end
                               AND al.userid <> 0
                               AND al.course = :course
                               AND al.userid IN (SELECT ra.userid
                                                   FROM {role_assignments} ra
                                                   JOIN {role} r ON ra.roleid = r.id
                                                   JOIN {context} ctx on ra.contextid = ctx.id
                                                    AND ctx.contextlevel = :contextlevel
                                                    AND ctx.instanceid = :course1
                                                    AND r.archetype = " . $DB->sql_compare_text(':archetype') . ")
                             GROUP BY al.datecreated, al.course
                             ORDER BY al.datecreated ASC";
                    break;
            }

            $records = $DB->get_recordset_sql($sql, $params);

            if (!isset($records->current()->datecreated)) {
                continue;
            }
            foreach ($records as $record) {
                if ($summaryid = $DB->get_field_sql(
                    "SELECT id FROM {edwreports_summary_detailed}
                      WHERE datecreated = :date
                        AND course = :course
                        AND " . $DB->sql_compare_text('datakey', 255) . " = " . $DB->sql_compare_text(':datakey', 255),
                    [
                        'date' => $record->datecreated,
                        'course' => $record->course,
                        'datakey' => $this->summarytype[$type]
                    ]
                )) {
                    $DB->update_record('edwreports_summary_detailed', ['id' => $summaryid, 'datavalue' => $record->$type]);
                } else {
                    $DB->insert_record('edwreports_summary_detailed', [
                        'datecreated' => $record->datecreated,
                        'course' => $record->course,
                        'datakey' => $this->summarytype[$type],
                        'datavalue' => $record->$type
                    ]);
                }
            }
        }

        $this->progress->end_progress();
    }

    /**
     * Calculate detailed summary of assignment submissions and activity completions
     *
     * @param string $type            Type of calculation (submissions/completions)
     * @param int    $lastcalculation Last calculation date
     * @param int    $today           Today's date
     *
     * @return void
     */
    private function calculate_summary_detailed_courseactivity($type, $lastcalculation, $today) {
        global $DB;
        $params = [
            'start' => $lastcalculation * 86400,
            'end' => $today * 86400 + 86399
        ];
        echo PHP_EOL . "Calculating $type:";
        switch ($type) {
            case 'submissions';
                $sql = "SELECT DISTINCT a.course id
                          FROM {assign} a
                          JOIN {assign_submission} asub ON a.id = asub.assignment
                         WHERE asub.timecreated >= :start
                           AND asub.timecreated <= :end";
                break;
            case 'completions':
                $sql = "SELECT DISTINCT cm.course id
                          FROM {course_modules} cm
                          JOIN {course_modules_completion} cmc ON cm.id = cmc.coursemoduleid
                         WHERE cmc.completionstate <> 0
                           AND cmc.timemodified >= :start
                           AND cmc.timemodified <= :end";
                break;
        }
        $courses = $DB->get_records_sql($sql, $params);
        $count = count($courses);
        if ($count < 1) {
            return;
        }
        $this->progress = new progress($this->summarytype[$type]);
        $this->progress->start_progress();
        $progress = 0;
        $increament = 100 / $count;
        foreach ($courses as $course) {
            $progress += $increament;
            $this->progress->update_progress($progress);
            $params['course'] = $course->id;
            switch ($type) {
                case 'submissions':
                    $sql = "SELECT floor(asub.timecreated / 86400) datecreated, a.course, count(asub.id) total
                              FROM {assign} a
                              JOIN {assign_submission} asub ON a.id = asub.assignment
                             WHERE asub.timecreated >= :start
                               AND asub.timecreated <= :end
                               AND a.course = :course
                             GROUP BY floor(asub.timecreated / 86400), a.course";
                    break;
                case 'completions':
                    $sql = "SELECT floor(cmc.timemodified / 86400) datecreated, cm.course, count(cmc.id) total
                              FROM {course_modules} cm
                              JOIN {course_modules_completion} cmc ON cm.id = cmc.coursemoduleid
                             WHERE cmc.completionstate <> 0
                               AND cmc.timemodified >= :start
                               AND cmc.timemodified <= :end
                             GROUP BY floor(cmc.timemodified / 86400), cm.course";
                    break;
            }
            $records = $DB->get_recordset_sql($sql, $params);
            if (!isset($records->current()->datecreated)) {
                continue;
            }

            foreach ($records as $record) {
                if ($subid = $DB->get_field_sql(
                    "SELECT id FROM {edwreports_summary_detailed}
                      WHERE datecreated = :date
                        AND course = :course
                        AND " . $DB->sql_compare_text('datakey', 255) . " = " . $DB->sql_compare_text(':datakey', 255),
                    [
                        'date' => $record->datecreated,
                        'course' => $record->course,
                        'datakey' => $this->summarytype[$type]
                    ]
                )) {
                    $DB->update_record('edwreports_summary_detailed', ['id' => $subid, 'datavalue' => $record->total]);
                } else {
                    $DB->insert_record('edwreports_summary_detailed', [
                        'datecreated' => $record->datecreated,
                        'course' => $record->course,
                        'datakey' => $this->summarytype[$type],
                        'datavalue' => $record->total
                    ]);
                }
            }
        }
        $this->progress->end_progress();
    }

    /**
     * Calculate visits and timespent on course.
     *
     * @param string $type   Type of calculation
     * @param int    $today  Today
     * @param string $period Data period
     * @param int    $days   Days
     */
    private function calculate_summary($type, $today, $period, $days) {
        global $DB;
        $sql = "SELECT course, sum(" . $DB->sql_cast_char2int("datavalue", true) . ") total
                  FROM {edwreports_summary_detailed}
                 WHERE datecreated >= :start
                   AND datecreated <= :end
                   AND " . $DB->sql_compare_text('datakey', 255) . " = " . $DB->sql_compare_text(':datakey', 255) . "
                 GROUP BY course";
        $params = [
            'start' => $today - $days,
            'end' => $today,
            'datakey' => $this->summarytype[$type]
        ];

        $records = $DB->get_recordset_sql($sql, $params);

        if (!isset($records->current()->course)) {
            return;
        }

        foreach ($records as $record) {
            if ($summaryid = $DB->get_field_sql(
                "SELECT id FROM {edwreports_summary}
                  WHERE course = :course
                    AND " . $DB->sql_compare_text('datakey', 255) . " = " . $DB->sql_compare_text(':datakey', 255),
                [
                    'course' => $record->course,
                    'datakey' => $this->summarytype[$type] . '-' . $period
                ]
            )) {
                $DB->update_record('edwreports_summary', ['id' => $summaryid, 'datavalue' => $record->total]);
            } else {
                $DB->insert_record('edwreports_summary', [
                    'course' => $record->course,
                    'datakey' => $this->summarytype[$type] . '-' . $period,
                    'datavalue' => $record->total
                ]);

            }
        }
    }

    /**
     * Execute the task.
     */
    public function execute() {

        // Today's date.
        $today = floor(time() / 86400);

        // Calculate detailed summary of visits.
        echo "Calculating visits:";
        // Get last calculation.
        $lastcalculation = $this->get_last_calculation_time('visits');
        $this->calculate_summary_detailed($lastcalculation, $today, 'visits');

        echo PHP_EOL . PHP_EOL . "Calculating timespent:";
        // Calculate detailed summary of timespent.
        // Get last calculation.
        $lastcalculation = $this->get_last_calculation_time('timespent');
        $this->calculate_summary_detailed($lastcalculation, $today, 'timespent');

        echo PHP_EOL . PHP_EOL . "Calculating course activity status:";
        // Calculate detailed summary of course activity.
        // Get last calculation.
        $lastcalculation = $this->get_last_calculation_time('courseactivity');
        $this->calculate_summary_detailed_courseactivity('submissions', $lastcalculation, $today);
        $this->calculate_summary_detailed_courseactivity('completions', $lastcalculation, $today);

        $periods = [
            'weekly' => LOCAL_SITEREPORT_WEEKLY_DAYS,
            'monthly' => LOCAL_SITEREPORT_MONTHLY_DAYS,
            'yearly' => LOCAL_SITEREPORT_YEARLY_DAYS
        ];

        foreach ($periods as $period => $days) {
            // Calculate summary of visits.
            $this->calculate_summary('visits', $today, $period, $days);

            // Calculate summary of timespent.
            $this->calculate_summary('timespent', $today, $period, $days);

            // Calculate summary of submissions.
            $this->calculate_summary('submissions', $today, $period, $days);

            // Calculate summary of completions.
            $this->calculate_summary('completions', $today, $period, $days);
        }
        return true;
    }
}
