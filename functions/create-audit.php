<?php
/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 10/30/15
 * Package: Functions/Create Audit
 * File: create-audit.php
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
 * Create an Audit
 *
 * @access public
 * @param mixed $audit
 * @return void
 */
function msa_async_create_audit($audit_data) {

	// Create our Audit and Audit Post Model objects

	$audit_model = new MSA_Audits_Model();
	$audit_posts_model = new MSA_Audit_Posts_Model();

	// Get all the data from the user

	$audit = array();

	$audit['name'] = $audit_data['name'];
	$audit['score'] = 0;
	$audit['date'] = date('Y-m-d H:i:s');
	$audit['user'] = get_current_user_id();
	$audit['args']['conditions'] = json_encode(msa_get_conditions());

	$audit['args']['before_date'] = $audit_data['after-date'];
	$audit['args']['before_date'] = $audit_data['before-date'];
	$audit['args']['post_types'] = $audit_data['post-types'];
	$audit['args']['max_posts'] = $audit_data['max-posts'];

	// Get all the posts that we are going to perform an audit on

	$args = array(
		'public' 			=> true,
		'date_query' 		=> array(
			array(
				'after'     => $audit_data['after-date'],
				'before'    => $audit_data['before-date'],
				'inclusive' => true,
			),
		),
		'post_type'			=> $audit_data['post-types'],
		'posts_per_page'	=> $audit_data['max-posts'],
	);

	$posts = get_posts($args);

	$audit['num_posts'] = count($posts);

	error_log('creating audit');

	// Only perform the audit if there are posts to perform the audit on

	if ( count($posts) > 0 ) {

		$audit_id = $audit_model->add_data($audit);

		if ( $audit_id ) {

			$audit_score = 0;

			foreach ( $posts as $post ) {

				error_log('created post');

				$data = msa_get_post_audit_data($post);
				$score = msa_calculate_score($post, $data);
				$data['score'] = $score['score'];

				// Add a new record in the audit posts table

				$audit_posts_model->add_data(array(
					'audit_id' 	=> $audit_id,
					'post'		=> $post,
					'data'		=> $data,
				));

				$audit_score += $score['score'];
			}

			$audit_score = round($audit_score / count($posts), 10);
			$audit['score'] = round($audit_score, 10);
			$audit_model->update_data($audit_id, $audit);
		}
	}
}
//add_action( 'msa_async_create_audit_event', 'msa_async_create_audit', 10, 1 );

/**
 * Create a new post for an audit
 *
 * @access public
 * @return void
 */
function msa_add_post_to_audit() {

	if ( !isset($_POST['audit_id']) || !isset($_POST['post_id']) ) {
		echo '';
		die();
	}

	$audit_posts_model = new MSA_Audit_Posts_Model();
	$post = get_post($_POST['post_id']);

	$data = msa_get_post_audit_data($post);

	$score = msa_calculate_score($post, $data);
	$data['score'] = $score['score'];

	// Add a new record in the audit posts table

	$audit_posts_model->add_data(array(
		'audit_id' 	=> $_POST['audit_id'],
		'post'		=> $post,
		'data'		=> $data,
	));

	echo $score['score'];
	die();

}
add_action( 'wp_ajax_msa_add_post_to_audit', 'msa_add_post_to_audit');

/**
 * Update the audit score
 *
 * @access public
 * @return void
 */
function msa_ajax_update_audit_score() {

	if ( !isset($_POST['audit_id']) ) {
		echo '';
		die();
	}

	$audit_model = new MSA_Audits_Model();
	$audit = $audit_model->get_data_from_id($_POST['audit_id']);
	$audit['score'] = round($_POST['score'] / $_POST['num_posts'], 10);
	$audit_model->update_data($_POST['audit_id'], $audit);

	echo $audit['score'];
	die();

}
add_action( 'wp_ajax_msa_update_audit_score', 'msa_ajax_update_audit_score');

/**
 * Get the post IDs for the audit
 *
 * @access public
 * @return void
 */
function msa_get_post_ids() {

	$query_parameters = html_entity_decode($_POST['data']);
	parse_str($query_parameters, $data);

	// Create our Audit and Audit Post Model objects

	$audit_model = new MSA_Audits_Model();

	// Get all the data from the user

	$audit = array();

	$audit['name'] = $data['name'];
	$audit['score'] = 0;
	$audit['date'] = date('Y-m-d H:i:s');
	$audit['user'] = get_current_user_id();
	$audit['args']['post_types'] = $data['post-types'];
	$audit['args']['conditions'] = json_encode(msa_get_conditions());

	// Get all the posts that we are going to perform an audit on

	$args = array(
		'public' 			=> true,
		'date_query' 		=> array(
			array(
				'after'     => $data['after-date'],
				'before'    => $data['before-date'],
				'inclusive' => true,
			),
		),
		'post_type'			=> $data['post-types'],
		'posts_per_page'	=> $data['max-posts'],
		'fields'			=> 'ids',
	);

	$post_ids = get_posts($args);

	if ( count($post_ids) > 0 ) {
		$audit['num_posts'] = count($post_ids);
		$audit_id = $audit_model->add_data($audit);

		echo json_encode( array('post_ids' => $post_ids, 'audit_id' => $audit_id ) );
		die();
	}

	echo '';
	die();
}
add_action( 'wp_ajax_msa_get_post_ids_for_audit', 'msa_get_post_ids' );