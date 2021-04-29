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

namespace local_edwiserreports;

defined('MOODLE_INTERNAL') || die;

use stdClass;
use moodle_url;

require_once($CFG->dirroot . '/local/edwiserreports/classes/block_base.php');

/**
 * Course progress block.
 */
class customreportsblock extends block_base {
    /**
     * Layout variable to prepare layout
     *
     * @var String
     */
    public $layout;

    /**
     * Get reports data for Course Progress block
     * @param  object $params Parameters
     * @return object         Response object
     */
    public function get_data($params = false) {
        global $DB;

        $fields = $params->fields;
        $courses = $params->courses;
        $cohorts = $params->cohorts;

        // Get selected fields in query format.
        list($customfields, $header, $columns, $resultfunc) = $this->create_query_fields($fields);

        // Check courses.
        $coursedb = '> 1';
        $params = array();
        if (!in_array(0, $courses)) {
            list($coursedb, $inparams) = $DB->get_in_or_equal($courses, SQL_PARAMS_NAMED, 'course', true, true);
            $params = array_merge($params, $inparams);
        }

        // Check Cohorts.
        $allusers = false;
        if (!in_array(0, $cohorts)) {
            $cohorts = \local_edwiserreports\utility::get_cohort_users($cohorts);
            $userids = array_column($cohorts['users'], 'id');
        } else {
            $allusers = true;
        }

        $userdb = '> 1';
        if (!$allusers) {
            list($userdb, $uparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'user', true, true);
            $params = array_merge($params, $uparams);
        }

        // Main query to execute the custom query reports.
        $sql = 'SELECT '.$customfields.'
                FROM {user} u
                JOIN {role_assignments} ra ON ra.userid = u.id
                JOIN {role} r ON r.id = ra.roleid
                JOIN {context} ct ON ct.id = ra.contextid
                JOIN {course} c ON c.id = ct.instanceid
                JOIN {edwreports_course_progress} ec ON ec.courseid = c.id AND ec.userid = u.id AND c.id '.$coursedb.'
                JOIN {course_categories} ctg ON ctg.id = c.category
                JOIN {course_format_options} cfo ON c.id = cfo.courseid
                JOIN {enrol} e ON c.id = e.courseid AND e.status = 0
                WHERE u.id '.$userdb.'
                AND ct.contextlevel = '.CONTEXT_COURSE.'
                AND r.archetype = \'student\'
                AND u.deleted = 0';

        $recordset = $DB->get_recordset_sql($sql, $params);
        $records = array();
        while ($recordset->key()) {
            $record = $recordset->current();
            if (!empty($resultfunc)) {
                foreach ($resultfunc as $id => $func) {
                    $record->$id = $func($record->$id);
                }
            }

            if (!in_array($record, $records)) {
                $records[] = $record;
            }
            $recordset->next();
        }

        $return = new stdClass();
        $return->columns = $columns;
        $return->reportsdata = array_values($records);

        // Return response.
        return $return;
    }

    /**
     * Create Query Fields by Filters
     * @param  array $fields Filtered fields
     * @return array         Fields array
     */
    public function create_query_fields($fields) {
        global $CFG;

        require_once($CFG->dirroot . '/local/edwiserreports/classes/output/custom_reports_block.php');

        // Get all the fields.
        $customreportsblock = new \local_edwiserreports\output\custom_reports_block();
        $userfields = $customreportsblock->get_custom_report_user_fields();
        $coursefields = $customreportsblock->get_custom_report_course_fields();
        $allfields = array_merge($userfields, $coursefields);

        // Sort fields according to selected fields.
        $header = array();
        $columns = array();
        $resultfunc = array();
        $allfields = array_map(function($value) use ($fields, &$header, &$columns, &$resultfunc) {
            if (in_array($value['id'], (array) $fields) ) {
                $header[] = $value['text'];
                $col = new stdClass();
                $col->data = $value['id'];
                $col->title = $value['text'];
                if (isset($value["resultfunc"])) {
                    $resultfunc[$value['id']] = $value['resultfunc'];
                }
                $columns[] = $col;
                return $value['dbkey'].' as '.$value['id'];
            }
            return false;
        }, $allfields);

        // Filter it and make a string.
        $allfields = array_filter($allfields);
        $allfields = implode(', ', $allfields);
        return array($allfields, $header, $columns, $resultfunc);
    }

    /**
     * Preapre layout for each block
     * @return object Response object
     */
    public function get_layout() {
        global $CFG, $DB;

        $customreport = $DB->get_record('edwreports_custom_reports', array('id' => $this->blockid));
        $reportsdata = json_decode($customreport->data);
        $reportsdata->fields = $reportsdata->selectedfield;
        unset($reportsdata->selectedfield);

        // Layout related data.
        $this->layout->id = 'customreportsblock-' . $customreport->id;
        $this->layout->name = $customreport->fullname;
        $this->layout->info = $customreport->fullname;
        $this->layout->hasdownloadlink = $reportsdata->downloadenable;
        $this->layout->iscustomblock = true;
        $this->layout->params = json_encode($reportsdata);

        // Set block edit capabilities.
        $this->set_block_edit_capabilities($this->layout->id);

        // Return blocks layout.
        return $this->layout;
    }

    /**
     * Get exportable data block.
     * @param  Integer $reportsid Custom Reports Id
     * @return Array              Array of records
     */
    public function get_exportable_data_block($reportsid) {
        global $DB;
        $customreport = $DB->get_record('edwreports_custom_reports', array('id' => $reportsid));
        $params = json_decode($customreport->data);
        $params->fields = $params->selectedfield;
        unset($params->selectedfield);

        $records = $this->get_data($params);
        $header = array_column($records->columns, 'title');
        $data = array_map(function($record) {
            return array_values((array) $record);
        }, $records->reportsdata);

        return array_merge(array($header), $data);
    }
}
