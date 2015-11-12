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
 * Save the dashboard panels order
 *
 * @access public
 * @return void
 */
function msa_save_dashboard_panel_order() {

	// Check if we have the right data

	if ( !isset($_POST['left_order']) ) {
		echo __('Unable to find the left order.', 'msa');
		die();
	}

	if ( !isset($_POST['right_order']) ) {
		echo __('Unable to find the right order', 'msa');
		die();
	}

	$dashboard_panel_order = get_option('msa_dashboard_panel_order');

	$dashboard_panel_order['left'] = $_POST['left_order'] == 'empty' ? array() : $_POST['left_order'];
	$dashboard_panel_order['right'] = $_POST['right_order'] == 'empty' ? array() : $_POST['right_order'];

	update_option('msa_dashboard_panel_order', $dashboard_panel_order);

	echo __('Order Saved', 'msa');
	die();


}
add_action('wp_ajax_msa_save_dashboard_panel_order', 'msa_save_dashboard_panel_order' );

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

		$output = '<table class="wp-list-table widefat striped">
			<thead>
				<tr>
					<th>' . __('Attribute', 'msa') . '</th>
					<th>' . __('Value', 'msa') . '</th>
				</tr>
			</thead>
			<tbody>';

			$user = get_userdata($audit['user']);

			$output .= '<tr class="msa-post-status-bg msa-post-status-bg-' . msa_get_score_status($audit['score']) . '"><td>' . __('Score', 'msa') . '</td> <td>' . round(100 * $audit['score']) . '%' . '</td></tr>';
			$output .= '<tr><td>' . __('Name', 'msa') . '</td> <td><a href="' . get_admin_url() . 'admin.php?page=msa-all-audits&audit=' . $audit['id'] . '">' . $audit['name'] . '</a></td></tr>';
			$output .= '<tr><td>' . __('Date', 'msa') . '</td> <td>' . date('M d Y, h:i:s', strtotime($audit['date'])) . '</td></tr>';
			$output .= '<tr><td>' . __('Number of Posts', 'msa') . '</td> <td>' . $audit['num_posts'] . '</td></tr>';
			$output .= '<tr><td>' . __('Created By', 'msa') . '</td> <td>' . $user->display_name . '</td></tr>';

		$output .= '</tbody>
		</table>';

	} else {

		$output = '<p>' . __('You do not have any audits yet.', 'msa') . ' <a href="' . get_admin_url() . 'admin.php?page=msa-all-audits">' . __('Create one now!', 'msa') . '</a></p>';

	}

	return $output;

}

add_filter('msa_dashboard_panel_content_last_audit', 'msa_dashboard_panel_last_audit_content');

/**
 * Create initial dashboard panels
 *
 * @access public
 * @return void
 */
function msa_create_initial_dashboard_panels() {

	// Last Audit

	msa_register_dashboard_panel('last_audit', array(
		'post_box' 	=> 0,
		'title'		=> __('Last Audit', 'msa'),
		'content'	=> '',
	));

	// Activity

	msa_register_dashboard_panel('activity', array(
		'post_box' 	=> 1,
		'title'		=> __('Activity', 'msa'),
		'content'	=> '',
	));

	do_action('msa_register_dashboard_panels');
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

	// If the dashboard panel order is not set then set it to default

	if ( false === ( $dashboard_panel_order = get_option('msa_dashboard_panel_order') ) ) {

		$dashboard_panel_order = array(
			'left' 	=> array(),
			'right'	=> array(),
		);

		foreach ( $msa_dashboard_panels as $key => $msa_dashboard_panel ) {

			if ( $msa_dashboard_panel['post_box'] == 0 && !in_array($key, $dashboard_panel_order['left']) ) {
				$dashboard_panel_order['left'][] = $key;
			}

			if ( $msa_dashboard_panel['post_box'] == 1 ) {
				$dashboard_panel_order['right'][] = $key;
			}
		}

		update_option('msa_dashboard_panel_order', $dashboard_panel_order);
	}

	// Add panels

	foreach ( $msa_dashboard_panels as $key => $msa_dashboard_panel ) {

		if ( !in_array($key, $dashboard_panel_order['left']) && !in_array($key, $dashboard_panel_order['right']) ) {

			if ( $msa_dashboard_panel['post_box'] == 0 ) {
				$dashboard_panel_order['left'][] = $key;
			}

			if ( $msa_dashboard_panel['post_box'] == 1 ) {
				$dashboard_panel_order['right'][] = $key;
			}

		}

		update_option('msa_dashboard_panel_order', $dashboard_panel_order);
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