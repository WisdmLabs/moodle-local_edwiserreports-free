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
 * Code to be executed after the plugin's database scheme has been installed is defined here.
 *
 * @package     local_edwiserreports
 * @category    upgrade
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$definitions = array(
    'activeusers' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'ttl' => 1 * 60 * 60
    ),
    'courseprogress' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'ttl' => 1 * 60 * 60
    ),
    'activecourses' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'ttl' => 3 * 60 * 60
    ),
    'siteaccess' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'ttl' => 12 * 60 * 60
    ),
    'certificates' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'ttl' => 1 * 60 * 60
    ),
    'inactiveusers' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'ttl' => 24 * 60 * 60
    ),
);
