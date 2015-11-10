<?php
/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 10/28/15
 * Package: Functions/Score Status
 * File: score-status.php
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
 * Get the status of a score
 *
 * @access public
 * @param mixed $score
 * @return void
 */
function msa_get_score_status($score) {

	$score_statuses = msa_get_score_statuses();

	foreach ( $score_statuses as $key => $score_status ) {

		if ( round($score, 4) >= $score_status['low'] && round($score, 4) <= $score_status['high'] ) {
			return $key;
		}
	}

	return null;

}

/**
 * Get the letter grade for this score
 *
 * @access public
 * @param mixed $score
 * @return void
 */
function msa_get_letter_grade($score) {

	if ( $score >= .96667 ) {
		$grade = 'A+';
	} else if ( $score >= .93334 ) {
		$grade = 'A';
	} else if ( $score >= .90 ) {
		$grade = 'A-';
	} else if ( $score >= .86667 ) {
		$grade = 'B+';
	} else if ( $score >= .83334 ) {
		$grade = 'B';
	} else if ( $score >= .80 ) {
		$grade = 'B-';
	} else if ( $score >= .76667 ) {
		$grade = 'C+';
	} else if ( $score >= .73334 ) {
		$grade = 'C';
	} else if ( $score >= .70 ) {
		$grade = 'C-';
	} else if ( $score >= .66667 ) {
		$grade = 'D+';
	} else if ( $score >= .63334 ) {
		$grade = 'D';
	} else if ( $score >= .60 ) {
		$grade = 'D-';
	} else {
		$grade = 'F';
	}

	return $grade;

}

/**
 * Get the number of posts within an audit that have a certain status
 *
 * @access public
 * @param mixed $posts
 * @param mixed $status
 * @return void
 */
function msa_get_post_count_by_status($posts, $status) {

	$score_statuses = msa_get_score_statuses();
	$score_status = $score_statuses[$status];

	$count = 0;

	foreach ( $posts as $key => $item ) {

		$score = $item['data']['score'];

		if ( $score['score'] >= $score_status['low'] && $score['score'] <= $score_status['high'] ) {
			$count++;
		}
	}

	return $count;
}

/**
 * Create initial score statuses
 *
 * @access public
 * @return void
 */
function msa_create_initial_score_statuses() {

	// Bad

	msa_register_score_status('bad', array(
		'name' 			=> __('Bad', 'msa'),
		'low'			=> 0,
		'high'			=> 0.3333,
	));

	// Poor

	msa_register_score_status('poor', array(
		'name' 			=> __('Poor', 'msa'),
		'low'			=> 0.3334,
		'high'			=> 0.5000,
	));


	// Ok

	msa_register_score_status('ok', array(
		'name' 			=> __('Ok', 'msa'),
		'low'			=> 0.5001,
		'high'			=> 0.6666,
	));


	// Good

	msa_register_score_status('good', array(
		'name' 			=> __('Good', 'msa'),
		'low'			=> 0.6667,
		'high'			=> 0.8333,
	));


	// Great

	msa_register_score_status('great', array(
		'name' 			=> __('Great', 'msa'),
		'low'			=> 0.8334,
		'high'			=> 1,
	));


	do_action('msa_register_score_status');
}

/**
 * Get all the score statuses
 *
 * @access public
 * @return void
 */
function msa_get_score_statuses() {

	global $msa_score_statuses;

	if ( ! is_array( $msa_score_statuses ) ) {
		$msa_score_statuses = array();
	}

	return apply_filters('msa_get_score_statuses', $msa_score_statuses);
}

/**
 * Register a new score status
 *
 * @access public
 * @param mixed $score_status
 * @param array $args (default: array())
 * @return void
 */
function msa_register_score_status( $score_status, $args = array() ) {

	global $msa_score_statuses;

	if ( ! is_array( $msa_score_statuses ) ) {
		$msa_score_statuses = array();
	}

	// Default score status

	$default = array(
		'name'			=> __('Bad', 'msa'),
		'high'			=> 0.1667,
		'low'			=> 0,
	);

	$args = array_merge($default, $args);

	// Add the score status to the global score statuses array

	$msa_score_statuses[ $score_status ] = apply_filters('msa_register_score_status_args', $args);

	/**
	* Fires after a score status is registered.
	*
	* @param string $score_status Score Status.
	* @param array $args      Arguments used to register the score status.
	*/
	do_action('msa_registed_score_status', $score_status, $args);

	return $args;
}