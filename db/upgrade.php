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
 * Code to be executed after the plugin's database scheme has been installed is defined here.
 *
 * @package     local_edwiserreports
 * @category    upgrade
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/edwiserreports/lib.php');

/**
 * Custom code to be run on upgrading the plugin.
 * @param int $oldversion Plugin's old version
 * @return bool True if upgrade successful
 */
function xmldb_local_edwiserreports_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // Check the old version.
    if (2020030400 <= $oldversion) {
        // Table name to be removed.
        $tablename = 'edwiserReport_completion';

        // Get all tables.
        $tables = $DB->get_tables();

        // If table exist.
        if (isset($tables[$tablename])) {
            $DB->execute('DROP table {' . $tablename . '}');
        }
    }

    if (2020120911 >= $oldversion) {

        // Define table block_remuiblck_tasklist to be created.
        $table = new xmldb_table('edwreports_custom_reports');

        // Adding fields to table block_remuiblck_tasklist.
        $table->add_field('id', XMLDB_TYPE_INTEGER, 10, null, true, true);
        $table->add_field('shortname', XMLDB_TYPE_CHAR, 255, null, true);
        $table->add_field('fullname', XMLDB_TYPE_CHAR, 255, null, true);
        $table->add_field('createdby', XMLDB_TYPE_INTEGER, 10, null, true);
        $table->add_field('data', XMLDB_TYPE_TEXT);
        $table->add_field('enabledesktop', XMLDB_TYPE_INTEGER, 2, null, true);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, 10, null, true, false, 0);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, 10, null, true);
        // Adding keys to table block_remuiblck_taskslist.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for block_remuiblck_taskslist.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Update table entry for comletion.
        $tablename = 'edwreports_course_progress';
        // Get all tables.
        $tables = $DB->get_tables();

        // If table exist.
        if (isset($tables[$tablename])) {
            // Update table data.
            $DB->set_field($tablename, 'pchange', true);
        }
    }

    if (2021040900 >= $oldversion) {

        $table = new xmldb_table('edwreports_custom_reports');

        // Change data field type to text.
        $field = new xmldb_field('data', XMLDB_TYPE_TEXT);
        $dbman->change_field_type($table, $field);
    }

    // Authentication table.
    $table = new xmldb_table('edwreports_authentication');
    if (!$dbman->table_exists($table)) {
        // Table fields.
        $table->add_field('id', XMLDB_TYPE_INTEGER, 10, null, true, true);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, 10, null, true);
        $table->add_field('secret', XMLDB_TYPE_TEXT, 10, null, true);

        // Table keys.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('unique', XMLDB_KEY_UNIQUE, array('userid'));

        // Create the table.
        $dbman->create_table($table);
    }

    // Adding new column in course progress table for storing completable activities.
    $table = new xmldb_table('edwreports_course_progress');

    $field = new xmldb_field('completablemods', XMLDB_TYPE_INTEGER, 10, null, true, false, 0);

    if ($dbman->table_exists($table) && !$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
        $DB->set_field('edwreports_course_progress', 'pchange', true);
    }

    // Removing activity log table.
    $table = new xmldb_table('edwreports_activity_log');
    if ($dbman->table_exists($table)) {
        $dbman->drop_table($table);
    }

    local_edwiserreports_process_block_creation();

    unset_config('siteaccessinformation', 'local_edwiserreports');

    unset_config('activecoursesdata', 'local_edwiserreports');

    set_config('siteaccessrecalculate', true, 'local_edwiserreports');

    return true;
}
