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
 * Create the initial settings extensions
 *
 * @access public
 * @return void
 */
function msa_create_initial_extensions() {

	// Conditions Control

	msa_register_extensions('conditions_control', array(
		'title'		=> __('Conditions Control', 'msa'),
		'content'	=> apply_filters('msa_extension_conditions_control', ''),
	));

	// Extension 1

	msa_register_extensions('extension_1', array(
		'title'		=> __('Extension 1', 'msa'),
		'content'	=> apply_filters('msa_extension_extension_1', ''),
	));

	// Extension 2

	msa_register_extensions('extension_2', array(
		'title'		=> __('Extension 2', 'msa'),
		'content'	=> apply_filters('msa_extension_extension_2', ''),
	));

	// Extension 3

	msa_register_extensions('extension_3', array(
		'title'		=> __('Extension 3', 'msa'),
		'content'	=> apply_filters('msa_extension_extension_3', ''),
	));

	// Extension 4

	msa_register_extensions('extension_4', array(
		'title'		=> __('Extension 4', 'msa'),
		'content'	=> apply_filters('msa_extension_extension_4', ''),
	));

	// Extension 5

	msa_register_extensions('extension_5', array(
		'title'		=> __('Extension 5', 'msa'),
		'content'	=> apply_filters('msa_extension_extension_5', ''),
	));

	// Extension 6

	msa_register_extensions('extension_6', array(
		'title'		=> __('Extension 6', 'msa'),
		'content'	=> apply_filters('msa_extension_extension_6', ''),
	));

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
function msa_register_extensions($extension, $args = array()) {

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