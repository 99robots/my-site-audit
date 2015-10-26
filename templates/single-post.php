<?php
/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 10/26/15
 * Package: Templates/Single Post
 * File: single-post.php
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

$post = get_post($_GET['post']); ?>

<h1><?php _e('Specific Post', 'msa'); ?>
	<a href="<?php echo get_admin_url() . 'admin.php?page=msa-dashboard'; ?>" class="page-title-action"><?php _e('All Posts', 'msa'); ?></a>
</h1>

<div class="msa-header msa-single-post">

	<div class="msa-column msa-header-column msa-header-score-wrap">
		<div class="msa-header-score-container">
			<span class="msa-header-score-description"><?php _e('Post Score', 'msa'); ?></span>
			<div class="msa-header-score">
				<?php
				$data = msa_get_post_audit_data($post);
				$score = msa_calculate_score($post, $data);
				echo msa_get_letter_grade($score['score']); ?>
			</div>
		</div>
	</div>

	<div class="msa-column msa-header-column msa-detail-container">
		<h1><?php _e('Content Analysis: Post Detail', 'msa'); ?></h1>
		<p><?php _e('Analysis Date ' . date('m/d/Y', time()), 'msa'); ?></p>
		<hr />
		<h1><?php echo $post->post_title; ?></h1>
		<a href="<?php echo get_permalink($post->ID); ?>" target="_blank"><?php echo get_permalink($post->ID); ?></a>
	</div>

	<div class="msa-column msa-header-column msa-action-container">
		Action
	</div>

</div>

<div class="msa-column msa-left-column">

	<div class="msa-column-container">

		<table class="wp-list-table widefat striped posts msa-audit-table">

			<thead>
				<th style="width: 33.333%;"><?php _e("Attribute", 'msa'); ?></th>
				<th><?php _e("Value", 'msa'); ?></th>
			</thead>

			<tbody>

				<?php msa_show_audit_data(get_post($_GET['post']), $settings); ?>

			</tbody>

		</table>

	</div>

</div>

<div class="msa-column msa-right-column">

	<div class="msa-column-container">

		<div class="msa-right-column-container">

			<div class="msa-right-column-item msa-content-details-container">
				<span class="msa-content-description"><?php _e('Content Description', 'msa'); ?></span>
				<div class="msa-content-details">
					<span class="msa-content-details-grade">
					<?php
					$data = msa_get_post_audit_data($post);
					$score = msa_calculate_score($post, $data);
					echo msa_get_letter_grade($score['score']); ?>
					</span>
					<button class="button button-primary msa-save-content-status"><?php _e('Save Content Status', 'msa'); ?></button>
				</div>
			</div>

			<div class="msa-right-column-item msa-google-preview-container">
				<div class="msa-google-preview">
					<a class="msa-google-preview-title" href="#"><?php echo $post->post_title; ?></a>
					<span class="msa-google-preview-url"><?php echo get_permalink($post->ID); ?></span>
					<p class="msa-google-preview-description">
						<span class="msa-google-preview-content"><?php echo substr( strip_shortcodes(strip_tags($post->post_content)), 0, 156); ?></span>
					</p>
				</div>
			</div>

			<div class="msa-right-column-item">
				<h3><?php _e('Add-on: Tasks', 'msa'); ?></h3>
			</div>

			<div class="msa-right-column-item">
				<h3><?php _e('Add-on: Content Attributes', 'msa'); ?></h3>
			</div>

		</div>

	</div>

</div>