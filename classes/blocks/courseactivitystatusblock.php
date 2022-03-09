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
class courseactivitystatusblock extends block_base {

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
        $this->sessioncache = cache::make('local_edwiserreports', 'courseactivitystatus');
        $this->precalculated = get_config('local_edwiserreports', 'precalculated');
    }

    /**
     * Preapre layout for Visits on site
     * @return object Layout object
     */
    public function get_layout() {
        global $CFG;

        // Layout related data.
        $this->layout->id = 'courseactivitystatusblock';
        $this->layout->name = get_string('courseactivitystatusheader', 'local_edwiserreports');
        $this->layout->info = get_string('courseactivitystatusblockhelp', 'local_edwiserreports');
        $this->layout->filters = $this->get_filter();
        $this->layout->filter = 'weekly-0';
        $this->layout->morelink = new moodle_url($CFG->wwwroot . "/local/edwiserreports/studentengagement.php");

        // To add export links.
        $this->layout->downloadlinks = $this->get_block_download_links();

        // Add block view in layout.
        $this->layout->blockview = $this->render_block('courseactivitystatusblock', $this->block);

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

        return $OUTPUT->render_from_template('local_edwiserreports/courseactivitystatusblockfilters', [
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
     * Calculate insight data for active users block.
     * @return object
     */
    public function calculate_insight() {
        $insight = [
            'insight' => [
                'title' => get_string('averagecompletion', 'local_edwiserreports'),
                'value' => '??'
            ],
            'details' => [
                'data' => [[
                    'title' => get_string('totalassignment', 'local_edwiserreports'),
                    'value' => '??'
                ], [
                    'title' => get_string('totalcompletion', 'local_edwiserreports'),
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
        global $DB;
        $userid = $filter->student;
        $course = $filter->course;
        $timeperiod = $filter->date;
        $cachekey = $this->generate_cache_key('studentengagement', 'courseactivitystatus-' . $timeperiod . '-' . $userid);

        if (!$response = $this->sessioncache->get($cachekey)) {
            $this->generate_labels($timeperiod);
            $params = [
                'startdate' => $this->startdate,
                'enddate' => $this->enddate
            ];

            if ($course == 0) {
                $courses = $this->get_courses_of_user($this->get_current_user());
            } else {
                $courses = [$course => 'Dummy'];
            }
            // Temporary course table.
            $coursetable = 'tmp_stengage_courses';
            // Creating temporary table.
            utility::create_temp_table($coursetable, array_keys($courses));
            switch ($timeperiod . '-' . $course . '-' . $userid . '-' . $this->precalculated) {
                case 'weekly-0-0-1':
                case 'monthly-0-0-1':
                case 'yearly-0-0-1':
                    $subsql = "SELECT esd.datecreated subdate, sum(" . $DB->sql_cast_char2int("esd.datavalue", true) . ") submission
                                 FROM {{$coursetable}} ct
                                 JOIN {edwreports_summary_detailed} esd ON ct.tempid = esd.course
                                WHERE " . $DB->sql_compare_text('datakey', 255) . " = " . $DB->sql_compare_text(':subdatakey', 255) . "
                                GROUP BY esd.datecreated";
                    $params['subdatakey'] = 'studentengagement-courseactivity-submissions';

                    $comsql = "SELECT esd.datecreated subdate, sum(" . $DB->sql_cast_char2int("esd.datavalue", true) . ") completed
                                 FROM {{$coursetable}} ct
                                 JOIN {edwreports_summary_detailed} esd ON ct.tempid = esd.course
                                WHERE " . $DB->sql_compare_text('esd.datakey', 255) . " = " . $DB->sql_compare_text(':comdatakey', 255) . "
                                GROUP BY esd.datecreated";
                    $params['comdatakey'] = 'studentengagement-courseactivity-completions';
                    break;
                default:
                    $subsql = "SELECT floor(asub.timecreated / 86400) subdate, count(asub.id) submission
                              FROM {{$coursetable}} ct
                              JOIN {assign} a ON ct.tempid = a.course
                              JOIN {assign_submission} asub ON a.id = asub.assignment
                             WHERE asub.timecreated >= :startdate
                               AND asub.timecreated <= :enddate ";
                    $comsql = "SELECT floor(cmc.timemodified / 86400) comdate, count(cmc.id) completed
                                 FROM {{$coursetable}} ct
                                 JOIN {course_modules} cm ON ct.tempid = cm.course
                                 JOIN {course_modules_completion} cmc ON cm.id = cmc.coursemoduleid
                                WHERE cmc.completionstate <> 0
                                  AND cmc.timemodified >= :startdate
                                  AND cmc.timemodified <= :enddate ";
                    if ($userid !== 0) { // User is selected in dropdown.
                        $subsql .= ' AND asub.userid = :userid ';
                        $comsql .= ' AND cmc.userid = :userid ';
                        $params['userid'] = $userid;
                    }
                    $subsql .= " GROUP BY floor(asub.timecreated / 86400)";
                    $comsql .= " GROUP BY floor(cmc.timemodified / 86400)";
                    break;
            }
            $sublogs = $DB->get_records_sql($subsql, $params);
            $comlogs = $DB->get_records_sql($comsql, $params);

            $completions = $submissions = $this->dates;
            $hasdata = false;
            foreach ($sublogs as $date => $log) {
                if (isset($submissions[$date])) {
                    $submissions[$date] = $log->submission;
                    if ($log->submission > 0) {
                        $hasdata = true;
                    }
                }
            }
            if ($hasdata == false) {
                $submissions = [];
            }

            $hasdata = false;
            foreach ($comlogs as $date => $log) {
                if (isset($completions[$date])) {
                    $completions[$date] = $log->completed;
                    if ($log->completed > 0) {
                        $hasdata = true;
                    }
                }
            }

            if ($hasdata == false) {
                $completions = [];
            }

            if (empty($submissions) && empty($completions)) {
                $this->labels = [];
            }

            $response = [
                'submissions' => array_values($submissions),
                'completions' => array_values($completions),
                'labels' => $this->labels
            ];

            $response['insight'] = $this->calculate_insight();
            utility::drop_temp_table($coursetable);

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

        // Get course id for submissions graph.
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
        $export = [[
            get_string('date'),
            get_string('courseactivitystatus-submissions', 'local_edwiserreports'),
            get_string('courseactivitystatus-completions', 'local_edwiserreports')
        ]];
        $recordname = ['submissions', 'completions'];
        $valuecallback = null;
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
