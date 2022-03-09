<?php

ob_start();
require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../classes/constants.php');
ob_clean();

$palette = local_edwiserreports\utility::get_active_palette();

header('Content-Type: text/css; charset=utf-8');

foreach (LOCAL_EDWISERREPORTS_COLOR_PALETTES[$palette] as $colindex => $color) {
    $mainclass = ".palette-" . $colindex . "-";
    echo $mainclass . "bg { background-color: $color !important; }\n";
    echo $mainclass . "text { color: $color !important; }\n";
    echo $mainclass . "border { border-color: $color !important; }\n";
}

$primarycolor = LOCAL_EDWISERREPORTS_COLOR_PALETTES[$palette][0];
$secondarycolor = LOCAL_EDWISERREPORTS_COLOR_PALETTES[$palette][2];
$mainclass = ".palette-primary-";
    echo $mainclass . "bg { background-color: $primarycolor !important; }\n";
    echo $mainclass . "text { color: $primarycolor !important; }\n";
    echo $mainclass . "border { border-color: $primarycolor !important; }\n";

// For SVG.
echo ".palette-primary-fill { fill: $primarycolor !important; }\n";
echo ".palette-secondary-fill { fill: " . $secondarycolor . "60 !important; }\n";


echo "
/* Dropdown colors. */
.filter-selector .dropdown-toggle::after {
    border-top-color: $primarycolor !important;
}
.filter-selector .dropdown-menu .dropdown-item:hover {
    color: white !important;
    background-color: $primarycolor !important;
}
.block-filters .dropdown-toggle::after {
    border-top-color: $primarycolor !important;
}
.block-filters .dropdown-menu .dropdown-item:hover {
    color: white !important;
    background-color: $primarycolor !important;
}
.filters .dropdown-toggle::after {
    border-top-color: $primarycolor !important;
}
.filters .dropdown-menu .dropdown-item:hover {
    color: white !important;
    background-color: $primarycolor !important;
}

/* Select 2 colors. */
.select2-container--default .select2-selection--single .select2-selection__arrow b {
    border-color: $primarycolor transparent transparent transparent !important;
}
.select2-results__option--highlighted,
.select2-results__option[aria-selected='true'] {
    background-color: $primarycolor !important;
    color: white !important;
}
.select2-selection__choice__remove {
    color: $primarycolor !important;
}

/* Nav colors. */
.nav-tabs .nav-link {
    color: $primarycolor !important;
}
.nav-tabs .nav-link.active {
    color: $secondarycolor !important;
    border-color: $secondarycolor !important;
}

/* Modal colors. */
.modal-header .close {
    color: $primarycolor !important;
}

/* Checkbox colors. */
.checkbox-custom input[type=checkbox]:checked+label::before, .radio-custom input[type=radio]:checked+label::before {
    border-color: $primarycolor !important;
}

/* Field group. */
.reports-filter-body .fa {
    color: $primarycolor !important;
}
";
