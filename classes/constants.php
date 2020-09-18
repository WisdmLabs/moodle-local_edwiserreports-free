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

/**
 * Define all constants for use
 */

defined('MOODLE_INTERNAL') || die();

/* Course completion constant */
define('COURSE_COMPLETE_00PER', 0);
define('COURSE_COMPLETE_20PER', 0.2);
define('COURSE_COMPLETE_40PER', 0.4);
define('COURSE_COMPLETE_60PER', 0.6);
define('COURSE_COMPLETE_80PER', 0.8);
define('COURSE_COMPLETE_100PER', 1);

/* Percentage constant */
define('PERCENTAGE_00', "0%");
define('PERCENTAGE_20', "20%");
define('PERCENTAGE_40', "40%");
define('PERCENTAGE_60', "60%");
define('PERCENTAGE_80', "80%");
define('PERCENTAGE_100', "100%");

/* Time constant */
define('ONEDAY', 24 * 60 * 60);
define('ONEWEEK', 7 * 24 * 60 * 60);
define('ONEMONTH', 30 * 24 * 60 * 60);
define('ONEYEAR', 365 * 24 * 60 * 60);
define('ALL', "all");
define('WEEKLY', "weekly");
define('MONTHLY', "monthly");
define('YEARLY', "yearly");
define('WEEKLY_DAYS', 7);
define('MONTHLY_DAYS', 30);
define('YEARLY_DAYS', 365);

define('ESR_DAILY_EMAIL', 0);
define('ESR_WEEKLY_EMAIL', 1);
define('ESR_MONTHLY_EMAIL', 2);

define('ESR_0630AM', 0);
define('ESR_1000AM', 1);
define('ESR_0430PM', 2);
define('ESR_1030PM', 3);

// Define block type
define('BLOCK_TYPE_DEFAULT', 0);
define('BLOCK_TYPE_CUSTOM', 1);

// Block View
define('BLOCK_DESKTOP_VIEW', 'desktopview');
define('BLOCK_TABLET_VIEW', 'tabletview');
define('BLOCK_MOBILE_VIEW', 'mobileview');
define('BLOCK_LARGE', 2);
define('BLOCK_MEDIUM', 1);
define('BLOCK_SMALL', 0);
