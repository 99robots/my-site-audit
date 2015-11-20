<?php
/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 10/29/15
 * Package: Functions/Settings Tab
 * File: settings-tab.php
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
 * Save all the general settings
 *
 * @access public
 * @param mixed $data
 * @return void
 */
function msa_settings_tab_settings_save($data) {

	// Check if we have data already saved

	if ( false === ( $settings = get_option('msa_settings') ) ) {
		$settings = array();
	}

	//$settings['use_slow_conditions'] = isset($data['msa-use-slow-conditions']) && $data['msa-use-slow-conditions'] ? true : false;

	$settings['notification_emails'] = isset($data['msa-notification-emails']) ? sanitize_text_field($data['msa-notification-emails']) : '';

	// Save the data

	update_option('msa_settings', $settings);

}
add_action('msa_save_settings', 'msa_settings_tab_settings_save', 10, 1);

/**
 * This function will show all the general settings for My Site Audit
 *
 * @access public
 * @param mixed $content
 * @return void
 */
function msa_settings_tab_settings_content($content) {

	// Check if we have data already saved

	if ( false === ( $settings = get_option('msa_settings') ) ) {
		$settings = array();
	}

/*
	<tr>
		<th scope="row"><label for="msa-use-slow-conditions">' . __('Use Slow Conditions', 'msa') . '</label></th>
		<td>
			<input type="checkbox" class="msa-use-slow-conditions" id="msa-use-slow-conditions" name="msa-use-slow-conditions" ' . ( isset($settings['use_slow_conditions']) && $settings['use_slow_conditions'] ? 'checked="checked"' : '' ) . '>
			<p class="description">' . __('Do you want to use conditions that take a long time to compute, like checking for broken links and broken images?') . '</p>
		</td>
	</tr>
*/

	$output = '<h3 class="msa-settings-heading">' . __('Settings', 'msa') . '</h3>';
	$output .= '<table class="form-table">
		<tbody>

			<tr>
				<th scope="row"><label for="msa-notification-emails">' . __('Notification Emails', 'msa') . '</label></th>
				<td>
					<input type="text" class="regular-text msa-notification-emails" id="msa-notification-emails" name="msa-notification-emails" value="' . ( isset($settings['notification_emails']) ? $settings['notification_emails'] : get_option('admin_email') ) . '">
					<p class="description">' . __('Add any email address you want us to notify for audit events, like the completion of an audit.  Separate each email with a comma.') . '</p>
				</td>
			</tr>

		</tbody>
	</table>';

	return $output;

}
add_filter('msa_settings_tab_content_settings', 'msa_settings_tab_settings_content', 10, 1);

/**
 * Create the initial settings tabs
 *
 * @access public
 * @return void
 */
function msa_create_initial_settings_tabs() {

	// Settings

	msa_register_settings_tabs('settings', array(
		'id'		=> 'settings',
		'current'	=> true,
		'tab'		=> __('Settings', 'msa'),
		'content'	=> '',
	));

	// Extensions

	msa_register_settings_tabs('extensions', array(
		'id'		=> 'extensions',
		'current'	=> false,
		'tab'		=> __('Extensions', 'msa'),
		'content'	=> '',
	));

	// System Info

	msa_register_settings_tabs('system_info', array(
		'id'		=> 'system_info',
		'current'	=> false,
		'tab'		=> __('System Info', 'msa'),
		'content'	=> '',
	));

	do_action('msa_register_settings_tabs');
}

/**
 * Get the settings tabs
 *
 * @access public
 * @return void
 */
function msa_get_settings_tabs() {

	global $msa_settings_tabs;

	if ( ! is_array( $msa_settings_tabs ) ) {
		$msa_settings_tabs = array();
	}

	return apply_filters('msa_get_settings_tabs', $msa_settings_tabs);

}

/**
 * Register a new Settings Tab
 *
 * @access public
 * @param mixed $tab
 * @param array $args (default: array())
 * @return void
 */
function msa_register_settings_tabs($tab, $args = array()) {

	global $msa_settings_tabs;

	if ( ! is_array( $msa_settings_tabs ) ) {
		$msa_settings_tabs = array();
	}

	// Default tab

	$default = array(
		'tab'			=> __('Tab', 'msa'),
	);

	$args = array_merge($default, $args);

	// Add the tab to the global dashboard tabs array

	$msa_settings_tabs[ $tab ] = apply_filters('msa_register_settings_tab_args', $args);

	/**
	* Fires after a dashboard tab is registered.
	*
	* @param string $tab 	  Settings Tab.
	* @param array $args      Arguments used to register the dashboard tab.
	*/
	do_action('msa_registed_settings_tab', $tab, $args);

	return $args;

}