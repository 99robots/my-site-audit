<?php
/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 10/29/15
 * Package: Functions/Attribute
 * File: attribute.php
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
 * Change the post sort data based on the sort
 *
 * @access public
 * @param mixed $value
 * @param mixed $array
 * @param mixed $orderby
 * @return void
 */
function msa_audit_posts_table_sort_data_attribute($value, $array, $orderby) {

	if ( $orderby == 'post_author' ) {

		return $array['post']->post_author;

	}

	return $value;

}
add_filter('msa_audit_posts_table_sort_data', 'msa_audit_posts_table_sort_data_attribute', 10, 3);

/**
 * Create the attribute content
 *
 * @access public
 * @param mixed $column
 * @param mixed $name
 * @return void
 */
function msa_attribute_table_column($content, $item, $name) {

	if ( $name == 'post_author' ) {

		$author = get_userdata($item['post']->post_author);

		return $author->display_name;
	}

	return $content;

}
add_filter('msa_all_posts_table_column_data', 'msa_attribute_table_column', 10, 3);

/**
 * Author Attribute options for the filters
 *
 * @access public
 * @param mixed $content
 * @return void
 */
function msa_filter_attribute_author_options($content) {

	if ( isset($_GET['audit']) ) {

		// Get all authors within an audit

		$audit_posts_model = new MSA_Audit_Posts_Model();
		$authors = $audit_posts_model->get_authors_in_audit($_GET['audit']);
		$content = array();

		foreach ( $authors as $author ) {

			$author_data = get_userdata($author['post_author']);

			$content[] = array(
				'name'	=> $author_data->display_name,
				'value'	=> $author['post_author'],
			);
		}

	}

	return $content;

}
add_filter('msa_filter_attribute_author', 'msa_filter_attribute_author_options', 10, 1);

/**
 * Filter all the posts shown by the author
 *
 * @access public
 * @param mixed $name
 * @param mixed $value
 * @return void
 */
function msa_filter_by_attribute_author($items, $name, $value) {

	// Filter by author

	if ( $name == 'author' && $value != '' ) {

		foreach ( $items as $key => $item ) {

			if ( $item['post']->post_author != $value ) {
				unset($items[$key]);
			}
		}
	}

	return $items;

}
add_filter('msa_filter_by_attribute', 'msa_filter_by_attribute_author', 10, 3);

/**
 * Create all inital attributes
 *
 * @access public
 * @return void
 */
function msa_create_initial_attributes() {

	// Post Author

	msa_register_attribute('post_author', array(
		'name' 			=> __('Author', 'msa'),
		'post_data'		=> true,
		'filter'		=> array(
			'label'		=> __('Authors', 'msa'),
			'name'		=> 'author',
			'options'	=> apply_filters('msa_filter_attribute_author', ''),
		)
	));

	do_action('msa_register_attributes');
}


/**
 * Get all the attributes
 *
 * @access public
 * @return void
 */
function msa_get_attributes() {

	global $msa_attributes;

	if ( ! is_array( $msa_attributes ) ) {
		$msa_attributes = array();
	}

	return apply_filters('msa_get_attributes', $msa_attributes);
}

/**
 * Register a new attribute
 *
 * @access public
 * @param mixed $attribute
 * @param array $args (default: array())
 * @return void
 */
function msa_register_attribute( $attribute, $args = array() ) {

	global $msa_attributes;

	if ( ! is_array( $msa_attributes ) ) {
		$msa_attributes = array();
	}

	// Default attribute

	$default = array(
		'name'			=> __('Attribute', 'msa'),
		'value'        	=> 0,
	);

	$args = array_merge($default, $args);

	// Add the attribute to the global attributes array

	$msa_attributes[ $attribute ] = apply_filters('msa_register_attribute_args', $args);

	/**
	* Fires after a attribute is registered.
	*
	* @param string $attribute Attribute.
	* @param array $args      Arguments used to register the attribute.
	*/
	do_action('msa_registed_attribute', $attribute, $args);

	return $args;
}