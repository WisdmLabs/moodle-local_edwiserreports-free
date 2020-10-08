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

use context_system;
use moodle_url;

require_once(__DIR__ . '/../../config.php');
require_once('classes/output/renderable.php');

// System context.
$context = context_system::instance();
$component = "local_edwiserreports";

require_login();

// The requested section isn't in the admin tree
// It could be because the user has inadequate capapbilities or because the section doesn't exist.
if (!has_capability('report/local_edwiserreports:view', $context)) {
    // The requested section could depend on a different capability
    // But most likely the user has inadequate capabilities.
    print_error('accessdenied', 'admin');
}

// Require JS for f2fsessions page.
$PAGE->requires->js_call_amd('local_edwiserreports/f2fsessions', 'init', array($context->id));

// Require CSS for f2fsessions page.
$PAGE->requires->css('/local/edwiserreports/styles/select2.min.css');

// Page URL.
$pageurl = new moodle_url($CFG->wwwroot . "/local/edwiserreports/f2fsessions.php");

// Set page context.
$PAGE->set_context($context);

// Set page URL.
$PAGE->set_url($pageurl);

// Get Renderable for f2fsession page.
$renderable = new \local_edwiserreports\output\f2fsessions_renderable();
$output = $PAGE->get_renderer($component)->render($renderable);

// Print output in page.
echo $OUTPUT->header();
echo $output;
echo $OUTPUT->footer();
