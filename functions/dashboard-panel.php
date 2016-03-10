<?php
/**
 * This file is responsible for handling dashboard panels and all of their functions.
 * Developers can register their own dashboard panels just like a custom post type.
 *
 * @package Functions / Dashboard Panels
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Save the dashboard panels order
 *
 * @access public
 * @return void
 */
function msa_save_dashboard_panel_order() {

	// Check if we have the right data.
	check_ajax_referer( 'save_dashboard_panel_order_nonce', 'save_dashboard_panel_order_nonce' );

	if ( ! isset( $_POST['left_order'] ) ) { // Input var okay.
		echo esc_attr__( 'Unable to find the left order.', 'msa' );
		die();
	}

	if ( ! isset( $_POST['right_order'] ) ) { // Input var okay.
		echo esc_attr__( 'Unable to find the right order', 'msa' );
		die();
	}

	$left_order = sanitize_text_field( wp_unslash( $_POST['left_order'] ) ); // Input var okay.
	$right_order = sanitize_text_field( wp_unslash( $_POST['right_order'] ) ); // Input var okay.

	$dashboard_panel_order = get_option( 'msa_dashboard_panel_order_' . get_current_user_id() );

	$dashboard_panel_order['left'] = 'empty' === $left_order ? array() : $left_order;
	$dashboard_panel_order['right'] = 'empty' === $right_order ? array() : $right_order;

	update_option( 'msa_dashboard_panel_order', $dashboard_panel_order );

	echo esc_attr__( 'Order Saved', 'msa' );
	die();
}
add_action( 'wp_ajax_msa_save_dashboard_panel_order', 'msa_save_dashboard_panel_order' );

/**
 * The Last Audit Panel Content
 *
 * @access public
 * @return string $output The HTML ouput of the last audit panel.
 */
function msa_dashboard_panel_last_audit_content() {

	// Get the latest Audit.
	$audit_model = new MSA_Audits_Model();
	$audit = $audit_model->get_latest();

	if ( isset( $audit ) ) {

		$output = '<div class="msa-left-column">
			<div class="msa-left-column-content">
				<div class="msa-circle msa-circle-border msa-post-status-border-' . msa_get_score_status( $audit['score'] ) . '">
				     <div class="msa-circle-inner">
				         <div class="msa-score-text msa-post-status-text-' . msa_get_score_status( $audit['score'] ) . '">' . round( 100 * $audit['score'] ) . '%</div>
				     </div>
				</div>
			</div>
		</div>';

		$output .= '<div class="msa-right-column">
			<div class="msa-right-column-content">
				<table class="wp-list-table widefat striped">
					<tbody>';

					$user = get_userdata( $audit['user'] );

					$output .= '<tr><td>' . __( 'Name', 'msa' ) . '</td> <td><a href="' . msa_get_single_audit_link( $audit['id'] ) . '">' . $audit['name'] . '</a></td></tr>';
					$output .= '<tr><td>' . __( 'Created On', 'msa' ) . '</td> <td>' . date( 'M d Y, h:i:s', strtotime( $audit['date'] ) ) . '</td></tr>';
					$output .= '<tr><td>' . __( 'Number of Posts', 'msa' ) . '</td> <td>' . $audit['num_posts'] . '</td></tr>';
					$output .= '<tr><td>' . __( 'Created By', 'msa' ) . '</td> <td>' . $user->display_name . '</td></tr>';

				$output .= '</tbody>
				</table>
			</div>
		</div>';

	} else {
		$output = '<p>' . __( 'You do not have any audits yet.', 'msa' ) . ' <a href="' . get_admin_url() . 'admin.php?page=msa-all-audits">' . __( 'Create one now!', 'msa' ) . '</a></p>';
	}

	return $output;
}
add_filter( 'msa_dashboard_panel_content_last_audit', 'msa_dashboard_panel_last_audit_content' );

/**
 * Create initial dashboard panels
 *
 * @access public
 * @return void
 */
function msa_create_initial_dashboard_panels() {

	// Last Audit.
	msa_register_dashboard_panel('last_audit', array(
		'post_box' 	=> 0,
		'title'		=> __( 'Last Audit', 'msa' ),
		'content'	=> '',
	));

	do_action( 'msa_register_dashboard_panels' );
}

/**
 * Get all the dashboard panels
 *
 * @access public
 * @return array $msa_dashboard_panels All dashboard panels.
 */
function msa_get_dashboard_panels() {

	global $msa_dashboard_panels;

	if ( ! is_array( $msa_dashboard_panels ) ) {
		$msa_dashboard_panels = array();
	}

	// If the dashboard panel order is not set then set it to default.
	if ( false === ( $dashboard_panel_order = get_option( 'msa_dashboard_panel_order_' . get_current_user_id() ) ) ) {

		$dashboard_panel_order = array(
			'left' 	=> array(),
			'right'	=> array(),
		);

		foreach ( $msa_dashboard_panels as $key => $msa_dashboard_panel ) {

			if ( 0 === $msa_dashboard_panel['post_box'] && ! in_array( $key, $dashboard_panel_order['left'], true ) ) {
				$dashboard_panel_order['left'][] = $key;
			}

			if ( 1 === $msa_dashboard_panel['post_box'] ) {
				$dashboard_panel_order['right'][] = $key;
			}
		}

		update_option( 'msa_dashboard_panel_order_' . get_current_user_id(), $dashboard_panel_order );
	}

	// Add panels.
	foreach ( $msa_dashboard_panels as $key => $msa_dashboard_panel ) {

		if ( ! in_array( $key, $dashboard_panel_order['left'], true ) && ! in_array( $key, $dashboard_panel_order['right'], true ) ) {

			if ( 0 === $msa_dashboard_panel['post_box'] ) {
				$dashboard_panel_order['left'][] = $key;
			}

			if ( 1 === $msa_dashboard_panel['post_box'] ) {
				$dashboard_panel_order['right'][] = $key;
			}
		}

		update_option( 'msa_dashboard_panel_order_' . get_current_user_id(), $dashboard_panel_order );
	}

	return apply_filters( 'msa_get_dashboard_panels', $msa_dashboard_panels );
}

/**
 * Register a new Dashboard Panel
 *
 * @access public
 * @param mixed $panel  The slug of the new dashboard panel.
 * @param array $args   The args of the new dashboard panel.
 * @return array $args  The args of the new dashboard panel.
 */
function msa_register_dashboard_panel( $panel, $args = array() ) {

	global $msa_dashboard_panels;

	if ( ! is_array( $msa_dashboard_panels ) ) {
		$msa_dashboard_panels = array();
	}

	// Default panel.
	$default = array(
		'title' => __( 'Title', 'msa' ),
	);

	$args = array_merge( $default, $args );

	// Add the panel to the global dashboard panels array.
	$msa_dashboard_panels[ $panel ] = apply_filters( 'msa_register_dashboard_panel_args', $args );

	/**
	* Fires after a dashboard panel is registered.
	*
	* @param string $panel 	  Dashboard Panel.
	* @param array $args      Arguments used to register the dashboard panel.
	*/
	do_action( 'msa_registed_dashboard_panel', $panel, $args );

	return $args;
}
