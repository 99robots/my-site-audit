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
	$data = $audit_post['data']['values'];
	$score = $audit_post['data']['score']; ?>

	<h1><?php _e('Post Audit Detials', 'msa'); ?>
		<a href="<?php echo get_admin_url() . 'admin.php?page=msa-all-audits&audit=' . $_GET['audit']; ?>" class="page-title-action"><?php _e('All Posts', 'msa'); ?></a>
	</h1>

	<div class="msa-header msa-single-post">

		<div class="msa-column msa-header-column msa-header-score-wrap">
			<div class="msa-header-score-container">
				<div class="msa-header-post-score msa-post-status-bg msa-post-status-bg-<?php echo msa_get_score_status($score['score']); ?>">
					<span><?php echo round($score['score'] * 100) . '%'; ?></span>
				</div>
			</div>
		</div>

		<div class="msa-column msa-header-column msa-detail-container">
			<h3><?php echo $post->post_title; ?></h3>

			<table>
				<tbody>
					<tr>
						<td class="msa-header-audit-attribute"><?php _e('Analysis Date:', 'msa'); ?></td>
						<td><?php echo date('m/d/Y', strtotime($audit['date'])); ?></td>
					</tr>
				</tbody>
			</table>

		</div>

		<div class="msa-column msa-header-column msa-action-container">

		</div>

	</div>

	<div class="msa-column msa-right-column">

		<div class="msa-column-container">

			<div class="msa-right-column-container metabox-holder">

				<div class="postbox" id="general">
					<h3 class="hndle ui-sortable-handle"><?php _e('General Data', 'msa'); ?>
						<a class="button" href="<?php echo get_edit_post_link($post->ID); ?>" target="_blank"><?php _e('Edit Post', 'msa'); ?></a>
						<a class="button" href="<?php echo get_permalink($post->ID); ?>" target="_blank"><?php _e('View Post', 'msa'); ?></a>
					</h3>
					<div class="inside">
						<table class="wp-list-table widefat striped posts msa-audit-table">
							<tbody>
								<tr>
									<td><?php _e('Title', 'msa'); ?></td>
									<td><?php echo $post->post_title; ?></td>
								</tr>
								<tr>
									<td><?php _e('Slug', 'msa'); ?></td>
									<td>/<?php echo $post->post_name; ?></td>
								</tr>
								<tr>
									<td><?php _e('ID', 'msa'); ?></td>
									<td><?php echo $post->ID; ?></td>
								</tr>
								<tr>
									<td><?php _e('Author', 'msa'); ?></td>
									<td><?php $user = get_userdata($post->post_author); echo $user->display_name; ?></td>
								</tr>
								<tr>
									<td><?php _e('Published Date', 'msa'); ?></td>
									<td><?php echo date('M j, Y', strtotime($post->post_date)); ?></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>

				<div class="postbox">
					<h3 class="hndle"><?php _e('Google Search Preview', 'msa'); ?></h3>
					<div class="inside">
						<div class="msa-google-preview">
							<a class="msa-google-preview-title" href="#"><?php echo $post->post_title; ?></a>
							<span class="msa-google-preview-url"><?php echo get_permalink($post->ID); ?></span>
							<p class="msa-google-preview-description">
								<span class="msa-google-preview-date"><?php echo date('M j, Y', strtotime($post->post_date)); ?> - </span>
								<span class="msa-google-preview-content"><?php echo strip_shortcodes(strip_tags( msa_get_post_excerpt($post))); ?></span>
							</p>
						</div>
					</div>
				</div>

				<?php echo do_action('msa_single_post_sidebar'); ?>

			</div>

		</div>

	</div>

	<div class="msa-column msa-left-column metabox-holder">

		<div class="msa-column-container">

			<?php $condition_categories = msa_get_condition_categories();
			foreach ( $condition_categories as $key => $condition_category ) { ?>

				<div class="postbox" id="<?php echo $key; ?>">
					<?php echo apply_filters('msa_condition_category_content', $key, $post, $data, $score ); ?>
				</div>

			<?php } ?>

		</div>

	</div>

<?php } else if ( isset($_GET['audit']) ) {

	// Get the Audit

	$audit_model = new MSA_Audits_Model();
	$audit = $audit_model->get_data_from_id($_GET['audit']);
	$form_fields = json_decode($audit['args']['form_fields'], true);

	// Get the posts for an audit

	$audit_posts_model = new MSA_Audit_Posts_Model();
	$posts = $audit_posts_model->get_data($_GET['audit']);

	// Get all the current filters

	$current_filters = '';

	$conditions = msa_get_conditions();
	$attributes = msa_get_attributes();

	foreach ( $conditions as $key => $condition ) {

		if ( isset($condition['filter']['name']) && isset($_GET[$condition['filter']['name']]) ) {
			$current_filters .= '&' . $condition['filter']['name'] . '=' . $_GET[$condition['filter']['name']];
		}
	}

	foreach ( $attributes as $key => $attribute ) {

		if ( isset($attribute['filter']['name']) && isset($_GET[$attribute['filter']['name']]) ) {
			$current_filters .= '&' . $attribute['filter']['name'] . '=' . $_GET[$attribute['filter']['name']];
		}
	}

	$post_type_labels = array();

	foreach( $audit['args']['post_types'] as $post_type ) {

		$labels = get_post_type_labels(get_post_type_object($post_type));

		$post_type_labels[] = $labels->name;

	} ?>

	<h1><?php _e('Audit Details', 'msa'); ?>
		<a href="<?php echo get_admin_url() . 'admin.php?page=msa-all-audits'; ?>" class="page-title-action"><?php _e('All Audits', 'msa'); ?></a>
	</h1>

	<div class="msa-header msa-single-audit">

		<div class="msa-column msa-header-column msa-header-score-wrap">
			<div class="msa-header-score-container">
				<div class="msa-header-post-score msa-post-status-bg msa-post-status-bg-<?php echo msa_get_score_status($audit['score']); ?>">
					<span><?php echo round($audit['score'] * 100) . '%'; ?></span>
				</div>
			</div>
		</div>

		<div class="msa-column msa-header-column msa-detail-container">

			<h3><?php echo esc_attr($audit['name']); ?></h3>

			<table>
				<tbody>
					<tr>
						<td class="msa-header-audit-attribute"><?php _e('Analysis Date:', 'msa'); ?></td>
						<td><?php echo date('m/d/Y', strtotime($audit['date'])); ?></td>
					</tr>
					<tr>
						<td class="msa-header-audit-attribute"><?php _e('Post Date Range:', 'msa'); ?></td>
						<td><?php echo date('m/d/Y', strtotime($form_fields['after-date'])) . ' - ' . date('m/d/Y', strtotime($form_fields['before-date'])); ?></td>
					</tr>
					<tr>
						<td class="msa-header-audit-attribute"><?php _e('Contains:', 'msa'); ?></td>
						<td><?php echo $audit['num_posts'] . ' ' . implode(', ', $post_type_labels); ?></td>
					</tr>
				</tbody>
			</table>

		</div>

		<div class="msa-column msa-header-column msa-action-container">
		</div>

	</div>

	<ul class="subsubsub">

		<li class="all">
			<a href="<?php echo get_admin_url() . 'admin.php?page=msa-all-audits&audit=' . $_GET['audit']; ?>" class="current"><?php _e('All', 'msa'); ?> <span class="count">(<?php echo count($posts); ?>)</span></a> |
		</li>

		<?php $i = 0; foreach ( msa_get_score_statuses() as $key => $score_status ) {

			$separator = ' |';

			if ( $i == count(msa_get_score_statuses()) - 1 ) {
				$separator = '';
			}

			$i++; ?>

			<li class="<?php echo $key; ?>">
				<a class="msa-post-status-filter" href="<?php echo get_admin_url() . 'admin.php?page=msa-all-audits&audit=' . $_GET['audit']; ?>&score-low=<?php echo $score_status['low']; ?>&score-high=<?php echo $score_status['high']; echo $current_filters; ?>"><?php echo $score_status['name']; ?>
					<span class="count">(<?php echo msa_get_post_count_by_status($posts, $key); ?>)</span>
					<span class="msa-tooltips">
						<i class="fa fa-info-circle"></i>
						<span><?php echo __('Scores between ', 'msa') . round($score_status['low'] * 100 ) . __('% and ', 'msa') . round($score_status['high'] * 100) . __('%', 'msa'); ?></span>
					</span>
				</a>
				<?php echo $separator; ?>

			</li>

		<?php } ?>

	</ul>

	<form method="post" class="msa-all-posts-form">
		<input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>" />
		<input type="hidden" name="audit" value="<?php echo $_REQUEST['audit']; ?>" />
		<?php
		$all_posts_table = new MSA_All_Posts_Table();
		$all_posts_table->prepare_items();
		$all_posts_table->search_box('Search Posts', 'msa');
		$all_posts_table->display(); ?>
	</form>

<?php } else {

	$audit_model = new MSA_Audits_Model();

    ?>

	<h1><?php _e('All Audits', 'msa'); ?>
		<a href="#" class="page-title-action msa-add-new-audit" <?php if ( false !== ( $in_progress = get_transient('msa_running_audit') ) ) { ?> onclick="alert('<?php _e('Cannot create a new audit while another audit is in progress.'); ?>');" <?php } ?>><?php _e('Add New', 'msa'); ?></a>
	</h1>

	<div class="msa-create-audit-wrap">

		<?php if ( false === ( $in_progress = get_transient('msa_running_audit') ) ) { ?>

			<form method="post" class="msa-create-audit-form">

				<table class="form-table">
					<tbody>

						<?php do_action('msa_all_audits_before_create_new_settings'); ?>

						<!-- Audit Name -->

						<tr>
							<th scope="row"><label for="msa-audit-name"><?php _e("Audit Name", 'msa'); ?></label></th>
							<td>
								<input id="msa-audit-name" name="name" value="<?php _e('My Audit', 'msa'); ?>" />
							</td>
						</tr>

						<!-- Post Range -->

						<tr>
							<th scope="row"><label for="msa-audit-date-range"><?php _e("Post Date Range", 'msa'); ?></label></th>
							<td>
								<input id="msa-audit-date-range" name="date-range" class="msa-datepicker" data-start-date="<?php echo date("m/d/Y", strtotime("-1 years")); ?>" data-end-date="<?php echo date("m/d/Y", strtotime("today")); ?>"/>
								<p class="description"><?php _e('Perform the audit on posts published between these dates.', 'msa'); ?></p>
							</td>
						</tr>

						<!-- Before Date -->

	<!--
						<tr>
							<th scope="row"><label for="msa-audit-before-date"><?php _e("Before Date", 'msa'); ?></label></th>
							<td>
								<input id="msa-audit-before-date" name="before-date" class="msa-datepicker" value="<?php echo date("m/d/Y", strtotime("today")); ?>"/>
								<p class="description"><?php _e('Perform the audit on posts published before this date.', 'msa'); ?></p>
							</td>
						</tr>
	-->

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
									<option value="-1" selected="selected"><?php _e('All Posts in Date Range', 'msa'); ?></option>
									<?php for ($i = 50; $i <= 3000; $i+= 50) { ?>
										<option value="<?php echo $i; ?>"><?php _e($i, 'msa'); ?></option>
									<?php } ?>
								</select>
								<p class="description"><?php _e('The maximum number of posts that will be audited.', 'msa'); ?></p>
							</td>
						</tr>

						<?php do_action('msa_all_audits_after_create_new_settings'); ?>

					</tbody>
				</table>

				<?php submit_button(__('Create Audit', 'msa')); ?>

				<?php wp_nonce_field('msa-add-audit'); ?>

			</form>

		<?php } ?>
	</div>

	<ul class="subsubsub">

		<li class="all">
			<a href="<?php echo get_admin_url() . 'admin.php?page=msa-all-audits'; ?>" class="<?php echo !isset($_GET['audit_status']) || ( isset($_GET['audit_status']) && $_GET['audit_status'] == 'all' ) ? 'current' : ''; ?>"><?php _e('All', 'msa'); ?> <span class="count">(<?php echo count($audit_model->get_data()); ?>)</span></a> |
		</li>

		<li class="completed">
			<a href="<?php echo get_admin_url() . 'admin.php?page=msa-all-audits&audit_status=completed'; ?>" class="<?php echo isset($_GET['audit_status']) && $_GET['audit_status'] == 'completed' ? 'current' : ''; ?>"><?php _e('Completed', 'msa'); ?> <span class="count">(<?php echo count($audit_model->get_data(array('status' => 'completed'))); ?>)</span></a> |
		</li>

		<li class="in-progress">
			<a href="<?php echo get_admin_url() . 'admin.php?page=msa-all-audits&audit_status=in-progress'; ?>" class="<?php echo isset($_GET['audit_status']) && $_GET['audit_status'] == 'in-progress' ? 'current' : ''; ?>"><?php _e('In Progress', 'msa'); ?> <span class="count">(<?php echo count($audit_model->get_data(array('status' => 'in-progress'))); ?>)</span></a>
		</li>

	</ul>

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