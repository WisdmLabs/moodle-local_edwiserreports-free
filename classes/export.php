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
namespace local_edwiserreports;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir."/csvlib.class.php");
require_once($CFG->libdir."/excellib.class.php");
require_once($CFG->libdir."/pdflib.php");
require_once($CFG->dirroot."/local/edwiserreports/classes/utility.php");
require_once($CFG->dirroot."/local/edwiserreports/lib.php");
require_once($CFG->dirroot."/local/edwiserreports/locallib.php");
require_once($CFG->dirroot."/local/edwiserreports/classes/output/renderable.php");

use html_writer;
use core_php_time_limit;

/**
 * Class to export data.
 */
class export {
    /**
     * Export data in this format
     * @var string
     */
    public $format = null;

    /**
     * Region to download reports
     * This may be block or report
     * @var string
     */
    public $region = null;

    /**
     * Action to get data for specific block
     * @var string
     */
    public $blockname = null;

    /**
     * Constructor to create export object
     * @param string $format    Type os export object
     * @param string $region    Region
     * @param string $blockname Name of block
     */
    public function __construct($format, $region, $blockname) {
        $this->format = $format;
        $this->region = $region;
        $this->blockname = $blockname;
    }

    /**
     * Get postfix for filename from block.
     *
     * @param string $filter
     *
     * @return string
     */
    public function data_export_file_postfix($filter) {
        global $CFG;
        if ($this->region == 'report') {
            return '';
        }
        // Check if class file exist.
        if (strpos($this->blockname, 'customreportsblock') !== false) {
            $params = explode('-', $this->blockname);
            $classname = isset($params[0]) ? $params[0] : '';
            $filter = isset($params[1]) ? $params[1] : '';
        } else {
            $classname = $this->blockname;
        }
        $filepath = $CFG->dirroot . '/local/edwiserreports/classes/blocks/' . $classname . '.php';
        if (!file_exists($filepath)) {
            debugging('Class file dosn\'t exist ' . $classname);
        }
        require_once($filepath);

        $classname = '\\local_edwiserreports\\' . $classname;
        $blockbase = new $classname();
        return $blockbase->get_exportable_data_block_file_postfix($filter);
    }

    /**
     * Get HTML Content to export
     * @param  array  $data Array of exportable Data
     * @return string       HTML String
     */
    public function get_html_for_pdf2($data) {
        global $DB;

        $headerrow = array_shift($data);
        if (strpos($this->blockname, 'customreportsblock') !== false) {
            $params = explode('-', $this->blockname);
            $filter = isset($params[1]) ? $params[1] : '';
            $header = get_string('customreport', 'local_edwiserreports');
            if ($field = $DB->get_field('edwreports_custom_reports', 'fullname', array('id' => $filter))) {
                $header .= ' - ' . $field;
            }
        } else {
            $header = get_string($this->blockname . "exportheader", "local_edwiserreports");
            $help = get_string($this->blockname . "exporthelp", "local_edwiserreports");
        }
        // Generate HTML to export.
        echo html_writer::tag("h1",
            $header,
            array(
                "style" => "width:100%; text-align:center;"
            )
        );

        echo html_writer::tag("p", $help);

        echo '<table style="font-size: 11px;" border="1px" cellpadding="3">';

        echo '<tr nobr="true">';
        foreach ($headerrow as $cell) {
            echo '<th bgcolor="#ddd" style="font-weight: bold">' . $cell . '</th>';
        }
        echo '</tr>';
        foreach ($data as $row) {
            echo '<tr nobr="true">';
            foreach ($row as $cell) {
                echo '<td>' . $cell . '</td>';
            }
            echo '</tr>';
        }

        echo '</table>';
    }

    /**
     * Export data
     * @param string $filename File name to export data
     * @param array  $data     Data to be export
     * @param array  $options  Options for pdf export
     */
    public function data_export($filename, $data, $options = null) {
        global $CFG;

        $filename .= '.pdf';

        $orientation = 'p';
        $format = 'A4';
        if ($options != null) {
            if (isset($options['orientation'])) {
                $orientation = $options['orientation'];
            }
            if (isset($options['format'])) {
                $format = $options['format'];
            }
        }

        // Raise memory and time limit.
        raise_memory_limit(MEMORY_HUGE); // MEMORY_HUGE uses 2G or MEMORY_EXTRA, whichever is bigger.
        core_php_time_limit::raise(1200); // Setting time limit to 20 minutes.

        // Generate HTML to export.
        ob_start();
        $this->get_html_for_pdf2($data);
        $html = ob_get_clean();

        require_once($CFG->libdir.'/pdflib.php');
        $pdf = new \pdf($orientation, 'pt', $format);
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->AddPage();
        $pdf->WriteHTML($html, true, false, false, false, '');
        $pdf->Output($filename, 'D');
        die;
    }

    /**
     * Get exportable data to export
     * @param  array        $filter Filter parameter
     * @return array|object         Return array for table. Return object for table and options for pdf only.
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
     * @param  string $blockname Block to get exportable data
     * @param  string $filter    Filter to get data
     * @return array             Array of exportable data
     */
    private function exportable_data_block($blockname, $filter) {
        global $CFG;

        // Check if class file exist.
        if (strpos($blockname, 'customreportsblock') !== false) {
            $params = explode('-', $blockname);
            $classname = isset($params[0]) ? $params[0] : '';
            $filter = isset($params[1]) ? $params[1] : '';
        } else {
            $classname = $blockname;
        }
        $filepath = $CFG->dirroot . '/local/edwiserreports/classes/blocks/' . $classname . '.php';
        if (!file_exists($filepath)) {
            debugging('Class file dosn\'t exist ' . $classname);
        }
        require_once($filepath);

        $classname = '\\local_edwiserreports\\' . $classname;
        $blockbase = new $classname();

        return $blockbase->get_exportable_data_block($filter);
    }

    /**
     * Get exportable data for individual page
     * @param  string $blockname Block to get exportable data
     * @param  string $filter    Filter to get data
     * @return array             Array of exportable data
     */
    private function exportable_data_report($blockname, $filter) {
        $export = null;
        switch ($blockname) {
            case "activeusersblock":
                $export = activeusersblock::get_exportable_data_report($filter);
                break;
            case "courseprogressblock":
                $export = courseprogressblock::get_exportable_data_report($filter);
                break;
            case "certificatesblock":
                $export = certificatesblock::get_exportable_data_report($filter);
                break;
            case "completionblock":
                $export = completionblock::get_exportable_data_report($filter);
                break;
            case "courseanalytics":
                $export = courseanalytics_block::get_exportable_data_report($filter);
                break;
        }
        return $export;
    }
}
