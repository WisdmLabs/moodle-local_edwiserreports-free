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
     * Dates main array.
     *
     * @var array
     */
    public $dates = [];

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
        $this->layout->pro = $this->image_icon('lock');

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
        global $OUTPUT;

        $users = [[
            'id' => 0,
            'fullname' => get_string('allusers', 'search')
        ]];

        $courses = [[
            'id' => 0,
            'fullname' => get_string('fulllistofcourses')
        ]];

        for ($i = 1; $i <= 5; $i++) {
            $users[] = [
                'id' => $i,
                'fullname' => get_string('user') . ' ' . $i
            ];
            $courses[] = [
                'id' => $i,
                'fullname' => get_string('course') . ' ' . $i
            ];
        }

        for ($i = 5; $i <= 100; $i++) {
            $users[] = [
                'id' => $i,
                'fullname' => 'User ' . $i
            ];
        }

        return $OUTPUT->render_from_template('local_edwiserreports/courseactivitystatusblockfilters', [
            'courses' => $courses,
            'students' => $users
        ]);
    }

    /**
     * Generate labels and dates array for graph
     * @param int $days Days
     */
    private function generate_labels($days) {
        $this->labels = [];
        $this->xlabelcount = $days;
        $this->enddate = floor(time() / 86400 + 1) * 86400 - 1;
        $this->startdate = (round($this->enddate / 86400) - $this->xlabelcount) * 86400;

        // Get all lables.
        for ($i = $this->xlabelcount - 1; $i >= 0; $i--) {
            $time = $this->enddate - $i * LOCAL_SITEREPORT_ONEDAY;
            $this->labels[] = $time * 1000;
        }
    }

    /**
     * Calculate insight data for active users block.
     * @param array $submissions Submissions array
     * @param array $completions Completions array
     * @param int   $days        Days
     * @return object
     */
    public function calculate_insight($submissions, $completions, $days) {
        $totalsubmission = array_sum($submissions);
        $totalcompletion = array_sum($completions);
        $insight = [
            'insight' => [
                'title' => 'averagecompletion',
                'value' => floor($totalcompletion / $days),
                'difference' => [
                    'direction' => 1,
                    'value' => '41.5'
                ]
            ],
            'details' => [
                'data' => [[
                    'title' => 'totalassignment',
                    'value' => $totalsubmission
                ], [
                    'title' => 'totalcompletion',
                    'value' => $totalcompletion
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

        $submissions = [
            336,
            314,
            356,
            343,
            355,
            336,
            375,
            390,
            304,
            363,
            399,
            317,
            393,
            388,
            393,
            334,
            312,
            369,
            362,
            364,
            335,
            395,
            306,
            300,
            389,
            325,
            385,
            379,
            358,
            388,
            301
        ];

        $completions = [
            255,
            251,
            259,
            273,
            274,
            272,
            282,
            290,
            264,
            265,
            269,
            277,
            262,
            284,
            262,
            284,
            299,
            259,
            266,
            279,
            257,
            257,
            255,
            296,
            295,
            300,
            250,
            255,
            251,
            299,
            281
        ];

        $days = count($submissions);

        $this->generate_labels($days);

        $response = [
            'submissions' => array_values($submissions),
            'completions' => array_values($completions),
            'labels' => $this->labels,
            'insight' => $this->calculate_insight($submissions, $completions, $days)
        ];

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
