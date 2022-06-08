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
 * Edwiser RemUI
 * @package    local_edwiserreports
 * @copyright  (c) 2021 WisdmLabs (https://wisdmlabs.com/)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yogesh Shirsath
 */
namespace local_edwiserreports\controller;

use stdClass;

class authentication {

    /**
     * Delete secret key of user using userid or secret key.
     *
     * @param int    $userid User id
     * @param string $secret Secret key
     *
     * @return bool
     */
    public function delete_secret_key(int $userid = null, string $secret = null) {
        global $USER, $DB;

        // Delete secret key record using secret key.
        if ($secret !== null) {
            return $DB->delete_records('edwreports_authentication', array('secret' => $secret));
        }

        if ($userid === null) {
            $userid = $USER->id;
        }

        // Delete secret key record using userid.
        return $DB->delete_records('edwreports_authentication', array('userid' => $userid));
    }

    /**
     * Create secret key for user.
     * If user id is false then secret key will be created for currently logged in user.
     * First existing secret key of user will be deleted.
     *
     * @param int $userid User id
     *
     * @return string Secret key
     */
    public function create_secret_key(int $userid = null): string {
        global $USER, $DB;
        if ($userid === null) {
            $userid = $USER->id;
        }

        // Delete existing secret key.
        $this->delete_secret_key($userid);

        // Generate random 10 character string using Moodle core function.
        $secret = random_string(10);
        $auth = new stdClass;
        $auth->userid = $userid;
        $auth->secret = $secret;
        $DB->insert_record('edwreports_authentication', $auth);

        return $secret;
    }

    /**
     * Get existing secret key using user id.
     *
     * @param int $userid User id
     *
     * @return string Secret key
     */
    public function get_secret_key(int $userid = null): string {
        global $USER, $DB;
        if ($userid === null) {
            $userid = $USER->id;
        }

        // If secret key present for user.
        if ($record = $DB->get_record('edwreports_authentication', array('userid' => $userid))) {
            return $record->secret;
        }

        // If secret key do not exists then create new and return.
        return $this->create_secret_key($userid);
    }


    /**
     * Get user id from table using secret key.
     *
     * @param string $secret Secret key
     *
     * @return int
     */
    public function get_user(string $secret): int {
        global $DB;

        // If secret key exists in database then return its owner.
        $sql = "SELECT * FROM {edwreports_authentication}
                WHERE " . $DB->sql_compare_text('secret') . " = ?";
        if ($record = $DB->get_record_sql($sql, array($secret))) {
            return $record->userid;
        }

        // Return false if secret key does not exists.
        return false;
    }
}
