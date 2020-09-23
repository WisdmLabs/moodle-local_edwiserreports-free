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

require('../../config.php');

use local_sitereport\controller\elucidsitereportKernel;
use local_sitereport\controller\elucidsitereportRouter;

// Define ajax script based on action value.
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRIPPED);
if (!isset($action) || empty($action)) {
    return;
}

// Only make ajax true if action has ajax in name.
$actionpattern = '/_ajax$/i';

// Include Moodle config
// This code is to run or include file at developer end
// It is because we use symlink for theme from local_gitrepo.
if (!@include_once(__DIR__.'/../../config.php')) {
    include_once('/var/www/html/elucid/v37/config.php');
}

require_sesskey();

$systemcontext = context_system::instance();

$contextid = optional_param('contextid', $systemcontext->id, PARAM_INT);

list($context, $course, $cm) = get_context_info_array($contextid);

$nologinactions = ['get_loginstatus', 'read_page', 'get_courses_ajax']; // Actions which do not require login checks.
if (!in_array($action, $nologinactions)) {
    $courseactions = ['get_media', 'get_page'];
    if (in_array($action, $courseactions)) {
        require_login($course, false, $cm, false, true);
    } else {
        require_login();
    }
}

$PAGE->set_context($context);
if ($course !== null) {
    $PAGE->set_course($course);
}
$PAGE->set_url('/local/sitereport/request_handler.php', array('action' => $action, 'contextid' => $context->id));

if ($cm !== null) {
    $PAGE->set_cm($cm);
}

$router = new elucidsitereportRouter();

// Add controllers automatically.
$controllerdir = __DIR__.'/classes/controller';
$contfiles = scandir($controllerdir);

foreach ($contfiles as $contfile) {
    // Include controllers.
    $pattern = '/Controller.php$/i';
    if (preg_match($pattern, $contfile)) {
        $classname = '\\local_sitereport\\controller\\'.str_ireplace('.php', '', $contfile);
        if (class_exists($classname)) {
            $rc = new ReflectionClass($classname);
            if ($rc->isSubclassOf('\\local_sitereport\\controller\\controllerAbstract')) {
                $router->add_controller(new $classname());
            }
        }
    }
}

$kernel = new elucidsitereportKernel($router);
$kernel->handle($action);
