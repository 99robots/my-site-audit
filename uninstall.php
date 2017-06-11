<?php
/**
 * This file is run when the plugin is uninstalled and it will remove all data
 * related to this plugin.
 *
 * @package Uninstall
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

do_action( 'msa_uninstall' );

require_once( plugin_dir_path( __FILE__ ) . 'model/audits.php' );
require_once( plugin_dir_path( __FILE__ ) . 'model/audit-posts.php' );


$audit_model = new MSA_Audits_Model();
$audit_model->delete_table();

$audit_posts_model = new MSA_Audit_Posts_Model();
$audit_posts_model->delete_table();


global $wpdb;

/**
 * Perform separate actions for multisite or single site
 */
if ( function_exists( 'is_multisite' ) && ! is_multisite() ) {

	delete_option( 'msa_version' );
	delete_option( 'msa_version_upgraded_from' );

	$wpdb->query( 'DELETE FROM `' . $wpdb->prefix . "options` WHERE `option_name` LIKE '%msa_dashboard_panel_order_%'" );
	$wpdb->query( 'DELETE FROM `' . $wpdb->prefix . "options` WHERE `option_name` LIKE '%msa_show_columns_%'" );
} else {

	delete_site_option( 'msa_version' );

	$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
	foreach ( $blog_ids as $blog_id ) {

		switch_to_blog( $blog_id );
		delete_option( 'msa_version_upgraded_from' );

		$wpdb->query( 'DELETE FROM `' . $wpdb->prefix . "options` WHERE `option_name` LIKE '%msa_dashboard_panel_order_%'" );
		$wpdb->query( 'DELETE FROM `' . $wpdb->prefix . "options` WHERE `option_name` LIKE '%msa_show_columns_%'" );

		restore_current_blog();
	}
}
