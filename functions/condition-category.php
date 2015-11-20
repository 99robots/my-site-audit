<?php
/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 11/4/15
 * Package: Functions/Condition Category
 * File: condition-category.php
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
 * Display the conditions within a category
 *
 * @access public
 * @param mixed $category
 * @param mixed $post
 * @param mixed $data
 * @param mixed $score
 * @return void
 */
function msa_display_condition_category_data($category, $post, $data, $score) {

	$total_weight          = msa_get_total_conditions_weight();
	$conditions            = msa_get_conditions_from_category($category);
	$condition_categories  = msa_get_condition_categories();
	$conditions_weight     = msa_get_total_weight_for_conditions($conditions);
	$post_score 		   = $score['data'];
	$overall_score         = 0;

	$output = '<table class="wp-list-table widefat striped posts msa-audit-table">
			<thead>
				<th class="msa-condition-score-col">' . __("Score", 'msa') . '</th>
				<th class="msa-condition-weight-col">' . __("Weight", 'msa') . '</th>
				<th class="msa-condition-attribute-col">' . __("Attribute", 'msa') . '</th>
				<th class="msa-condition-goal-col">' . __("Goal", 'msa') . '</th>
				<th class="msa-condition-value-col">' . __("Value", 'msa') . '</th>
			</thead>

			<tbody>';

				foreach ( $conditions as $key => $condition ) {

					// Goal

					if ( $condition['comparison'] == 1 ) {
						$goal = __('Greater Than ' . $condition['min'], 'msa');
					}

					else if ( $condition['comparison'] == 2 ) {
						$goal = __('Less Than ' . $condition['max'], 'msa');
					}

					else if ( $condition['comparison'] == 3 ) {
						$goal = __('In Between ' . $condition['min'] . ' - ' . $condition['max'], 'msa');
					}

					$score  = apply_filters( 'msa_condition_category_content_score', round( $post_score[$key] * 100 ) . '%', $data, $post, $key );
					$weight = apply_filters( 'msa_condition_category_content_weight', round( ( $condition['weight'] / $total_weight ) * 100 ) . '%', $data, $post, $key );
					$name   = apply_filters( 'msa_condition_category_content_name', $condition['name'], $data, $post, $key );
					$goal   = apply_filters( 'msa_condition_category_content_goal', $goal, $data, $post, $key, $condition);
					$value  = apply_filters( 'msa_condition_category_content_value', $data[$key], $data, $post, $key );

					$more_info = '';

					if ( isset($condition['description']) ) {

						$learn_more = '';

						if ( isset($condition['learn_more_link']) ) {
							$learn_more = '<div class="msa-modal-learn-more-link">
								<a href="' . $condition['learn_more_link'] . '" target="_blank">' . __('Learn More', 'msa') . '</a>
							</div>';
						}

						$more_info = '<span class="msa-condition-more-info" data-condition="' . $key . '">
							<i class="fa fa-info-circle"></i>
							<div class="msa-modal" data-condition="' . $key . '">
								<div class="msa-modal-container">
									<div class="msa-modal-title">
										<h3>' . $condition['name'] . '</h3>
									</div>
									<div class="msa-modal-content">' . $condition['description'] . '</div>' .
									$learn_more .
								'</div>
							</div>
						</span>';
					}

					$output .= '<tr class="msa-post-status msa-post-status-' . msa_get_score_status($post_score[$key]) . '">
						<td class="msa-condition-score">' . $score . '</td>
						<td class="msa-condition-weight">' . $weight . '</td>
						<td class="msa-condition-name">' . $name . $more_info . '</td>
						<td class="msa-condition-goal">' . $goal . '</td>
						<td class="msa-condition-value">' . $value . '</td>
					</tr>';

					$overall_score += $post_score[$key] * $condition['weight'];

				}

			$output .= '</tbody>

		</table>
	</div>';

	$output = '<h3 class="msa-condition-category-heading hndle ui-sortable-handle"><span class="msa-condition-category-score msa-post-status-bg msa-post-status-bg-' . msa_get_score_status( $overall_score / $conditions_weight ) . '">' . round( ( $overall_score / $conditions_weight ) * 100, 2 ) . '%</span> <span>' .  $condition_categories[$category]['name'] . '</span><span style="float:right;">' . __('Weight: ', 'msa') . ' ' . round( ( $conditions_weight / $total_weight ) * 100, 2 ) . '%</span></h3>
		<div class="inside">' .
		$output;

	return $output;
}
add_filter('msa_condition_category_content', 'msa_display_condition_category_data', 10, 4);

/**
 * Create initial conidtion categories
 *
 * @access public
 * @return void
 */
function msa_create_initial_condition_categories() {

	// Content

	msa_register_condition_category('content', array(
		'name'	=> __('Content', 'msa'),
	));

	// Images

	msa_register_condition_category('images', array(
		'name'	=> __('Images', 'msa'),
	));

	// Links

	msa_register_condition_category('links', array(
		'name'	=> __('Links', 'msa'),
	));

	// Headings

	msa_register_condition_category('headings', array(
		'name'	=> __('Headings', 'msa'),
	));

	do_action('msa_register_condition_categories');

}

/**
 * Get the score value for a condition category
 *
 * @access public
 * @param mixed $category
 * @param mixed $score
 * @return void
 */
function msa_get_condition_catergory_score($category, $score) {

	$cat_conditions = msa_get_conditions_from_category($category);
	$cat_score = 0;
	$cat_weight = 0;

	foreach ( $cat_conditions as $key => $cat_condition ) {
		$cat_score += $score[$key] * $cat_condition['weight'];
		$cat_weight += $cat_condition['weight'];
	}

	return $cat_score / $cat_weight;
}

/**
 * Get all the conditions from a specific category
 *
 * @access public
 * @param mixed $category
 * @return void
 */
function msa_get_conditions_from_category($category) {

	if ( !isset($category) || $category == '' ) {
		return array();
	}

	$conditions = msa_get_conditions();
	$conditions_in_cat = array();

	foreach ( $conditions as $key => $condition ) {

		if ( isset($condition['category']) && $category == $condition['category'] ) {
			$conditions_in_cat[$key] = $condition;
		}
	}

	return apply_filters('msa_get_conditions_from_category', $conditions_in_cat) ;
}

/**
 * Get all the condition categories
 *
 * @access public
 * @return void
 */
function msa_get_condition_categories() {

	global $msa_condition_categories;

	if ( ! is_array( $msa_condition_categories ) ) {
		$msa_condition_categories = array();
	}

	return apply_filters('msa_get_condition_categories', $msa_condition_categories);
}

/**
 * Register a new condition category
 *
 * @access public
 * @param mixed $condition_category
 * @param array $args (default: array())
 * @return void
 */
function msa_register_condition_category( $condition_category, $args = array() ) {

	global $msa_condition_categories;

	if ( ! is_array( $msa_condition_categories ) ) {
		$msa_condition_categories = array();
	}

	// Default condition category

	$default = array(
		'name'			=> __('Condition Category', 'msa'),
	);

	$args = array_merge($default, $args);

	// Add the condition to the global condition categories array

	$msa_condition_categories[ $condition_category ] = apply_filters('msa_register_condition_category_args', $args);

	/**
	* Fires after a condition category is registered.
	*
	* @param string $condition_category Condition.
	* @param array $args      Arguments used to register the condition.
	*/
	do_action('msa_registed_condition_category', $condition_category, $args);

	return $args;
}