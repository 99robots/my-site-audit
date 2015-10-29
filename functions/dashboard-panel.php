<?php
/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 10/28/15
 * Package: Functions/Dashboard Panels
 * File: dashboard-panels.php
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
 * The Last Audit Panel Content
 *
 * @access public
 * @return void
 */
function msa_dashboard_panel_last_audit_content() {

	// Get the latest Audit

	$audit_model = new MSA_Audits_Model();
	$audit = $audit_model->get_latest();

	if ( isset($audit) ) {

		$user = get_userdata($audit['user']);

		$output = '<p>' . __('Name: ', 'msa') . ' <span>' . $audit['name'] . '<span></p>';
		$output .= '<p>' . __('Date: ', 'msa') . ' <span>' . date('M d Y, h:i:s', strtotime($audit['date'])) . '<span></p>';
		$output .= '<p>' . __('Number of Posts: ', 'msa') . ' <span>' . $audit['num_posts'] . '<span></p>';
		$output .= '<p>' . __('Score: ', 'msa') . ' <span>' . round(100 * $audit['score']) . '%' . '<span></p>';
		$output .= '<p>' . __('User: ', 'msa') . ' <span>' . $user->display_name . '<span></p>';

	} else {

		$output = '<p>' . __('You do not have any audits yet.', 'msa') . ' <a href="' . get_admin_url() . 'admin.php?page=msa-all-audits">' . __('Create one now!', 'msa') . '</a></p>';

	}

	return $output;

}

add_filter('msa_dashboard_panel_last_audit', 'msa_dashboard_panel_last_audit_content');

/**
 * Create initial dashboard panels
 *
 * @access public
 * @return void
 */
function msa_create_initial_dashboard_panels() {

	// Last Audit

	msa_register_dashboard_panel('last_audit', array(
		'postbox'	=> 1,
		'title'		=> __('Last Audit', 'msa'),
		'content'	=> apply_filters('msa_dashboard_panel_last_audit', ''),
	));

	// Example Post Box

	msa_register_dashboard_panel('example_1', array(
		'postbox'	=> 1,
		'title'		=> __('Example Post Box', 'msa'),
		'content'	=> apply_filters('msa_dashboard_panel_example_1', ''),
	));

	// Example Post Box

	msa_register_dashboard_panel('example_2', array(
		'postbox'	=> 2,
		'title'		=> __('Example Post Box', 'msa'),
		'content'	=> apply_filters('msa_dashboard_panel_example_2', ''),
	));

	// Example Post Box

	msa_register_dashboard_panel('example_3', array(
		'postbox'	=> 2,
		'title'		=> __('Example Post Box', 'msa'),
		'content'	=> apply_filters('msa_dashboard_panel_example_3', ''),
	));

}

/**
 * Get all the dashboard panels
 *
 * @access public
 * @return void
 */
function msa_get_dashboard_panels() {

	global $msa_dashboard_panels;

	if ( ! is_array( $msa_dashboard_panels ) ) {
		$msa_dashboard_panels = array();
	}

	return apply_filters('msa_get_dashboard_panels', $msa_dashboard_panels);
}

/**
 * Register a new Dashboard Panel
 *
 * @access public
 * @param mixed $panel
 * @param array $args (default: array())
 * @return void
 */
function msa_register_dashboard_panel($panel, $args = array()) {

	global $msa_dashboard_panels;

	if ( ! is_array( $msa_dashboard_panels ) ) {
		$msa_dashboard_panels = array();
	}

	// Default panel

	$default = array(
		'title'			=> __('Title', 'msa'),
	);

	$args = array_merge($default, $args);

	// Add the panel to the global dashboard panels array

	$msa_dashboard_panels[ $panel ] = apply_filters('msa_register_dashboard_panel_args', $args);

	/**
	* Fires after a dashboard panel is registered.
	*
	* @param string $panel 	  Dashboard Panel.
	* @param array $args      Arguments used to register the dashboard panel.
	*/
	do_action('msa_registed_dashboard_panel', $panel, $args);

	return $args;
}