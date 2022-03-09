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
 * Local Course Progress Manager Plugin Events Observer.
 *
 * @package     local_edwiserreports
 * @category    admin
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$observerfile = '/local/edwiserreports/classes/observer.php';
$observerclassname = '\local_edwiserreports\observers\event_observer';

$observers = array(
    // Event observer for role assignment.
    array(
        'eventname' => '\core\event\role_assigned',
        'callback' => $observerclassname . '::role_assigned',
        'includefile' => $observerfile
    ),

    // Event observer for role unassignment.
    array(
        'eventname' => '\core\event\role_unassigned',
        'callback' => $observerclassname . '::role_unassigned',
        'includefile' => $observerfile
    ),

    // Event observer for Course Module created.
    array(
        'eventname' => '\core\event\course_module_created',
        'callback' => $observerclassname . '::course_module_created',
        'includefile' => $observerfile
    ),

    // Event observer for Course Module delete.
    array(
        'eventname' => '\core\event\course_module_deleted',
        'callback' => $observerclassname . '::course_module_deleted',
        'includefile' => $observerfile
    ),

    // Module update event.
    array(
        'eventname' => '\core\event\course_module_updated',
        'callback' => $observerclassname . '::course_module_updated',
        'includefile' => $observerfile
    ),

    // Course Delete Observer.
    array(
        'eventname' => '\core\event\course_deleted',
        'callback' => $observerclassname . '::course_deleted',
        'includefile' => $observerfile
    ),

    // Course Delete Observer.
    array(
        'eventname' => '\core\event\course_updated',
        'callback' => $observerclassname . '::course_updated',
        'includefile' => $observerfile
    ),

    // Course Delete Observer.
    array(
        'eventname' => '\core\event\course_module_completion_updated',
        'callback' => $observerclassname . '::course_module_completion_updated',
        'includefile' => $observerfile
    ),

    // User login observer.
    array(
        'eventname' => 'core\event\user_loggedin',
        'callback' => $observerclassname . '::user_loggedin',
        'includefile' => $observerfile
    ),

    // User logout observer.
    array(
        'eventname' => 'core\event\user_loggedout',
        'callback' => $observerclassname . '::user_loggedout',
        'includefile' => $observerfile
    )
);
