<?php
/**
 * This file is responsible for the entire audit creation workflow.  It uses WP
 * Cron to handle auditing many posts at a time.
 *
 * @package Functions / Create Audit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Run an audit
 *
 * @access public
 * @return void
 */
function msa_run_audit() {

	// Create an audit if we have one in the queue.
	$next_audit = msa_get_next_audit_to_run();

	if ( isset( $next_audit ) && is_array( $next_audit ) ) {
		wp_schedule_single_event( time(), 'msa_run_audit_background', array( $next_audit ) );
		set_transient( 'msa_schedule_audit', true );
		msa_clear_audit_queue();
	}

}
add_action( 'init', 'msa_run_audit' );

/**
 * Set the current create audit data
 *
 * @access public
 * @param array $data      The audit data.
 * @return bool true|false Was the audit added to the queue?
 */
function msa_add_audit_to_queue( $data ) {

	// Check if we are already performing an audit.
	if ( false !== ( $current_audit = get_transient( 'msa_run_audit' ) ) || false !== ( $can_run = get_transient( 'msa_running_audit' ) ) ) {
		return null;
	}

	// Add this audit to the queue.
	set_transient( 'msa_run_audit', $data );

	return true;
}

/**
 * Get the next audit to run
 *
 * @access public
 * @return bool $result Was the audit queue cleared?
 */
function msa_clear_audit_queue() {
	$result = delete_transient( 'msa_run_audit' );
	return $result;
}

/**
 * Get the next audit to run
 *
 * @access public
 * @return array $current_audit The next audit to run.
 */
function msa_get_next_audit_to_run() {

	// Check if we are already performing an audit.
	if ( false !== ( $current_audit = get_transient( 'msa_run_audit' ) ) && false === ( $can_run = get_transient( 'msa_running_audit' ) ) ) {
		return $current_audit;
	}

	return null;
}

/**
 * Create a new post for an audit
 *
 * @access public
 * @return void
 */
function msa_add_post_to_audit() {

	if ( ! isset( $_POST['audit_id'] ) || ! isset( $_POST['post_id'] ) ) { // Input var okay. WPCS: CSRF ok.
		echo '';
		die();
	}

	$audit_id = sanitize_text_field( wp_unslash( $_POST['audit_id'] ) ); // Input var okay. WPCS: CSRF ok.
	$post_id = sanitize_text_field( wp_unslash( $_POST['post_id'] ) ); // Input var okay. WPCS: CSRF ok.

	$audit_posts_model = new MSA_Audit_Posts_Model();
	$post = get_post( $post_id );

	$data = msa_get_post_audit_data( $post );
	$score = msa_calculate_score( $post, $data );
	$data['score'] = $score['score'];

	// Add a new record in the audit posts table.
	$audit_posts_model->add_data(array(
		'audit_id' 	=> $audit_id,
		'post'		=> $post,
		'data'		=> array(
		'score'		=> $score,
		'values'	=> $data,
		),
	) );

	echo esc_attr_e( $score['score'] );
	die();

}
add_action( 'wp_ajax_msa_add_post_to_audit', 'msa_add_post_to_audit' );

/**
 * Update the audit score
 *
 * @access public
 * @return void
 */
function msa_ajax_update_audit_score() {

	if ( ! isset( $_POST['audit_id'] ) || ! isset( $_POST['score'] ) || ! isset( $_POST['num_posts'] ) ) { // Input var okay. WPCS: CSRF ok.
		echo '';
		die();
	}

	$audit_id = sanitize_text_field( wp_unslash( $_POST['audit_id'] ) ); // Input var okay. WPCS: CSRF ok.
	$score = sanitize_text_field( wp_unslash( $_POST['score'] ) ); // Input var okay. WPCS: CSRF ok.
	$num_posts = sanitize_text_field( wp_unslash( $_POST['num_posts'] ) ); // Input var okay. WPCS: CSRF ok.

	$audit_model = new MSA_Audits_Model();
	$audit = $audit_model->get_data_from_id( $audit_id );
	$audit['score'] = round( $score / $num_posts, 10 );
	$audit_model->update_data( $audit_id, $audit );

	echo esc_attr_e( $audit['score'] );
	die();

}
add_action( 'wp_ajax_msa_update_audit_score', 'msa_ajax_update_audit_score' );

/**
 * Get the post IDs for the audit
 *
 * @access public
 * @return void
 */
function msa_get_post_ids() {

	// Check to see if we can add a new audit.
	if ( ! apply_filters( 'msa_can_add_new_audit', true ) ) {
		echo wp_json_encode( array(
			'status'	=> 'error',
			'message'	=> __( 'Cannot create a new audit.  You already have the maximum amount of audits saved.  Please delete one in order to create one.', 'msa' ),
		) );
		die();
	}

	$query_parameters = array();
	if ( isset( $_POST['data'] ) ) { // Input var okay. WPCS: CSRF ok.
		$query_parameters = html_entity_decode( sanitize_text_field( wp_unslash( $_POST['data'] ) ) ); // Input var okay. WPCS: CSRF ok.
	}

	parse_str( $query_parameters, $data );

	// Create our Audit and Audit Post Model objects.
	$audit_model = new MSA_Audits_Model();

	// Get all the data from the user.
	$audit = array();

	$audit['name'] = stripcslashes( sanitize_text_field( $data['name'] ) );
	$audit['score'] = 0;
	$audit['date'] = date( 'Y-m-d H:i:s' );
	$audit['user'] = get_current_user_id();
	$audit['args']['post_types'] = $data['post-types'];
	$audit['args']['conditions'] = wp_json_encode( msa_get_conditions() );
	$data['after-date'] = '' !== $data['after-date'] ? strip_tags( $data['after-date'] ) : date( 'm/d/Y', strtotime( '-1 years' ) );
	$data['before-date'] = '' !== $data['before-date'] ? strip_tags( $data['before-date'] ) : date( 'm/d/Y', strtotime( 'today' ) );

	$audit['args']['form_fields'] = wp_json_encode( $data );

	// Get all the posts that we are going to perform an audit on.
	$get_posts = new WP_Query;
	$post_ids = $get_posts->query( array(
		'public' 			=> true,
		'date_query' 		=> array(
		array(
			'after'     => $data['after-date'],
			'before'    => $data['before-date'],
			'inclusive' => true,
		),
		),
		'post_type'			=> $data['post-types'],
		'posts_per_page'	=> strip_tags( $data['max-posts'] ),
		'fields'			=> 'ids',
	) );

	if ( count( $post_ids ) > 0 ) {
		$audit['num_posts'] = count( $post_ids );
		$audit_id = $audit_model->add_data( $audit );

		echo wp_json_encode( array(
			'status'	=> 'success',
			'post_ids' 	=> $post_ids,
			'audit_id' 	=> $audit_id,
		) );
		die();
	}

	echo wp_json_encode( array(
		'status'	=> 'error',
		'message'	=> __( 'No posts found.', 'msa' ),
	) );
	die();
}
add_action( 'wp_ajax_msa_get_post_ids_for_audit', 'msa_get_post_ids' );
