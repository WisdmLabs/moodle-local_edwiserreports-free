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

namespace report_elucidsitereport\output;

class elucidreport {
    public function __construct() {
    }

    /**
     * Returns the renderer of the local_learning_program
     *
     * @return mixed context|null The course context
     */
    public function get_renderer() {
        global $PAGE;
        $this->output = $PAGE->get_renderer('report_elucidsitereport', null, RENDERER_TARGET_GENERAL);
        return $this->output;
    }
}

class activeusers {
    /**
     * Returns the renderer of the local_learning_program
     *
     * @return mixed context|null The course context
     */
    public function get_renderer() {
        global $PAGE;
        $this->output = $PAGE->get_renderer('report_elucidsitereport', null, RENDERER_TARGET_GENERAL);
        return $this->output;
    }
}

class coursereport {
    /**
     * Returns the renderer of the Course Report
     *
     * @return mixed context|null The course context
     */
    public function get_renderer() {
        global $PAGE;
        $this->output = $PAGE->get_renderer('report_elucidsitereport', null, RENDERER_TARGET_GENERAL);
        return $this->output;
    }
}

class certificates {
    /**
     * Returns the renderer of the local_learning_program
     *
     * @return mixed context|null The course context
     */
    public function get_renderer() {
        global $PAGE;
        $this->output = $PAGE->get_renderer('report_elucidsitereport', null, RENDERER_TARGET_GENERAL);
        return $this->output;
    }
}

class f2fsessions {
    /**
     * Returns the renderer of the local_learning_program
     *
     * @return mixed context|null The course context
     */
    public function get_renderer() {
        global $PAGE;
        $this->output = $PAGE->get_renderer('report_elucidsitereport', null, RENDERER_TARGET_GENERAL);
        return $this->output;
    }
}

class lpstats {
    /**
     * Returns the renderer of the local_learning_program
     *
     * @return mixed context|null The course context
     */
    public function get_renderer() {
        global $PAGE;
        $this->output = $PAGE->get_renderer('report_elucidsitereport', null, RENDERER_TARGET_GENERAL);
        return $this->output;
    }
}
