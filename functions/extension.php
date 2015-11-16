<?php
/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 10/29/15
 * Package: Functions/Extensions
 * File: extension.php
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
							<input type="text" class="regular-text ' . $setting['class'] . '-license-key" id="' . $setting['id'] . '-license-key" name="msa-extension-' . $key . '-license-key" value="' . $license_key . '">
							<p class="description">' . __('The license key for the extension that will allow you to update and get support for this extension.') . '</p>
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
 * Create the initial settings extensions
 *
 * @access public
 * @return void
 */
function msa_create_initial_extensions() {

	do_action('msa_register_extension');
}

/**
 * Get all the remote extensions
 *
 * @access public
 * @return void
 */
function msa_get_remote_extensions() {

	$remote_extensions = array();

	if ( function_exists('curl_init') ) {

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, 'https://99robots.com/my-site-audit/extensions.json');
		$remote_extensions = curl_exec($ch);
		curl_close($ch);

		$remote_extensions = json_decode($remote_extensions, true);
	}

	return $remote_extensions;

}

/**
 * Get the settings extensions
 *
 * @access public
 * @return void
 */
function msa_get_extensions() {

	global $msa_extensions;

	if ( ! is_array( $msa_extensions ) ) {
		$msa_extensions = array();
	}

	return apply_filters('msa_get_extensions', $msa_extensions);

}

/**
 * Register a new Extension
 *
 * @access public
 * @param mixed $extension
 * @param array $args (default: array())
 * @return void
 */
function msa_register_extension($extension, $args = array()) {

	global $msa_extensions;

	if ( ! is_array( $msa_extensions ) ) {
		$msa_extensions = array();
	}

	// Default extension

	$default = array(
		'title'			=> __('Extension', 'msa'),
	);

	$args = array_merge($default, $args);

	// Add the extension to the global extensions array

	$msa_extensions[ $extension ] = apply_filters('msa_register_extension_args', $args);

	/**
	* Fires after a dashboard extension is registered.
	*
	* @param string $extension 	  Extension.
	* @param array $args      Arguments used to register the extension.
	*/
	do_action('msa_registed_extension', $extension, $args);

	return $args;

}