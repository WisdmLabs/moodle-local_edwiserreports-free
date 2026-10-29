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
 * Install recovery function - called by Moodle if installation fails.
 * This handles the case where tables already exist from a previous partial installation.
 * Moodle standard: Check for existing tables and set plugin version to treat as upgrade.
 */
function xmldb_local_edwiserreports_install_recovery() {
    global $DB;
    
    $dbman = $DB->get_manager();
    $component = 'local_edwiserreports';
    
    // Check if any of our tables exist.
    $tablesexist = false;
    $tables = array(
        'edwreports_blocks',
        'edwreports_schedemails',
        'edwreports_course_progress',
        'edwreports_custom_reports',
        'edwreports_authentication'
    );
    
    foreach ($tables as $tablename) {
        $table = new xmldb_table($tablename);
        if ($dbman->table_exists($table)) {
            $tablesexist = true;
            break;
        }
    }
    
    // If tables exist, set a minimum plugin version so Moodle treats this as an upgrade.
    if ($tablesexist) {
        // Set plugin version to an old version to trigger upgrade path.
        $oldversion = 2019091100; // First version from install.xml
        
        // Check if version is already set.
        $installedversion = $DB->get_field('config_plugins', 'value', 
            array('name' => 'version', 'plugin' => $component));
        
        if (empty($installedversion)) {
            // Set version to trigger upgrade path.
            set_config('version', $oldversion, $component);
        }
    }
}

/**
 * Pre-install check - This function can be called before installation
 * to check if tables exist and set plugin version accordingly.
 * Moodle standard: This prevents "table already exists" errors.
 */
function xmldb_local_edwiserreports_pre_install_check() {
    global $DB;
    
    $dbman = $DB->get_manager();
    $component = 'local_edwiserreports';
    
    // Check if any of our tables exist.
    $tables = array(
        'edwreports_blocks',
        'edwreports_schedemails',
        'edwreports_course_progress',
        'edwreports_custom_reports',
        'edwreports_authentication'
    );
    
    $tablesexist = false;
    foreach ($tables as $tablename) {
        $table = new xmldb_table($tablename);
        if ($dbman->table_exists($table)) {
            $tablesexist = true;
            break;
        }
    }
    
    // If tables exist but plugin version is not set, set it to trigger upgrade path.
    if ($tablesexist) {
        $installedversion = $DB->get_field('config_plugins', 'value', 
            array('name' => 'version', 'plugin' => $component));
        
        if (empty($installedversion)) {
            // Set version to an old version to trigger upgrade path instead of fresh install.
            set_config('version', 2019091100, $component);
            return true; // Indicates tables exist, should use upgrade path
        }
    }
    
    return false; // Tables don't exist, proceed with fresh install
}

/**
 * Custom code to be run on installing the plugin.
 */
function xmldb_local_edwiserreports_install() {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // Moodle standard: Check and create tables only if they don't exist.
    // This handles the case where tables might already exist from a previous partial installation.
    
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

    set_config('edwiserreportsinstallation', true, 'local_edwiserreports');

    // All Default blocks.
    $defaultblocks = local_edwiserreports_get_default_block_settings();

    // Create each block.
    $blocks = array();
    foreach ($defaultblocks as $key => $block) {
        $blockdata = new stdClass();
        $blockdata->blockname = $key;
        $blockdata->classname = $block['classname'];
        $blockdata->blocktype = LOCAL_SITEREPORT_BLOCK_TYPE_DEFAULT;
        $blockdata->blockdata = json_encode((object) array(
            LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => $block[LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW],
            LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => $block[LOCAL_SITEREPORT_BLOCK_TABLET_VIEW],
            'position' => $block['position']
        ));
        $blockdata->timecreated = time();
        $blocks[] = $blockdata;
    }

    // Database controller.
    $dbcontroller = new local_edwiserreports\db_controller();

    // Sync all users in installations process.
    $completionupgrade = $dbcontroller->sync_old_users_with_course_progress();
    $reportpluginupgrade = $DB->insert_records('edwreports_blocks', $blocks);

    return $completionupgrade && $reportpluginupgrade;
}
