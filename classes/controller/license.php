<?php
// This file is part of Edwiser Reports Moodle Local Plugin.
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
 * License controller functionality
 * @package   local_edwiserreports
 * @copyright (c) 2020 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserreports\controller;

defined('MOODLE_INTERNAL') || die();

use html_writer;

class license {

    /**
     *
     * @var string Slug to be used in url and functions name
     */
    private $pluginslug = '';

    /**
     *
     * @var string stores the current plugin version
     */
    private $pluginversion = '';

    /**
     *
     * @var string Handles the plugin name
     */
    private $pluginname = '';

    /**
     *
     * @var string  Stores the URL of store. Retrieves updates from
     *              this store
     */
    private $storeurl = '';

    public static $responsedata;

    /**
     * Developer Note: This variable is used everywhere to check license information and verify the data.
     * Change the Name of this variable in this file wherever it appears and also remove this comment
     * After you are done with adding Licensing
     */
    public $wdmedwiserreportsdata = array (
        // Plugins short name appears on the License Menu Page.
        'plugin_short_name' => 'Edwiser Reports Pro',
        // This slug is used to store the data in db. License is checked using two options viz
        // edd_<slug>_license_key and edd_<slug>_license_status.
        'plugin_slug' => 'edwiser+reports+pro',
        // Current Version of the plugin. This should be similar to Version tag mentioned in Plugin headers.
        'plugin_version' => '1.0.0',
        // Under this Name product should be created on WisdmLabs Site.
        'plugin_name' => 'Edwiser Reports Pro',
        // Url where program pings to check if update is available and license validity.
        'store_url' => 'https://edwiser.org/check-update',
        // Author Name.
        'author_name' => 'WisdmLabs',
    );

    /**
     * Initialize data on instance creation.
     */
    public function __construct() {
        $this->authorname       = $this->wdmedwiserreportsdata[ 'author_name' ];
        $this->pluginname       = $this->wdmedwiserreportsdata[ 'plugin_name' ];
        $this->pluginshortname = $this->wdmedwiserreportsdata[ 'plugin_short_name' ];
        $this->pluginslug       = $this->wdmedwiserreportsdata[ 'plugin_slug' ];
        $this->pluginversion    = $this->wdmedwiserreportsdata[ 'plugin_version' ];
        $this->storeurl         = $this->wdmedwiserreportsdata[ 'store_url' ];
    }

    public function status_update($licensedata) {

        $status = "";
        if ((empty($licensedata->success)) && isset($licensedata->error) && ($licensedata->error == "expired")) {
            $status = 'expired';
        } else if ($licensedata->license == 'invalid' && isset($licensedata->error) && $licensedata->error == "disabled") {
            $status = 'disabled';
        } else if ($licensedata->license == 'invalid' && isset($licensedata->error)
            && $licensedata->error == "no_activations_left") {
            $status = 'no_activations_left';
        } else if ($licensedata->license == 'failed') {
            $status = 'failed';
        } else {
            $status = $licensedata->license;
        }

        set_config('license_status', $status, 'local_edwiserreports');

        return $status;
    }

    /**
     * Check if there no data
     * @param  string $licensedata          License data
     * @param  int    $currentresponsecode Current response code
     * @param  array  $validresponsecode   Valid response code
     * @return bool                          Boolean
     */
    public function check_if_no_data($licensedata, $currentresponsecode, $validresponsecode) {
        global $DB;

        if ($licensedata == null || ! in_array($currentresponsecode, $validresponsecode)) {
            set_config(
                'license_trans',
                serialize(array('server_did_not_respond', time() + (60 * 60 * 24))),
                'local_edwiserreports'
            );
        }
        return true;
    }

    /**
     * Activate license key
     */
    public function activate_license() {
        global $DB, $CFG;
        $licensekey = trim($_POST[ 'edd_' . $this->pluginslug . '_license_key' ]);
        if ($licensekey) {

            set_config(
                'license_key',
                $licensekey,
                'local_edwiserreports'
            );

            // Get cURL resource.
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $this->storeurl,
                CURLOPT_POST => 1,
                CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'].' - '.$CFG->wwwroot,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_POSTFIELDS => array(
                    'edd_action' => 'activate_license',
                    'license' => $licensekey,
                    'item_name' => urlencode($this->pluginname),
                    'current_version' => $this->pluginversion,
                    'url' => urlencode($CFG->wwwroot),
                )
            ));

            // Send the request & save response to $resp.
            $resp = curl_exec($curl);
            // phpcs:ignore
            error_log('EDWISER_REPORTS_LICENSE_ACTIVATE_CURL_FILE: ' . __FILE__ . ':' . __LINE__);
            // phpcs:ignore
            error_log('EDWISER_REPORTS_LICENSE_ACTIVATE_CURL: ' . $resp);

            $currentresponsecode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            // Close request to clear up some resources.
            curl_close($curl);

            $licensedata = json_decode($resp);

            $validresponsecode = array( '200', '301' );

            $isdataavailable = $this->check_if_no_data($licensedata, $currentresponsecode, $validresponsecode);

            if ($isdataavailable == false) {
                return;
            }

            $licensestatus = $this->status_update($licensedata);

            if ($licensestatus == 'expired') {
                if (isset($licensedata->renew_link) && $licensedata->renew_link != "") {
                    // If the license key's validity is expired then save the renew link for the product.
                    $renewlink = $licensedata->renew_link;
                } else {
                    $renewlink = "https://edwiser.org";
                }
                set_config('product_site', $renewlink, 'local_edwiserreports');
            }
            $this->set_transient_on_activation($licensestatus);
        }
    }

    /**
     * Set transient on activation for frequent license check
     * @param string $licensestatus License status
     */
    public function set_transient_on_activation($licensestatus) {

        $transexpired = false;

        // Check license trans.
        $transvar = get_config('local_edwiserreports', 'license_trans');

        if ($transvar) {
            $transvar = unserialize($transvar);

            if (is_array($transvar) && time() > $transvar[1] && $transvar[1] > 0) {

                $transexpired = true;
                unset_config('license_trans', 'local_edwiserreports');
            }
        } else {
            $transexpired = true;
        }

        if ($transexpired == false) {

            unset_config('license_trans', 'local_edwiserreports');

            if (! empty($licensestatus)) {
                if ($licensestatus == 'valid') {
                    $time = time() + 60 * 60 * 24 * 7;
                } else {
                    $time = time() + 60 * 60 * 24;
                }

                set_config(
                    'license_trans',
                    serialize(array($licensestatus, $time)),
                    'local_edwiserreports'
                );
            }
        }
    }

    /**
     * Deactivate license key
     */
    public function deactivate_license() {
        global $DB, $CFG;

        $wpeplicensekey = get_config('local_edwiserreports', 'license_key');

        if (!empty($wpeplicensekey)) {

            // Get cURL resource.
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $this->storeurl,
                CURLOPT_POST => 1,
                CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'].' - '.$CFG->wwwroot,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_POSTFIELDS => array(
                    'edd_action' => 'deactivate_license',
                    'license' => $wpeplicensekey,
                    'item_name' => urlencode($this->pluginname),
                    'current_version' => $this->pluginversion,
                    'url' => urlencode($CFG->wwwroot),
                )
            ));

            // Send the request & save response to $resp.
            $resp = curl_exec($curl);

            // phpcs:ignore
            error_log('EDWISER_REPORTS_LICENSE_DEACTIVATE_CURL_FILE: ' . __FILE__ . ':' . __LINE__);
            // phpcs:ignore
            error_log('EDWISER_REPORTS_LICENSE_DEACTIVATE_CURL: ' . $resp);

            $currentresponsecode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            // Close request to clear up some resources.
            curl_close($curl);

            $licensedata = json_decode($resp);

            $validresponsecode = array( '200', '301' );

            $isdataavailable = $this->check_if_no_data($licensedata, $currentresponsecode, $validresponsecode);

            if ($isdataavailable == false) {
                return;
            }

            if ($licensedata->license == 'deactivated' || $licensedata->license == 'failed') {
                set_config('license_status', 'deactivated', 'local_edwiserreports');
            }
            set_config('license_trans', serialize(array($licensedata->license, 0)), 'local_edwiserreports');
        }
    }

    /**
     * Common method for license operation
     * @return mixed Activation/Deactivation status
     */
    public function serve_license_data() {

        if (is_siteadmin()) {

            // Return if did not come from license page.
            if (!isset($_POST['onEdwiserReportsLicensePage']) || $_POST['onEdwiserReportsLicensePage'] == 0) {
                return;
            }
            $_POST['onEdwiserReportsLicensePage'] = false;

            if (empty(@$_POST['edd_' . $this->pluginslug .'_license_key'])) {
                    $lk = 'empty';
            } else {
                $lk = @$_POST['edd_' . $this->pluginslug .'_license_key'];
            }

            if (isset($_POST[ 'edd_' . $this->pluginslug . '_license_activate' ])) {
                // Jugad to tackle the page redirect after save license.
                set_config('licensekey', $lk, 'local_edwiserreports');
                set_config('licensekeyactivate', @$_POST['edd_' . $this->pluginslug . '_license_activate'], 'local_edwiserreports');

                return $this->activate_license();
            } else if (isset($_POST[ 'edd_' . $this->pluginslug . '_license_deactivate' ])) {

                // Jugad to tackle the page redirect after save license.
                set_config('licensekey', $lk, 'local_edwiserreports');
                set_config(
                    'licensekeydeactivate',
                    @$_POST['edd_' . $this->pluginslug . '_license_deactivate'],
                    'local_edwiserreports'
                );
                return $this->deactivate_license();
            }
        }
    }

    /**
     * Get data from database
     * @return string Response status
     */
    public function get_data_from_db() {

        global $CFG;
        return 'available';
        if (null !== self::$responsedata) {
            return self::$responsedata;
        }

        $transexpired = false;

        $gettrans = get_config('local_edwiserreports', 'license_trans');

        if ($gettrans) {
            $gettrans = unserialize($gettrans);

            if (is_array($gettrans) && time() > $gettrans[1] && $gettrans[1] > 0) {

                $transexpired = true;
                unset_config('license_trans', 'local_edwiserreports');
            }
        } else {
            $transexpired = true;
        }

        if ($transexpired == true) {

            $licensekey = get_config('local_edwiserreports', 'license_key');

            if ($licensekey) {

                // Get cURL resource.
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_URL => $this->storeurl,
                    CURLOPT_POST => 1,
                    CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'].' - '.$CFG->wwwroot,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_POSTFIELDS => array(
                        'edd_action' => 'check_license',
                        'license' => $licensekey,
                        'item_name' => urlencode($this->pluginname),
                        'current_version' => $this->pluginversion,
                        'url' => urlencode($CFG->wwwroot),
                    )
                ));
                // Send the request & save response to $resp.
                $resp = curl_exec($curl);
                // phpcs:ignore
                error_log('EDWISER_REPORTS_LICENSE_GET_FORM_DATA_CURL_FILE: ' . __FILE__ . ':' . __LINE__);
                // phpcs:ignore
                error_log('EDWISER_REPORTS_LICENSE_GET_FORM_DATA_CURL: ' . $resp);

                $currentresponsecode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

                // Close request to clear up some resources.
                curl_close($curl);

                $licensedata = json_decode($resp);

                $validresponsecode = array( '200', '301' );

                if ($licensedata == null || ! in_array($currentresponsecode, $validresponsecode)) {
                    // If server does not respond, read current license information.
                    $licensestatus = get_config('local_edwiserreports', 'license_status');

                    if (empty($licensedata)) {
                        // Insert new license transient.
                        set_config(
                            'license_status',
                            serialize(array('server_did_not_respond', time() + (60 * 60 * 24))),
                            'local_edwiserreports'
                        );
                    }
                } else {
                    $licensestatus = $licensedata->license;
                }

                if (empty($licensestatus)) {
                    return;
                }

                if (isset($licensedata->license) && ! empty($licensedata->license)) {
                    set_config('license_status', $licensestatus, 'local_edwiserreports');
                }

                $this->set_response_data($licensestatus, $this->pluginslug, true);
                return self::$responsedata;
            }
        } else {

            $licensestatus = get_config('local_edwiserreports', 'license_status');

            $this->set_response_data($licensestatus, $this->pluginslug);
            return self::$responsedata;
        }
    }

    /**
     * Generate random license tag to prevent user from hiding it.
     *
     * @return string
     */
    private function generate_license_tag() {
        $randombytes = random_bytes_emulate(10);
        $pool  = 'abcdefghijklmnopqrstuvwxyz';
        $poollen = strlen($pool);
        $string = '';
        for ($i = 0; $i < 10; $i++) {
            $rand = ord($randombytes[$i]);
            $string .= substr($pool, ($rand%($poollen)), 1);
        }
        return $string;
    }

    /**
     * If license is not active then generate notice content to show on page.
     *
     * @return void
     */
    public function get_license_notice() {
        $status = $this->get_data_from_db();
        if ($status == 'available') {
            return '';
        }
        $labelid = 'licensenotactive';
        if (is_siteadmin()) {
            $labelid = 'licensenotactiveadmin';
        }

        $label = get_string($labelid, 'local_edwiserreports');
        return html_writer::tag($this->generate_license_tag(), $label, array(
            'style' => 'position: fixed;
                        background: #d9534f;
                        color: white;
                        padding: 1rem;
                        top: 9rem;
                        left: 0;
                        right: 0;
                        z-index: 1710;
                        text-align: center;'
        ));
    }

    /**
     * Set response data in static properties
     * @param string  $licensestatus License status
     * @param string  $pluginslug    Plugin slug
     * @param boolean $settransient  Transient
     */
    public function set_response_data($licensestatus, $pluginslug, $settransient = false) {
        global $DB;

        if ($licensestatus == 'valid') {
            self::$responsedata = 'available';
        } else if ($licensestatus == 'expired') {
            self::$responsedata = 'available';
        } else {
            self::$responsedata  = 'unavailable';
        }

        if ($settransient) {
            if ($licensestatus == 'valid') {
                $time = 60 * 60 * 24 * 7;
            } else {
                $time = 60 * 60 * 24;
            }

            set_config('license_trans', serialize(array($licensestatus, time() + (60 * 60 * 24))), 'local_edwiserreports');
        }
    }

    /**
     * This function is used to get list of sites where license key is already acvtivated.
     *
     * @param type $pluginslug current plugin's slug
     * @return string  list of site
     */
    public function get_site_list() {

        global $DB, $CFG;

        $sites = get_config('local_edwiserreports', 'license_key_sites');

        $max = get_config('local_edwiserreports', 'license_max_site');

        $sites = unserialize($sites);

        $cursite    = $CFG->wwwroot;
        $cursite    = preg_replace('#^https?://#', '', $cursite);

        $sitecount  = 0;
        $activesite = "";

        if (!empty($sites) || $sites != "") {
            foreach ($sites as $key) {
                foreach ($key as $value) {
                    $value = rtrim($value, "/");

                    if (strcasecmp($value, $cursite) != 0) {
                        $activesite .= "<li>" . $value . "</li>";
                        $sitecount ++;
                    }
                }
            }
        }

        if ($sitecount >= $max) {
            return $activesite;
        } else {
            return "";
        }
    }
}
