<?php
/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 10/26/15
 * Package: Views/All Audits
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

require_once('header.php');

if ( isset($_GET['post']) && isset($_GET['audit']) ) {

	$audit_model = new MSA_Audits_Model();
	$audit = $audit_model->get_data_from_id($_GET['audit']);

	$audit_posts_model 	= new MSA_Audit_Posts_Model();
	$audit_post = $audit_posts_model->get_data_from_id($_GET['audit'], $_GET['post']);
	$post = (object) $audit_post['post'];
	$data = $audit_post['data'];
	$score = msa_calculate_score($post, $audit_post['data']); ?>

	<h1><?php _e('Single Post', 'msa'); ?>
		<a href="<?php echo get_admin_url() . 'admin.php?page=msa-all-audits&audit=' . $_GET['audit']; ?>" class="page-title-action"><?php _e('All Posts', 'msa'); ?></a>
	</h1>

	<div class="msa-header msa-single-post">

		<div class="msa-column msa-header-column msa-header-score-wrap">
			<div class="msa-header-score-container">
				<span class="msa-header-score-description"><?php _e('Post Score', 'msa'); ?></span>
				<div class="msa-header-score msa-post-status-text-<?php echo msa_get_score_status($score['score']); ?>">
					<?php echo msa_get_letter_grade($score['score']); ?>
				</div>
			</div>
		</div>

		<div class="msa-column msa-header-column msa-detail-container">
			<h1><?php _e('Content Analysis: Post Detail', 'msa'); ?></h1>
			<p><?php _e('Analysis Date' . date('m/d/Y', strtotime($audit['date'])), 'msa'); ?></p>
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

					<tr class="msa-post-status-bg msa-post-status-bg-<?php echo msa_get_score_status($score['score']); ?>">
						<td><?php _e('Score', 'msa'); ?></td>
						<td><?php echo 100 * $score['score']; ?>%</td>
					</tr>

					<tr>
						<td><?php _e('Published Date', 'msa'); ?></td>
						<td><?php echo date('M j, Y', strtotime($post->post_date)); ?></td>
					</tr>

					<tr>
						<td><?php _e('ID', 'msa'); ?></td>
						<td><?php echo $post->ID; ?></td>
					</tr>

					<tr>
						<td><?php _e('Slug', 'msa'); ?></td>
						<td><a href="<?php echo get_permalink($post->ID); ?>" target="_blank">/<?php echo $post->post_name; ?></a></td>
					</tr>

					<tr>
						<td><?php _e('Title', 'msa'); ?></td>
						<td><?php echo $post->post_title; ?></td>
					</tr>

					<tr class="msa-post-status msa-post-status-<?php echo msa_get_score_status($score['data']['title']); ?>">
						<td><?php _e('Title Length', 'msa'); ?></td>
						<td><?php echo strlen($post->post_title); ?></td>
					</tr>

					<tr class="msa-post-status msa-post-status-<?php echo msa_get_score_status($score['data']['modified_date']); ?>">
						<td><?php _e('Modified Date', 'msa'); ?></td>
						<td><?php echo date('M j, Y', strtotime($post->post_modified)); ?></td>
					</tr>

					<tr class="msa-post-status msa-post-status-<?php echo msa_get_score_status($score['data']['word_count']); ?>">
						<td><?php _e('Word Count', 'msa'); ?></td>
						<td><?php echo $data['word_count']; ?></td>
					</tr>

					<tr class="msa-post-status msa-post-status-<?php echo msa_get_score_status($score['data']['comment_count']); ?>">
						<td><?php _e('Comment Count', 'msa'); ?></td>
						<td><?php echo $data['comment_count']; ?></td>
					</tr>

	<!--
					if ( file_exists(WP_PLUGIN_DIR . '/wordpress-seo/inc/class-wpseo-utils.php') && is_plugin_active('wordpress-seo/wp-seo.php') ) {

						<tr>
							<td><?php _e('SEO Score', 'msa'); ?></td>
							<td><div class="wpseo-score-icon <?php echo esc_attr( $data['yoast-seo-score-label'] ); ?>"></div></td>
						</tr>

						<tr>
							<td><?php _e('Focus Keyword', 'msa'); ?></td>
							<td><?php echo $data['yoast-seo-focuskw']; ?></td>
						</tr>

						<tr>
							<td><?php _e('Meta Description', 'msa'); ?></td>
							<td><?php echo $data['yoast-seo-meta-desc']; ?></td>
						</tr>

						<tr>
							<td><?php _e('Meta Description Length', 'msa'); ?></td>
							<td><?php echo strlen($data['yoast-seo-meta-desc']); ?></td>
						</tr>

					}
	-->

					<tr class="msa-post-status msa-post-status-<?php echo msa_get_score_status($score['data']['internal_links']); ?>">
						<td><?php _e('Internal Links', 'msa'); ?></td>
						<td>
							<?php echo msa_show_internal_links($data['link_matches']); ?>
						</td>
					</tr>

					<tr class="msa-post-status msa-post-status-<?php echo msa_get_score_status($score['data']['external_links']); ?>">
						<td><?php _e('External Links', 'msa'); ?></td>
						<td>
							<?php echo msa_show_external_links($data['link_matches']); ?>
						</td>
					</tr>

	<!--
					if ( isset($settings['use_shared_count']) && $settings['use_shared_count'] ) {
						<tr>
							<td><?php _e('Share Count', 'msa'); ?></td>
							<td class="msa-share-count" data-post="<?php echo $post->ID; ?>"><i class="fa fa-refresh fa-spin"></i></td>
						</tr>
					}
	-->

					<tr class="msa-post-status msa-post-status-<?php echo msa_get_score_status($score['data']['images']); ?>">
						<td><?php _e('Image Count', 'msa'); ?></td>
						<td><?php echo $data['images']; ?></td>
					</tr>

					<tr class="msa-post-status msa-post-status-<?php echo msa_get_score_status($score['data']['headings']); ?>">
						<td><?php _e('Heading Count', 'msa'); ?></td>
						<td><?php echo $data['headings']; ?></td>
					</tr>

					<tr>
						<td><?php _e('Heading 1 Count', 'msa'); ?></td>
						<td><?php echo $data['h1']; ?></td>
					</tr>

					<tr>
						<td><?php _e('Heading 2 Count', 'msa'); ?></td>
						<td><?php echo $data['h2']; ?></td>
					</tr>

					<tr>
						<td><?php _e('Heading 3 Count', 'msa'); ?></td>
						<td><?php echo $data['h3']; ?></td>
					</tr>

					<tr>
						<td><?php _e('Heading 4 Count', 'msa'); ?></td>
						<td><?php echo $data['h4']; ?></td>
					</tr>

					<tr>
						<td><?php _e('Heading 5 Count', 'msa'); ?></td>
						<td><?php echo $data['h5']; ?></td>
					</tr>

					<tr>
						<td><?php _e('Heading 6 Count', 'msa'); ?></td>
						<td><?php echo $data['h6']; ?></td>
					</tr>

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

<?php } else if ( isset($_GET['audit']) ) {

	$audit_model = new MSA_Audits_Model();
	$audit = $audit_model->get_data_from_id($_GET['audit']); ?>

	<h1><?php _e('Single Audit', 'msa'); ?>
		<a href="<?php echo get_admin_url() . 'admin.php?page=msa-all-audits'; ?>" class="page-title-action"><?php _e('All Audits', 'msa'); ?></a>
	</h1>

	<div class="msa-header msa-single-audit">

		<div class="msa-column msa-header-column msa-header-score-wrap">
			<div class="msa-header-score-container">
				<span class="msa-header-score-description"><?php _e('Audit Score', 'msa'); ?></span>
				<div class="msa-header-score msa-post-status-text-<?php echo msa_get_score_status($audit['score']); ?>">
					<?php echo msa_get_letter_grade($audit['score']); ?>
				</div>
			</div>
		</div>

		<div class="msa-column msa-header-column msa-detail-container">
			<h1><?php _e('Content Analysis: ' . $audit['name'], 'msa'); ?></h1>
			<p><?php _e('Analysis Date' . date('m/d/Y', strtotime($audit['date'])), 'msa'); ?></p>
		</div>

		<div class="msa-column msa-header-column msa-action-container">
		</div>

	</div>

	<form method="post" class="msa-all-posts-form">
		<input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>" />
		<input type="hidden" name="audit" value="<?php echo $_REQUEST['audit']; ?>" />
		<?php
		$all_posts_table = new MSA_All_Posts_Table();
		$all_posts_table->prepare_items();
		$all_posts_table->search_box('Search Posts', 'msa');
		$all_posts_table->display(); ?>
	</form>

<?php } else { ?>

	<h1><?php _e('All Audits', 'msa'); ?>
		<a href="#" class="page-title-action msa-add-new-audit"><?php _e('Add New', 'msa'); ?></a>
	</h1>

	<div class="msa-create-audit-wrap">
		<form method="post">

			<table class="form-table">
				<tbody>

					<!-- Audit Name -->

					<tr>
						<th scope="row"><label for="msa-audit-name"><?php _e("Audit Name", 'msa'); ?></label></th>
						<td>
							<input id="msa-audit-name" name="name" value="<?php _e('My Audit', 'msa'); ?>" />
						</td>
					</tr>

					<!-- After Date -->

					<tr>
						<th scope="row"><label for="msa-audit-after-date"><?php _e("After Date", 'msa'); ?></label></th>
						<td>
							<input id="msa-audit-after-date" name="after-date" class="msa-datepicker" value="<?php echo date("m/d/Y", strtotime("first day of previous month")); ?>" />
							<p class="description"><?php _e('Perform the audit on posts published after this date.', 'msa'); ?></p>
						</td>
					</tr>

					<!-- Before Date -->

					<tr>
						<th scope="row"><label for="msa-audit-before-date"><?php _e("Before Date", 'msa'); ?></label></th>
						<td>
							<input id="msa-audit-before-date" name="before-date" class="msa-datepicker" value="<?php echo date("m/d/Y", strtotime("last day of previous month")); ?>"/>
							<p class="description"><?php _e('Perform the audit on posts published before this date.', 'msa'); ?></p>
						</td>
					</tr>

					<!-- Post Types -->

					<tr>
						<th scope="row"><label for="msa-audit-post-types"><?php _e("Post Types", 'msa'); ?></label></th>
						<td>
							<select id="msa-audit-post-types" name="post-types[]" multiple>
								<?php foreach( get_post_types() as $post_type) { ?>
									<option value="<?php echo $post_type; ?>" <?php selected($post_type, 'post', true); ?>><?php echo $post_type; ?></option>
								<?php } ?>
							</select>
							<p class="description"><?php _e('Perform the audit on posts of these post types.', 'msa'); ?></p>
						</td>
					</tr>

					<!-- Maximum Posts -->

					<tr>
						<th scope="row"><label for="msa-audit-max-posts"><?php _e("Maximum Posts", 'msa'); ?></label></th>
						<td>
							<select id="msa-audit-max-posts" name="max-posts">
								<?php for ($i = 10; $i <= 250; $i+= 10) { ?>
									<option value="<?php echo $i; ?>" <?php selected($i, 50, true); ?>><?php _e($i, 'msa'); ?></option>
								<?php } ?>
							</select>
							<p class="description"><?php _e('The maximum number of posts that will be audited.', 'msa'); ?></p>
						</td>
					</tr>

				</tbody>
			</table>

			<?php submit_button(__('Create Audit', 'msa')); ?>

			<?php wp_nonce_field('msa-add-audit'); ?>

		</form>
	</div>

	<script>
	jQuery(document).ready(function($){
		$(".msa-datepicker").datepicker();

		$('.msa-add-new-audit').click(function(){

			if ( $('.msa-create-audit-wrap').css('display') != 'none' ) {
				$('.msa-create-audit-wrap').slideUp();
			} else {
				$('.msa-create-audit-wrap').slideDown();
			}
		});
	});
	</script>

	<form method="post">
		<input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>" />
		<?php
		$all_audits_table = new MSA_All_Audits_Table();
		$all_audits_table->prepare_items();
		$all_audits_table->search_box('Search Audits', 'msa');
		$all_audits_table->display(); ?>
	</form>

<?php }

require_once('footer.php');