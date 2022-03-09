<?php
// This file is part of Edwiserreports Moodle Local Plugin.
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
 * Edwiser Forms license page.
 * @package   local_edwiserreports
 * @copyright (c) 2020 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $DB, $CFG;

// License controller.
$pluginslug = 'edwiser+reports+pro';

// Get License Status.
$status = get_config('local_edwiserreports', 'license_status');

// Get renew link.
$renewlink = get_config('local_edwiserreports', 'product_site');
$licensekey = get_config('local_edwiserreports', 'license_key');
$licensekeyactivate = get_config('local_edwiserreports', 'licensekeyactivate');
$licensekeydeactivate = get_config('local_edwiserreports', 'licensekeydeactivate');

// Show proper reponse to user on license activation/deactivation.
if ($licensekey == 'empty') {
    // If empty, show error message.
    echo '<div class="alert alert-danger">
       <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
       <h4><i class="icon fa fa-ban"></i> Error</h4>'.get_string("enterlicensekey", "local_edwiserreports").'
    </div>';
} else if (!empty($licensekey) && $licensekey != 'empty') {
    if ($status !== false && $status == 'valid' && !empty($licensekeyactivate)) {
        // Valid license key.
        echo '<div class="alert alert-success">
           <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
           <h4><i class="icon fa fa-check"></i> Success</h4>'.get_string("licensekeyactivated", "local_edwiserreports").'
        </div>';
    } else if ($status !== 'valid') {
        $errormessage = [
            'expired' => 'licensekeyhasexpired', // Expired license key.
            'disabled' => 'licensekeyisdisabled', // Disabled license key.
            'invalid' => 'entervalidlicensekey', // Invalid license key.
            'site_inactive' => 'siteinactive', // Site is inactive.
            'deactivated' => 'licensekeydeactivated', // License key deactivated.
            'no_activations_left' => 'nolicenselimitleft', // No activations left.
            'no_response' => 'noresponsereceived' // No response.
        ];
        echo '<div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-ban"></i> Alert!</h4>'.get_string($errormessage[$status], "local_edwiserreports").'
            </div>';
    }
}

// Remove config vars.
unset_config('licensekey', 'local_edwiserreports');
unset_config('licensekeyactivate', 'local_edwiserreports');
unset_config('licensekeydeactivate', 'local_edwiserreports');
?>
<div class="license-box box box-warning">

    <input type="hidden" name="activetab" value="local_edwiserreports_license_status">

    <?php

    if ($status == "valid") {
        ?>

        <div class="form-group has-success">

            <label class="control-label text-black col-sm-3">
                <?php echo get_string('licensekey', 'local_edwiserreports') ?>:
            </label>

            <div class="col-sm-9">
                <?php echo "<input id='edd_{$pluginslug}_license_key'
                class='form-control regular-text' name='edd_{$pluginslug}_license_key'
                type='text' value='{$licensekey}' placeholder='Enter license key...'
                readonly/>"; ?>
            </div>
        </div>

        <?php
    } else if ($status == "expired") {
        ?>

        <div class="form-group has-error">

            <label class="control-label text-black col-sm-3">
                <?php echo get_string('licensekey', 'local_edwiserreports') ?>:
            </label>

            <div class="col-sm-9">
                <?php echo "<input id='edd_{$pluginslug}_license_key'
                class='form-control regular-text' name='edd_{$pluginslug}_license_key'
                type='text' value='{$licensekey}' placeholder='Enter license key...'
                readonly/>"; ?>
            </div>
        </div>

        <?php
    } else {
        ?>

        <div class="form-group has-error">

            <label class="control-label text-black col-sm-3">
                <?php echo get_string('licensekey', 'local_edwiserreports') ?>:
            </label>

            <div class="col-sm-9">
                <?php echo "<input id='edd_{$pluginslug}_license_key'
                class='form-control regular-text'  name='edd_{$pluginslug}_license_key'
                type='text' value='{$licensekey}' placeholder='Enter license key...'
                />"; ?>
            </div>
        </div>

        <?php
    } ?>

    <div class="form-group">
        <?php
            echo '<label class="control-label col-sm-3">'.get_string('licensestatus', 'local_edwiserreports').':</label>';

            echo '<div class="col-sm-9">';

            $statustextactive = get_string('active', 'local_edwiserreports');
            $statustextactivetext = "<p style='color:green;'>{$statustextactive}</p>";
            $statustextinactive = get_string('notactive', 'local_edwiserreports');
            $statustextinactivetext = "<p style='color:red;'>{$statustextinactive}</p>";
            $statustextexpired = get_string('expired', 'local_edwiserreports');
            $statustextexpiredtext = "<p style='color:red;'>{$statustextexpired}</p>";

        if ($status !== false && $status == 'valid') {
            echo $statustextactivetext;
        } else if ($status == 'site_inactive') {
            echo $statustextinactivetext;
        } else if ($status == 'expired') {
            echo $statustextexpiredtext;
        } else if ($status == 'invalid') {
            echo $statustextinactivetext;
        } else {
            echo $statustextinactivetext;
        }

            echo '</div>';
        ?>
    </div>

    <div class="form-group">
        <?php

            $activatelicensetext = get_string('activatelicense', 'local_edwiserreports');
            $deactivatelicensetext = get_string('deactivatelicense', 'local_edwiserreports');
            $renewlicensetext = get_string('renewlicense', 'local_edwiserreports');

            echo '<div class="col-sm-9">';

            // Hidden field to cehck if on license tab.
            echo "<input type='hidden' id='onEdwiserReportsLicensePage' name='onEdwiserReportsLicensePage' value='1'/>";

        if ($status !== false && $status == 'valid') {
            echo "<input type='submit' class='btn btn-primary text-white'
            style='color:white;' name='edd_{$pluginslug}_license_deactivate'
            value='{$deactivatelicensetext}'/>";
        } else if ($status == 'expired') {
            echo "<input type='submit' class='btn btn-primary' style='color:white;'
            name='edd_{$pluginslug}_license_deactivate' value='{$deactivatelicensetext}'/>&nbsp&nbsp";
            echo '<input type="button" class="btn btn-primary" style="color:white;"
            name="edd_'.$pluginslug.'_license_renew" value="'.$renewlicensetext.'"
            onclick="window.open(\''.$renewlink.'\');">';
        } else {
            echo "<input type='submit' class='btn btn-primary' style='color:white;'
            name='edd_{$pluginslug}_license_activate' value='{$activatelicensetext}'/>";
        }
        echo '</div>';
        ?>
    </div>
</div>
