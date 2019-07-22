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

defined('MOODLE_INTERNAL') || die();

use renderable;
use templatable;
use renderer_base;
use stdClass;

require_once($CFG->dirroot . "/report/elucidsitereport/lib.php");

class elucidreport_renderable implements renderable, templatable  {
    /**
     * Function to export the renderer data in a format that is suitable for a
     * edit mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */
    public function export_for_template(renderer_base $output) {
        global $PAGE;
        $output = null;
        $export = new stdClass();
        $export->timenow = date("Y-m-d", time());
        $export->courses = \report_elucidsitereport\utility::get_courses();
        $export->hasf2fpluign = has_plugin("mod", "facetoface");

        if ($export->hasf2fpluign) {
            $PAGE->requires->js_call_amd('report_elucidsitereport/f2fsessionblock', 'init');
        }

        $export->hascustomcertpluign = has_plugin("mod", "customcert");

        if ($export->hascustomcertpluign) {
            $PAGE->requires->js_call_amd('report_elucidsitereport/certificatestatsblock', 'init');
        }

        $export->haslppluign = has_plugin("local", "learning_program");

        if ($export->haslppluign) {
            $export->lpearchfilter = \report_elucidsitereport\utility::generate_lp_filter();
        }

        return  $export;
    }
}