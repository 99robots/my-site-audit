<?php
/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
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
 * Show the share count for a specific post
 *
 * @access public
 * @static
 * @return void
 */
function msa_share_count_post( $export = false, $post = null ) {

	// Parse data based on HTTP call

	if ( !$export ) {
		$data_start_date = isset($_POST['start_date']) ? urldecode($_POST['start_date']) : null;
		$data_end_date   = isset($_POST['end_date']) ? urldecode($_POST['end_date']) : null;
		$data_post_types = isset($_POST['post_types']) ? urldecode($_POST['post_types']) : null;
		$data_user_roles = isset($_POST['user_roles']) ? urldecode($_POST['user_roles']) : null;
		$data_author     = isset($_POST['author']) ? $_POST['author'] : '';
		$post			 = isset($_POST['post']) ? $_POST['post'] : null;
	} else {
		$data_start_date = isset($_GET[self::$prefix_dash . 'start-date']) ? urldecode($_GET[self::$prefix_dash . 'start-date']) : null;
		$data_end_date   = isset($_GET[self::$prefix_dash . 'end-date']) ? urldecode($_GET[self::$prefix_dash . 'end-date']) : null;
		$data_post_types = isset($_GET[self::$prefix_dash . 'post-types']) ? urldecode($_GET[self::$prefix_dash . 'post-types']) : null;
		$data_user_roles = isset($_GET[self::$prefix_dash . 'user-roles']) ? urldecode($_GET[self::$prefix_dash . 'user-roles']) : null;
		$data_author     = isset($_GET['author']) ? $_GET['author'] : '';
	}

	// Check if author data was passed

	if ( !$export && !isset($post) ) {
		echo json_encode(array(
			'count'	=> __('No post found', 'msa'),
		));
		die();
	}

	// Get global settings

	$settings = get_option(self::$prefix . 'settings');

	if ( $settings == false ) {
		$settings = self::default_options();
	}

	// Get share count api key

	$share_count_api_key = '';

	if ( isset($settings['shared_count_api_key']) ) {
		$share_count_api_key = $settings['shared_count_api_key'];
	}

	// Get the share count

	if ( false === ( $share_count = get_transient( self::$prefix . 'single_share_count_' . $post ) ) ) {
		$share_count = 0;

		// Make sure the share count option is set to ture

		if ( isset($settings['use_shared_count']) && $settings['use_shared_count'] &&
			isset($share_count_api_key) && $share_count_api_key != '' ) {

			$url = 'https://free.sharedcount.com/url?url=' . get_the_permalink($post) . '&apikey=' . $share_count_api_key;

			$ch = curl_init();

		    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
		    curl_setopt($ch, CURLOPT_HEADER, 0);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt($ch, CURLOPT_URL, $url);
		    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

		    $share_data = json_decode(curl_exec($ch));
		    curl_close($ch);

		    foreach ( (array) $share_data as $data ) {

			    if ( !is_array($data) && !is_object($data)) {
				    $share_count = (int) $data;
			    }
		    }

		    // Save data for faster load times later

			set_transient( self::$prefix . 'single_share_count_' . $post , $share_count, 60 * MINUTE_IN_SECONDS );

		}
	}

	// Export Share Count

	if ( $export ) {
		return $share_count;
	}

	// Return the share count

	if ( isset($share_count) ) {
		echo json_encode(array(
			'post'		=> $_POST['post'],
			'count'		=> $share_count,
		));
		die();
	}

	echo json_encode(array(
		'post'		=> $_POST['post'],
		'count'		=> __('No data', 'msa'),
	));
	die();

}

add_action('wp_ajax_msa_share_count_post', 'msa_share_count_post');

/**
 * Test the shared count api
 *
 * @access public
 * @static
 * @return void
 */
function msa_shared_count_test() {

	// Check if the API key is set

	if ( !isset($_POST['api_key']) ) {

		echo json_encode(array(
		   'status'		=> 'warning',
		   'message'	=> __('API Key was not found', 'msa'),
		));

		die();
	}

	// Grab data

	$url = 'https://free.sharedcount.com/url?url=' . get_site_url() . '&apikey=' . $_POST['api_key'];
	$ch = curl_init();

    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

    $share_data = json_decode(curl_exec($ch));
    curl_close($ch);

    if ( is_object($share_data) && isset($share_data->Error) ) {
	   echo json_encode(array(
		   'status'		=> 'warning',
		   'message'	=> $share_data->Error
	   ));

	   die();
    }

   echo json_encode(array(
	   'status'		=> 'check',
	   'message'	=> __('API Key is valid', 'msa'),
   ));

   die();
}

add_action('wp_ajax_msa_shared_count_test', 'msa_shared_count_test');