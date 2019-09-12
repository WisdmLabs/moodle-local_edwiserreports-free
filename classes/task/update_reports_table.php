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
 * Plugin administration pages are defined here.
 *
 * @package     report_elucidsitereport
 * @category    admin
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_elucidsitereport\task;

defined('MOODLE_INTERNAL') || die();

use stdClass;
use context_course;
 
/**
 * Scheduled Task to Update Report Plugin Table.
 */
class update_reports_table extends \core\task\scheduled_task {
 
    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('updatetables', 'report_elucidsitereport');
    }
 
    /**
     * Execute the task.
     */
    public function execute() {
        global $DB;

        // Report Completion Table
        $tablename = "elucidsitereport_completion";

        // Get all courses to get completion
        // $courses = get_courses();

        $data = array();
        mtrace(get_string('updatingrecordstarted', 'report_elucidsitereport'));

        // SQL Query to get completions records
        $sql = "SELECT CONCAT(mc.userid, '-', m.course) as id,
            mc.userid, m.course as courseid, (COUNT(mc.userid)/
            (SELECT COUNT(*) FROM {course_modules}
            WHERE completion = m.completion
            AND course = m.course)) AS 'progress'
            FROM {course_modules} m, {course_modules_completion} mc
            WHERE m.id=mc.coursemoduleid
            AND mc.completionstate = :completionstatus
            AND m.completion > :completion
            GROUP BY mc.userid, m.course";

        // Parameters to get completions
        $params = array(
            "completion" => 0,
            "completionstatus" => true
        );

        // Get compeltions records
        $records = $DB->get_records_sql($sql, $params);

        // Parse each records and save in database
        foreach($records as $key => $record) {
            $course = get_course($record->courseid);

            // Completion param to get time completion
            $completionparam = array(
                "courseid" => $record->courseid,
                "userid" => $record->userid
            );

            // Get Course Comletion Time
            $timecompleted = \report_elucidsitereport\utility::get_time_completion($record->courseid, $record->userid);

            // Get Progress Percantage
            $progressper = 0;
            $completion = \report_elucidsitereport\utility::get_course_completion_info($course, $record->userid);
            // If completion is not empty then update progress percentage
            if (!empty($completion)) {
                $completion = (object) $completion;
                $progressper = $completion->progresspercentage;
            }

            // Get Course Grades
            $coursegrade = 0;
            $grades = \report_elucidsitereport\utility::get_grades($record->courseid, $record->userid);
            // If course grade is set then update course grade
            if ($grades && $grades->finalgrade) {
                $coursegrade = $grades->finalgrade;
            }

            // Created data oabject
            $dataobject = array(
                "courseid" => $record->courseid,
                "userid" => $record->userid,
                "completion" => $progressper,
                "grade" => $coursegrade,
                "timecompleted" => $timecompleted
            );

            $strparams = array(
                'userid' => $record->userid,
                'courseid' => $record->courseid
            );

            // Get previous completion recordid
            $prevcompletion = $DB->get_record($tablename, $completionparam, "id");
            if ($prevcompletion) {
                // If same record then dont update
                if ($DB->record_exists($tablename, $dataobject)) {
                    continue;
                }

                mtrace(get_string('updatinguserrecord', 'report_elucidsitereport', $strparams));
                // If exist then Update records
                $dataobject["id"] = $prevcompletion->id;
                $DB->update_record($tablename, $dataobject);
            } else {
                // Save data to inseart ar the end
                mtrace(get_string('gettinguserrecord', 'report_elucidsitereport', $strparams));
                $data[] = $dataobject;
            }
        }
        // If not exist then insert records
        mtrace(get_string('creatinguserrecord', 'report_elucidsitereport'));
        $DB->insert_records($tablename, $data);
        mtrace(get_string('updatingrecordended', 'report_elucidsitereport'));
    }
}