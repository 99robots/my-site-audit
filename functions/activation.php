<?php
/**
 * This file handles all operations upon plugin activation.
 *
 * @package Functions / Activation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The plugin is activated
 *
 * @access public
 * @return void
 */
function msa_activation() {

	// Add the version number to the database.
	if ( function_exists( 'is_multisite' ) && is_multisite() ) {
		update_site_option( 'msa_version', MY_SITE_AUDIT_VERSION );
	} else {
		update_option( 'msa_version', MY_SITE_AUDIT_VERSION );
	}

	// Create the audits table.
	$audit_model = new MSA_Audits_Model();
	$audit_model->create_table();

	// Create the audit posts table.
	$audit_posts_model = new MSA_Audit_Posts_Model();
	$audit_posts_model->create_table();

	// Add the transient to redirect.
	set_transient( '_msa_activation_redirect', true, 30 );

	// Add Upgraded From Option.
	update_option( 'msa_version_upgraded_from', MY_SITE_AUDIT_VERSION );
	delete_transient( 'msa_running_audit' );
}
register_activation_hook( MY_SITE_AUDIT_PLUGIN_FILE, 'msa_activation' );
