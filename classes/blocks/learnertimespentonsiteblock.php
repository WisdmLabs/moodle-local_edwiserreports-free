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

/**
 * Class Visits on site. To get the data related to Visits on site.
 */
class learnertimespentonsiteblock extends block_base {

    /**
     * Dates main array.
     *
     * @var array
     */
    public $dates = [];

    /**
     * No. of labels for active users.
     *
     * @var int
     */
    public $xlabelcount;

    /**
     * The $singleday property represents a single day for which the time spent on the site is being tracked.
     */
    public $singleday;

    /**
     * The $startday property represents the start day for which the time spent on the site is being tracked.
     */

    public $startday;

    /**
     * The $endday property represents the end day for which the time spent on the site is being tracked.
     */
    public $endday;

    /**
     * The $timespent property represents the total time spent by a learner on the site.
     */
    public $timespent;

    /**
     * Active users block labels
     *
     * @var array
     */
    public $labels;

    /**
     * The start date for the data being analyzed.
     *
     * @var int
     */
    public $startdate;

    /**
     * The start date for the data being analyzed.
     *
     * @var int
     */
    public $enddate;

    /**
     * Preapre layout for Visits on site
     * @return object Layout object
     */
    public function get_layout() {
        global $OUTPUT;

        // Layout related data.
        $this->layout->id = 'learnertimespentonsiteblock';
        $this->layout->name = get_string('learnertimespentonsiteheader', 'local_edwiserreports');
        $this->layout->info = get_string('learnertimespentonsiteblockhelp', 'local_edwiserreports');
        $this->layout->filters = $OUTPUT->render_from_template('local_edwiserreports/learnertimespentonsiteblockfilters', []);
        $this->layout->pro = $this->image_icon('lock');
        $this->layout->morelink = 1;


        // Add block view in layout.
        $this->layout->blockview = $this->render_block('learnertimespentonsiteblock', $this->block);

        // Set block edit capabilities.
        $this->set_block_edit_capabilities($this->layout->id);

        // Return blocks layout.
        return $this->layout;
    }

    /**
     * Generate labels and dates array for graph
     *
     * @param string $timeperiod Filter time period Weekly/Monthly/Yearly or custom dates.
     */
    private function generate_labels($timespent) {
        $this->labels = [];
        $this->enddate = floor(time() / 86400 + 1) * 86400 - 1;
        $this->xlabelcount = count($timespent);
        $this->startdate = (round($this->enddate / 86400) - $this->xlabelcount) * 86400;

        // Get all lables.
        for ($i = $this->xlabelcount - 1; $i >= 0; $i--) {
            $time = $this->enddate - $i * LOCAL_SITEREPORT_ONEDAY;
            $this->labels[] = $time * 1000;
        }
    }

    /**
     * Use this method to return data for block.
     * Get Data for block
     * @param  object $filter Filter object
     * @return object         Response
     */
    public function get_data($filter = false) {
        $timespent = [
            9611,
            9977,
            9838,
            8762,
            9100,
            9069,
            10230,
            8752,
            9675,
            10437,
            10671,
            10423,
            10519,
            9905,
            9157,
            10599,
            9785,
            9867,
            9317,
            10625,
            9826,
            9357,
            9276,
            8675,
            9367,
            8825,
            8672,
            9939,
            10301,
            9789,
            9717
        ];

        $this->generate_labels($timespent);

        $response = [
            'timespent' => $timespent,
            'labels' => $this->labels,
        ];
        return $response;
    }
}
