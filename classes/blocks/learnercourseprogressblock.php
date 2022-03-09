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
use moodle_url;
use context_system;
/**
 * Class Visits on site. To get the data related to Visits on site.
 */
class learnercourseprogressblock extends block_base {

    /**
     * Preapre layout for Visits on site
     * @return object Layout object
     */
    public function get_layout() {
        global $CFG;

        // Layout related data.
        $this->layout->id = 'learnercourseprogressblock';
        $this->layout->name = get_string('learnercourseprogressheader', 'local_edwiserreports');
        $this->layout->info = get_string('learnercourseprogressblockhelp', 'local_edwiserreports');
        $this->layout->filters = $this->get_filter();
        $this->layout->pro = $this->image_icon('lock');

        // Add block view in layout.
        $this->layout->blockview = $this->render_block('learnercourseprogressblock', $this->block);

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
        $courses = [[
            'id' => 0,
            'fullname' => get_string('fulllistofcourses')
        ]];

        for ($i = 1; $i <= 5; $i++) {
            $courses[] = [
                'id' => $i,
                'fullname' => get_string('course') . ' ' . $i
            ];
        }
        return $OUTPUT->render_from_template('local_edwiserreports/learnercourseprogressblockfilters', [
            'courses' => $courses
        ]);
    }

    /**
     * Use this method to return data for block.
     * Get Data for block
     * @param  object $filter Filter object
     * @return object         Response
     */
    public function get_data($filter = false) {
        $labels = [];
        $progress = [
            80,
            64,
            88,
            97,
            59
        ];

        for ($i = 1; $i <= 5; $i++) {
            $labels[] = get_string('course') . ' ' . $i;
        }

        return [
            'labels' => $labels,
            'progress' => $progress
        ];
    }
}
