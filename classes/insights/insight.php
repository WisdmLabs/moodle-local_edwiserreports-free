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
use context_helper;
use cache;
use local_edwiserreports\block_base;

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
     * @param string $timeperiod Filter time period Weekly/Monthly/Yearly or custom dates.
     *
     * @return array
     */
    private function generate_dates($timeperiod) {
        $enddate = floor(time() / 86400 + 1) * 86400 - 1;
        switch ($timeperiod) {
            case 'weekly':
                // Monthly days.
                $days = LOCAL_SITEREPORT_WEEKLY_DAYS;
                break;
            case 'monthly':
                // Yearly days.
                $days = LOCAL_SITEREPORT_MONTHLY_DAYS;
                break;
            case 'yearly':
                // Weekly days.
                $days = LOCAL_SITEREPORT_YEARLY_DAYS;
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
                    $enddate = $enddate;
                } else {
                    $days = LOCAL_SITEREPORT_WEEKLY_DAYS;
                }
                break;
        }

        $startdate = (round($enddate / 86400) - $days) * 86400;

        $timedifference = $enddate - $startdate;
        $oldenddate = $startdate - 1;
        $oldstartdate = $oldenddate - $timedifference;

        return [
            $startdate,
            $enddate,
            $oldstartdate,
            $oldenddate,
        ];
    }

    /**
     * Get students courses.
     *
     * @return array
     */
    private function get_students_courses() {
        $blockbase = new block_base();
        $userid = $blockbase->get_current_user();
        // Admin or Manager.
        if (is_siteadmin($userid) || has_capability('moodle/site:configview', context_system::instance(), $userid)) {
            return get_courses();
        }

        $courses = enrol_get_all_users_courses($userid);

        // Preload contexts and check visibility.
        foreach ($courses as $id => $course) {
            context_helper::preload_from_record($course);
            if (!$course->visible) {
                unset($courses[$id]);
                continue;
            }
        }
        return $courses;
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
            $startdate,
            $enddate,
            $oldstartdate,
            $oldenddate
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

            if ($difference == 0) {
                $data = [
                    'value' => $currentdata
                ];
            } else if ($difference > 0) {
                $data = [
                    'value' => $currentdata,
                    'difference' => [
                        'direction' => true,
                        'value' => floor($difference / $currentdata * 100)
                    ]
                ];
            } else {
                $data = [
                    'value' => $currentdata,
                    'difference' => [
                        'direction' => false,
                        'value' => floor($difference / $olddata * -100)
                    ]
                ];
            }

            $cache->set($id . '-' . $filter, $data);

            return $data;
        }
    }
}
