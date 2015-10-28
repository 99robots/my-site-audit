<?php
/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 10/22/15
 * Package: Functions/Post Meta Box
 * File: post-meta-box.php
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
 * Add a meta box to the post screen
 *
 * @access public
 * @return void
 */
function msa_add_meta_box() {

	add_meta_box(
		'msa-meta-box',
		__('Content Audit', 'msa' ),
		'msa_meta_box_callback'
	);
}
//add_action('add_meta_boxes', 'msa_add_meta_box');

/**
 * Prints the box content.
 *
 * @access public
 * @param mixed $post
 * @return void
 */
function msa_meta_box_callback( $post ) {

	if ( false === ( $settings = get_option('msa_settings') ) ) {
		$settings = array();
	}

	$output = '<table class="wp-list-table widefat fixed striped posts">';
		$output .= '<thead>';
			$output .= '<th style="width:20%">' . __('Attribute', 'msa') . '</th>';
			$output .= '<th>' . __('Value', 'msa') . '</th>';
		$output .= '</thead>';
		$output .= '<tbody>';

		$output .= msa_show_audit_data($post, $settings, 'inline');

		$output .= '</tbody>';
	$output .= '</table>';

	echo $output;

}