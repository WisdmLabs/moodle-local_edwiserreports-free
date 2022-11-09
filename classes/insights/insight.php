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

defined('MOODLE_INTERNAL') or die;
require_once($CFG->dirroot . '/local/edwiserreports/classes/constants.php');

use context_system;
use cache;


/**
 * Class for insight details
 */
class insight {

    // Using all traits.
    use newregistrations;
    use courseenrolments;
    use coursecompletions;
    use activeusers;
    use activitycompletions;
    use timespentoncourses;
    use totalcoursesenrolled;
    use coursecompleted;
    use activitiescompleted;
    use timespentonsite;

    /**
     * @var array $insight Insights list.
     */
    public $insights = array();

    /**
     * Constructor
     */
    public function __construct() {
        $upgradelink = '';
        if (is_siteadmin()) {
            $upgradelink = UPGRADE_URL;
        }
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
                'internal' => true,
                'pro' => $this->image_icon('lock'),
                'upgradelink' => $upgradelink
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
                'internal' => true,
                'pro' => $this->image_icon('lock'),
                'upgradelink' => $upgradelink
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
        $preference = json_decode($preference);
        if ($preference !== null) {
            foreach ($preference as $key) {
                if (isset($insights[$key])) {
                    $visible[] = $insights[$key];
                    unset($insights[$key]);
                }
            }
            $additional = array_values($insights);
        } else {
            foreach ($insights as $key => $insight) {
                if (count($visible) <= 3) {
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
            $insight['present'] = true;
            return $insight;
        }
        return [
            'present' => false
        ];
    }

    /**
     * Generate labels and dates array for graph
     *
     * @param string $timeperiod Filter time period Last 7 Days/Weekly/Monthly/Yearly or custom dates.
     *
     * @return array
     */
    private function generate_dates($timeperiod) {
        // Get start and end date.
        $base = new \local_edwiserreports\block_base();
        list($startdate, $enddate) = $base->get_date_range($timeperiod);
        list($oldstartdate, $oldenddate) = $base->get_old_date_range($timeperiod, $startdate, $enddate);
        return [
            floor($oldstartdate / 86400),
            floor($oldenddate / 86400),
            floor($startdate / 86400),
            floor($enddate / 86400)
        ];
    }

    /**
     * Get insight card data to show insight details.
     *
     * @param string $id     Insight card id
     * @param string $filter Date filter
     *
     * @return array
     */
    public function get_card_data($id, $filter) {
        $cache = cache::make('local_edwiserreports', 'insight');
        if ($data = $cache->get($id . '-' . $filter)) {
            return $data;
        }
        $insight = $this->insights[$id];
        list(
            $oldstartdate,
            $oldenddate,
            $startdate,
            $enddate
        ) = $this->generate_dates($filter);
        if ($insight['internal']) {
            $method = 'get_' . $id . '_data';
            list($currentdata, $olddata) = $this->$method(
                $startdate,
                $enddate,
                $oldstartdate,
                $oldenddate
            );
            $difference = $currentdata - $olddata;
            $data = [
                'value' => $currentdata,
                'oldvalue' => $olddata
            ];
            switch (true) {
                case $currentdata == $olddata:
                    break;
                case $olddata == 0:
                    $data['difference'] = [
                        'direction' => true,
                        'value' => 100
                    ];
                    break;
                default:
                    $data['difference'] = [
                        'direction' => $difference > 0,
                        'value' => abs(round($difference / $olddata * 100))
                    ];
                    break;
            }

            $cache->set($id . '-' . $filter, $data);

            return $data;
        }
    }
}
