<?php
/**
 * This is the main controller for the All Audits, Single Audit and Single posts
 * page.
 *
 * @package Controllers / All Audits
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Create a new Audit
 */

if ( isset( $_POST['submit'] ) && check_admin_referer( 'msa-add-audit' ) ) { // Input var okay.

	// Check if they can add a new audit.
	if ( apply_filters( 'msa_can_add_new_audit', true ) ) {

		/**
		 * This is the main action for creating an audit
		 *
		 * @param mixed $_POST All of the audit attributes set by the user.
		 */
		do_action( 'msa_create_audit', $_POST ); // Input var okay.

		$_POST['user'] = get_current_user_id();

		$date_range = array();
		if ( isset( $_POST['date-range'] ) ) { // Input var okay.
			$date_range = json_decode( stripcslashes( sanitize_text_field( wp_unslash( $_POST['date-range'] ) ) ), true ); // Input var okay.
		}

		$_POST['after-date'] = $date_range['start'];
		$_POST['before-date'] = $date_range['end'];

		msa_add_audit_to_queue( $_POST ); // Input var okay.

	} else {
		set_transient( 'msa_unable_to_create_audit', __( 'Unable to create your audit because you reached the maximum number of audits you can have.  In order to add a new audit you will first need to delete one.  If you want want to increase the amount of audits you can have please ' ) . '<a href="' . MY_SITE_AUDIT_EXT_URL . '" target="_blank">' . __( 'download the extension' ) . '</a>' );
	}

	msa_force_redirect( get_admin_url() . 'admin.php?page=msa-all-audits' );
}

/**
 *  Delete an audit
 */

if ( isset( $_GET['action'] ) && 'delete' === $_GET['action'] && check_admin_referer( 'msa-delete-audit' ) ) { // Input var okay.

	$audit = -1;
	if ( isset( $_GET['audit'] ) ) { // Input var okay.
		$audit = sanitize_text_field( wp_unslash( $_GET['audit'] ) ); // Input var okay.
	}

	$audit_model = new MSA_Audits_Model();
	$audit_model->delete_data( $audit );

	msa_force_redirect( get_admin_url() . 'admin.php?page=msa-all-audits' );
}

/**
 *  Force Stop an audit
 */

if ( isset( $_GET['action'] ) && 'force_stop_audit' === $_GET['action'] && check_admin_referer( 'msa-force-stop-audit' ) ) { // Input var okay.

	delete_transient( 'msa_running_audit' );

	$audit_model = new MSA_Audits_Model();
	$audits = $audit_model->get_data( array( 'status' => 'in-progress' ) );

	if ( isset( $audits[0] ) ) {
		$audit = $audits[0];
		$audit['status'] = 'completed';
		$audit_model->update_data( $audit['id'], $audit );
	}

	msa_force_redirect( get_admin_url() . 'admin.php?page=msa-all-audits' );
}

include_once( MY_SITE_AUDIT_PLUGIN_DIR . 'views/all-audits.php' );
