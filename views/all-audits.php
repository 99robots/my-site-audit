<?php
/**
 * This file is responsible for showing the data on the All Audits Page.
 *
 * @package Views / All Audits
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once( 'header.php' );

if ( isset( $_GET['post'] ) && isset( $_GET['audit'] ) && check_admin_referer( 'msa-single-audit-post' ) ) { // Input var okay.

	$post_id = sanitize_text_field( wp_unslash( $_GET['post'] ) ); // Input var okay.
	$audit_id = sanitize_text_field( wp_unslash( $_GET['audit'] ) ); // Input var okay.

	$audit_model = new MSA_Audits_Model();
	$audit = $audit_model->get_data_from_id( $audit_id );

	$audit_posts_model 	= new MSA_Audit_Posts_Model();
	$audit_post = $audit_posts_model->get_data_from_id( $audit_id, $post_id );
	$post = (object) $audit_post['post'];
	$data = $audit_post['data']['values'];
	$score = $audit_post['data']['score']; ?>

	<h1><?php esc_attr_e( 'Post Audit Details', 'msa' ); ?>
		<a href="<?php esc_attr_e( msa_get_single_audit_link( $audit_id ) ); ?>" class="page-title-action"><?php esc_attr_e( 'All Posts', 'msa' ); ?></a>
	</h1>

	<div class="msa-header msa-single-post">

		<div class="msa-column msa-header-column msa-header-score-wrap">
			<div class="msa-header-score-container">
				<div class="msa-header-post-score msa-post-status-bg msa-post-status-bg-<?php esc_attr_e( msa_get_score_status( $score['score'] ) ); ?>">
					<span><?php esc_attr_e( round( $score['score'] * 100 ) . '%' ); ?></span>
				</div>
			</div>
		</div>

		<div class="msa-column msa-header-column msa-detail-container">
			<h3><?php esc_attr_e( $post->post_title ); ?></h3>

			<table>
				<tbody>
					<tr>
						<td class="msa-header-audit-attribute"><?php esc_attr_e( 'Analysis Date:', 'msa' ); ?></td>
						<td><?php esc_attr_e( date( 'm/d/Y', strtotime( $audit['date'] ) ) ); ?></td>
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
					<h3 class="hndle ui-sortable-handle"><?php esc_attr_e( 'General Data', 'msa' ); ?>
						<a class="button" href="<?php esc_attr_e( get_edit_post_link( $post->ID ) ); ?>" target="_blank"><?php esc_attr_e( 'Edit Post', 'msa' ); ?></a>
						<a class="button" href="<?php esc_attr_e( get_permalink( $post->ID ) ); ?>" target="_blank"><?php esc_attr_e( 'View Post', 'msa' ); ?></a>
					</h3>
					<div class="inside">
						<table class="wp-list-table widefat striped posts msa-audit-table">
							<tbody>
								<tr>
									<td><?php esc_attr_e( 'Title', 'msa' ); ?></td>
									<td><?php esc_attr_e( $post->post_title ); ?></td>
								</tr>
								<tr>
									<td><?php esc_attr_e( 'Slug', 'msa' ); ?></td>
									<td>/<?php esc_attr_e( $post->post_name ); ?></td>
								</tr>
								<tr>
									<td><?php esc_attr_e( 'ID', 'msa' ); ?></td>
									<td><?php esc_attr_e( $post->ID ); ?></td>
								</tr>
								<tr>
									<td><?php esc_attr_e( 'Author', 'msa' ); ?></td>
									<td><?php $user = get_userdata( $post->post_author );
									esc_attr_e( $user->display_name ); ?></td>
								</tr>
								<tr>
									<td><?php esc_attr_e( 'Published Date', 'msa' ); ?></td>
									<td><?php esc_attr_e( date( 'M j, Y', strtotime( $post->post_date ) ) ); ?></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>

				<div class="postbox">
					<h3 class="hndle"><?php esc_attr_e( 'Google Search Preview', 'msa' ); ?></h3>
					<div class="inside">
						<div class="msa-google-preview">
							<a class="msa-google-preview-title" href="#"><?php esc_attr_e( $post->post_title ); ?></a>
							<span class="msa-google-preview-url"><?php esc_attr_e( get_permalink( $post->ID ) ); ?></span>
							<p class="msa-google-preview-description">
								<span class="msa-google-preview-date"><?php esc_attr_e( date( 'M j, Y', strtotime( $post->post_date ) ) ); ?> - </span>
								<span class="msa-google-preview-content"><?php esc_attr_e( strip_shortcodes( strip_tags( msa_get_post_excerpt( $post ) ) ) ); ?></span>
							</p>
						</div>
					</div>
				</div>

				<?php do_action( 'msa_single_post_sidebar' ); ?>

			</div>

		</div>

	</div>

	<div class="msa-column msa-left-column metabox-holder">

		<div class="msa-column-container">

			<?php $condition_categories = msa_get_condition_categories();
			foreach ( $condition_categories as $key => $condition_category ) { ?>

				<div class="postbox" id="<?php esc_attr_e( $key ); ?>">
					<?php echo ( apply_filters( 'msa_condition_category_content' , $key, $post, $data, $score ) ); // WPCS: XSS ok. ?>
				</div>

			<?php } ?>

		</div>

	</div>

<?php } else if ( isset( $_GET['audit'] ) && check_admin_referer( 'msa-single-audit' ) ) { // Input var okay.

	$audit_id = sanitize_text_field( wp_unslash( $_GET['audit'] ) ); // Input var okay.

	// Get the Audit.
	$audit_model = new MSA_Audits_Model();
	$audit = $audit_model->get_data_from_id( $audit_id );
	$form_fields = json_decode( $audit['args']['form_fields'], true );

	// Get the posts for an audit.
	$audit_posts_model = new MSA_Audit_Posts_Model();
	$posts = $audit_posts_model->get_data( $audit_id );

	// Get all the current filters.
	$current_filters = '';

	$conditions = msa_get_conditions();
	$attributes = msa_get_attributes();

	foreach ( $conditions as $key => $condition ) {
		if ( isset( $condition['filter']['name'] ) && isset( $_GET[ $condition['filter']['name'] ] ) ) { // Input var okay.
			$current_filters .= '&' . $condition['filter']['name'] . '=' . sanitize_text_field( wp_unslash( $_GET[ $condition['filter']['name'] ] ) ); // Input var okay.
		}
	}

	foreach ( $attributes as $key => $attribute ) {
		if ( isset( $attribute['filter']['name'] ) && isset( $_GET[ $attribute['filter']['name'] ] ) ) { // Input var okay.
			$current_filters .= '&' . $attribute['filter']['name'] . '=' . sanitize_text_field( wp_unslash( $_GET[ $attribute['filter']['name'] ] ) ); // Input var okay.
		}
	}

	$post_type_labels = array();

	foreach ( $audit['args']['post_types'] as $post_type ) {
		$labels = get_post_type_labels( get_post_type_object( $post_type ) );
		$post_type_labels[] = $labels->name;
	} ?>

	<h1><?php esc_attr_e( 'Audit Details', 'msa' ); ?>
		<a href="<?php esc_attr_e( get_admin_url() . 'admin.php?page=msa-all-audits' ); ?>" class="page-title-action"><?php esc_attr_e( 'All Audits', 'msa' ); ?></a>
	</h1>

	<div class="msa-header msa-single-audit">

		<div class="msa-column msa-header-column msa-header-score-wrap">
			<div class="msa-header-score-container">
				<div class="msa-header-post-score msa-post-status-bg msa-post-status-bg-<?php esc_attr_e( msa_get_score_status( $audit['score'] ) ); ?>">
					<span><?php esc_attr_e( round( $audit['score'] * 100 ) . '%' ); ?></span>
				</div>
			</div>
		</div>

		<div class="msa-column msa-header-column msa-detail-container">

			<h3><?php esc_attr_e( $audit['name'] ); ?></h3>

			<table>
				<tbody>
					<tr>
						<td class="msa-header-audit-attribute"><?php esc_attr_e( 'Analysis Date:', 'msa' ); ?></td>
						<td><?php esc_attr_e( date( 'm/d/Y', strtotime( $audit['date'] ) ) ); ?></td>
					</tr>
					<tr>
						<td class="msa-header-audit-attribute"><?php esc_attr_e( 'Post Date Range:', 'msa' ); ?></td>
						<td><?php esc_attr_e( date( 'm/d/Y', strtotime( $form_fields['after-date'] ) ) . ' - ' . date( 'm/d/Y', strtotime( $form_fields['before-date'] ) ) ); ?></td>
					</tr>
					<tr>
						<td class="msa-header-audit-attribute"><?php esc_attr_e( 'Contains:', 'msa' ); ?></td>
						<td><?php esc_attr_e( $audit['num_posts'] . ' ' . implode( ', ', $post_type_labels ) ); ?></td>
					</tr>
				</tbody>
			</table>

		</div>

		<div class="msa-column msa-header-column msa-action-container">
		</div>

	</div>

	<ul class="subsubsub">

		<li class="all">
			<a href="<?php esc_attr_e( get_admin_url() . 'admin.php?page=msa-all-audits&audit=' . $audit_id ); ?>" class="current"><?php esc_attr_e( 'All', 'msa' ); ?> <span class="count">(<?php esc_attr_e( count( $posts ) ); ?>)</span></a> |
		</li>

		<?php $i = 0; foreach ( msa_get_score_statuses() as $key => $score_status ) {

			$separator = ' |';

			if ( count( msa_get_score_statuses() ) - 1 === $i ) {
				$separator = '';
			}

			$i++; ?>

			<li class="<?php esc_attr_e( $key ); ?>">
				<a class="msa-post-status-filter" href="<?php esc_attr_e( get_admin_url() . 'admin.php?page=msa-all-audits&audit=' . $audit_id ); ?>&score-low=<?php esc_attr_e( $score_status['low'] ); ?>&score-high=<?php esc_attr_e( $score_status['high'] ); ?><?php esc_attr_e( $current_filters ); ?>"><?php esc_attr_e( $score_status['name'] ); ?>
					<span class="count">(<?php esc_attr_e( msa_get_post_count_by_status( $posts , $key ) ); ?>)</span>
					<span class="msa-tooltips">
						<i class="fa fa-info-circle"></i>
						<span><?php esc_attr_e( __( 'Scores between ', 'msa' ) . round( $score_status['low'] * 100 ) . __( '% and ', 'msa' ) . round( $score_status['high'] * 100 ) . __( '%', 'msa' ) ); ?></span>
					</span>
				</a>
				<?php esc_attr_e( $separator ); ?>

			</li>

		<?php } ?>

	</ul>

	<form method="post" class="msa-all-posts-form">
		<input type="hidden" name="page" value="<?php echo ( isset( $_REQUEST['page'] ) ? esc_attr__( sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ) ) : '' ); // Input var okay. ?>" />
		<input type="hidden" name="audit" value="<?php echo ( isset( $_REQUEST['audit'] ) ? esc_attr__( sanitize_text_field( wp_unslash( $_REQUEST['audit'] ) ) ) : '' ); // Input var okay. ?>" />
		<?php
		$all_posts_table = new MSA_All_Posts_Table();
		$all_posts_table->prepare_items();
		$all_posts_table->search_box( 'Search Posts', 'msa' );
		$all_posts_table->display();
		wp_nonce_field( 'msa-all-audit-posts-table' ); ?>
	</form>

<?php } else {

	$audit_model = new MSA_Audits_Model();

	$audit_status = 'all';
	if ( isset( $_GET['audit_status'] ) ) { // Input var okay.
		$audit_status = sanitize_text_field( wp_unslash( $_GET['audit_status'] ) ); // Input var okay.
	} ?>

	<h1><?php esc_attr_e( 'All Audits', 'msa' ); ?>
		<a href="#" class="page-title-action msa-add-new-audit" <?php if ( false !== ( $in_progress = get_transient( 'msa_running_audit' ) ) ) { ?> onclick="alert('<?php esc_attr_e( 'Cannot create a new audit while another audit is in progress.', 'msa' ); ?>');" <?php } ?>><?php esc_attr_e( 'Add New', 'msa' ); ?></a>
	</h1>

	<div class="msa-create-audit-wrap">

		<?php if ( false === ( $in_progress = get_transient( 'msa_running_audit' ) ) ) { ?>

			<form method="post" class="msa-create-audit-form">

				<table class="form-table">
					<tbody>

						<?php do_action( 'msa_all_audits_before_create_new_settings' ); ?>

						<!-- Audit Name -->

						<tr>
							<th scope="row"><label for="msa-audit-name"><?php esc_attr_e( 'Audit Name', 'msa' ); ?></label></th>
							<td>
								<input id="msa-audit-name" name="name" value="<?php esc_attr_e( 'My Audit' , 'msa' ); ?>" />
							</td>
						</tr>

						<!-- Post Range -->

						<tr>
							<th scope="row"><label for="msa-audit-date-range"><?php esc_attr_e( 'Post Date Range', 'msa' ); ?></label></th>
							<td>
								<input id="msa-audit-date-range" name="date-range" class="msa-datepicker" data-start-date="<?php esc_attr_e( date( 'm/d/Y', strtotime( '-1 years' ) ) ); ?>" data-end-date="<?php esc_attr_e( date( 'm/d/Y', strtotime( 'today' ) ) ); ?>"/>
								<p class="description"><?php esc_attr_e( 'Perform the audit on posts published between these dates.', 'msa' ); ?></p>
							</td>
						</tr>

						<!-- Before Date -->

						<!--<tr>
							<th scope="row"><label for="msa-audit-before-date"><?php esc_attr_e( 'Before Date', 'msa' ); ?></label></th>
							<td>
								<input id="msa-audit-before-date" name="before-date" class="msa-datepicker" value="<?php esc_attr_e( date( 'm/d/Y', strtotime( 'today' ) ) ); ?>"/>
								<p class="description"><?php esc_attr_e( 'Perform the audit on posts published before this date.', 'msa' ); ?></p>
							</td>
						</tr>-->

						<!-- Post Types -->

						<tr>
							<th scope="row"><label for="msa-audit-post-types"><?php esc_attr_e( 'Post Types', 'msa' ); ?></label></th>
							<td>
								<select id="msa-audit-post-types" name="post-types[]" multiple>
									<?php foreach ( get_post_types() as $post_type ) { ?>
										<option value="<?php esc_attr_e( $post_type ); ?>" <?php selected( $post_type, 'post', true ); ?>><?php esc_attr_e( $post_type ); ?></option>
									<?php } ?>
								</select>
								<p class="description"><?php esc_attr_e( 'Perform the audit on posts of these post types.', 'msa' ); ?></p>
							</td>
						</tr>

						<!-- Maximum Posts -->

						<tr>
							<th scope="row"><label for="msa-audit-max-posts"><?php esc_attr_e( 'Maximum Posts', 'msa' ); ?></label></th>
							<td>
								<select id="msa-audit-max-posts" name="max-posts">
									<?php esc_attr_e( apply_filters( 'msa_create_audit_maximum_posts_select', '' ) ); ?>
									<?php $maximum_posts = apply_filters( 'msa_create_audit_maximum_posts', 250 );
									for ( $i = 50; $i <= $maximum_posts; $i += 50 ) { ?>
										<option value="<?php esc_attr_e( $i ); ?>"><?php esc_attr_e( $i, 'msa' ); ?></option>
									<?php } ?>
								</select>
								<p class="description"><?php esc_attr_e( 'The maximum number of posts that will be audited. You can increase the maximum number of posts you can audit, just', 'msa' ); ?> <a href="<?php esc_attr_e( MY_SITE_AUDIT_EXT_URL ); ?>" target="_blank"><?php esc_attr_e( 'download the extension.' ); ?></a></p>
							</td>
						</tr>

						<?php do_action( 'msa_all_audits_after_create_new_settings' ); ?>

					</tbody>
				</table>

				<?php submit_button( __( 'Create Audit', 'msa' ) ); ?>

				<?php wp_nonce_field( 'msa-add-audit' ); ?>

			</form>

		<?php } ?>
	</div>

	<ul class="subsubsub">

		<li class="all">
			<a href="<?php esc_attr_e( get_admin_url() . 'admin.php?page=msa-all-audits' ); ?>" class="<?php esc_attr_e( ! isset( $audit_status ) || ( isset( $audit_status ) && 'all' === $audit_status ) ? 'current' : '' ); ?>"><?php esc_attr_e( 'All', 'msa' ); ?> <span class="count">(<?php esc_attr_e( count( $audit_model->get_data() ) ); ?>)</span></a> |
		</li>

		<li class="completed">
			<a href="<?php esc_attr_e( get_admin_url() . 'admin.php?page=msa-all-audits&audit_status=completed' ); ?>" class="<?php esc_attr_e( isset( $audit_status ) && 'completed' === $audit_status ? 'current' : '' ); ?>"><?php esc_attr_e( 'Completed', 'msa' ); ?> <span class="count">(<?php esc_attr_e( count( $audit_model->get_data( array( 'status' => 'completed' ) ) ) ); ?>)</span></a> |
		</li>

		<li class="in-progress">
			<a href="<?php esc_attr_e( get_admin_url() . 'admin.php?page=msa-all-audits&audit_status=in-progress' ); ?>" class="<?php esc_attr_e( isset( $audit_status ) && 'in-progress' === $audit_status ? 'current' : '' ); ?>"><?php esc_attr_e( 'In Progress', 'msa' ); ?> <span class="count">(<?php esc_attr_e( count( $audit_model->get_data( array( 'status' => 'in-progress' ) ) ) ); ?>)</span></a>
		</li>

	</ul>

	<form method="post">
		<input type="hidden" name="page" value="<?php echo ( isset( $_REQUEST['page'] ) ? esc_attr__( sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ) ) : '' ); // Input var okay. ?>" />
		<?php
		$all_audits_table = new MSA_All_Audits_Table();
		$all_audits_table->prepare_items();
		$all_audits_table->search_box( 'Search Audits', 'msa' );
		$all_audits_table->display();
		wp_nonce_field( 'msa-all-audits-table' ); ?>
	</form>

<?php }

require_once( 'footer.php' );
