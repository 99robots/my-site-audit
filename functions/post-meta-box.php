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
		__('My Site Audit: Post Details', 'msa' ),
		'msa_meta_box_callback'
	);
}
add_action('add_meta_boxes', 'msa_add_meta_box');

/**
 * Prints the box content.
 *
 * @access public
 * @param mixed $post
 * @return void
 */
function msa_meta_box_callback( $post ) {

	// Get the latest audit

	$audit_model = new MSA_Audits_Model();
	$audit = $audit_model->get_latest();

	// Check to see if we have an audit

	$output = '';

	if ( isset($audit) ) {

		$audit = $audit_model->get_data_from_id($audit['id']);
		$audit_posts_model 	= new MSA_Audit_Posts_Model();
		$audit_post = $audit_posts_model->get_data_from_id($audit['id'], $_GET['post']);
		$post = (object) $audit_post['post'];
		$data = $audit_post['data'];

		$score = msa_calculate_score($post, $audit_post['data']);

		$condition_categories = msa_get_condition_categories();
		foreach ( $condition_categories as $key => $condition_category ) {
			?><div class="postbox" id="<?php echo $key; ?>">
				<?php echo apply_filters('msa_condition_category_content', $key, $post, $data ); ?>
			</div><?php
		}

	}

	wp_enqueue_style('msa-all-audits-css', 			MY_SITE_AUDIT_PLUGIN_URL . '/css/all-audits.css');
	wp_enqueue_style('msa-common-css', 				MY_SITE_AUDIT_PLUGIN_URL . '/css/common.css');

	echo $output;

}