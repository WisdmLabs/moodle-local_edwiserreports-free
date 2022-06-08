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
 * Plugin strings are defined here.
 *
 * @package     local_edwiserreports
 * @category    Styles
 * @copyright   2022 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author      Yogesh Shirsath
 */

// phpcs:disable

ob_start();
require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../classes/constants.php');
ob_clean();

// phpcs:enable
$theme = local_edwiserreports\utility::get_active_theme();

header('Content-Type: text/css; charset=utf-8');

foreach (LOCAL_EDWISERREPORTS_COLOR_THEMES[$theme] as $colindex => $color) {
    $mainclass = ".theme-" . $colindex . "-";
    echo $mainclass . "bg { background-color: $color !important; }\n";
    echo $mainclass . "text { color: $color !important; }\n";
    echo $mainclass . "border { border-color: $color !important; }\n";
}

$primarycolor = LOCAL_EDWISERREPORTS_COLOR_THEMES[$theme][0];
$secondarycolor = LOCAL_EDWISERREPORTS_COLOR_THEMES[$theme][2];
$mainclass = ".theme-primary-";
    echo $mainclass . "bg { background-color: $primarycolor !important; }\n";
    echo $mainclass . "text { color: $primarycolor !important; }\n";
    echo $mainclass . "border { border-color: $primarycolor !important; }\n";

// For SVG.
echo ".theme-primary-fill { fill: $primarycolor !important; }\n";
echo ".theme-secondary-fill { fill: " . $secondarycolor . "60 !important; }\n";

// Date dropdown color.
echo "
.filter-selector .dropdown-toggle::after {
    border-top-color: $primarycolor !important;
}

.filter-selector .dropdown-menu .dropdown-item.active,
.filter-selector .dropdown-menu .dropdown-item.active input,
.filter-selector .dropdown-menu .dropdown-item:hover {
    color: $primarycolor !important;
}

.filter-selector .dropdown-menu .custom:hover input::-webkit-input-placeholder {
    color: $primarycolor !important;
}

.filter-selector .dropdown-menu .custom:hover input:-moz-placeholder {
    color: $primarycolor !important;
}

.filter-selector .dropdown-menu .custom:hover input::-moz-placeholder {
    color: $primarycolor !important;
}

.filter-selector .dropdown-menu .custom:hover input:-ms-input-placeholder {
    color: $primarycolor !important;
}

.filter-selector .dropdown-menu .custom:hover input::-ms-input-placeholder {
    color: $primarycolor !important;
}

.filter-selector .dropdown-menu .custom:hover input::placeholder {
    color: $primarycolor !important;
}
.panel-header .dropdown-menu .dropdown-item:not(.disabled):hover {
    color: $primarycolor !important;
}

.panel-header .dropdown.show .fa {
    color: " . LOCAL_EDWISERREPORTS_COLOR_THEMES[$theme][1] . " !important;
}

.block-filters .dropdown-toggle::after {
    border-top-color: $primarycolor !important;
}
.block-filters .dropdown-menu .dropdown-item:hover {
    color: $primarycolor !important;
}
.filters .dropdown-toggle::after {
    border-top-color: $primarycolor !important;
}
.filters .dropdown-menu .dropdown-item:hover {
    color: $primarycolor !important;
}

#scheduletab .date-filters .dropdown button::after {
	border-top-color: $primarycolor !important;
}

#scheduletab .date-filters .dropdown .dropdown-item:hover {
	color: $primarycolor !important;
}

.panel-header .dropdown-menu .pro-highlight span {
    background-color: " . LOCAL_EDWISERREPORTS_COLOR_THEMES[$theme][1] . " !important;
}

.panel-header .dropdown-menu .pro-highlight span::after {
    border-top-color: " . LOCAL_EDWISERREPORTS_COLOR_THEMES[$theme][1] . " !important;
}

";

// Select 2 colors.
echo ".select2-container--default .select2-selection--single .select2-selection__arrow b {
    border-color: $primarycolor transparent transparent transparent !important;
}
.select2-results__option--highlighted,
.select2-results__option[aria-selected='true'] {
    color: $primarycolor !important;
}
.select2-selection__choice__remove {
    color: $primarycolor !important;
}";

// Nav colors.
echo ".nav-tabs .nav-link {
    color: $primarycolor !important;
}
.nav-tabs .nav-link.active {
    color: " . LOCAL_EDWISERREPORTS_COLOR_THEMES[$theme][1] . " !important;
    border-color: " . LOCAL_EDWISERREPORTS_COLOR_THEMES[$theme][1] . " !important;
}";

// Modal colors.
echo ".modal-header .close {
    color: $primarycolor !important;
}";

// Checkbox colors.
echo ".checkbox-edwiserreports input:checked + .checkmark {
    background-color: $primarycolor !important;
}";

// Field group.
echo ".reports-filter-body .fa {
    color: $primarycolor !important;
}";

// Table dropdown.
echo "
.edwiserreports-table .dataTables_length select option:hover {
    color: $primarycolor !important;
}";
