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

	// Conditions

	msa_register_settings_tabs('conditions', array(
		'id'		=> 'conditions',
		'current'	=> false,
		'tab'		=> __('Conditions', 'msa'),
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