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
        $this->layout->id = 'visitsonsiteblock';
        $this->layout->name = get_string('visitsonsiteheader', 'local_edwiserreports');
        $this->layout->info = get_string('visitsonsiteblockhelp', 'local_edwiserreports');
        $this->layout->filters = $this->get_filter();
        $this->layout->pro = $this->image_icon('lock');

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
        global $OUTPUT;

        $users = [[
            'id' => 0,
            'fullname' => get_string('allusers', 'search')
        ]];

        for ($i = 1; $i <= 100; $i++) {
            $users[] = [
                'id' => $i,
                'fullname' => 'User ' . $i
            ];
        }
        return $OUTPUT->render_from_template('local_edwiserreports/visitsonsiteblockfilters', [
            'students' => $users
        ]);
    }

    /**
     * Generate labels and dates array for graph
     * @param array $visits Visits array
     */
    private function generate_labels($visits) {
        $this->labels = [];
        $this->xlabelcount = count($visits);
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
     * @param array $visits Visits array
     * @return object
     */
    public function calculate_insight($visits) {
        $total = array_sum($visits);
        $insight = [
            'insight' => [
                'title' => 'averagesitevisits',
                'value' => floor($total / $this->xlabelcount),
                'difference' => [
                    'direction' => 1,
                    'value' => '26.22'
                ]
            ],
            'details' => [
                'data' => [[
                    'title' => 'totalsitevisits',
                    'value' => $total
                ]]
            ]
        ];
        return $insight;
    }

    /**
     * Use this method to return data for block.
     * Get Data for block
     * @param bool $filters Filter
     * @return object         Response
     */
    public function get_data($filter = false) {
        $visits = [
            801,
            842,
            853,
            965,
            807,
            808,
            912,
            945,
            871,
            871,
            964,
            919,
            925,
            933,
            994,
            849,
            826,
            800,
            870,
            814,
            873,
            955,
            897,
            878,
            905,
            895,
            865,
            929,
            880,
            951,
            849
        ];

        $this->generate_labels($visits);

        $response = [
            'visits' => $visits,
            'labels' => $this->labels,
            'insight' => $this->calculate_insight($visits)
        ];


        return $response;
    }
}
