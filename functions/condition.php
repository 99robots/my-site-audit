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

	/*
	 * Comparsion:
	 *
	 * 0 = greater than some number
	 * 1 = less than some number
	 * 2 = in between some numbers
	 *
	 */
	/*
	 * Value:
	 *
	 * 0 = boolean result (i.e pass or fail)
	 * 1 = ratio (i.e. .123)
	 *
	 */

	// Title

	msa_register_condition('title', array(
		'name' 			=> __('Title', 'msa'),
		'weight'        => 2,
		'comparison'	=> 1,
		'value'			=> 0,
		'max'           => 60,
		'settings'		=> array(
			'id'				=> 'msa-condition-title',
			'class'				=> 'msa-condition-title',
			'name'				=> 'msa-condition-title',
			'key'				=> 'title',
			'description-max'	=> __('The maximum number of characters a title is allowed to be.', 'msa'),
		),
		'filter'		=> array(
			'label'		=> __('Title Length', 'msa'),
			'name'		=> 'title',
			'options'	=> array(
				array(
					'name' 	=> __('More Than 60 Characters', 'msa'),
					'value'	=> 'more-60',
				),
				array(
					'name' 	=> __('Less Than Characters', 'msa'),
					'value'	=> 'less-60',
				),
			)
		)
	));

	// Modified Date

	msa_register_condition('modified_date', array(
		'name' 			=> __('Modified Date', 'msa'),
		'weight'        => 3,
		'comparison'	=> 1,
		'value'			=> 1,
		'max'           => DAY_IN_SECONDS * 90,
		'settings'		=> array(
			'id'				=> 'msa-condition-modified-date',
			'class'				=> 'msa-condition-modified-date',
			'name'				=> 'msa-condition-modified-date',
			'key'				=> 'modified_date',
			'description-max'	=> __('The maximum number of seconds your posts can go without being modified.', 'msa'),
		),
		'filter'		=> array(
			'label'		=> __('Modified Date', 'msa'),
			'name'		=> 'modified_date',
			'options'	=> array(
				array(
					'name' 	=> __('More Than 90 Days Ago', 'msa'),
					'value'	=> 'more-' . DAY_IN_SECONDS * 90,
				),
				array(
					'name' 	=> __('Less Than 90 Days Ago', 'msa'),
					'value'	=> 'less-' . DAY_IN_SECONDS * 90,
				),
			)
		)
	));

	// Word Count

	msa_register_condition('word_count', array(
		'name' 			=> __('Word Count', 'msa'),
		'weight'        => 10,
		'comparison'	=> 0,
		'value'			=> 1,
		'min'           => 750,
		'settings'		=> array(
			'id'				=> 'msa-condition-word-count',
			'class'				=> 'msa-condition-word-count',
			'name'				=> 'msa-condition-word-count',
			'key'				=> 'word_count',
			'description-min'	=> __('The minimum number of words each post should have.', 'msa'),
		),
		'filter'		=> array(
			'label'		=> __('Word Count', 'msa'),
			'name'		=> 'word_count',
			'options'	=> array(
				array(
					'name' 	=> __('More Than 750 Words', 'msa'),
					'value'	=> 'more-750',
				),
				array(
					'name' 	=> __('Less Than 750 Words', 'msa'),
					'value'	=> 'less-750',
				),
			)
		)
	));

	// Comment Count

	msa_register_condition('comment_count', array(
		'name' 			=> __('Comment Count', 'msa'),
		'weight'        => 1,
		'comparison'	=> 0,
		'value'			=> 1,
		'min'           => 5,
		'settings'		=> array(
			'id'				=> 'msa-condition-comment-count',
			'class'				=> 'msa-condition-comment-count',
			'name'				=> 'msa-condition-comment-count',
			'key'				=> 'word_count',
			'description-min'	=> __('The minimum number of comments each post should have.', 'msa'),
		),
		'filter'		=> array(
			'label'		=> __('Comment Count', 'msa'),
			'name'		=> 'comment_count',
			'options'	=> array(
				array(
					'name' 	=> __('More Than 5 Comments', 'msa'),
					'value'	=> 'more-5',
				),
				array(
					'name' 	=> __('Less Than 5 Comments', 'msa'),
					'value'	=> 'less-5',
				),
			)
		)
	));

	// Internal Links

	msa_register_condition('internal_links', array(
		'name' 			=> __('Internal Links', 'msa'),
		'weight'        => 5,
		'comparison'	=> 2,
		'value'			=> 1,
		'min'           => 2,
		'max'           => 6,
		'settings'		=> array(
			'id'				=> 'msa-condition-internal-links',
			'class'				=> 'msa-condition-internal-links',
			'name'				=> 'msa-condition-internal-links',
			'key'				=> 'internal_links',
			'description-min'	=> __('The minimum number of Internal Links each post should have.', 'msa'),
			'description-max'	=> __('The maximum number of Internal Links each post should have.', 'msa'),
		),
		'filter'		=> array(
			'label'		=> __('Internal Links', 'msa'),
			'name'		=> 'internal_links',
			'options'	=> array(
				array(
					'name' 	=> __('More Than 4 Internal Links', 'msa'),
					'value'	=> 'more-4',
				),
				array(
					'name' 	=> __('Less Than 4 Internal Links', 'msa'),
					'value'	=> 'less-4',
				),
			)
		)
	));

	// External Links

	msa_register_condition('external_links', array(
		'name' 			=> __('External Links', 'msa'),
		'weight'        => 5,
		'comparison'	=> 2,
		'value'			=> 1,
		'min'           => 1,
		'max'           => 14,
		'settings'		=> array(
			'id'				=> 'msa-condition-external-links',
			'class'				=> 'msa-condition-external-links',
			'name'				=> 'msa-condition-external-links',
			'key'				=> 'external_links',
			'description-min'	=> __('The minimum number of External Links each post should have.', 'msa'),
			'description-max'	=> __('The maximum number of External Links each post should have.', 'msa'),
		),
		'filter'		=> array(
			'label'		=> __('External Links', 'msa'),
			'name'		=> 'external_links',
			'options'	=> array(
				array(
					'name' 	=> __('More Than 7 External Links', 'msa'),
					'value'	=> 'more-7',
				),
				array(
					'name' 	=> __('Less Than 7 External Links', 'msa'),
					'value'	=> 'less-7',
				),
			)
		)
	));

	// Images

	msa_register_condition('images', array(
		'name' 			=> __('Images', 'msa'),
		'weight'        => 3,
		'comparison'	=> 0,
		'value'			=> 1,
		'min'           => 2,
		'settings'		=> array(
			'id'				=> 'msa-condition-images',
			'class'				=> 'msa-condition-images',
			'name'				=> 'msa-condition-images',
			'key'				=> 'images',
			'description-min'	=> __('The minimum number of images each post should have.', 'msa'),
		),
		'filter'		=> array(
			'label'		=> __('Images', 'msa'),
			'name'		=> 'images',
			'options'	=> array(
				array(
					'name' 	=> __('More Than 2 Images', 'msa'),
					'value'	=> 'more-2',
				),
				array(
					'name' 	=> __('Less Than 2 Images', 'msa'),
					'value'	=> 'less-2',
				),
			)
		)
	));

	// Headings

	msa_register_condition('headings', array(
		'name' 			=> __('Headings', 'msa'),
		'weight'        => 6,
		'comparison'	=> 0,
		'value'			=> 1,
		'min'           => 5,
		'settings'		=> array(
			'id'				=> 'msa-condition-headings',
			'class'				=> 'msa-condition-headings',
			'name'				=> 'msa-condition-headings',
			'key'				=> 'images',
			'description-min'	=> __('The minimum number of headings each post should have.', 'msa'),
		),
		'filter'		=> array(
			'label'		=> __('Headings', 'msa'),
			'name'		=> 'headings',
			'options'	=> array(
				array(
					'name' 	=> __('More Than 5 Headings', 'msa'),
					'value'	=> 'more-5',
				),
				array(
					'name' 	=> __('Less Than 5 Headings', 'msa'),
					'value'	=> 'less-5',
				),
			)
		)
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