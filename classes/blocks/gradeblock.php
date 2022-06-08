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
 * Reports abstract block will define here to which will extend for each repoers blocks
 *
 * @package     local_edwiserreports
 * @copyright   2021 wisdmlabs <support@wisdmlabs.com>
 * @author      Yogesh Shirsath
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserreports;

defined('MOODLE_INTERNAL') or die;

require_once($CFG->dirroot . '/local/edwiserreports/classes/block_base.php');

/**
 * Active users block.
 */
class gradeblock extends block_base {

    /**
     * Preapre layout for each block
     * @return object Layout
     */
    public function get_layout() {
        global $CFG;

        // Layout related data.
        $this->layout->id = 'gradeblock';
        $this->layout->name = get_string('gradeheader', 'local_edwiserreports');
        $this->layout->info = get_string('gradeblockhelp', 'local_edwiserreports');
        $this->layout->filters = $this->get_grade_filter();
        $this->layout->pro = $this->image_icon('lock');

        // Add block view in layout.
        $this->layout->blockview = $this->render_block('gradeblock', $this->block);

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
    public function get_grade_filter($onlycourses = false) {
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

        // Return only courses array if $onlycourses is true.
        if ($onlycourses == true) {
            return $courses;
        }
        return $OUTPUT->render_from_template('local_edwiserreports/gradeblockfilters', [
            'courses' => $courses,
            'students' => $users
        ]);
    }

    /**
     * Get pie chart data
     *
     * @return array
     */
    public function get_graph_data() {

        // Default grade scores.
        $gradescores = [
            '0% - 20%' => 79,
            '21% - 40%' => 48,
            '41% - 60%' => 78,
            '61% - 80%' => 35,
            '81% - 100%' => 24
        ];

        $labels = array_keys($gradescores);
        $grades = array_values($gradescores);
        $response = [
            'labels' => $labels,
            'grades' => $grades,
            'header' => get_string('studentgrades', 'local_edwiserreports'),
            'average' => array_sum($grades) / count($grades)
        ];
        return $response;
    }
}
