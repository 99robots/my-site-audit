<?php
/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 10/26/15
 * Package: Functions/Conditions
 * File: condition.php
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
 * Create initial conidtions
 *
 * @access public
 * @return void
 */
function msa_create_initial_conditions() {

	// Title

	msa_register_condition('title', array(
		'name' 			=> __('Title', 'msa'),
		'weight'        => 2,
		'comparison'	=> 1,
		'value'			=> 0,
		'max'           => 60,
	));

	// Modified Date

	msa_register_condition('modified_date', array(
		'name' 			=> __('Modified Date', 'msa'),
		'weight'        => 3,
		'comparison'	=> 1,
		'value'			=> 1,
		'max'           => DAY_IN_SECONDS * 90,
	));

	// Word Count

	msa_register_condition('word_count', array(
		'name' 			=> __('Word Count', 'msa'),
		'weight'        => 10,
		'comparison'	=> 0,
		'value'			=> 1,
		'min'           => 750,
	));

	// Comment Count

	msa_register_condition('comment_count', array(
		'name' 			=> __('Comment Count', 'msa'),
		'weight'        => 1,
		'comparison'	=> 0,
		'value'			=> 1,
		'min'           => 5,
	));

	// Internal Links

	msa_register_condition('internal_links', array(
		'name' 			=> __('Internal Links', 'msa'),
		'weight'        => 5,
		'comparison'	=> 2,
		'value'			=> 1,
		'min'           => 2,
		'max'           => 6,
	));

	// External Links

	msa_register_condition('external_links', array(
		'name' 			=> __('External Links', 'msa'),
		'weight'        => 5,
		'comparison'	=> 2,
		'value'			=> 1,
		'min'           => 1,
		'max'           => 14,
	));

	// Images

	msa_register_condition('images', array(
		'name' 			=> __('Images', 'msa'),
		'weight'        => 3,
		'comparison'	=> 0,
		'value'			=> 0,
		'min'           => 2,
	));

	// Headings

	msa_register_condition('headings', array(
		'name' 			=> __('Headings', 'msa'),
		'weight'        => 6,
		'comparison'	=> 0,
		'value'			=> 0,
		'min'           => 5,
	));

}

/**
 * Get all the conditions
 *
 * @access public
 * @return void
 */
function msa_get_conditions() {

	global $msa_conditions;

	if ( ! is_array( $msa_conditions ) ) {
		$msa_conditions = array();
	}

	return apply_filters('msa_get_conditions', $msa_conditions);
}

/**
 * Register a new condition
 *
 * @access public
 * @param mixed $condition
 * @param array $args (default: array())
 * @return void
 */
function msa_register_condition( $condition, $args = array() ) {

	global $msa_conditions;

	if ( ! is_array( $msa_conditions ) ) {
		$msa_conditions = array();
	}

	// Default condition

	$default = array(
		'name'			=> __('Condition', 'msa'),
		'weight'        => 1,
		'comparison'	=> 0,
		'value'			=> 0,
		'max'           => 0,
		'min'			=> 0,
	);

	$args = array_merge($default, $args);

	// Add the condition to the global conditions array

	$msa_conditions[ $condition ] = apply_filters('msa_register_condition_args', $args);

	/**
	* Fires after a condition is registered.
	*
	* @param string $condition Condition.
	* @param array $args      Arguments used to register the condition.
	*/
	do_action('msa_registed_condition', $condition, $args);

	return $args;
}