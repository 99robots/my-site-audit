<?php
/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 10/30/15
 * Package: Functions/Plugin
 * File: plugin.php
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
 * Add the actions rows for the all plugins page
 *
 * @access public
 * @param mixed $links
 * @param mixed $file
 * @return void
 */
function msa_plugin_action_links( $links, $file ) {

	$settings_link = '<a href="' . admin_url('admin.php?page=msa-settings') . '">' . esc_html__( 'General Settings', 'msa' ) . '</a>';

	// Remove the action if we are on our own page

	if ( $file == 'my-site-audit/my-site-audit.php' ) {
		array_unshift( $links, $settings_link );
	}

	return $links;
}
add_filter( 'plugin_action_links', 'msa_plugin_action_links', 10, 2 );

/**
 * Add the row meta data for the all plugins page
 *
 * @access public
 * @param mixed $input
 * @param mixed $file
 * @return void
 */
function msa_plugin_row_meta( $input, $file ) {

	// Remove the action if we are on our own page

	if ( $file != 'my-site-audit/my-site-audit.php' ) {
		return $input;
	}

	$msa_link = esc_url( add_query_arg( array(
			'utm_source'   => 'plugins-page',
			'utm_medium'   => 'plugin-row',
			'utm_campaign' => 'admin',
		), 'https://mysiteaudit.com/extensions' )
	);

	$links = array(
		'<a href="' . admin_url( 'index.php?page=msa-getting-started' ) . '">' . esc_html__( 'Getting Started', 'msa' ) . '</a>',
		'<a href="' . $msa_link . '">' . esc_html__( 'Extensions', 'msa' ) . '</a>',
	);

	$input = array_merge( $input, $links );

	return $input;
}
add_filter( 'plugin_row_meta', 'msa_plugin_row_meta', 10, 2 );
