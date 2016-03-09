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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add a meta box to the post screen
 *
 * @access public
 * @return void
 */
function msa_add_meta_box() {

	add_meta_box(
		'msa-meta-box',
		__( 'My Site Audit', 'msa' ),
		'msa_meta_box_callback'
	);
}
add_action( 'add_meta_boxes', 'msa_add_meta_box' );

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

	if ( isset( $audit ) ) {

		$post_id = -1;
		if ( isset( $_GET['post'] ) ) {  // Input var okay.
			$post_id = sanitize_text_field( wp_unslash( $_GET['post'] ) ); // Input var okay.
		}

		$audit = $audit_model->get_data_from_id( $audit['id'] );
		$audit_posts_model 	= new MSA_Audit_Posts_Model();
		$audit_post = $audit_posts_model->get_data_from_id( $audit['id'], $post_id );

		if ( $audit_post ) {

			$post = (object) $audit_post['post'];
			$data = $audit_post['data']['values'];
			$score = $audit_post['data']['score'];

			$condition_categories = msa_get_condition_categories();

			$user = get_userdata( $audit['user'] );

			do_action( 'msa_before_post_meta_box', $audit['id'], $post_id );

			?><div class="msa-post-meta-container msa-post-meta-audit-meta-attributes">
				<p class="msa-post-meta-attribute"><?php esc_attr_e( 'Score: ', 'msa' ); ?></p>
				<p class="msa-post-meta-attribute"><?php esc_attr_e( 'From Audit: ', 'msa' ); ?></p>
				<p class="msa-post-meta-attribute"><?php esc_attr_e( 'Created On: ', 'msa' ); ?></p>
				<p class="msa-post-meta-attribute"><?php esc_attr_e( 'Created By: ', 'msa' ); ?></p>
			</div>

			<div class="msa-post-meta-container msa-post-meta-audit-meta-values">
				<p class="msa-post-meta-value msa-post-status-bg msa-post-status-bg-<?php ecs_attr_e( msa_get_score_status( $score['score'] ) ); ?>"><?php ecs_attr_e( round( $score['score'] * 100, 2 ) ); ?>%</p>
				<p class="msa-post-meta-value"><a href="<?php ecs_attr_e( get_admin_url() . 'admin.php?page=msa-all-audits&audit=' . $audit['id'] ); ?>" target="_blank"><?php ecs_attr_e( $audit['name'] ); ?></a></p>
				<p class="msa-post-meta-value"><?php ecs_attr_e( date( 'M j, Y', strtotime( $audit['date'] ) ) ); ?></p>
				<p class="msa-post-meta-value"><?php ecs_attr_e( $user->display_name ); ?></p>
			</div><?php

			foreach ( $condition_categories as $key => $condition_category ) {
				?><div class="postbox" id="<?php ecs_attr_e( $key ); ?>" style="pointer-events: none;">
					<?php ecs_attr_e( apply_filters( 'msa_condition_category_content', $key, $post, $data, $score ) ); ?>
				</div><?php
			}

			do_action( 'msa_after_post_meta_box', $audit['id'], $post_id );
		}
	}

	wp_enqueue_style( 'msa-all-audits-css',	MY_SITE_AUDIT_PLUGIN_URL . '/css/all-audits.css' );
	wp_enqueue_style( 'msa-post-meta-css',	MY_SITE_AUDIT_PLUGIN_URL . '/css/post-meta.css' );
	wp_enqueue_style( 'msa-common-css',		MY_SITE_AUDIT_PLUGIN_URL . '/css/common.css' );
}
