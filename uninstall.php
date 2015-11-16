<?php
/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 10/22/15
 * Package: Uninstall
 * File: uninstall.php
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

// if uninstall not called from WordPress exit

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit ();

do_action('msa_uninstall');

// Delete all existence of this plugin

require_once( plugin_dir_path( __FILE__ ) . 'model/audits.php' );
require_once( plugin_dir_path( __FILE__ ) . 'model/audit-posts.php' );

// Delete the audits table

$audit_model = new MSA_Audits_Model();
$audit_model->delete_table();

// Delete the audit posts table

$audit_posts_model = new MSA_Audit_Posts_Model();
$audit_posts_model->delete_table();

// Loop through all blogs and delete data

global $wpdb;

// Single Site

if ( function_exists('is_multisite') && !is_multisite() ) {

	// Version

	delete_option('msa_version');
	delete_option('msa_version_upgraded_from');

	// Dashboard Panels Order

    $wpdb->query("DELETE FROM `" . $wpdb->prefix . "options` WHERE `option_name` LIKE '%msa_dashboard_panel_order_%'");

    // Show Columns

    $wpdb->query("DELETE FROM `" . $wpdb->prefix . "options` WHERE `option_name` LIKE '%msa_show_columns_%'");

}

// Multisite

else {

	// Version

	delete_site_option('msa_version');

	// Delete data from each blog

    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

    foreach ( $blog_ids as $blog_id ) {

        switch_to_blog( $blog_id );

		delete_option('msa_version_upgraded_from');

        // Dashboard Panels Order

        $wpdb->query("DELETE FROM `" . $wpdb->prefix . "options` WHERE `option_name` LIKE '%msa_dashboard_panel_order_%'");

        // Show Columns

        $wpdb->query("DELETE FROM `" . $wpdb->prefix . "options` WHERE `option_name` LIKE '%msa_show_columns_%'");

        restore_current_blog();
    }
}