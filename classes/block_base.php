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
use context_system;

/**
 * Abstract class for reports_block
 */
class block_base {
    /** Prepare layout */
    public $layout;

    /** Prepare blocks data */
    public $block;

    /**
     * Constructor to prepate data
     */
    public function __construct() {
        $context = context_system::instance();

        $this->layout = new stdClass();
        $this->layout->sesskey = sesskey();
        $this->layout->class = '';
        $this->layout->contextid = $context->id;

        $this->block = new stdClass();
    }

    /**
     * Create blocks data
     */
    public function get_data($params = false) {
        debugging('extend the reports_block class and add get_data function');
    }

    /**
     * Preapre layout for each block
     */
    public function get_layout() {
        debugging('extend the reports_block class and add get_layout function');   
    }

    /**
     * Create blocks data
     */
    public function render_block($templatename, $context = array()) {
        global $PAGE;

        $base = new \plugin_renderer_base($PAGE, RENDERER_TARGET_GENERAL);
        return $base->render_from_template('report_elucidsitereport/' . $templatename, $context);
    }

    /**
     * Generate cache key for blocks
     */
    public function generate_cache_key($blockname, $id, $cohortid = 0) {
        return $blockname . "-" . $id . "-" . $cohortid;
    }

    /**
     * Set block size
     */
    public function set_block_size($params) {
        $sizes = array();
        $sizes[BLOCK_DESKTOP_VIEW] = $params[BLOCK_DESKTOP_VIEW];
        $sizes[BLOCK_TABLET_VIEW] = $params[BLOCK_TABLET_VIEW];
        $sizes[BLOCK_MOBILE_VIEW] = $params[BLOCK_MOBILE_VIEW];

        $devicecolclass = array(
            BLOCK_DESKTOP_VIEW => 'col-lg-',
            BLOCK_TABLET_VIEW => 'col-md-',
            BLOCK_MOBILE_VIEW => 'col-sm-'
        );

        foreach ($sizes as $media => $size) {
            switch($size) {
                case BLOCK_LARGE:
                    $this->layout->class .= $devicecolclass[$media] . '12 ';
                    break;
                case BLOCK_MEDIUM:
                    $this->layout->class .= $devicecolclass[$media] . '6 ';
                    break;
                case BLOCK_SMALL:
                    $this->layout->class .= $devicecolclass[$media] . '4 ';
                    break;
                default:
                    break;
            }
        }
    }
}