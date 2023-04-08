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
 * Reports base class.
 *
 * @package     local_edwiserreports
 * @category    reports
 * @author      Yogesh Shirsath
 * @copyright   2022 Wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserreports\reports;

use local_edwiserreports\block_base;

class base {

    /**
     * Initialization
     */
    public function __construct() {
        $this->bb = new block_base();
    }

    /**
     * Get sections list.
     *
     * @param int $courseid Course id.
     *
     * @return array
     */
    public function get_sections($courseid) {
        global $DB;

        // Prepare sections list.
        $sections = get_fast_modinfo($courseid)->get_section_info_all();

        $sql = "SELECT cm.section, COUNT(cm.id) mods
                  FROM {course_modules} cm
                 WHERE cm.course = :course
              GROUP BY cm.section";
        $mods = $DB->get_records_sql($sql, ['course' => $courseid]);
        if (count($mods) < 2) {
            return [];
        }

        $sectionlist = [[
            'id' => 0,
            'name' => get_string('allsections', 'local_edwiserreports'),
            'selected' => 'selected'
        ]];

        $courseformat = course_get_format($courseid);

        foreach ($sections as $section) {
            if (!isset($mods[$section->id]) || $mods[$section->id]->mods < 1) {
                continue;
            }
            $sectionlist[] = [
                'id' => $section->id,
                'name' => $courseformat->get_section_name($section)
            ];
        }
        return $sectionlist;
    }

    /**
     * Get module types from course id.
     *
     * @param int   $courseid   Course id
     * @param mixed $sectionid  Section id. all/id
     *
     * @return array
     */
    public function get_modules($courseid, $sectionid = 0) {
        global $DB;
        $modules = [[
            'type' => 'all',
            'name' => get_string('allmodules', 'local_edwiserreports'),
            'selected' => 'selected'
        ]];

        $params = ['course' => $courseid];
        $where = 'WHERE cm.course = :course';

        if ($sectionid) {
            $params['section'] = $sectionid;
            $where .= ' AND cm.section = :section';
        }

        $sql = "SELECT DISTINCT m.name
                  FROM {course_modules} cm
                  JOIN {modules} m ON cm.module = m.id
                  $where
              ORDER BY m.name";
        $records = $DB->get_records_sql($sql, $params);

        if (count($records) < 2) {
            return [];
        }

        foreach ($records as $mod) {
            $modules[] = [
                'type' => $mod->name,
                'name' => get_string('pluginname', 'mod_' . $mod->name)
            ];
        }
        return array_values($modules);
    }

    /**
     * Get cms created in course.
     *
     * @param int $courseid     Course id
     *
     * @return array
     */
    public function get_cms($courseid) {
        $cms = [];
        foreach (get_fast_modinfo($courseid)->get_cms() as $cm) {
            $cms[$cm->id] = [
                'id' => $cm->id,
                'name' => $cm->name,
                'instance' => $cm->instance,
                'module' => $cm->module,
                'modname' => $cm->modname
            ];
        }
        return $cms;
    }

    /**
     * Set block edit capabilities for each block
     * @param  String $blockname Block name
     * @return Bool              false If not supported
     */
    public function can_edit_report_capability($capname) {
        global $USER;

        if (!isset($USER->editing)) {
            return false;
        }

        // If user is not editing.
        if (!$USER->editing) {
            return false;
        }

        // Based on capability show the edit button
        // If user dont have capability to see the block.
        return \can_edit_capability('report/edwiserreports_' . $capname . ':editadvance');
    }
}
