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
 * @package     local_edwiserreports
 * @category    admin
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserreports\task;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot."/local/edwiserreports/classes/export.php");

use core_user;
use stdClass;
use local_edwiserreports\export;

defined('MOODLE_INTERNAL') || die();

/**
 * Scheduled Task to Update Report Plugin Table.
 */
class send_scheduled_emails extends \core\task\scheduled_task {

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('sendscheduledemails', 'local_edwiserreports');
    }

    /**
     * Execute the task.
     */
    public function execute() {
        global $DB;

        $timenow = time();

        // Get data from table.
        $table = "edwreports_schedemails";
        $records = $DB->get_records($table);
        foreach ($records as $key => $record) {
            // If it dosent have email data.
            if (!$emaildata = json_decode($record->emaildata)) {
                continue;
            }

            // If dta is not an array.
            if (!is_array($emaildata)) {
                continue;
            }

            // If empty then continue.
            if (empty($emaildata)) {
                continue;
            }

            foreach ($emaildata as $k => $email) {
                // Not scheduled for this time.
                if ($timenow < $email->esrnextrun) {
                    echo "Not scheduled yet";
                    continue;
                }

                if (!$email->esremailenable) {
                    echo "Email Disable";
                    continue;
                }

                if (!isset($email->reportparams)) {
                    echo "No reports param";
                    continue;
                }

                $filter = $email->reportparams->filter;
                $region = $email->reportparams->region;
                $blockname = $email->reportparams->blockname;

                $export = new export("email", $region, $blockname);
                $data = $export->get_exportable_data($filter);

                // If data exist then send emails.
                if ($data) {
                    mtrace(get_string('sendingscheduledemails', 'local_edwiserreports'));
                    ob_start();

                    // If email successfully sent.
                    $this->send_sceduled_email($export, $data, $email);
                    $email->esrlastrun = time();

                    $esrduration = $email->esrduration;
                    $esrtime = $email->esrtime;
                    list($frequency, $schedtime) = local_edwiserreports_get_email_schedule_next_run($esrduration, $esrtime);
                    $email->esrnextrun = $schedtime;
                    $emaildata[$k] = $email;
                    ob_clean();
                }
            }

            $record->emaildata = json_encode($emaildata);
            $DB->update_record($table, $record);
        }
    }

    /**
     * Send Shcedule Email
     *
     * @param  object $export    Export object
     * @param  object $data      Data to export
     * @param  object $emailinfo Email information
     */
    private function send_sceduled_email($export, $data, $emailinfo) {
        global $USER;

        $region = $emailinfo->reportparams->region;
        $blockname = $emailinfo->reportparams->blockname;

        $recuser = $USER;
        $senduser = core_user::get_noreply_user();

        // Generate file to send emails.
        $filename = $region . '_' . $blockname . '.csv';
        $filepath = $export->generate_csv_file($filename, $data, "F");

        // Get email data from submited form.
        $emailids = trim($emailinfo->esrrecepient);
        $subject = trim($emailinfo->esrsubject);

        // Optional parameter causing issue because this is an array.
        $contenttext = trim($emailinfo->esrmessage);

        // If subject is not set the get default subject.
        if (!$subject && $subject == '') {
            $subject = get_string($blockname . "exportheader", "local_edwiserreports");
        }

        // Get content text to send emails.
        if ($contenttext == '') {
            $contenttext = get_string($blockname . "exporthelp", "local_edwiserreports");
        }

        // Send emails foreach email ids.
        if ($emailids && $emailids !== '') {
            // Process in background and dont show message in console.
            ob_start();
            $emailids = explode(";", $emailids);
            foreach ($emailids as $emailcommaids) {
                foreach (explode(",", $emailcommaids) as $emailid) {
                    // Trim email id if white spaces are added.
                    $recuser->email = trim($emailid);

                    // Send email to user.
                    email_to_user(
                        $recuser,
                        $senduser,
                        $subject,
                        '',
                        $contenttext,
                        $filepath,
                        $filename
                    );
                }
            }
            ob_end_clean();
        }

        // Remove file after email sending process.
        unlink($filepath);
    }
}
