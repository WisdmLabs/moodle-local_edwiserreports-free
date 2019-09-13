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

namespace report_elucidsitereport;

require_once($CFG->libdir."/csvlib.class.php");
require_once($CFG->libdir."/excellib.class.php");
require_once($CFG->libdir."/pdflib.php");
require_once($CFG->dirroot."/report/elucidsitereport/classes/blocks/active_users_block.php");
require_once($CFG->dirroot."/report/elucidsitereport/classes/blocks/active_courses_block.php");

use csv_export_writer;
use moodle_exception;
use core_user;
use context_course;
use MoodleExcelWorkbook;
use pdf;
use html_table;
use html_writer;
use html_table_row;
use html_table_cell;
use file_storage;
use stdClass;

class export {
    /**
     * Export data in this format
     */
    public $format = null;

    /**
     * Region to download reports
     * This may be block or report
     */
    public $region = null;

    /**
     * Action to get data for specific block
     */
    public $blockname = null;

    /**
     * Constructor to create export object
     * @param $format type os export object
     */
    public function __construct($format, $region, $blockname) {
        $this->format = $format;
        $this->region = $region;
        $this->blockname = $blockname;
    }

    /**
     * Export data
     * @param $filenme file name to export data
     * @param $data data to be export
     * @return Return status after export the data
     */
    public function data_export($filename, $data) {
        switch($this->format) {
            case "csv":
                $this->data_export_csv($filename, $data);
                break;
            case "excel":
                $this->data_export_excel($filename, $data);
                break;
            case "pdf":
                $this->data_export_pdf($filename, $data);
                break;
            case "email":
                $this->data_export_email($filename, $data);
                break;
        }
    }

    /**
     * Export data in CSV format
     * @param $filenme file name to export data
     * @param $data data to be export
     * @return Return status after export the data
     */
    public function data_export_csv($filename, $data) {
        csv_export_writer::download_array($filename, $data);
    }

    /**
     * Export data in Excel format
     * @param $filenme file name to export data
     * @param $data data to be export
     * @return Return status after export the data
     */
    public function data_export_excel($filename, $data) {
        // Creating a workbook
        $workbook = new MoodleExcelWorkbook("-");

        // Adding the worksheet
        $myxls = $workbook->add_worksheet($this->region . "_" . $this->blockname);

        foreach ($data as $rownum => $row) {
            foreach ($row as $colnum => $val) {
                $myxls->write_string($rownum, $colnum, $val);
            }
        }

        // Sending HTTP headers
        $workbook->send($filename);

        // Close the workbook
        $workbook->close();
    }

    /**
     * Export data in Pdf format
     * @param $filenme file name to export data
     * @param $data data to be export
     * @return Return status after export the data
     */
    public function data_export_pdf($filename, $data) {
        $filename .= '.pdf';
        $this->generate_pdf_file($filename, $data, "D");
    }

    /**
     * Export data email to user
     * @param $filenme file name to export data
     * @param $data data to be export
     * @return Return status after export the data
     */
    public function data_export_email($filename, $data) {
        global $USER;
        $recuser = $USER;
        $senduser = core_user::get_noreply_user();

        // Generate file to send emails
        $filename .= '.pdf';
        $filepath = $this->generate_pdf_file($filename, $data, "F");

        // Get email data from submited form
        $emailids = trim(optional_param("email", false, PARAM_TEXT));
        $subject = trim(optional_param("subject", false, PARAM_TEXT));

        // Optional parameter causing issue because this is an array
        $content = $_POST["content"];

        // If subject is not set the get default subject
        if (!$subject && $subject == '') {
            $subject = get_string($this->blockname . "exportheader", "report_elucidsitereport");
        }

        // Get content text to send emails
        $contenttext = '';
        if ($content["format"] == 1) { // Text Foramt = 1
            // If content text is not set then set default value of content text
            if (!$content || !isset($content["text"]) || $content["text"] == '') {
                $contenttext = get_string($this->blockname . "exporthelp", "report_elucidsitereport");
            } else {
                $contenttext = $content["text"];
            }
        }

        // Send emails foreach email ids
        if ($emailids && $emailids !== '') {
            // process in background and dont show message in console
            ob_start();
            foreach(explode(";", $emailids) as $emailid) {
                // trim email id if white spaces are added
                $recuser->email = trim($emailid);

                // Send email to user
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
            ob_end_clean();

            // If failed then return error
            $res = new stdClass();
            $res->error = false;
            $res->errormsg = get_string('emailsent', 'report_elucidsitereport');
            echo json_encode($res);
        } else {
            // If failed then return error
            $res = new stdClass();
            $res->error = true;
            $res->errormsg = get_string('emailnotsent', 'report_elucidsitereport');
            echo json_encode($res);
        }

        // Remove file after email sending process
        unlink($filepath);
    }

    /**
     * Generate PDF file to export
     * @param [string] $filename File Name
     * @param [array] $data Data to export
     * @param [string] $destination location to create file
     * @return [string] File Path
     */
    private function generate_pdf_file($filename, $data, $dest) {
        global $CFG;
        $pdf = new pdf();

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        // $pdf->SetAutoPageBreak(true, 72);

        $pdf->AddPage();

        // Generate HTML to export
        ob_start();
        $html = $this->get_html_for_pdf($data);
        ob_clean();

        //  Create proper HTML ro export in PDF
        $pdf->writeHTML($html);

        if ($dest == "F") {
            $filepath = $CFG->tempdir . '/' . $filename;
        } else {
            $filepath = $filename;
        }

        $pdf->Output($filepath, $dest);

        return $filepath;
    }

    /**
     * Get HTML Content to export
     * @param  [array] $data Array of exportable Data
     * @return [string] HTML String
     */
    public function get_html_for_pdf($data) {
        $table = new html_table();
        foreach ($data as $key => $val) {
            if ($key == 0) {
                foreach($val as $v) {
                    $table->head[] = html_writer::tag("h4", $v);
                }
                $cell = new html_table_cell();
                $cell->colspan = count($val);
                $cell->style = "border-top: 1px solid #000; padding: 5px;";
                $row = new html_table_row(array($cell));
                $table->data[] = $row;
            } else {
                $table->data[] = $val;
            }
        }
        // Generate HTML to export
        $html = html_writer::tag("h1",
            get_string($this->blockname . "exportheader", "report_elucidsitereport"),
            array(
                "style" => "text-align:center" 
            )
        ).
        html_writer::tag("p",
            get_string($this->blockname . "exporthelp", "report_elucidsitereport"),
            array(
                "style" => "text-indent: 50px"
            )
        ).
        html_writer::table($table);

        return $html;
    }

    /**
     * Get exportable data to export
     * @param $filenme file name to export data
     * @param $data data to be export
     * @return Return status after export the data
     */
    public function get_exportable_data($filter) {
        $export = null;

        switch ($this->region) {
            case "block":
                $export = $this->exportable_data_block($this->blockname, $filter);
                break;
            case "report":
                $export = $this->exportable_data_report($this->blockname, $filter);
                break;
        }

        return $export;
    }

    /**
     * Get exportable data for dashboard block
     * @param [string] $blockname Block to get exportable data
     * @param [string] $filter Filter to get data
     * @return [array] Array of exportable data
     */
    private function exportable_data_block($blockname, $filter) {
        $export = null;
        switch ($blockname) {
            case "activeusers":
                $export = active_users_block::get_exportable_data_block($filter);
                break;
            case "activecourses":
                $export = active_courses_block::get_exportable_data_block();
                break;
            case "courseprogress":
                $export = course_progress_block::get_exportable_data_block($filter);
                break;
            case "certificates":
                $export = certificates_block::get_exportable_data_block($filter);
                break;
            case "f2fsession":
                $export = f2fsession_block::get_exportable_data_block($filter);
                break;
            case "lpstats":
                $export = lpstats_block::get_exportable_data_block($filter);
                break;
        }
        return $export;
    }

    /**
     * Get exportable data for individual page
     * @param [string] $blockname Block to get exportable data
     * @param [string] $filter Filter to get data
     * @return [array] Array of exportable data
     */
    private function exportable_data_report($blockname, $filter) {
        $export = null;
        switch ($blockname) {
            case "activeusers":
                $export = active_users_block::get_exportable_data_report($filter);
                break;
            case "courseprogress":
                $export = course_progress_block::get_exportable_data_report($filter);
                break;
            case "courseengage":
                $export = courseengage_block::get_exportable_data_report();
                break;
            case "certificates":
                $export = certificates_block::get_exportable_data_report($filter);
                break;
            case "f2fsession":
                $export = f2fsession_block::get_exportable_data_report($filter);
                break;
            case "lpstats":
                $export = lpstats_block::get_exportable_data_report($filter);
                break;
            case "completion":
                $export = completion_block::get_exportable_data_report($filter);
                break;
            case "courseanalytics":
                $export = courseanalytics_block::get_exportable_data_report($filter);
                break;
        }
        return $export;
    }
}
