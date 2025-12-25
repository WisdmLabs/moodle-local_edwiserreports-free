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

    // Handle case where tables might already exist during installation.
    // This can happen if plugin was partially installed or database state is inconsistent.
    // Moodle standard: Check and create tables only if they don't exist.
    if ($oldversion == 0) {
        // Initial installation - ensure all tables from install.xml exist.
        // If they already exist, we skip creation (Moodle standard behavior).
        
        // Check and create edwreports_blocks table if it doesn't exist.
        $table = new xmldb_table('edwreports_blocks');
        if (!$dbman->table_exists($table)) {
            $table->add_field('id', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
            $table->add_field('blockname', XMLDB_TYPE_CHAR, 255, null, XMLDB_NOTNULL, null, null, null, '');
            $table->add_field('classname', XMLDB_TYPE_CHAR, 255, null, XMLDB_NOTNULL, null, null, null, '');
            $table->add_field('blocktype', XMLDB_TYPE_INTEGER, 1, null, XMLDB_NOTNULL, null, null, null, 0);
            $table->add_field('blockdata', XMLDB_TYPE_CHAR, 255, null, null, null, null, null);
            $table->add_field('timecreated', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, null, null, 0);
            $table->add_field('timemodified', XMLDB_TYPE_INTEGER, 10, null, null, null, null, null, 0);
            $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
            $dbman->create_table($table);
        }

        // Check and create edwreports_schedemails table if it doesn't exist.
        $table = new xmldb_table('edwreports_schedemails');
        if (!$dbman->table_exists($table)) {
            $table->add_field('id', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
            $table->add_field('blockname', XMLDB_TYPE_TEXT, null, null, null, null, null, null);
            $table->add_field('component', XMLDB_TYPE_TEXT, null, null, null, null, null, null);
            $table->add_field('emaildata', XMLDB_TYPE_TEXT, null, null, null, null, null, null);
            $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
            $dbman->create_table($table);
        }

        // Check and create edwreports_course_progress table if it doesn't exist.
        $table = new xmldb_table('edwreports_course_progress');
        if (!$dbman->table_exists($table)) {
            $table->add_field('id', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
            $table->add_field('courseid', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, null, null);
            $table->add_field('userid', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, null, null);
            $table->add_field('completedmodules', XMLDB_TYPE_TEXT, null, null, null, null, null, null);
            $table->add_field('totalmodules', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, null, null, 0);
            $table->add_field('completablemods', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, null, null, 0);
            $table->add_field('progress', XMLDB_TYPE_INTEGER, 5, null, XMLDB_NOTNULL, null, null, null, 0);
            $table->add_field('completiontime', XMLDB_TYPE_INTEGER, 10, null, null, null, null, null);
            $table->add_field('pchange', XMLDB_TYPE_INTEGER, 2, null, XMLDB_NOTNULL, null, null, null, 1);
            $table->add_field('criteria', XMLDB_TYPE_INTEGER, 2, null, XMLDB_NOTNULL, null, null, null, 0);
            $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->add_index('courseid', XMLDB_INDEX_NOTUNIQUE, array('courseid'));
            $table->add_index('userid', XMLDB_INDEX_NOTUNIQUE, array('userid'));
            $dbman->create_table($table);
        }

        // Check and create edwreports_custom_reports table if it doesn't exist.
        $table = new xmldb_table('edwreports_custom_reports');
        if (!$dbman->table_exists($table)) {
            $table->add_field('id', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
            $table->add_field('shortname', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, null);
            $table->add_field('fullname', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, null);
            $table->add_field('createdby', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, null, null);
            $table->add_field('data', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, null);
            $table->add_field('enabledesktop', XMLDB_TYPE_INTEGER, 2, null, XMLDB_NOTNULL, null, null, null, 0);
            $table->add_field('timecreated', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, null, null, 0);
            $table->add_field('timemodified', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, null, null, 0);
            $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
            $dbman->create_table($table);
        }

        // Check and create edwreports_authentication table if it doesn't exist.
        $table = new xmldb_table('edwreports_authentication');
        if (!$dbman->table_exists($table)) {
            $table->add_field('id', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
            $table->add_field('userid', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, null, null);
            $table->add_field('secret', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, null);
            $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->add_key('unique', XMLDB_KEY_UNIQUE, array('userid'));
            $dbman->create_table($table);
        }
    }

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
    if ($dbman->table_exists($table)) {
        $field = new xmldb_field('completablemods', XMLDB_TYPE_INTEGER, 10, null, true, false, 0);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
            $DB->set_field('edwreports_course_progress', 'pchange', true);
        }

        // Adding courseid index on course progress table.
        $courseindex = new xmldb_index('courseid', XMLDB_INDEX_NOTUNIQUE, ['courseid']);
        if (!$dbman->index_exists($table, $courseindex)) {
            $dbman->add_index($table, $courseindex);
        }

        // Adding userid index on course progress table.
        $courseindex = new xmldb_index('userid', XMLDB_INDEX_NOTUNIQUE, ['userid']);
        if (!$dbman->index_exists($table, $courseindex)) {
            $dbman->add_index($table, $courseindex);
        }
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

    set_config('showwhatsnew', true, 'local_edwiserreports');

    return true;
}
