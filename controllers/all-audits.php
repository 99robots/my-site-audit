<?php
/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 10/26/15
 * Package: Controllers/All Audits
 * File: all-audits.php
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

// Create a new Audit

if ( isset($_POST['submit']) && check_admin_referer('msa-add-audit') ) {

	// Create our Audit and Audit Post Model objects

	$audit_model = new MSA_Audits_Model();
	$audit_posts_model = new MSA_Audit_Posts_Model();

	// Get all the data from the user

	$audit = array();

	$audit['name'] = $_POST['name'];
	$audit['score'] = 0;
	$audit['date'] = date('Y-m-d H:i:s');
	$audit['user'] = get_current_user_id();
	$audit['args']['post_types'] = $_POST['post-types'];

	// Get all the posts that we are going to perform an audit on

	$args = array(
		'public' 			=> true,
		'date_query' 		=> array(
			array(
				'after'     => $_POST['after-date'],
				'before'    => $_POST['before-date'],
				'inclusive' => true,
			),
		),
		'post_type'			=> $_POST['post-types'],
		'posts_per_page'	=> $_POST['max-posts'],
	);

	$posts = get_posts($args);

	$audit['num_posts'] = count($posts);

	// Only perform the audit if there are posts to perform the audit on

	if ( count($posts) > 0 ) {

		$audit_id = $audit_model->add_data($audit);
		$audit_score = 0;

		foreach ( $posts as $post ) {

			$data = msa_get_post_audit_data($post);

			// Add a new record in the audit posts table

			$audit_posts_model->add_data(array(
				'audit_id' 	=> $audit_id,
				'post'		=> $post,
				'data'		=> $data,
			));

			$score = msa_calculate_score($post, $data);
			$audit_score += $score['score'];
		}

		$audit_score = round($audit_score / count($posts), 10);
		$audit['score'] = round($audit_score, 10);
		$audit_model->update_data($audit_id, $audit);

		?><script>
			window.location = "<?php echo get_admin_url() . 'admin.php?page=msa-all-audits&audit=' . $audit_id; ?>";
		</script><?php

	} else {
		?><script>
			window.location = "<?php echo get_admin_url() . 'admin.php?page=msa-all-audits'; ?>";
		</script><?php
	}
}

// Delete an audit

if ( isset($_GET['action']) && $_GET['action'] == 'delete' && check_admin_referer('msa-delete-audit')) {

	$audit_model = new MSA_Audits_Model();
	$audit_model->delete_data($_GET['audit']);

	?><script>
		window.location = "<?php echo get_admin_url() . 'admin.php?page=msa-all-audits'; ?>";
	</script><?php

}

include_once(MY_SITE_AUDIT_PLUGIN_DIR . 'views/all-audits.php');