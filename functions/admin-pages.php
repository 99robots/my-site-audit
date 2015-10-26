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
    	MY_SITE_AUDIT_PLUGIN_URL . 'img/logo.png" style="width:20px;padding-top: 6px;'
    );

    // Dashboard

    $dashboard_page_load = add_submenu_page(
    	'msa-dashboard',
    	__('Posts', 'msa'),
    	__('Posts', 'msa'),
    	'manage_options',
    	'msa-dashboard',
    	'msa_dashboard'
    );
    add_action("admin_print_scripts-$dashboard_page_load", 'msa_dashboard_scripts');

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

	wp_enqueue_style('msa-dashboard-css', 			MY_SITE_AUDIT_PLUGIN_URL . '/css/dashboard.css');
	wp_enqueue_style('msa-fontawesome-css', 		MY_SITE_AUDIT_PLUGIN_URL . '/css/font-awesome.min.css');

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
	wp_enqueue_style('msa-fontawesome-css', 		MY_SITE_AUDIT_PLUGIN_URL . '/css/font-awesome.min.css');
	wp_enqueue_style('msa-settings-css', 			MY_SITE_AUDIT_PLUGIN_URL . '/css/settings.css');

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

	wp_enqueue_style('msa-fontawesome-css', 		MY_SITE_AUDIT_PLUGIN_URL . '/css/font-awesome.min.css');

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