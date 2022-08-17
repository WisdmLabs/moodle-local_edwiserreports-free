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

use local_edwiserreports\block_base;
use html_writer;
use moodle_url;
use stdClass;

/**
 * Class courseengagement Block. To get the data related to courseengagement block.
 */
class courseengagementblock extends block_base {
    /**
     * Preapre layout for courseengagement block
     * @return object Layout object
     */
    public function get_layout() {

        // Layout related data.
        $this->layout->id = 'courseengagementblock';
        $this->layout->name = get_string('courseengagementheader', 'local_edwiserreports');
        $this->layout->info = get_string('courseengagementblockhelp', 'local_edwiserreports');
        $this->layout->pro = $this->image_icon('lock');

        // To add export links.
        $this->layout->downloadlinks = $this->get_block_download_links();

        // Add block view in layout.
        $this->layout->blockview = $this->render_block('courseengagementblock', $this->block);

        // Add filters.
        $this->layout->filters = $this->get_filter();

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

        return $OUTPUT->render_from_template('local_edwiserreports/courseengagementblockfilters', [
            'cohort' => $this->get_cohorts(),
            'searchicon' => $this->image_icon('actions/search'),
            'placeholder' => get_string('searchcourse', 'local_edwiserreports')
        ]);
    }

    /**
     * Use this method to return data for block.
     * Get Data for block
     * @param  object $params Parameteres
     * @return object         Response
     */
    public function get_data($params = false) {
        $response = new stdClass();
        $response->data = $this->get_courseengage();
        return $response;
    }

    /**
     * Get Course Engagement Data
     * @return array           Array of course engagement
     */
    public function get_courseengage() {
        global $DB;
        $engagedata = array();
        $userid = $this->get_current_user();
        $courses = $this->get_courses_of_user($userid);
        $categories = $DB->get_records_sql('SELECT id, name FROM {course_categories}');
        unset($courses[SITEID]);

        foreach ($courses as $course) {
            $engagedata[] = $this->get_engagement(
                $course,
                $categories[$course->category]->name
            );
        }
        return $engagedata;
    }

    /**
     * Get Course Engagement for a course
     * @param object $course    Course object
     * @param string $category  Category name
     * @return object           Engagement data
     */
    public function get_engagement($course, $category) {
        // Generate course url.
        $courseurl = new moodle_url(
            "/local/edwiserreports/completion.php",
            array(
                'courseid' => $course->id,
                'backurl' => new moodle_url('/local/edwiserreports/index.php')
            )
        );

        // Create engagement object.
        $engagement = new stdClass();

        // Get course name with course url.
        $engagement->coursename = html_writer::tag('a', $course->fullname, ['href' => $courseurl, 'class' => 'course-link']);

        // Course category.
        $engagement->category = $category;

        $enrolments = [
            866,
            845,
            699,
            717,
            772,
            522,
            709,
            818,
            589,
            555,
            752
        ];

        $completed = [
            343,
            419,
            263,
            262,
            388,
            335,
            238,
            398,
            124,
            478,
            221
        ];

        $visits = [
            13113,
            11301,
            11348,
            7966,
            14428,
            11843,
            5736,
            12772,
            7115,
            15866,
            14478
        ];

        // Generate enrolments link.
        $engagement->enrolment = $this->get_course_engagement_link(
            "enrolment",
            $course,
            $enrolments[$course->id % 10]
        );

        // Generate course completion link.
        $engagement->coursecompleted = $this->get_course_engagement_link(
            "coursecompleted",
            $course,
            $completed[$course->id % 10]
        );

        // Calculate completion percentage.
        $engagement->completionspercentage = $this->get_course_engagement_link(
            "coursecompleted",
            $course,
            round($completed[$course->id % 10] / $enrolments[$course->id % 10] * 100, 2)
        );

        // Generate visits link.
        $engagement->visited = $this->get_course_engagement_link(
            "visited",
            $course,
            $visits[$course->id % 10]
        );

        $engagement->averagevisits = round($visits[$course->id % 10] / $enrolments[$course->id % 10], 2);

        $timespent = [
            88926,
            75971,
            53890,
            70318,
            54719,
            99109,
            64198,
            77565,
            79997,
            61198,
            69022
        ];

        $seconds = $timespent[$course->id % 10];

        // Generate timespent link.
        $engagement->timespent = $this->get_course_engagement_link(
            "timespent",
            $course,
            sprintf("%02d:%02d:%02d", floor($seconds / 3600), ($seconds / 60) % 60, $seconds % 60)
        );

        // Generate Average timespent on course.
        $seconds = round($seconds / $enrolments[$course->id % 10]);
        $engagement->averagetimespent = sprintf("%02d:%02d:%02d", floor($seconds / 3600), ($seconds / 60) % 60, $seconds % 60);

        // Return engagement object.
        return $engagement;
    }


    /**
     * Get Engagement Attributes
     * @param  string $attrname Attribute name
     * @param  object $course   Course object
     * @param  string $val      Value for link
     * @return string           HTML link
     */
    public static function get_course_engagement_link($attrname, $course, $val) {
        return html_writer::link("javascript:void(0)", $val,
            array(
                "class" => "modal-trigger",
                "data-courseid" => $course->id,
                "data-coursename" => $course->fullname,
                "data-action" => $attrname
            )
        );
    }
}
