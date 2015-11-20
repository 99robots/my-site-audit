<?php
/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 11/16/15
 * Package: Functions/System Info
 * File: system-info.php
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
 * System Info Tab
 *
 * @access public
 * @param mixed $content
 * @return void
 */
function msa_settings_tab_system_info_content($content) {

	$content = '<h3 class="msa-settings-heading">' . __('System Information') . '</h3>';

	do_action( 'msa_before_system_info' );

	$break_line = "======================================================================\n\n";
	$white_space = "                              ";

	$content .= '<textarea class="msa-system-info-text" rows="10">';

	$system_info = msa_get_system_info();

	foreach ( $system_info as $group ) {

		$content .= $break_line;

		foreach ( $group as $item ) {
			$content .= $item['name'] . substr($white_space, strlen($item['name']) ) . $item['value'] . "\n";
		}

		$content .= "\n";
	}

	do_action( 'msa_after_system_info' );

	$content .= '</textarea>';

	return $content;

}
add_filter('msa_settings_tab_content_system_info', 'msa_settings_tab_system_info_content', 10, 1);

/**
 * Get the information about this wordpress install
 *
 * @access public
 * @return void
 */
function msa_get_system_info() {

	global $wp_version;

	if ( get_bloginfo( 'version' ) < '3.4' ) {
		$theme_data = get_theme_data( get_stylesheet_directory() . '/style.css' );
		$theme      = $theme_data['Name'] . ' ' . $theme_data['Version'];
	} else {
		$theme_data = wp_get_theme();
		$theme      = $theme_data->Name . ' ' . $theme_data->Version;
	}

	$white_space = "                              ";

	$plugins = get_plugins();
	$active_plugins = get_option( 'active_plugins', array() );

	$inactive_plugins_data = array();
	$active_plugins_data = array();
	$network_activated_plugins_data = array();

	foreach ( $plugins as $plugin_path => $plugin ) {

		if ( ! in_array( $plugin_path, $active_plugins ) ) {
			$inactive_plugins_data[] = $plugin['Name'] . ': ' . $plugin['Version'];
		} else {
			$active_plugins_data[] = $plugin['Name'] . ': ' . $plugin['Version'];
		}
	}

	if ( is_multisite() ) {

		$plugins = wp_get_active_network_plugins();
		$active_plugins = get_site_option( 'active_sitewide_plugins', array() );

		foreach ( $plugins as $plugin_path ) {

			$plugin_base = plugin_basename( $plugin_path );

			if ( ! array_key_exists( $plugin_base, $active_plugins ) ) {
				continue;
			}
			$plugin = get_plugin_data( $plugin_path );

			$network_activated_plugins_data[] = $plugin['Name'] . ' :' . $plugin['Version'];
		}

	}

	return array(
		array(
			array(
				'name'	=> 'Site URL',
				'value'	=> site_url(),
			),
			array(
				'name'	=> 'Home URL',
				'value'	=> home_url(),
			),
			array(
				'name'	=> 'Admin URL',
				'value'	=> admin_url(),
			),
		),
		array(
			array(
				'name'	=> 'WordPress Version',
				'value'	=> $wp_version,
			),
			array(
				'name'	=> 'Permalink Structure',
				'value'	=> get_option( 'permalink_structure' ),
			),
			array(
				'name'	=> 'Active Theme',
				'value'	=> $theme,
			),
			array(
				'name'	=> 'WP_DEBUG',
				'value'	=> defined( 'WP_DEBUG' ) ? WP_DEBUG ? 'Enabled' : 'Disabled' : 'Not set',
			),
			array(
				'name'	=> 'Registered Post Stati',
				'value'	=> implode( "\n" . $white_space, get_post_stati() ),
			),
			array(
				'name'	=> 'Active Plugins',
				'value'	=> implode( "\n" . $white_space, $active_plugins_data ),
			),
			array(
				'name'	=> 'Inactive Plugins',
				'value'	=> implode( "\n" . $white_space, $inactive_plugins_data ),
			),
			array(
				'name'	=> 'Network Activated Plugins',
				'value'	=> implode( "\n" . $white_space, $network_activated_plugins_data ),
			),
		),
		array(
			array(
				'name'	=> 'My Site Audit Version',
				'value'	=> MY_SITE_AUDIT_VERSION,
			),
		),
		array(
			array(
				'name'	=> 'PHP Version',
				'value'	=> PHP_VERSION,
			),
/*
			array(
				'name'	=> 'MySQL Version',
				'value'	=> mysqli_get_server_info(),
			),
*/
			array(
				'name'	=> 'Web Server Info',
				'value'	=> $_SERVER['SERVER_SOFTWARE'],
			),
			array(
				'name'	=> 'WordPress Memory Limit',
				'value'	=> WP_MEMORY_LIMIT,
			),
			array(
				'name'	=> 'PHP Safe Mode',
				'value'	=> ini_get( 'safe_mode' ) ? "Yes" : "No",
			),
			array(
				'name'	=> 'PHP Memory Limit',
				'value'	=> ini_get( 'memory_limit' ),
			),
			array(
				'name'	=> 'PHP Upload Max Size',
				'value'	=> ini_get( 'upload_max_filesize' ),
			),
			array(
				'name'	=> 'PHP Post Max Size',
				'value'	=> ini_get( 'post_max_size' ),
			),
			array(
				'name'	=> 'PHP Upload Max File-size',
				'value'	=> ini_get( 'upload_max_filesize' ),
			),
			array(
				'name'	=> 'PHP Time Limit',
				'value'	=> ini_get( 'max_execution_time' ),
			),
			array(
				'name'	=> 'PHP Max Input Vars',
				'value'	=> ini_get( 'max_input_vars' ),
			),
			array(
				'name'	=> 'PHP Arg Separator',
				'value'	=> ini_get( 'arg_separator.output' ),
			),
			array(
				'name'	=> 'PHP Allow URL File Open',
				'value'	=> ini_get( 'allow_url_fopen' ) ? "Yes" : "No",
			),
		)
	);
}