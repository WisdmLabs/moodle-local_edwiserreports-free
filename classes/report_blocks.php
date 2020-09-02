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
 * Get Reports blocks to render in the dashboard
 *
 * @package     report_elucidsitereport
 * @copyright   2020 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_elucidsitereport;

/**
 * Class to serve the report blocks
 */
class report_blocks {
    /**
     * Reports block
     */
    protected $reports_block;

    /**
     * Constructor to prepare all reports blocks
     */
    public function __construct($blocks) {
        global $CFG;

        // Prepare layout for each block
        foreach ($blocks as $key => $block) {
            // Check if class file exist
            $classname = $block->classname;
            $filepath = $CFG->dirroot . '/report/elucidsitereport/classes/blocks/' . $classname . '.php';
            if (!file_exists($filepath)) {
                debugging('Class file dosn\'t exist ' . $classname);
            }
            require_once($filepath);

            $classname = '\\report_elucidsitereport\\' . $classname;
            $blockbase = new $classname();
            $this->reports_block[] = $blockbase->get_layout();
        }
    }

    /**
     * Functions to get report blocks
     */
    public function get_report_blocks() {
        return $this->reports_block;
    }
}