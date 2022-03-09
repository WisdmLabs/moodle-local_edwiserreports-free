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
class visitsonsiteblock extends block_base {

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
        $this->sessioncache = cache::make('local_edwiserreports', 'visitsonsite');
        $this->precalculated = get_config('local_edwiserreports', 'precalculated');
    }

    /**
     * Preapre layout for Visits on site
     * @return object Layout object
     */
    public function get_layout() {
        global $CFG;

        // Layout related data.
        $this->layout->id = 'visitsonsiteblock';
        $this->layout->name = get_string('visitsonsiteheader', 'local_edwiserreports');
        $this->layout->info = get_string('visitsonsiteblockhelp', 'local_edwiserreports');
        $this->layout->filters = $this->get_filter();
        $this->layout->filter = 'weekly-0';
        $this->layout->morelink = new moodle_url($CFG->wwwroot . "/local/edwiserreports/studentengagement.php");

        // To add export links.
        $this->layout->downloadlinks = $this->get_block_download_links();

        // Add block view in layout.
        $this->layout->blockview = $this->render_block('visitsonsiteblock', $this->block);

        // Set block edit capabilities.
        $this->set_block_edit_capabilities($this->layout->id);

        // Return blocks layout.
        return $this->layout;
    }

    /**
     * Prepare active users block filters
     * @return array filters array
     */
    public function get_filter() {
        global $OUTPUT, $USER, $COURSE, $USER;

        $courses = $this->get_courses_of_user($USER->id);

        unset($courses[$COURSE->id]);

        $users = $this->get_users_of_courses($USER->id, $courses);

        array_unshift($users, (object)[
            'id' => 0,
            'fullname' => get_string('allusers', 'search')
        ]);
        return $OUTPUT->render_from_template('local_edwiserreports/visitsonsiteblockfilters', [
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
                'title' => get_string('averagesitevisits', 'local_edwiserreports'),
                'value' => '??'
            ],
            'details' => [
                'data' => [[
                    'title' => get_string('totalsitevisits', 'local_edwiserreports'),
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
        $timeperiod = $filter->date;
        $cachekey = $this->generate_cache_key('visitsonsite', $timeperiod . '-' . $userid);

        if (!$response = $this->sessioncache->get($cachekey)) {
            $this->generate_labels($timeperiod);
            $params = [
                'startdate' => floor($this->startdate / 86400),
                'enddate' => floor($this->enddate / 86400)
            ];
            $courses = $this->get_courses_of_user($this->get_current_user());
            // Temporary course table.
            $coursetable = 'tmp_visitsonsite_courses';
            // Creating temporary table.
            utility::create_temp_table($coursetable, array_keys($courses));
            switch ($timeperiod . '-' . $userid . '-' . $this->precalculated) {
                case 'weekly-0-1':
                case 'monthly-0-1':
                case 'yearly-0-1':
                    $sql = "SELECT sd.datecreated, sum(" . $DB->sql_cast_char2int("sd.datavalue", true) . ") visits
                              FROM {edwreports_summary_detailed} sd
                              JOIN {{$coursetable}} ct ON sd.course = ct.tempid
                             WHERE " . $DB->sql_compare_text('sd.datakey', 255) . " = " . $DB->sql_compare_text(':datakey', 255) . "
                               AND sd.datecreated >= :startdate
                               AND sd.datecreated <= :enddate
                             GROUP BY sd.datecreated";
                    $params['datakey'] = 'studentengagement-visits';
                    break;
                default:

                    $wheresql = " JOIN {{$coursetable}} ct ON al.course = ct.tempid
                                 WHERE al.datecreated >= :startdate
                                   AND al.datecreated <= :enddate
                                   AND al.userid <> 0";

                    if ($userid !== 0) { // User is selected in dropdown.
                        $params['userid'] = $userid;
                        $wheresql .= ' AND al.userid = :userid ';
                    }

                    $sql = "SELECT al.datecreated, count(al.id) visits
                            FROM {edwreports_activity_log} al
                            $wheresql
                            GROUP BY al.datecreated";

                    break;
            }
            $logs = $DB->get_records_sql($sql, $params);
            foreach ($logs as $log) {
                if (!isset($this->dates[$log->datecreated])) {
                    continue;
                }
                $this->dates[$log->datecreated] = $log->visits;
            }
            $response = [
                'visits' => array_values($this->dates),
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

        // Time period.
        $filterobject->date = implode('-', $filter);

        // Fetching graph record.
        $records = $this->get_data($filterobject);

        $labelcallback = function($label) {
            return date('d-m-Y', $label / 1000);
        };

        $export = [[
            get_string('date'),
            get_string('visitsonsiteheader', 'local_edwiserreports')
        ]];
        $recordname = 'visits';

        if (is_array($recordname)) {
            $datacallback = function(&$row, $recordnames, $key, $records) {
                foreach ($recordnames as $recordname) {
                    $value = isset($records[$recordname][$key]) ? $records[$recordname][$key] : 0;
                    $row[] = $value;
                }
            };
        } else {
            $datacallback = function(&$row, $recordname, $key, $records) {
                $value = isset($records[$recordname][$key]) ? $records[$recordname][$key] : 0;
                $row[] = $value;
            };
        }
        foreach ($records['labels'] as $key => $label) {
            $row = [$labelcallback == null ? $label : $labelcallback($label)];
            $datacallback($row, $recordname, $key, $records);
            $export[] = $row;
        }
        return $export;
    }
}
