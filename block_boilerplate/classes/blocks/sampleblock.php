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
 * Block layout and ajax service methods are defined in this file.
 *
 * @package     local_edwiserreports
 * @author      Yogesh Shirsath
 * @copyright   2022 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserreports;

use stdClass;

/**
 * Class sample Block. To get the data related to sample block.
 */
class sampleblock extends block_base {
    /**
     * Preapre layout for sample block
     * @return object Layout object
     */
    public function get_layout() {
        global $CFG;

        // Layout related data.
        $this->layout->id = 'sampleblock';
        $this->layout->name = get_string('sampleheader', 'local_edwiserreports');
        $this->layout->info = get_string('sampleblockhelp', 'local_edwiserreports');

        // To add export links.
        // $this->layout->downloadlinks = $this->get_block_download_links();.

        // Add block view in layout.
        $this->layout->blockview = $this->render_block('sampleblock', $this->block);

        // Set block edit capabilities.
        $this->set_block_edit_capabilities($this->layout->id);

        // Return blocks layout.
        return $this->layout;
    }

    /**
     * Use this method to return data for block.
     * Get Data for block
     * @param  object $params Parameteres
     * @return object         Response
     */
    public function get_data($params = false) {
        $response = new stdClass();
        $response->data = "Data";
        return $response;
    }

    /**
     * If block is exporting any data then include this method.
     * Get Exportable data for sample Block
     * @return array Array of exportable data
     */
    public static function get_exportable_data_block() {
        $export = array();
        return $export;
    }


}
