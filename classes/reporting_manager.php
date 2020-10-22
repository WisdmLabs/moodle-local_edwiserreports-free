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
 * Reportng manager operations are defined here.
 *
 * @package     local_edwiserreports
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserreports;

defined('MOODLE_INTERNAL') || die();

use context_system;

/**
 * Class to make reporting manager related operations.
 */
class reporting_manager
{
    public static $instance = false;
    public $userid;
    public $isrpm = false;
    public $rpmusers = array();
    public $insql = '> 1';
    public $inparams = array();
    public $rpmcache = '';
    public $rpmindentusers = array();

    /**
     * Private constructor to make this a singleton
     *
     * @access private
     */
    private function __construct() {
        global $DB;
        $this->check_user_is_reporting_manager();
        if ($this->isrpm) {
            $this->get_repoting_manager_students();
            $this->get_reporting_manager_sql();
            $this->get_reporting_manager_cachekey();
        }
    }

    /**
     * Get RPM level hierarchy
     * @param  [int]   &$indentlvl Indent Level
     * @param  [int]   $userid     User Id
     */
    public function get_rpm_hierarchy2(&$rpmindentusers, $prefix, $userid, $level) {
        global $DB;

        // Restrict levels of reporting managers.
        if ($level > 2) {
            return false;
        }

        // Get curent users reporting persons.
        $sql = "SELECT uid.*
                FROM {user_info_data} uid,
                     {user_info_field} uif
                WHERE uif.shortname = :rolename
                AND uid.data = :data
                AND uid.fieldid = uif.id
                AND uid.data > 0";
        $params = array('rolename' => 'reportingmanager', 'data' => $userid);
        $data = $DB->get_records_sql($sql, $params);

        // If data is empty then return false.
        if (empty($data)) {
            return false;
        }

        // Get all rpm users data.
        foreach ($data as $key => $d) {
            $this->prepare_rpm_uers_data($rpmindentusers, $d, $prefix, $level);
        }
        // Return RPM indent data.
        return $rpmindentusers;
    }

    /**
     * Prepare rpm users data
     * @param  [type] &$rpmindentusers [description]
     * @param  [type] $d               [description]
     * @param  [type] &$prefix         [description]
     * @param  [type] $level           [description]
     * @return [type]                  [description]
     */
    public function prepare_rpm_uers_data(&$rpmindentusers, $d, &$prefix, $level) {
        $user = \core_user::get_user($d->userid);
        $uname = fullname($user);

        // If already get all child reporting manager then ingnore.
        if (isset($rpmindentusers[$d->userid]) && $rpmindentusers[$d->userid]) {
            if (strlen($rpmindentusers[$d->userid]->uname) > strlen($uname)) {
                return false;
            }
        }

        $rpmindentusers[$d->userid] = new \stdClass();
        $rpmindentusers[$d->userid]->id = $d->userid;
        $rpmindentusers[$d->userid]->level = $level;

        $rpmindentusers[$d->userid]->uname = $uname;
        $child = $this->get_rpm_hierarchy2($rpmindentusers, $uname, $d->userid, $level + 1);

        // If there is no children then remove this from reporting managers.
        if (!$child || empty(array_filter($child))) {
            $rpmindentusers[$d->userid] = false;
        }

        return $rpmindentusers;
    }

    /**
     * Get RPM level hierarchy
     * @param  [int]   &$indentlvl Indent Level
     * @param  [int]   $userid     User Id
     */
    public function get_rpm_hierarchy($indentlvl, $userid) {
        global $DB;

        // If indent level exceeded.
        if ($indentlvl > 5) {
            return false;
        }

        // Get curent users reporting persons.
        $sql = "SELECT * FROM {user_info_data} WHERE data = :data";
        $data = $DB->get_records_sql($sql, array('data' => $userid));

        // If data is empty then return false.
        if (empty($data)) {
            return false;
        }

        // Get reporting managers hierarchy.
        $rpmindentusers = array();

        // Get all rpm users data.
        $count = 0;
        foreach ($data as $key => $d) {
            $user = \core_user::get_user($d->userid);
            $rpmindentusers[$count] = new \stdClass();
            $rpmindentusers[$count]->id = $d->userid;
            $rpmindentusers[$count]->uname = fullname($user);
            $rpmindentusers[$count]->child = $this->get_rpm_hierarchy($indentlvl++, $d->userid);
            if ($rpmindentusers[$count]->child) {
                $rpmindentusers[$count]->haschild = true;
            }
            $count++;
        }

        // Return RPM indent data.
        return $rpmindentusers;
    }

    /**
     * Function to instantiate our class and make it a singleton
     */
    public static function get_instance() {
        if (!self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    /**
     * Check user is reporting manager or not
     * @return [boolean] Reporting manager
     */
    public function check_user_is_reporting_manager() {
        global $USER;
        $this->userid = $USER->id;
        $roles = get_user_roles(\context_system::instance(), $this->userid);
        if (!empty($roles)) {
            foreach ($roles as $role) {
                if ($role->shortname == 'reportingmanager') {
                    $this->isrpm = true;
                }
            }
        }
    }
    /**
     * Get reporting manager students
     * @return [array] [reporting manager students]
     */
    public function get_repoting_manager_students() {
        global $DB;
        // Query to get users of reporting manager.
        $sql = "SELECT userid FROM {user_info_data} WHERE data = ? OR data IN (SELECT userid FROM {user_info_data} WHERE data = ?)";

        // Get all users who are inactive.
        $users = $DB->get_records_sql($sql, array($this->userid, $this->userid));
        $this->rpmusers = array_keys($users);
    }
    /**
     * Function to get reportingmanager SQL IN query
     */
    public function get_reporting_manager_sql() {
        global $DB;
        if (empty($this->rpmusers )) {
            $this->insql = 'IN(1.2)';
            $this->inparams = array();
        } else {
            // Get reporeting manager studets in "In" query.
            list($this->insql, $this->inparams) = $DB->get_in_or_equal($this->rpmusers, SQL_PARAMS_NAMED, 'param', true);
        }
    }
    /**
     * Function to get reportingmanager cachekey
     */
    public function get_reporting_manager_cachekey() {
        // Set cache for reporting manager.
        $this->rpmcache = "_".$this->userid;
    }
    /**
     * Get all reporting managers
     */
    public function get_all_reporting_managers() {
        global $DB;
        $sql = 'SELECT u.id, concat(u.firstname, " ", u.lastname) as uname
        FROM {user} u
        JOIN {role_assignments} ra ON ra.userid = u.id
        JOIN {role} r ON r.id = ra.roleid
        WHERE r.shortname = :shortname';
        $params['shortname'] = 'reportingmanager';
        $rpms = $DB->get_records_sql($sql, $params);
        return array_values($rpms);
    }
    /**
     * Get students of all reporting managers
     * @param  [array] $reportingmanagers
     */
    public function get_all_reporting_managers_students($reportingmanagers = null) {
        global $DB;
        $insql = '> 1';
        $inparams = array();
        if ($reportingmanagers) {
            list($insql, $inparams) = $DB->get_in_or_equal($reportingmanagers, SQL_PARAMS_NAMED, 'rpm', true);
        }
        $sql = 'SELECT d.userid
        FROM {user_info_data} d
        JOIN {role_assignments} ra ON ra.userid = d.data
        JOIN {role} r ON r.id = ra.roleid
        WHERE r.shortname = :shortname AND d.data '.$insql;
        $params['shortname'] = 'reportingmanager';
        $params = array_merge($params, $inparams);
        $records = $DB->get_records_sql($sql, $params);
        return array_keys($records);
    }
}
