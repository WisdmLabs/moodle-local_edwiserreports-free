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
        $this->layout->id = 'timespentoncourseblock';
        $this->layout->name = get_string('timespentoncourseheader', 'local_edwiserreports');
        $this->layout->info = get_string('timespentoncourseblockhelp', 'local_edwiserreports');
        $this->layout->filters = $this->get_filter();
        $this->layout->pro = $this->image_icon('lock');

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
        global $OUTPUT;

        $users = [[
            'id' => 0,
            'fullname' => get_string('allusers', 'search')
        ]];

        $courses = [[
            'id' => 0,
            'fullname' => get_string('fulllistofcourses')
        ]];

        for ($i = 1; $i <= 10; $i++) {
            $users[] = [
                'id' => $i,
                'fullname' => get_string('user') . ' ' . $i
            ];
            $courses[] = [
                'id' => $i,
                'fullname' => get_string('course') . ' ' . $i
            ];
        }

        for ($i = 10; $i <= 100; $i++) {
            $users[] = [
                'id' => $i,
                'fullname' => 'User ' . $i
            ];
        }

        return $OUTPUT->render_from_template('local_edwiserreports/timespentoncourseblockfilters', [
            'courses' => $courses,
            'students' => $users
        ]);
    }

    /**
     * Calculate insight data for active users block.
     * @return object
     */
    public function calculate_insight($courses) {
        $total = array_sum($courses);
        $insight = [
            'insight' => [
                'title' => 'averagetimespent',
                'value' => $total / count($courses),
                'difference' => [
                    'direction' => 0,
                    'value' => '11'
                ]
            ],
            'details' => [
                'data' => [[
                    'title' => 'totaltimespent',
                    'value' => $total
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
        $time = [
            34108,
            82934,
            60206,
            15750,
            36693
        ];

        $courses = [];
        $labels = [];
        for ($i = 0; $i < count($time); $i++) {
            $courses[] = $time[$i];
            $labels[] = get_string('course') . ' ' . ($i + 1);
        }

        $response = [
            'timespent' => $courses,
            'labels' => $labels,
            'insight' => $this->calculate_insight($courses)
        ];

        return $response;
    }
}
