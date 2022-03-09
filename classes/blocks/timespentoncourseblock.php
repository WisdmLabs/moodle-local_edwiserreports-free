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
 * Block layout and ajax service methods are defined in this file.
 *
 * @package     local_edwiserreports
 * @author      Yogesh Shirsath
 * @copyright   2022 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserreports;

defined('MOODLE_INTERNAL') or die;

use stdClass;
use cache;
use moodle_url;
/**
 * Class Visits on site. To get the data related to Visits on site.
 */
class timespentoncourseblock extends block_base {

    /**
     * Get the first site access data.
     *
     * @var null
     */
    public $firstsiteaccess;

    /**
     * Current time
     *
     * @var int
     */
    public $enddate;

    /**
     * Active users block labels
     *
     * @var array
     */
    public $labels;

    /**
     * No. of labels for active users.
     *
     * @var int
     */
    public $xlabelcount;

    /**
     * Cache
     *
     * @var object
     */
    public $cache;

    /**
     * Dates main array.
     *
     * @var array
     */
    public $dates = [];

    /**
     * Instantiate object
     *
     * @param int $blockid Block id
     */
    public function __construct($blockid = false) {
        parent::__construct($blockid);
        // Set cache for student engagement block.
        $this->sessioncache = cache::make('local_edwiserreports', 'timespentoncourse');
        $this->precalculated = get_config('local_edwiserreports', 'precalculated');
    }

    /**
     * Preapre layout for Visits on site
     * @return object Layout object
     */
    public function get_layout() {
        global $CFG;

        // Layout related data.
        $this->layout->id = 'timespentoncourseblock';
        $this->layout->name = get_string('timespentoncourseheader', 'local_edwiserreports');
        $this->layout->info = get_string('timespentoncourseblockhelp', 'local_edwiserreports');
        $this->layout->filters = $this->get_filter();
        $this->layout->filter = 'weekly-0';
        $this->layout->pro = $this->image_icon('lock');

        // To add export links.
        $this->layout->downloadlinks = $this->get_block_download_links();

        // Add block view in layout.
        $this->layout->blockview = $this->render_block('timespentoncourseblock', $this->block);

        // Set block edit capabilities.
        $this->set_block_edit_capabilities($this->layout->id);

        // Return blocks layout.
        return $this->layout;
    }

    /**
     * Prepare active users block filters
     * @param  $onlycourses Return only courses dropdown for current user.
     * @return array filters array
     */
    public function get_filter($onlycourses = false) {
        global $OUTPUT, $USER, $COURSE, $USER, $DB;

        $courses = $this->get_courses_of_user($USER->id);

        unset($courses[$COURSE->id]);

        $users = $this->get_users_of_courses($USER->id, $courses);

        array_unshift($users, (object)[
            'id' => 0,
            'fullname' => get_string('allusers', 'search')
        ]);

        array_unshift($courses, (object)[
            'id' => 0,
            'fullname' => get_string('fulllistofcourses')
        ]);

        // Return only courses array if $onlycourses is true.
        if ($onlycourses == true) {
            return $courses;
        }

        return $OUTPUT->render_from_template('local_edwiserreports/timespentoncourseblockfilters', [
            'courses' => $courses,
            'students' => $users
        ]);
    }

    /**
     * Generate labels and dates array for graph
     *
     * @param string $timeperiod Filter time period Weekly/Monthly/Yearly or custom dates.
     */
    private function generate_labels($timeperiod) {
        $this->dates = [];
        $this->labels = [];
        $this->enddate = floor(time() / 86400 + 1) * 86400 - 1;
        switch ($timeperiod) {
            case 'weekly':
                // Monthly days.
                $this->xlabelcount = LOCAL_SITEREPORT_WEEKLY_DAYS;
                break;
            case 'monthly':
                // Yearly days.
                $this->xlabelcount = LOCAL_SITEREPORT_MONTHLY_DAYS;
                break;
            case 'yearly':
                // Weekly days.
                $this->xlabelcount = LOCAL_SITEREPORT_YEARLY_DAYS;
                break;
            default:
                // Explode dates from custom date filter.
                $dates = explode(" to ", $timeperiod);
                if (count($dates) == 2) {
                    $startdate = strtotime($dates[0]." 00:00:00");
                    $enddate = strtotime($dates[1]." 23:59:59");
                }
                // If it has correct startdat and end date then count xlabel.
                if (isset($startdate) && isset($enddate)) {
                    $days = round(($enddate - $startdate) / LOCAL_SITEREPORT_ONEDAY);
                    $this->xlabelcount = $days;
                    $this->enddate = $enddate;
                } else {
                    $this->xlabelcount = LOCAL_SITEREPORT_WEEKLY_DAYS; // Default one week.
                }
                break;
        }

        $this->startdate = (round($this->enddate / 86400) - $this->xlabelcount) * 86400;

        // Get all lables.
        for ($i = $this->xlabelcount - 1; $i >= 0; $i--) {
            $time = $this->enddate - $i * LOCAL_SITEREPORT_ONEDAY;
            $this->dates[floor($time / LOCAL_SITEREPORT_ONEDAY)] = 0;
            $this->labels[] = $time * 1000;
        }
    }

    /**
     * Generate courses labels and date boundaries for sql.
     *
     * @param string $timeperiod Filter time period Weekly/Monthly/Yearly or custom dates.
     * @param array $courses     Courses array
     */
    private function generate_courses_labels($timeperiod, $courses) {
        $this->enddate = floor(time() / 86400 + 1) * 86400 - 1;
        switch ($timeperiod) {
            case 'weekly':
                // Monthly days.
                $this->startdate = (($this->enddate / 86400) - LOCAL_SITEREPORT_WEEKLY_DAYS) * 86400;
                break;
            case 'monthly':
                // Yearly days.
                $this->startdate = (($this->enddate / 86400) - LOCAL_SITEREPORT_MONTHLY_DAYS) * 86400;
                break;
            case 'yearly':
                // Weekly days.
                $this->startdate = (($this->enddate / 86400) - LOCAL_SITEREPORT_YEARLY_DAYS) * 86400;
                break;
            default:
                // Explode dates from custom date filter.
                $dates = explode(" to ", $timeperiod);
                if (count($dates) == 2) {
                    $startdate = strtotime($dates[0]." 00:00:00");
                    $enddate = strtotime($dates[1]." 23:59:59");
                }
                // If it has correct startdat and end date then count xlabel.
                if (isset($startdate) && isset($enddate)) {
                    $this->startdate = $startdate;
                    $this->enddate = $enddate;
                } else {
                    $this->startdate = (($this->enddate / 86400) - LOCAL_SITEREPORT_WEEKLY_DAYS) * 86400;
                }
                break;
        }
        $this->courses = [];
        $this->labels = [];
        if (!empty($courses)) {
            foreach ($courses as $id => $course) {
                $this->courses[$id] = 0;
                $this->labels[$id] = $course->fullname;
            }
        }
    }

    /**
     * Calculate insight data for active users block.
     * @return object
     */
    public function calculate_insight() {
        $insight = [
            'insight' => [
                'title' => get_string('averagetimespent', 'local_edwiserreports'),
                'value' => '??'
            ],
            'details' => [
                'data' => [[
                    'title' => get_string('totaltimespent', 'local_edwiserreports'),
                    'value' => '??'
                ]]
            ]
        ];
        return $insight;
    }

    /**
     * Use this method to return data for block.
     * Get Data for block
     * @param  object $filter Filter object
     * @return object         Response
     */
    public function get_data($filter = false) {
        global $DB, $COURSE;
        $userid = $filter->student;
        $course = $filter->course;
        $timeperiod = $filter->date;

        $cachekey = $this->generate_cache_key(
            'studentengagement',
            'timespentoncourse-' . $timeperiod . '-' . $userid . '-' . $course
        );

        if (!$response = $this->sessioncache->get($cachekey)) {
            if ($course !== 0) { // Course is selected in dropdown.
                $this->generate_labels($timeperiod);
            } else {
                $courses = $this->get_courses_of_user($this->get_current_user());
                unset($courses[$COURSE->id]);
                $this->generate_courses_labels($timeperiod, $courses);
            }

            $params = [
                'startdate' => floor($this->startdate / 86400),
                'enddate' => floor($this->enddate / 86400)
            ];
            $wheresql = 'WHERE datecreated >= :startdate
            AND datecreated <= :enddate';

            switch ($timeperiod . '-' . $userid . '-' . $this->precalculated) {
                case 'weekly-0-1':
                case 'monthly-0-1':
                case 'yearly-0-1':
                    if ($course !== 0) { // Course is selected in dropdown.
                        $params['course'] = $course;
                        $wheresql .= ' AND course = :course ';

                        $sql = "SELECT datecreated, sum(" . $DB->sql_cast_char2int("datavalue", true) . ") timespent
                                  FROM {edwreports_summary_detailed}
                                       $wheresql
                                 GROUP BY datecreated";
                    } else {
                        $sql = "SELECT course, sum(" . $DB->sql_cast_char2int("datavalue", true) . ") timespent
                                  FROM {edwreports_summary}
                                   WHERE " . $DB->sql_compare_text('datakey', 255) . " = " . $DB->sql_compare_text(':datakey', 255) . "
                                 GROUP BY course";
                        $params['datakey'] = 'studentengagement-timespent-' . $timeperiod;
                    }
                    break;
                default:
                    if ($userid !== 0) { // User is selected in dropdown.
                        $params['userid'] = $userid;
                        $wheresql .= ' AND userid = :userid ';
                    }
                    if ($course !== 0) { // Course is selected in dropdown.
                        $params['course'] = $course;
                        $wheresql .= ' AND course = :course ';

                        $sql = "SELECT datecreated, sum(" . $DB->sql_cast_char2int("timespent") . ") timespent
                                FROM {edwreports_activity_log}
                                $wheresql
                                GROUP BY datecreated";

                    } else {
                        $sql = "SELECT course, sum(" . $DB->sql_cast_char2int("timespent") . ") timespent
                                FROM {edwreports_activity_log}
                                $wheresql
                                GROUP BY course";
                    }
                    break;
            }
            $logs = $DB->get_records_sql($sql, $params);
            if ($course !== 0) { // Course is selected in dropdown.

                foreach ($logs as $log) {
                    if (!isset($this->dates[$log->datecreated])) {
                        continue;
                    }
                    $this->dates[$log->datecreated] = $log->timespent;
                }
                $response = [
                    'timespent' => array_values($this->dates),
                    'labels' => array_values($this->labels)
                ];
            } else {
                $hasdata = false;
                foreach ($logs as $log) {
                    if (!isset($this->courses[$log->course])) {
                        continue;
                    }
                    if ($log->timespent > 0) {
                        $hasdata = true;
                    }
                    $this->courses[$log->course] = $log->timespent;
                }
                if (!$hasdata) {
                    $this->courses = [];
                }
                $response = [
                    'timespent' => array_values($this->courses),
                    'labels' => array_values($this->labels)
                ];
            }

            $response['insight'] = $this->calculate_insight();

            // Set response in cache.
            $this->sessioncache->set($cachekey, $response);
        }

        return $response;
    }

    /**
     * If block is exporting any data then include this method.
     * Get Exportable data for Visits on site
     * @param  string $filter Filter object
     * @return array          Array of exportable data
     */
    public function get_exportable_data_block($filter) {
        // Exploding filter string to get parameters.
        $filter = explode('-', $filter);

        // Filter object for graph methods.
        $filterobject = new stdClass;

        // Student id.
        $filterobject->student = (int) array_pop($filter);

        // Get course id for timespentoncourse graph.
        $filterobject->course = (int) array_pop($filter);

        // Time period.
        $filterobject->date = implode('-', $filter);

        // Fetching graph record.
        $records = $this->get_data($filterobject);
        $valuecallback = function($time) {
            return date('H:i:s', mktime(0, 0, $time));
        };

        $labelcallback = function($label) {
            return date('d-m-Y', $label / 1000);
        };
        if ($filterobject->course === 0) {
            $export = [[
                get_string('course'),
                get_string('timespentoncourse', 'local_edwiserreports')
            ]];
            $labelcallback = null;
        } else {
            $export = [[
                get_string('date'),
                get_string('timespentoncourse', 'local_edwiserreports')
            ]];
        }
        $recordname = 'timespent';
        if (is_array($recordname)) {
            $datacallback = function(&$row, $recordnames, $key, $records, $valuecallback) {
                foreach ($recordnames as $recordname) {
                    $value = isset($records[$recordname][$key]) ? $records[$recordname][$key] : 0;
                    $row[] = $valuecallback == null ? $value : $valuecallback($value);
                }
            };
        } else {
            $datacallback = function(&$row, $recordname, $key, $records, $valuecallback) {
                $value = isset($records[$recordname][$key]) ? $records[$recordname][$key] : 0;
                $row[] = $valuecallback == null ? $value : $valuecallback($value);
            };
        }
        foreach ($records['labels'] as $key => $label) {
            $row = [$labelcallback == null ? $label : $labelcallback($label)];
            $datacallback($row, $recordname, $key, $records, $valuecallback);
            $export[] = $row;
        }
        return $export;
    }
}
