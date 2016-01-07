<?php
/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 11/16/15
 * Package: Functions/ Licensing
 * File: licensing.php
 * Author: Kyle Benk
 *
 *
 * Copyright 2015
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * ================================================================= */

// Exit if accessed directly

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Save all the extension license keys
 *
 * @access public
 * @param mixed $data
 * @return void
 */
function msa_settings_tab_extensions_save($data) {

	// Check if we have data already saved

	if ( false === ( $extension_license_keys = get_option('msa_extension_license_keys') ) ) {
		$extension_license_keys = array();
	}

	// Save the data

	$extensions = msa_get_extensions();

	foreach ( $extensions as $key => $extension ) {

		if ( isset($extension['settings']) ) {

			// License Key

			$extension_license_keys[$key]['license_key'] = isset($data['msa-extension-' . $key . '-license-key']) ? sanitize_text_field($data['msa-extension-' . $key . '-license-key']) : '';

		}

	}

	update_option('msa_extension_license_keys', $extension_license_keys);

}
add_action('msa_save_settings', 'msa_settings_tab_extensions_save', 10, 1);

/**
 * The content of the Extensions Page
 *
 * @access public
 * @param mixed $content
 * @return void
 */
function msa_settings_tab_extensions_content($content) {

	// Check if we have data already saved

	if ( false === ( $settings = get_option('msa_extension_license_keys') ) ) {
		$settings = array();
	}

	$output = '';

	$extensions = msa_get_extensions();

	if ( count($extensions) == 0 ) {

		$output .= '<h3 class="msa-settings-heading">' . __('No Extensions', 'msa') . '</h3>';
		$output .= '<p>' . __('It looks like you do not have any extensions active yet.  Please visit the', 'msa') . ' <a href="' . MY_SITE_AUDIT_EXT_URL . '" target="_blank">' .  __('Extensions Store', 'msa') . '</a> ' . __('to buy some :)', 'msa') . '</p>';

		return $output;

	}

	foreach ( $extensions as $key => $extension ) {

		if ( isset($extension['settings']) ) {

			$setting = $extension['settings'];
			$license_key = isset($settings[$key]['license_key']) ? $settings[$key]['license_key'] : $extension['license_key'];

			$output .= '<h3 class="msa-settings-heading">' . $extension['title'] . '</h3>';
			$output .= '<table class="form-table">
				<tbody>

					<tr>
						<th scope="row"><label for="' . $setting['id'] . '">' . __('License Key', 'msa') . '</label></th>
						<td>
							<input type="text" data-extension="' . $key . '" class="regular-text msa-license-key ' . $setting['class'] . '-license-key" id="' . $setting['id'] . '-license-key" name="msa-extension-' . $key . '-license-key" value="' . $license_key . '">
						</td>
					</tr>

				</tbody>
			</table>';

		}

	}

	return $output;

}
add_filter('msa_settings_tab_content_extensions', 'msa_settings_tab_extensions_content', 10, 1);

/**
 * Perform all the actions with a license
 *
 * @access public
 * @static
 * @return void
 */
function msa_license_action() {

	// Check if we have an extension

	if ( !isset($_POST['extension']) ) {
		echo '';
		die();
	}

	// Check if we have a license key

	if ( !isset($_POST['license_key']) ) {
		echo '';
		die();
	}

	// data to send in our API request

	$api_params = array(
		'edd_action'	=> $_POST['license_action'],
		'license' 		=> $_POST['license_key'],
		'item_name' 	=> urlencode( MY_SITE_AUDIT_ITEM_NAME ), // the name of our product in EDD
		'url'       	=> is_multisite() ? network_home_url() : home_url()
	);

	// Call the custom API.

	$response = wp_remote_post( MY_SITE_AUDIT_STORE_URL , array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

	// make sure the response came back okay

	if ( is_wp_error( $response ) ) {
		echo $response->get_error_message();
		die();
	}

	$license_data = json_decode( wp_remote_retrieve_body( $response ) );

/*
	if ( $_POST['license_action'] == 'activate_license' ) {

		msa_update_license_key($_POST['extension'], $_POST['license_key']);

	} else if ( $_POST['license_action'] == 'deactivate_license' && $license_data->license == 'deactivated' ) {

		msa_update_license_key($_POST['extension'], '');

	}
*/

	// decode the license data

	echo $license_data->license;

	die();

}
add_action( 'wp_ajax_msa_license_action', 'msa_license_action' );

/**
 * Check if license key is active
 *
 * @access public
 * @static
 * @param mixed $license_key
 * @return void
 */
function msa_is_license_active($license_key) {

	if ( ! isset( $license_key ) ) {
		return false;
	}

	// data to send in our API request

	$api_params = array(
		'edd_action'	=> 'check_license',
		'license' 		=> $license_key,
		'item_name' 	=> urlencode( MY_SITE_AUDIT_ITEM_NAME ), // the name of our product in EDD
		'url'       	=> is_multisite() ? network_home_url() : home_url()
	);

	// Call the custom API.

	$response = wp_remote_post( MY_SITE_AUDIT_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

	// make sure the response came back okay

	if ( is_wp_error( $response ) ) {
		return false;
	}

	$license_data = json_decode( wp_remote_retrieve_body( $response ) );

	// decode the license data

	if ( isset($license_data->license) && $license_data->license == 'valid' ) {
		return true;
	} else {
		return false;
	}

}

/**
 * Get the license key
 *
 * @access public
 * @static
 * @return void
 */
function msa_get_license_key($extension = null) {

	// Get the license key from based on WordPress install

	if ( function_exists('is_multisite') && is_multisite() ) {
		$license_keys = get_site_option('msa_extension_license_keys');
	} else {
		$license_keys = get_option('msa_extension_license_keys');
	}

	// Check if license key is set

	if ( $license_keys === false ) {
		$license_keys = array();
	}

	if ( isset($extension) ) {
		return isset($license_keys[$extension]['license_key']) ? $license_keys[$extension]['license_key'] : '';
	}

	return $license_keys;
}

/**
 * Update the license key
 *
 * @access public
 * @static
 * @param mixed $license_key
 * @return void
 */
function msa_update_license_key($extension, $license_key) {

	$license_keys = msa_get_license_key();

	if ( isset( $license_key ) ) {

		$license_keys[$extension] = $license_key;

		// Get the license key from based on WordPress install

		if ( function_exists('is_multisite') && is_multisite() ) {
			update_site_option('msa_extension_license_keys', $license_keys);
		} else {
			update_option('msa_extension_license_keys', $license_keys);
		}
	}
}