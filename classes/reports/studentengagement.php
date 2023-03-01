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
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserreports\reports;

use local_edwiserreports\block_base;
use local_edwiserreports\utility;
use html_writer;
use moodle_url;


/**
 * Active users block.
 */
class studentengagement extends block_base {
    /**
     * Get Breadcrumbs for Course Completion
     * @return object Breadcrumbs for Course Completion
     */
    public function get_breadcrumb() {

        return array(
            'items' => array(
                array(
                    'item' => get_string('alllearnersummary', 'local_edwiserreports')
                )
            )
        );
    }

    /**
     * Prepare active users block filters
     * @return array filters array
     */
    public function get_studentengagement_courses() {
        global $USER, $COURSE, $USER;

        $courses = $this->get_courses_of_user($USER->id);

        unset($courses[$COURSE->id]);

        array_unshift($courses, (object)[
            'id' => 0,
            'fullname' => get_string('fulllistofcourses')
        ]);

        return $courses;
    }

    /**
     * Get student engagement table data based on filters
     *
     * @param object $filter Table filters.
     *
     * @return array
     */
    public function get_table_data($filter) {
        $response = array();

        $search = html_writer::tag('i', '', ['class' => 'fa fa-search-plus']);
        $link = html_writer::link(
                    new moodle_url(
                        "/local/edwiserreports/learnercourseprogress.php",
                        array("learner" => 2)
                    ),
                    $search,
                    array(
                        'style' => 'margin-left: 0.5rem;'
                    )
                );


        for ($i = 0; $i < 20; $i++) {
            $name = 'Student ';
            $response[] = array(
                'student' => $name . $i,
                'lastaccesson' => '2 Jan 2022 <br> 09:39 AM',
                'email' => $name . $i . '@' . $name . $i . '.com',
                'status' => $i % 2 != 0 ? 0 : 1,
                'enrolledcourses' => $i . $link,
                'inprogresscourses' => $i,
                'completedcourses' => 2,
                'completionprogress' => $i + 30 . '%',
                'totalgrade' => 100 + $i * 3,
                'timespentonlms' => $i . ':30:45',
                'timespentoncourse' => '01:' . $i . ':34',
                'activitiescompleted' => $i,
                'visitsoncourse' => 1000 + $i * 3,
                'completedassignments' => 5,
                'completedquizzes' => 4,
                'completedscorms' => 10,
            );
        }

        return [
            "data" => $response,
            "recordsTotal" => 21,
            "recordsFiltered" => 21
        ];
    }

    /**
     * Get exportable data for report.
     * @param  string $filter     Filter to apply on data
     * @param  bool   $filterdata If enabled then filter data
     * @return array
     */
    public static function get_exportable_data_report($filter, $filterdata = true) {
        $filter = json_decode($filter);
        if (!$filterdata) {
            $filter->cohort = 0;
            $filter->course = 0;
            $filter->group = 0;
        }

        $filter->search = "";
        $filter->start = 0;
        $filter->length = 0;
        $filter->table = false;

        $obj = new self();

        $header = [
            get_string('student', 'local_edwiserreports'),
            get_string('email', 'local_edwiserreports'),
            get_string('status', 'local_edwiserreports'),
            get_string('lastaccesson', 'local_edwiserreports')
        ];

        if ($filter->course == 0) {
            $header = array_merge($header, [
                get_string('enrolledcourses', 'local_edwiserreports'),
                get_string('inprogresscourse', 'local_edwiserreports'),
                get_string('completecourse', 'local_edwiserreports')
            ]);
        }
        $header = array_merge($header, [
            get_string('completionprogress', 'local_edwiserreports'),
            get_string('totalgrade', 'local_edwiserreports'),
            get_string('timespentonlms', 'local_edwiserreports'),
            get_string('timespentoncourse', 'local_edwiserreports'),
            get_string('activitiescompleted', 'local_edwiserreports'),
            get_string('visitsoncourse', 'local_edwiserreports'),
            get_string('completedassign', 'local_edwiserreports'),
            get_string('completedquiz', 'local_edwiserreports'),
            get_string('completedscorm', 'local_edwiserreports')
        ]);

        $data = $obj->get_table_data($filter)['data'];
        array_unshift($data, $header);

        return (object) [
            'data' => $data,
            'options' => [
                'format' => 'a4',
                'orientation' => 'l',
            ]
        ];
    }

    /**
     * Get Exportable data for Course Completion Page
     * @param  string $filters    Filter string
     * @param  bool   $filterdata If enabled then filter data
     * @return array              Array of LP Stats
     */
    public function get_summary_data($filters, $filterdata = true) {
        global $CFG;
        return array(
            'body' => array(
                array(
                    'title'   => get_string('totalvisits', 'local_edwiserreports'),
                    'data' => '2080'
                ),
                array(
                    'title'   => get_string('avgvisits', 'local_edwiserreports'),
                    'data' => '150'
                )
            ),
            'footer' => array(
                array(
                    'icon'  => file_get_contents($CFG->dirroot . '/local/edwiserreports/pix/summary-card/learners.svg'),
                    'title' => get_string('totallearners', 'local_edwiserreports'),
                    'data'  => '20'
                ),
                array(
                    'icon'  => file_get_contents($CFG->dirroot . '/local/edwiserreports/pix/summary-card/time.svg'),
                    'title' => get_string('totaltimespentoncourse', 'local_edwiserreports'),
                    'data'  => '10:02:45'
                ),
                array(
                    'icon'  => file_get_contents($CFG->dirroot . '/local/edwiserreports/pix/summary-card/avgtime.svg'),
                    'title' => get_string('avgtimespentoncourse', 'local_edwiserreports'),
                    'data'  => '00:30:89'
                )
            )
        );
    }




}
