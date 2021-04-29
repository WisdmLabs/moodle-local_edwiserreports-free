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
 * @package     local_edwiserreports
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserreports;

use stdClass;
use context_system;

/**
 * Abstract class for reports_block
 */
class block_base {
    /**
     * Prepare layout
     * @var Object
     */
    public $layout;

    /**
     * Block object
     * @var Object
     */
    public $block;

    /**
     * Constructor to prepate data
     * @param Integer $blockid Block id
     */
    public function __construct($blockid = false) {
        $context = context_system::instance();

        $this->layout = new stdClass();
        $this->layout->sesskey = sesskey();
        $this->layout->extraclasses = '';
        $this->layout->contextid = $context->id;
        $this->layout->caneditadv = false;
        $this->layout->region = 'block';
        $this->block = new stdClass();

        if ($blockid) {
            $this->blockid = $blockid;
        }
    }

    /**
     * Create blocks data
     * @param Array $params Parameters
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
     * @param  String $templatename Template name to render
     * @param  Object $context      Context object
     * @return String               HTML content
     */
    public function render_block($templatename, $context = array()) {
        // @codingStandardsIgnoreStart
        global $PAGE;

        $base = new \plugin_renderer_base($PAGE, RENDERER_TARGET_GENERAL);
        // @codingStandardsIgnoreEnd
        return $base->render_from_template('local_edwiserreports/' . $templatename, $context);
    }

    /**
     * Generate cache key for blocks
     * @param  String $blockname Block name
     * @param  Int    $id        Id
     * @param  Int    $cohortid  Cohort id
     * @return String            Cache key
     */
    public function generate_cache_key($blockname, $id, $cohortid = 0) {
        return $blockname . "-" . $id . "-" . $cohortid;
    }

    /**
     * Set block size
     * @param String $block Block name
     */
    public function set_block_size($block) {
        $prefname = 'pref_' . $block->classname;
        if ($block->classname == 'customreportsblock') {
            $prefname .= '-' . $block->id;
        }

        $sizes = array();
        if ($prefrences = get_user_preferences($prefname)) {
            $blockdata = json_decode($prefrences, true);
            $position = $blockdata['position'];
            $sizes[LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW] = $blockdata[LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW];
            $sizes[LOCAL_SITEREPORT_BLOCK_TABLET_VIEW] = $blockdata[LOCAL_SITEREPORT_BLOCK_TABLET_VIEW];
        } else {
            $blockdata = json_decode($block->blockdata, true);
            $position = get_config('local_edwiserreports', $block->blockname . 'position');
            $position = $position ? $position : $blockdata['position'];
            $desktopview = get_config('local_edwiserreports', $block->blockname . 'desktopsize');
            $sizes[LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW] = $desktopview ? $desktopview : $blockdata['desktopview'];
            $tabletview = get_config('local_edwiserreports', $block->blockname . 'tabletsize');
            $sizes[LOCAL_SITEREPORT_BLOCK_TABLET_VIEW] = $tabletview ? $tabletview : $blockdata['tabletview'];
        }

        $devicecolclass = array(
            LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => 'col-lg-',
            LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => 'col-md-'
        );

        foreach ($sizes as $media => $size) {
            switch($size) {
                case LOCAL_SITEREPORT_BLOCK_LARGE:
                    $this->layout->extraclasses .= $devicecolclass[$media] . '12 ';
                    break;
                case LOCAL_SITEREPORT_BLOCK_MEDIUM:
                    $this->layout->extraclasses .= $devicecolclass[$media] . '6 ';
                    break;
                case LOCAL_SITEREPORT_BLOCK_SMALL:
                    $this->layout->extraclasses .= $devicecolclass[$media] . '4 ';
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * Get block position
     * @param Array $pref Preference
     */
    public function get_block_position($pref) {
        $position = $pref['position'];
    }

    /**
     * Set block edit capabilities for each block
     * @param  String $blockname Block name
     * @return Bool              false If not supported
     */
    public function set_block_edit_capabilities($blockname) {
        global $DB, $USER;

        if (!isset($USER->editing)) {
            return false;
        }

        // If user is not editing.
        if (!$USER->editing) {
            return false;
        }

        $block = \local_edwiserreports\utility::get_reportsblock_by_name($blockname);
        if (!$block) {
            return false;
        }

        $pref = \local_edwiserreports\utility::get_reportsblock_preferences($block);

        $this->layout->hidden = isset($pref["hidden"]) ? $pref["hidden"] : 0;

        $context = context_system::instance();
        if (strpos($blockname, 'customreportsblock') === false) {
            // Based on capability show the edit button
            // If user dont have capability to see the block.
            $this->layout->caneditadv = has_capability('report/edwiserreports_' . $blockname . ':editadvance', $context);
        } else {
            $this->layout->caneditadv = has_capability('report/edwiserreports_customreports:manage', $context);
        }

        // If have capability to edit.
        $this->layout->editopt = true;
    }
}
