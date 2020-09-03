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
 * Reports abstract block will define here to which will extend for each repoers blocks
 *
 * @package     report_elucidsitereport
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_elucidsitereport;

use stdClass;
use moodle_url;

require_once($CFG->dirroot . '/report/elucidsitereport/classes/block_base.php');

class activeusersblock extends block_base {
    /**
     * Get reports data for active users block
     */
    public function get_data($id, $cohortid = 0) {

    }

    /**
     * Preapre layout for each block
     */
    public function get_layout() {
        global $CFG;

        // Layout related data
        $this->layout->id = 'activeusersblock';
        $this->layout->class = 'col-6';
        $this->layout->name = get_string('activeusersheader', 'report_elucidsitereport');
        $this->layout->info = get_string('activeusersblocktitlehelp', 'report_elucidsitereport');
        $this->layout->morelink = new moodle_url($CFG->wwwroot . "/report/elucidsitereport/activeusers.php");
        $this->layout->hasdownloadlink = true;
        $this->layout->filters = '';

        // Block related data
        $this->block = new stdClass();
        $this->block->displaytype = 'line-chart';

        // Add block view in layout
        $this->layout->blockview = $this->render_block('activeusersblock', $this->block);

        // Return blocks layout
        return $this->layout;
    }
}