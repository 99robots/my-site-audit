<?php
/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 10/29/15
 * Package: Functions/Settings Tab
 * File: settings-tab.php
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
 * Save the conditions data
 *
 * @access public
 * @param mixed $data
 * @return void
 */
function msa_settings_tab_conditions_save($data) {

	// Check if we have data already saved

	if ( false === ( $settings = get_option('msa_conditions') ) ) {
		$settings = array();
	}

	// Save the data

	$conditions = msa_get_conditions();

	foreach ( $conditions as $key => $condition ) {

		if ( isset($condition['settings']) ) {

			// Weight

			if ( isset($data['msa-condition-' . $key . '-weight']) && !empty($data['msa-condition-' . $key . '-weight']) ) {
				$settings[$key]['weight'] = $data['msa-condition-' . $key . '-weight'];
			}

			// Comparison

			if ( isset($data['msa-condition-' . $key . '-comparison']) && !empty($data['msa-condition-' . $key . '-comparison']) ) {
				$settings[$key]['comparison'] = $data['msa-condition-' . $key . '-comparison'];
			}

			// Value

			if ( isset($data['msa-condition-' . $key . '-value']) ) {
				$settings[$key]['value'] = $data['msa-condition-' . $key . '-value'];
			}

			// Min

			if ( isset($data['msa-condition-' . $key . '-min']) && !empty($data['msa-condition-' . $key . '-min']) ) {
				$settings[$key]['min'] = $data['msa-condition-' . $key . '-min'];
			}

			// Max

			if ( isset($data['msa-condition-' . $key . '-max']) && !empty($data['msa-condition-' . $key . '-max']) ) {
				$settings[$key]['max'] = $data['msa-condition-' . $key . '-max'];
			}

		}

	}

	update_option('msa_conditions', $settings);

}
add_action('msa_save_settings', 'msa_settings_tab_conditions_save', 10, 1);

/**
 * The Conditions Tab Content
 *
 * @access public
 * @return void
 */
function msa_settings_tab_conditions_content() {

	// Check if we have data already saved

	if ( false === ( $settings = get_option('msa_conditions') ) ) {
		$settings = array();
	}

	$conditions = msa_get_conditions();

	$output = '';

	foreach ( $conditions as $key => $condition ) {

		if ( !isset($condition['settings']) ) {
			continue;
		}

		$setting      = $condition['settings'];
		$weight       = isset($settings[$key]['weight']) 		? $settings[$key]['weight'] 	: $condition['weight'];
		$comparison   = isset($settings[$key]['comparison']) 	? $settings[$key]['comparison'] : $condition['comparison'];
		$value        = isset($settings[$key]['value']) 		? $settings[$key]['value'] 		: $condition['value'];
		$min          = isset($settings[$key]['min']) 			? $settings[$key]['min'] 		: $condition['min'];
		$max          = isset($settings[$key]['max']) 			? $settings[$key]['max'] 		: $condition['max'];

		$output .= '<h3>' . $condition['name'] . '</h3>';

		$output .= '<table class="form-table">
			<tbody>

				<tr>
					<th scope="row"><label for="' . $setting['id'] . '">' . __('Weight', 'msa') . '</label></th>
					<td>
						<input type="text" class="regular-text ' . $setting['class'] . '-weight" id="' . $setting['id'] . '-weight" name="' . $setting['name'] . '-weight" value="' . $weight . '">
						<p class="description">' . __('How important is this condition to the score of the audit?  The higher the value the more important.') . '</p>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="' . $setting['id'] . '">' . __('Comparison', 'msa') . '</label></th>
					<td>
						<select class="' . $setting['class'] . '-comparison" id="' . $setting['id'] . '-comparison" name="' . $setting['name'] . '-comparison">
							<option value="0" ' . selected('0', $comparison, false) . '>' . __('Greater Than') . '</option>
							<option value="1" ' . selected('1', $comparison, false) . '>' . __('Less Than') . '</option>
							<option value="2" ' . selected('2', $comparison, false) . '>' . __('In Between') . '</option>
						</select>
						<p class="description">' . __('The type of comparison you want to make against your post data.') . '</p>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="' . $setting['id'] . '">' . __('Value', 'msa') . '</label></th>
					<td>
						<select class="' . $setting['class'] . '-value" id="' . $setting['id'] . '-value" name="' . $setting['name'] . '-value">
							<option value="0" ' . selected('0', $value, false) . '>' . __('Pass or Fail') . '</option>
							<option value="1" ' . selected('1', $value, false) . '>' . __('Percentage') . '</option>
						</select>
						<p class="description">' . __('The resulting value for that post attribute.') . '</p>
					</td>
				</tr>';

				if ( isset($condition['min']) && $condition['min'] != '' ) {

				$output .= '<tr>
					<th scope="row"><label for="' . $setting['id'] . '">' . __('Minimum Value', 'msa') . '</label></th>
					<td>
						<input type="text" class="regular-text ' . $setting['class'] . '-min" id="' . $setting['id'] . '-min" name="' . $setting['name'] . '-min" value="' . $min . '">
						<p class="description">' . $setting['description-min'] . '</p>
					</td>
				</tr>';

				}

				if ( isset($condition['max']) && $condition['max'] != '' ) {

				$output .= '<tr>
					<th scope="row"><label for="' . $setting['id'] . '">' . __('Maximum Value', 'msa') . '</label></th>
					<td>
						<input type="text" class="regular-text ' . $setting['class'] . '-max" id="' . $setting['id'] . '-max" name="' . $setting['name'] . '-max" value="' . $max . '">
						<p class="description">' . $setting['description-max'] . '</p>
					</td>
				</tr>';

				}

			$output .= '</tbody>
		</table>';

	}

	return $output;

}
add_filter('msa_settings_tab_conditions', 'msa_settings_tab_conditions_content');

/**
 * Create the initial settings tabs
 *
 * @access public
 * @return void
 */
function msa_create_initial_settings_tabs() {

	// Settings

	msa_register_settings_tabs('settings', array(
		'id'		=> 'settings',
		'current'	=> true,
		'tab'		=> __('Settings', 'msa'),
		'content'	=> apply_filters('msa_settings_tab_settings', ''),
	));

	// Conditions

	msa_register_settings_tabs('conditions', array(
		'id'		=> 'conditions',
		'current'	=> false,
		'tab'		=> __('Conditions', 'msa'),
		'content'	=> apply_filters('msa_settings_tab_conditions', ''),
	));

}

/**
 * Get the settings tabs
 *
 * @access public
 * @return void
 */
function msa_get_settings_tabs() {

	global $msa_settings_tabs;

	if ( ! is_array( $msa_settings_tabs ) ) {
		$msa_settings_tabs = array();
	}

	return apply_filters('msa_get_settings_tabs', $msa_settings_tabs);

}

/**
 * Register a new Settings Tab
 *
 * @access public
 * @param mixed $tab
 * @param array $args (default: array())
 * @return void
 */
function msa_register_settings_tabs($tab, $args = array()) {

	global $msa_settings_tabs;

	if ( ! is_array( $msa_settings_tabs ) ) {
		$msa_settings_tabs = array();
	}

	// Default tab

	$default = array(
		'tab'			=> __('Tab', 'msa'),
	);

	$args = array_merge($default, $args);

	// Add the tab to the global dashboard tabs array

	$msa_settings_tabs[ $tab ] = apply_filters('msa_register_settings_tab_args', $args);

	/**
	* Fires after a dashboard tab is registered.
	*
	* @param string $tab 	  Settings Tab.
	* @param array $args      Arguments used to register the dashboard tab.
	*/
	do_action('msa_registed_settings_tab', $tab, $args);

	return $args;

}