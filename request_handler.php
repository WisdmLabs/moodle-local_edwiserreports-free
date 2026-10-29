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
 * Ajax request handler
 *
 * @package     local_edwiserreports
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// phpcs:disable

if (isset($_GET['secret']) || isset($_POST['secret'])) {
    define('NO_MOODLE_COOKIES', true);
    define('ALLOW_GET_PARAMETERS', true);
}

ob_start();
require('../../config.php');

// phpcs:enable
global $USER, $DB;

use local_edwiserreports\controller\edwiserReportKernel;
use local_edwiserreports\controller\edwiserReportRouter;

// Define ajax script based on action value.
$action = required_param('action', PARAM_TEXT);
if (!isset($action) || empty($action)) {
    return;
}

$systemcontext = context_system::instance();

$contextid = optional_param('contextid', $systemcontext->id, PARAM_INT);

list($context, $course, $cm) = get_context_info_array($contextid);

$secret = optional_param('secret', null, PARAM_TEXT);
if ($secret == null) {
    // Actions which do not require login checks.
    $nologinactions = [
        'get_loginstatus',
        'read_page',
        'get_courses_ajax',
        'get_tracking_id',
        'is_installed_ajax'
    ];
    if (!in_array($action, $nologinactions)) {
        $courseactions = ['get_media', 'get_page'];
        if (in_array($action, $courseactions)) {
            require_login($course, false, $cm, false, true);
        } else {
            require_login();
        }
    }
} else {
    $authentication = new local_edwiserreports\controller\authentication();
    $user = $authentication->get_user($secret);
    if ($user === null) {
        ob_clean();
        try {
            throw new moodle_exception('invalidsecretkey', 'local_edwiserreports');
        } catch (Throwable $e) {
            $exception = get_exception_info($e);
            unset($exception->a);
            $exception->backtrace = format_backtrace($exception->backtrace, true);
            if (!debugging('', DEBUG_DEVELOPER)) {
                unset($exception->debuginfo);
                unset($exception->backtrace);
            }
            echo json_encode([
                'error' => true,
                'exception' => $exception
            ]);
            die;
            // Do not process the remaining requests.
        }
    }
    $USER = $DB->get_record('user', ['id' => $user]);
    $USER->lang = optional_param('lang', $USER->lang, PARAM_LANG);
}

$PAGE->set_context($context);
if ($course !== null) {
    $PAGE->set_course($course);
}
$PAGE->set_url('/local/edwiserreports/request_handler.php', array('action' => $action, 'contextid' => $context->id));

if ($cm !== null) {
    $PAGE->set_cm($cm);
}

$router = new edwiserReportRouter();

// Add controllers automatically.
$controllerdir = __DIR__.'/classes/controller';
$contfiles = scandir($controllerdir);

foreach ($contfiles as $contfile) {
    // Include controllers.
    $pattern = '/Controller.php$/i';
    if (preg_match($pattern, $contfile)) {
        $classname = '\\local_edwiserreports\\controller\\'.str_ireplace('.php', '', $contfile);
        if (class_exists($classname)) {
            $rc = new ReflectionClass($classname);
            if ($rc->isSubclassOf('\\local_edwiserreports\\controller\\controllerAbstract')) {
                $router->add_controller(new $classname());
            }
        }
    }
}

$kernel = new edwiserReportKernel($router);
$kernel->handle($action);
