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
 * Show the content for the condition values that need custom implementation.
 *
 * @access public
 * @param mixed $value
 * @param mixed $data
 * @param mixed $post
 * @param mixed $key
 * @return void
 */
function msa_condition_category_content_value($value, $data, $post, $key) {

	// Modified Date

	if ( $key == 'modified_date' ) {
		return date('M j, Y', strtotime($post->post_modified));
	}

	// Links

	if ( $key == 'internal_links' || $key == 'external_links' || $key == 'broken_links' ) {
		return msa_show_links($data, $key);
	}

	// Images

	if ( $key == 'images' ) {
		return msa_show_images($post->post_content);
	}

	// Missing Alt Tag

	if ( $key == 'missing_alt_tag' ) {
		return msa_show_images_without_alt($post->post_content);
	}

	// H1 Tags

	if ( $key == 'h1_tag' ) {
		return msa_show_h1_tags($post->post_content, $data);
	}

	// Invalid Headings

	if ( $key == 'invalid_headings' ) {
		return msa_show_invalid_headings($data['invalid_headings_data']);
	}

	// Headings

	if ( $key == 'headings' ) {
		return msa_show_headings($post->post_content, $data);
	}

	return $value;

}
add_filter('msa_condition_category_content_value', 'msa_condition_category_content_value', 10, 4);

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
	 * 1 = greater than some number
	 * 2 = less than some number
	 * 3 = in between some numbers
	 *
	 */
	/*
	 * Value:
	 *
	 * 1 = boolean result (i.e pass or fail)
	 * 2 = ratio (i.e. .123)
	 *
	 */

	/* =======================================================================
	 *
	 * Content
	 *
	 * ===================================================================== */

	// Title

	msa_register_condition('title', array(
		'name' 				=> __('Title', 'msa'),
		'weight'        	=> 5,
		'comparison'		=> 2,
		'value'				=> 1,
		'max'           	=> 60,
		'units'				=> 'characters',
		'max_display_val'	=> __('60 Characters', 'msa'),
		'category'			=> 'content',
		'show_column'		=> true,
		'settings'		=> array(
			'id'				=> 'msa-condition-title',
			'class'				=> 'msa-condition-title',
			'name'				=> 'msa-condition-title',
			'description-max'	=> __('The maximum number of characters a title is allowed to be.', 'msa'),
		),
		'filter'		=> array(
			'label'		=> __('Title Lengths', 'msa'),
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
		'name' 				=> __('Modified Date', 'msa'),
		'weight'        	=> 5,
		'comparison'		=> 2,
		'value'				=> 2,
		'units'				=> 'seconds',
		'max'          		=> DAY_IN_SECONDS * 180,
		'max_display_val'	=> __('90 Days', 'msa'),
		'category'			=> 'content',
		'show_column'		=> true,
		'settings'		=> array(
			'id'				=> 'msa-condition-modified_date',
			'class'				=> 'msa-condition-modified_date',
			'name'				=> 'msa-condition-modified_date',
			'description-max'	=> __('The maximum number of seconds your posts can go without being modified.', 'msa'),
		),
		'filter'		=> array(
			'label'		=> __('Modified Dates', 'msa'),
			'name'		=> 'modified_date',
			'options'	=> array(
				array(
					'name' 	=> __('More Than 90 Days Ago', 'msa'),
					'value'	=> 'more-' . DAY_IN_SECONDS * 180,
				),
				array(
					'name' 	=> __('Less Than 90 Days Ago', 'msa'),
					'value'	=> 'less-' . DAY_IN_SECONDS * 180,
				),
			)
		)
	));

	// Word Count

	msa_register_condition('word_count', array(
		'name' 				=> __('Word Count', 'msa'),
		'weight'        	=> 15,
		'comparison'		=> 1,
		'value'				=> 2,
		'min'           	=> 750,
		'units'				=> 'words',
		'min_display_val'	=> __('750 Words', 'msa'),
		'category'			=> 'content',
		'show_column'		=> true,
		'settings'		=> array(
			'id'				=> 'msa-condition-word_count',
			'class'				=> 'msa-condition-word_count',
			'name'				=> 'msa-condition-word_count',
			'description-min'	=> __('The minimum number of words each post should have.', 'msa'),
		),
		'filter'		=> array(
			'label'		=> __('Word Counts', 'msa'),
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
		'name' 				=> __('Comment Count', 'msa'),
		'weight'        	=> 5,
		'comparison'		=> 1,
		'value'				=> 2,
		'min'           	=> 5,
		'units'				=> 'comments',
		'min_display_val'	=> __('5 Comments', 'msa'),
		'category'			=> 'content',
		'show_column'		=> true,
		'settings'		=> array(
			'id'				=> 'msa-condition-comment_count',
			'class'				=> 'msa-condition-comment_count',
			'name'				=> 'msa-condition-comment_count',
			'description-min'	=> __('The minimum number of comments each post should have.', 'msa'),
		),
		'filter'		=> array(
			'label'		=> __('Comment Counts', 'msa'),
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

	/* =======================================================================
	 *
	 * Links
	 *
	 * ===================================================================== */

	// Internal Links

	msa_register_condition('internal_links', array(
		'name' 				=> __('Internal Links', 'msa'),
		'weight'        	=> 8,
		'comparison'		=> 1,
		'value'				=> 2,
		'min'           	=> 3,
		'units'				=> 'links',
		'min_display_val'	=> __('2 Internal Links', 'msa'),
		'max_display_val'	=> __('6 Internal Links', 'msa'),
		'category'			=> 'links',
		'show_column'		=> true,
		'settings'		=> array(
			'id'				=> 'msa-condition-internal_links',
			'class'				=> 'msa-condition-internal_links',
			'name'				=> 'msa-condition-internal_links',
			'description-min'	=> __('The minimum number of Internal Links each post should have.', 'msa'),
			'description-max'	=> __('The maximum number of Internal Links each post should have.', 'msa'),
		),
		'filter'		=> array(
			'label'		=> __('Internal Links', 'msa'),
			'name'		=> 'internal_links',
			'options'	=> array(
				array(
					'name' 	=> __('More Than 4 Internal Links', 'msa'),
					'value'	=> 'more-3',
				),
				array(
					'name' 	=> __('Less Than 4 Internal Links', 'msa'),
					'value'	=> 'less-3',
				),
			)
		)
	));

	// External Links

	msa_register_condition('external_links', array(
		'name' 				=> __('External Links', 'msa'),
		'weight'        	=> 8,
		'comparison'		=> 1,
		'value'				=> 2,
		'min'           	=> 6,
		'units'				=> 'links',
		'min_display_val'	=> __('1 Internal Links', 'msa'),
		'max_display_val'	=> __('14 Internal Links', 'msa'),
		'category'			=> 'links',
		'show_column'		=> true,
		'settings'		=> array(
			'id'				=> 'msa-condition-external_links',
			'class'				=> 'msa-condition-external_links',
			'name'				=> 'msa-condition-external_links',
			'description-min'	=> __('The minimum number of External Links each post should have.', 'msa'),
			'description-max'	=> __('The maximum number of External Links each post should have.', 'msa'),
		),
		'filter'		=> array(
			'label'		=> __('External Links', 'msa'),
			'name'		=> 'external_links',
			'options'	=> array(
				array(
					'name' 	=> __('More Than 7 External Links', 'msa'),
					'value'	=> 'more-6',
				),
				array(
					'name' 	=> __('Less Than 7 External Links', 'msa'),
					'value'	=> 'less-6',
				),
			)
		)
	));

	// Broken Links

	msa_register_condition('broken_links', array(
		'name' 			=> __('Broken Links', 'msa'),
		'weight'        	=> 14,
		'comparison'		=> 2,
		'value'				=> 1,
		'max'           	=> 1,
		'units'				=> 'links',
		'max_display_val'	=> __('1 Link', 'msa'),
		'category'			=> 'links',
		'show_column'		=> true,
		'settings'		=> array(
			'id'				=> 'msa-condition-broken_links',
			'class'				=> 'msa-condition-broken_links',
			'name'				=> 'msa-condition-broken_links',
			'description-max'	=> __('The maximum number of broken links allowed per post.', 'msa'),
		),
		'filter'		=> array(
			'label'		=> __('Broken Links', 'msa'),
			'name'		=> 'broken_links',
			'options'	=> array(
				array(
					'name' 	=> __('Has Broken Links', 'msa'),
					'value'	=> 'notequal-0',
				),
				array(
					'name' 	=> __('Does Not Have Broken Links', 'msa'),
					'value'	=> 'equal-0',
				),
			),
		)
	));

	/* =======================================================================
	 *
	 * Images
	 *
	 * ===================================================================== */

	// Images

	msa_register_condition('images', array(
		'name' 				=> __('Images', 'msa'),
		'weight'        	=> 12,
		'comparison'		=> 1,
		'value'				=> 2,
		'min'           	=> 3,
		'units'				=> 'images',
		'min_display_val'	=> __('2 Images', 'msa'),
		'category'			=> 'images',
		'show_column'		=> true,
		'settings'		=> array(
			'id'				=> 'msa-condition-images',
			'class'				=> 'msa-condition-images',
			'name'				=> 'msa-condition-images',
			'description-min'	=> __('The minimum number of images each post should have.', 'msa'),
		),
		'filter'		=> array(
			'label'		=> __('Images', 'msa'),
			'name'		=> 'images',
			'options'	=> array(
				array(
					'name' 	=> __('More Than 2 Images', 'msa'),
					'value'	=> 'more-3',
				),
				array(
					'name' 	=> __('Less Than 2 Images', 'msa'),
					'value'	=> 'less-3',
				),
			)
		)
	));

	// Missing Alt Tag

	msa_register_condition('missing_alt_tag', array(
		'name' 			=> __('Missing Alt Tag', 'msa'),
		'weight'        	=> 8,
		'comparison'		=> 2,
		'value'				=> 1,
		'max'           	=> 1,
		'units'				=> 'missing alt tags',
		'max_display_val'	=> __('1 Missing Alt Tag', 'msa'),
		'category'			=> 'images',
		'show_column'		=> true,
		'settings'		=> array(
			'id'				=> 'msa-condition-missing_alt_tag',
			'class'				=> 'msa-condition-missing_alt_tag',
			'name'				=> 'msa-condition-missing_alt_tag',
			'description-max'	=> __('The maximum number of images without an alt tag allowed per post.', 'msa'),
		),
		'filter'		=> array(
			'label'		=> __('Missing Alt Tags', 'msa'),
			'name'		=> 'missing_alt_tag',
			'options'	=> array(
				array(
					'name' 	=> __('Has a Missing Alt Tag', 'msa'),
					'value'	=> 'notequal-0',
				),
				array(
					'name' 	=> __('Does Not Have a Missing Alt Tag', 'msa'),
					'value'	=> 'equal-0',
				),
			),
		)
	));

	/* =======================================================================
	 *
	 * Headings
	 *
	 * ===================================================================== */

	// No h1 tag

	msa_register_condition('h1_tag', array(
		'name' 				=> __('Has H1 Tags', 'msa'),
		'weight'        	=> 3,
		'comparison'		=> 2,
		'value'				=> 1,
		'max'           	=> 1,
		'units'				=> 'h1 tags',
		'max_display_val'	=> __('1 H1 Tag', 'msa'),
		'category'			=> 'headings',
		'show_column'		=> true,
		'settings'		=> array(
			'id'				=> 'msa-condition-h1-tag',
			'class'				=> 'msa-condition-h1-tag',
			'name'				=> 'msa-condition-h1-tag',
			'description-max'	=> __('The minimum number of H1 tags allowed for each post.', 'msa'),
		),
		'filter'		=> array(
			'label'		=> __('H1 Tags', 'msa'),
			'name'		=> 'h1_tag',
			'options'	=> array(
				array(
					'name' 	=> __('Has 1 Tags', 'msa'),
					'value'	=> 'notequal-0',
				),
				array(
					'name' 	=> __('Does Not Have an H1 Tag', 'msa'),
					'value'	=> 'equal-0',
				),
			)
		)
	));

	// Invalid Headings

	msa_register_condition('invalid_headings', array(
		'name' 				=> __('Invalid Headings', 'msa'),
		'weight'        	=> 4,
		'comparison'		=> 2,
		'value'				=> 1,
		'max'           	=> 1,
		'units'				=> 'invalid headings',
		'max_display_val'	=> __('1 Invalid Heading', 'msa'),
		'category'			=> 'headings',
		'show_column'		=> true,
		'settings'		=> array(
			'id'				=> 'msa-condition-invalid-headings',
			'class'				=> 'msa-condition-invalid-headings',
			'name'				=> 'msa-condition-invalid-headings',
			'description-max'	=> __('The minimum number of invalid headings allowed for each post.', 'msa'),
		),
		'filter'		=> array(
			'label'		=> __('Invalid Headings', 'msa'),
			'name'		=> 'invalid_headings',
			'options'	=> array(
				array(
					'name' 	=> __('Has Invalid Headings', 'msa'),
					'value'	=> 'notequal-0',
				),
				array(
					'name' 	=> __('Does Not Have Invalid Headings', 'msa'),
					'value'	=> 'equal-0',
				),
			)
		)
	));

	// Headings

	msa_register_condition('headings', array(
		'name' 				=> __('Headings', 'msa'),
		'weight'        	=> 13,
		'comparison'		=> 1,
		'value'				=> 2,
		'min'           	=> 5,
		'units'				=> 'headings',
		'min_display_val'	=> __('5 Headings', 'msa'),
		'category'			=> 'headings',
		'show_column'		=> true,
		'settings'		=> array(
			'id'				=> 'msa-condition-headings',
			'class'				=> 'msa-condition-headings',
			'name'				=> 'msa-condition-headings',
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

	do_action('msa_register_conditions');
}

/**
 * Get total weight of all registered conditions
 *
 * @access public
 * @return void
 */
function msa_get_total_conditions_weight() {

	$conditions = msa_get_conditions();
	$weight = 0;

	foreach ( $conditions as $condition ) {
		$weight += $condition['weight'];
	}

	return $weight;
}

/**
 * Get total weight for specific conditions
 *
 * @access public
 * @return void
 */
function msa_get_total_weight_for_conditions($conditions) {

	$weight = 0;

	foreach ( $conditions as $condition ) {
		$weight += $condition['weight'];
	}

	return $weight;
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