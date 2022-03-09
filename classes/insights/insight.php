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
 * Insight cards logic.
 *
 * @package     local_edwiserreports
 * @copyright   2022 wisdmlabs <support@wisdmlabs.com>
 * @author      Yogesh Shirsath
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserreports\insights;

use context_system;

/**
 * Class for insight details
 */
class insight {
    /**
     * @var array $insight Insights list.
     */
    public $insights = array();

    /**
     * Constructor
     */
    public function __construct() {
        $this->insights = array(
            'newregistrations' => array(
                'icon' => $this->image_icon('registration'),
                'title' => get_string('newregistrations', 'local_edwiserreports'),
                'internal' => true
            ),
            'courseenrolments' => array(
                'icon' => $this->image_icon('enrolment'),
                'title' => get_string('courseenrolments', 'local_edwiserreports'),
                'internal' => true
            ),
            'coursecompletions' => array(
                'icon' => $this->image_icon('coursecompletion'),
                'title' => get_string('coursecompletions', 'local_edwiserreports'),
                'internal' => true
            ),
            'activeusers' => array(
                'icon' => $this->image_icon('activeusers'),
                'title' => get_string('activeusers', 'local_edwiserreports'),
                'internal' => true
            ),
            'activitycompletions' => array(
                'icon' => $this->image_icon('activitycompletion'),
                'title' => get_string('activitycompletions', 'local_edwiserreports'),
                'internal' => true
            ),
            'timespentoncourses' => array(
                'icon' => $this->image_icon('timespent'),
                'title' => get_string('timespentoncourses', 'local_edwiserreports'),
                'internal' => true
            ),
            'totalcoursesenrolled' => array(
                'icon' => $this->image_icon('enrolment'),
                'title' => get_string('totalcoursesenrolled', 'local_edwiserreports'),
                'internal' => true
            ),
            'coursecompleted' => array(
                'icon' => $this->image_icon('coursecompletion'),
                'title' => get_string('coursecompleted', 'local_edwiserreports'),
                'internal' => true
            ),
            'activitiescompleted' => array(
                'icon' => $this->image_icon('activitycompletion'),
                'title' => get_string('activitiescompleted', 'local_edwiserreports'),
                'internal' => true
            ),
            'timespentonsite' => array(
                'icon' => $this->image_icon('timespent'),
                'title' => get_string('timespentonsite', 'local_edwiserreports'),
                'internal' => true
            )
        );
    }

    /**
     * Get svg content.
     *
     * @return string
     */
    private function image_icon($type) {
        global $CFG;
        $image = file_get_contents($CFG->dirroot . '/local/edwiserreports/pix/' . $type . '.svg');
        return $image;
    }

    /**
     * Get insight card list to render.
     *
     * @return void
     */
    public function get_insights() {
        $insights = array();
        // Get context.
        $context = context_system::instance();
        foreach ($this->insights as $key => $insight) {
            // Check capability.
            $capname = 'report/edwiserreports_insight' . $key . ':view';
            if (!has_capability($capname, $context, null) &&
                !can_view_block($capname)) {
                continue;
            }
            $insight['id'] = $key;
            $insights[$key] = $insight;
        }
        $visible = $additional = [];
        $preference = get_user_preferences('local_edwiserreports_insights_order');
        if ($preference = json_decode($preference)) {
            foreach ($preference as $key) {
                if (isset($insights[$key])) {
                    $insights[$key]['lock'] = $this->image_icon('lock');
                    $visible[] = $insights[$key];
                    unset($insights[$key]);
                }
            }
            $additional = array_values($insights);
        } else {
            foreach ($insights as $key => $insight) {
                if (count($visible) <= 3) {
                    $insight['lock'] = $this->image_icon('lock');
                    $visible[] = $insight;
                } else {
                    $additional[] = $insight;
                }
                unset($key);
            }
        }
        return [
            'visible' => $visible,
            'additional' => $additional,
            'hasadditional' => count($additional) > 0
        ];
    }

    /**
     * Get insight card details based on id.
     *
     * @param string $id Insight card id
     *
     * @return array
     */
    public function get_card_context(string $id) {
        if (isset($this->insights[$id])) {
            $insight = $this->insights[$id];
            $insight['id'] = $id;
            $insight['lock'] = $this->image_icon('lock');
            $insight['present'] = true;
            return $insight;
        }
        return [
            'present' => false
        ];
    }
}
