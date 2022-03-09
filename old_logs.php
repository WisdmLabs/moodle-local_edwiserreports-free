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
 * This file is for tasks listing and processing.
 *
 * @package     local_edwiserreports
 * @copyright   2021 wisdmlabs <support@wisdmlabs.com>
 * @author      Yogesh Shirsath
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

define('EDWISER_REPORTS_WEB_SCRIPT', true);

ob_implicit_flush();

// Require admin user to run task.
require_admin();

// Set page context.
$context = context_system::instance();
$PAGE->set_context($context);

$PAGE->set_title(get_string('fetcholdlogs', 'local_edwiserreports'));

$PAGE->set_heading(get_string('fetcholdlogsdescription', 'local_edwiserreports'));

$oldlogs = new local_edwiserreports\controller\old_logs();
$tracking = local_edwiserreports\controller\tracking::instance(1);
$logform = new local_edwiserreports\old_log_form();

if ($logform->is_submitted()) {
    // Params from form post.
    $formdata = $logform->get_data();
    $mintime = $formdata->mintime;
    $limit = $formdata->limit;
    $run = true;
} else {
    // Params from request or default values.
    $mintime = optional_param('mintime', time() - 86400 * 365, PARAM_INT);
    $limit = optional_param('limit', $tracking->get_minimum_timeslot(), PARAM_INT);
    $logform->set_data(array('mintime' => $mintime, 'limit' => $limit));
    $run = false;
}

// Set page url.
$url = new moodle_url('/local/edwiserreports/old_logs.php', array('run' => $run));
$PAGE->set_url($url);

// Courses to calculate log.
$courses = $run == true ? array_values(get_courses()) : [];
sort($courses);
$templatecontext = array(
    'run' => $run,
    'courses' => $courses
);

if ($run == true) {
    $templatecontext['note'] = get_string('oldlognote', 'local_edwiserreports', [
        'from' => date('d F Y', $mintime),
        'to' => date('d F Y', time())
    ]);
}

$license = new local_edwiserreports\controller\license();

echo $OUTPUT->header();
echo $license->get_license_notice();
echo $OUTPUT->render_from_template('local_edwiserreports/old_logs', $templatecontext);
if ($run == true) {
    $oldlogs->process_old_logs($run, $courses, $mintime, $limit);
    set_config('fetch', true, 'local_edwiserreports');
} else {
    $logform->display();
}
echo $OUTPUT->footer();
