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
 * @package     local_sitereport
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_sitereport;

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
        $this->layout->canedit = true;
        $this->layout->caneditadv = false;

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
        // @codingStandardsIgnoreStart
        global $PAGE;

        $base = new \plugin_renderer_base($PAGE, RENDERER_TARGET_GENERAL);
        // @codingStandardsIgnoreEnd
        return $base->render_from_template('local_sitereport/' . $templatename, $context);
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
        $sizes[LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW] = $params[LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW];
        $sizes[LOCAL_SITEREPORT_BLOCK_TABLET_VIEW] = $params[LOCAL_SITEREPORT_BLOCK_TABLET_VIEW];
        $sizes[LOCAL_SITEREPORT_BLOCK_MOBILE_VIEW] = $params[LOCAL_SITEREPORT_BLOCK_MOBILE_VIEW];

        $devicecolclass = array(
            LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => 'col-lg-',
            LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => 'col-md-',
            LOCAL_SITEREPORT_BLOCK_MOBILE_VIEW => 'col-sm-'
        );

        foreach ($sizes as $media => $size) {
            switch($size) {
                case LOCAL_SITEREPORT_BLOCK_LARGE:
                    $this->layout->class .= $devicecolclass[$media] . '12 ';
                    break;
                case LOCAL_SITEREPORT_BLOCK_MEDIUM:
                    $this->layout->class .= $devicecolclass[$media] . '6 ';
                    break;
                case LOCAL_SITEREPORT_BLOCK_SMLOCAL_SITEREPORT_ALL:
                    $this->layout->class .= $devicecolclass[$media] . '4 ';
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * Get block position
     */
    public function get_block_position($pref) {
        $position = $pref['position'];

    }

    /**
     * Set block edit capabilities for each block
     */
    public function set_block_edit_capabilities($blockname) {
        $context = context_system::instance();

        // Based on capability show the edit button
        // If user dont have capability to see the block.
        $this->layout->canedit = has_capability('report/sitereport_' . $blockname . ':edit', $context);
        $this->layout->caneditadv = has_capability('report/sitereport_' . $blockname . ':editadvance', $context);

        // If have capability to edit.
        $this->layout->editopt = $this->layout->canedit || $this->layout->caneditadv;
    }
}
