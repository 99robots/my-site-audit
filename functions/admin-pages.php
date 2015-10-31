<?php
/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 10/22/15
 * Package: Functions/Admin-Pages
 * File: admin-pages.php
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

/**
 * Hooks intot the 'admin_menu' hook to show the settings page
 *
 * @access public
 * @static
 * @return void
 */
function msa_menu() {

	// Dashboard

	add_menu_page(
		__(MY_SITE_AUDIT_ITEM_NAME, 'msa'),
		__(MY_SITE_AUDIT_ITEM_NAME, 'msa'),
    	'manage_options',
    	'msa-dashboard',
    	'msa_dashboard',
    	MY_SITE_AUDIT_PLUGIN_URL . 'images/logo.png" style="width:20px;padding-top: 6px;'
    );

    // Dashboard

    $dashboard_page_load = add_submenu_page(
    	'msa-dashboard',
    	__('Dashboard', 'msa'),
    	__('Dashboard', 'msa'),
    	'manage_options',
    	'msa-dashboard',
    	'msa_dashboard'
    );
    add_action("admin_print_scripts-$dashboard_page_load", 'msa_dashboard_scripts');

    // All Audits

    $all_audits_page_load = add_submenu_page(
    	'msa-dashboard',
    	__('All Audits', 'msa'),
    	__('All Audits', 'msa'),
    	'manage_options',
    	'msa-all-audits',
    	'msa_all_audits'
    );
    add_action("admin_print_scripts-$all_audits_page_load", 'msa_all_audits_scripts');
    add_action("load-$all_audits_page_load", 'msa_all_audits_load');

    // Settings

	$settings_page_load = add_submenu_page(
    	'msa-dashboard',
    	__('Settings', 'msa'),
    	__('Settings', 'msa'),
    	'manage_options',
    	'msa-settings',
    	'msa_settings'
    );
    add_action("admin_print_scripts-$settings_page_load" , 'msa_settings_scripts');

    // Extensions

	$extensions_page_load = add_submenu_page(
    	'msa-dashboard',
    	__('Extensions', 'msa'),
    	__('Extensions', 'msa'),
    	'manage_options',
    	'msa-extensions',
    	'msa_extensions'
    );
    add_action("admin_print_scripts-$extensions_page_load" , 'msa_extensions_scripts');
}
add_action('admin_menu', 'msa_menu');

/**
 * Hooks into the 'admin_print_scripts-$page' to inlcude the scripts for the dashboard page
 *
 * @access public
 * @static
 * @return void
 */
function msa_dashboard_scripts() {

	// Style

	wp_enqueue_style('msa-common-css', 				MY_SITE_AUDIT_PLUGIN_URL . '/css/common.css');
	wp_enqueue_style('msa-dashboard-css', 			MY_SITE_AUDIT_PLUGIN_URL . '/css/dashboard.css');

	wp_enqueue_style('msa-fontawesome-css', 		MY_SITE_AUDIT_PLUGIN_URL . '/includes/font-awesome/css/font-awesome.min.css');

}

/**
 * Hooks into the 'admin_print_scripts-$page' to inlcude the scripts for the all audits page
 *
 * @access public
 * @static
 * @return void
 */
function msa_all_audits_scripts() {

	// Style

	wp_enqueue_style('msa-all-audits-css', 			MY_SITE_AUDIT_PLUGIN_URL . '/css/all-audits.css');
	wp_enqueue_style('msa-common-css', 				MY_SITE_AUDIT_PLUGIN_URL . '/css/common.css');

	wp_enqueue_style('msa-fontawesome-css', 		MY_SITE_AUDIT_PLUGIN_URL . '/includes/font-awesome/css/font-awesome.min.css');
	wp_enqueue_style('msa-jquery-ui-css', 			MY_SITE_AUDIT_PLUGIN_URL . '/includes/jquery-datepicker/jquery-ui.min.css');
	wp_enqueue_style('msa-jquery-ui-theme-css', 	MY_SITE_AUDIT_PLUGIN_URL . '/includes/jquery-datepicker/jquery-ui.theme.min.css');

	// Scripts

	wp_enqueue_script('msa-all-audits-js', 			MY_SITE_AUDIT_PLUGIN_URL . '/js/all-audits.js');
	wp_localize_script('msa-all-audits-js', 'msa_all_audits_data', array(
		'site_url'			=> get_site_url(),
		'success_message'	=> __('Your Audit has been created!  Refresh your page to see it.', 'msa'),
	));

	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-datepicker');

}

/**
 * Hooks into the 'admin_print_scripts-$page' to inlcude the scripts for the settings page
 *
 * @access public
 * @static
 * @return void
 */
function msa_settings_scripts() {

	// Style

	wp_enqueue_style('msa-settings-css', 			MY_SITE_AUDIT_PLUGIN_URL . '/css/settings.css');

	wp_enqueue_style('msa-fontawesome-css', 		MY_SITE_AUDIT_PLUGIN_URL . '/includes/font-awesome/css/font-awesome.min.css');

	// Script

	wp_enqueue_script('msa-settings-js', 			MY_SITE_AUDIT_PLUGIN_URL . '/js/settings.js');

}

/**
 * Hooks into the 'admin_print_scripts-$page' to inlcude the scripts for the extensions page
 *
 * @access public
 * @static
 * @return void
 */
function msa_extensions_scripts() {

	// Style

	wp_enqueue_style('msa-extensions-css', 			MY_SITE_AUDIT_PLUGIN_URL . '/css/extensions.css');

	wp_enqueue_style('msa-fontawesome-css', 		MY_SITE_AUDIT_PLUGIN_URL . '/includes/font-awesome/css/font-awesome.min.css');

}

/**
 * This is the main function for the dashboard page
 *
 * @access public
 * @static
 * @return void
 */
function msa_dashboard() {

	require_once(MY_SITE_AUDIT_PLUGIN_DIR . 'controllers/dashboard.php');

}

/**
 * This is the main function for the All Audits page
 *
 * @access public
 * @static
 * @return void
 */
function msa_all_audits() {

	require_once(MY_SITE_AUDIT_PLUGIN_DIR . 'controllers/all-audits.php');

}

/**
 * Show the screen options on the Single Audit page
 *
 * @access public
 * @return void
 */
function msa_all_audits_load() {

	// Single Post

	if ( isset($_GET['audit']) && isset($_GET['post']) ) {

	}

	// Single Audit

	else if ( isset($_GET['audit']) ) {

		// Screen Options

		add_screen_option( 'per_page' );

		$screen = get_current_screen();

		add_filter( 'manage_my-site-audit_page_msa-all-audits_columns', 'msa_all_audits_add_column' );

	}

	// All Audits

	else {

	}
}

function msa_all_audits_add_column( $columns ) {

	$conditions = msa_get_conditions();

	foreach ( $conditions as $key => $condition ) {
		$columns[$key] = $condition['name'];
	}

	return $columns;

}

/**
 * This is the main function for the settings page
 *
 * @access public
 * @static
 * @return void
 */
function msa_settings() {

	require_once(MY_SITE_AUDIT_PLUGIN_DIR . 'controllers/settings.php');

}

/**
 * This is the main function for the extensions page
 *
 * @access public
 * @static
 * @return void
 */
function msa_extensions() {

	require_once(MY_SITE_AUDIT_PLUGIN_DIR . 'controllers/extensions.php');

}