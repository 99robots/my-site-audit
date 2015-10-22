<?php
/*
Plugin Name: Content Audit by 99 Robots
plugin URI:
Description:
version: 1.0.0
Author: 99 Robots
Author URI: https://99robots.com
License: GPL2
*/

/* ===================================================================
 *
 * 99 Robots https://99robots.com
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

// Item Name

if ( !defined('NNROBOTS_CONTENT_AUDIT_ITEM_NAME') ) {
	define('NNROBOTS_CONTENT_AUDIT_ITEM_NAME', 'Content Audit');
}

// Store URL

if ( !defined('NNROBOTS_CONTENT_AUDIT_STORE_URL') ) {
	define('NNROBOTS_CONTENT_AUDIT_STORE_URL', 'https://99robots.com');
}

// Minimum PHP version

if ( !defined('NNROBOTS_CONTENT_AUDIT_MIN_PHP_VERSION') ) {
	define('NNROBOTS_CONTENT_AUDIT_MIN_PHP_VERSION', '5.4.0');
}

// Plugin Name

if ( !defined('NNROBOTS_CONTENT_AUDIT_PLUGIN_NAME') ) {
	define('NNROBOTS_CONTENT_AUDIT_PLUGIN_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));
}

// Plugin directory

if ( !defined('NNROBOTS_CONTENT_AUDIT_PLUGIN_DIR') ) {
	define('NNROBOTS_CONTENT_AUDIT_PLUGIN_DIR', plugin_dir_path(__FILE__) );
}

// Plugin url

if ( !defined('NNROBOTS_CONTENT_AUDIT_PLUGIN_URL') ) {
	define('NNROBOTS_CONTENT_AUDIT_PLUGIN_URL', plugins_url() . '/' . NNROBOTS_CONTENT_AUDIT_PLUGIN_NAME);
}

// Plugin verison

if ( !defined('NNROBOTS_CONTENT_AUDIT_VERSION_NUM') ) {
	define('NNROBOTS_CONTENT_AUDIT_VERSION_NUM', '1.0.0');
}

if ( !class_exists('NNR_Content_Audit') ) :

// Set default timezone

date_default_timezone_set(timezone_name_from_abbr(null, (int) get_option('gmt_offset') * 3600 , true));

register_activation_hook( __FILE__, 							array('NNR_Content_Audit', 'register_activation'));
add_action('init', 												array('NNR_Content_Audit', 'init'));
add_filter('plugin_action_links_' . plugin_basename(__FILE__),  array('NNR_Content_Audit', 'settings_link'));
add_action('admin_menu', 										array('NNR_Content_Audit', 'menu'));
add_action('add_meta_boxes', 									array('NNR_Content_Audit', 'add_meta_box'));

add_action('wp_ajax_nnr_ca_share_count_post', 					array('NNR_Content_Audit', 'share_count_post'));
add_action('wp_ajax_nnr_ca_shared_count_test', 					array('NNR_Content_Audit', 'shared_count_test'));

/**
 * NNR_Content_Audit class.
 */
class NNR_Content_Audit {

	/**
	 * prefix
	 *
	 * (default value: 'nnr_content_audit_')
	 *
	 * @var string
	 * @access public
	 * @static
	 */
	static $prefix = 'nnr_content_audit_';

	/**
	 * prefix_dash
	 *
	 * (default value: 'nnr-ca-')
	 *
	 * @var string
	 * @access public
	 * @static
	 */
	static $prefix_dash = 'nnr-ca-';

	/**
	 * text_domain
	 *
	 * (default value: '99robots-content-audit')
	 *
	 * @var string
	 * @access public
	 * @static
	 */
	static $text_domain = '99robots-content-audit';

	/**
	 * dashboard_page
	 *
	 * (default value: 'nnr-ca-dashboard-page')
	 *
	 * @var string
	 * @access public
	 * @static
	 */
	static $dashboard_page = 'nnr-ca-dashboard-page';

	/**
	 * settings_page
	 *
	 * (default value: 'nnr-ca-settings-page')
	 *
	 * @var string
	 * @access public
	 * @static
	 */
	static $settings_page = 'nnr-ca-settings-page';

	/**
	 * updates_page
	 *
	 * (default value: 'nnr-ca-updates-page')
	 *
	 * @var string
	 * @access public
	 * @static
	 */
	static $updates_page = 'nnr-ca-updates-page';

	/**
	 * Runs on the init hook
	 *
	 * @since 1.0.0
	 */
	static function init() {
		load_plugin_textdomain(self::$text_domain, false, basename( dirname( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Performs tasks needed upon activation
	 *
	 * @since 1.0.0
	 */
	static function register_activation() {

		// Check if multisite, if so then save as site option

		if ( function_exists('is_multisite') && is_multisite() ) {
			update_site_option(self::$prefix . 'version', NNROBOTS_CONTENT_AUDIT_VERSION_NUM);
		} else {
			update_option(self::$prefix . 'version', NNROBOTS_CONTENT_AUDIT_VERSION_NUM);
		}
	}

	/**
	 * Hooks to 'plugin_action_links_' filter
	 *
	 * @since 1.0.0
	 */
	static function settings_link($links) {
		$settings_link = '<a href="' . get_admin_url() . 'options-general.php?page=' . self::$settings_page . '">' . __('Settings', self::$text_domain) . '</a>';
		array_unshift($links, $settings_link);
		return $links;
	}

	/**
	 * Hooks intot the 'admin_menu' hook to show the settings page
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	static function menu() {

		// Dashboard

		add_menu_page(
			__(NNROBOTS_CONTENT_AUDIT_ITEM_NAME, self::$text_domain),	// Page Title
			__(NNROBOTS_CONTENT_AUDIT_ITEM_NAME, self::$text_domain), 	// Menu Name
	    	'manage_options', 											// Capabilities
	    	self::$dashboard_page, 										// slug
	    	array('NNR_Content_Audit', 'dashboard'),					// Callback function
	    	plugin_dir_url(__FILE__) . 'img/logo.png" style="width:20px;padding-top: 6px;'
	    );

	    // Dashboard

	    $dashboard_page_load = add_submenu_page(
	    	self::$dashboard_page, 												// parent slug
	    	__('Content Audit', self::$text_domain), 							// Page title
	    	__('Content Audit', self::$text_domain), 							// Menu name
	    	'manage_options', 													// Capabilities
	    	self::$dashboard_page, 												// slug
	    	array('NNR_Content_Audit', 'dashboard')								// Callback function
	    );
	    add_action("admin_print_scripts-$dashboard_page_load", array('NNR_Content_Audit', 'dashboard_scripts'));

		$settings_page_load = add_submenu_page(
	    	self::$dashboard_page,
	    	__('Settings', self::$text_domain),
	    	__('Settings', self::$text_domain),
	    	'manage_options',
	    	self::$settings_page,
	    	array('NNR_Content_Audit', 'settings')
	    );
	    add_action("admin_print_scripts-$settings_page_load" , array('NNR_Content_Audit', 'settings_scripts'));

		$update_page_load = add_submenu_page(
	    	self::$dashboard_page,
	    	__('Update', self::$text_domain),
	    	__('Update', self::$text_domain),
	    	'manage_options',
	    	self::$updates_page,
	    	array('NNR_Content_Audit', 'updates')
	    );
	    add_action("admin_print_scripts-$update_page_load" , array('NNR_Content_Audit', 'updates_scripts'));
	}

	/**
	 * Hooks into the 'admin_print_scripts-$page' to inlcude the scripts for the settings page
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	static function dashboard_scripts() {

		// Style

		wp_enqueue_style(self::$prefix . 'settings_css', 			NNROBOTS_CONTENT_AUDIT_PLUGIN_URL . '/css/settings.css');
		wp_enqueue_style(self::$prefix . 'bootstrap_css', 			NNROBOTS_CONTENT_AUDIT_PLUGIN_URL . '/css/nnr-bootstrap.min.css');
		wp_enqueue_style(self::$prefix . 'bootstrap_sortable_css', 	NNROBOTS_CONTENT_AUDIT_PLUGIN_URL . '/css/bootstrap-sortable.css');
		wp_enqueue_style(self::$prefix . 'fontawesome_css', 		NNROBOTS_CONTENT_AUDIT_PLUGIN_URL . '/css/font-awesome.min.css');

		// Script

		wp_enqueue_script(self::$prefix . 'bootstrap_js', 			NNROBOTS_CONTENT_AUDIT_PLUGIN_URL . '/js/bootstrap.min.js');
		wp_enqueue_script(self::$prefix . 'bootstrap_sortable_js', 	NNROBOTS_CONTENT_AUDIT_PLUGIN_URL . '/js/bootstrap-sortable.js');

		wp_enqueue_script(self::$prefix . 'dashboard_js', 			NNROBOTS_CONTENT_AUDIT_PLUGIN_URL . '/js/dashboard.js', array('jquery', 'jquery-ui-sortable'));
		wp_localize_script(self::$prefix . 'dashboard_js', 			'nnr_ca_data', array(
			'prefix'	=> self::$prefix_dash,
		) );

	}

	/**
	 * Hooks into the 'admin_print_scripts-$page' to inlcude the scripts for the settings page
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	static function settings_scripts() {

		// Style

		wp_enqueue_style(self::$prefix . 'settings_css', 			NNROBOTS_CONTENT_AUDIT_PLUGIN_URL . '/css/settings.css');
		wp_enqueue_style(self::$prefix . 'bootstrap_css', 			NNROBOTS_CONTENT_AUDIT_PLUGIN_URL . '/css/nnr-bootstrap.min.css');
		wp_enqueue_style(self::$prefix . 'bootstrap_sortable_css', 	NNROBOTS_CONTENT_AUDIT_PLUGIN_URL . '/css/bootstrap-sortable.css');
		wp_enqueue_style(self::$prefix . 'fontawesome_css', 		NNROBOTS_CONTENT_AUDIT_PLUGIN_URL . '/css/font-awesome.min.css');

		// Script

		wp_enqueue_script(self::$prefix . 'bootstrap_js', 			NNROBOTS_CONTENT_AUDIT_PLUGIN_URL . '/js/bootstrap.min.js');
		wp_enqueue_script(self::$prefix . 'bootstrap_sortable_js', 	NNROBOTS_CONTENT_AUDIT_PLUGIN_URL . '/js/bootstrap-sortable.js');
		wp_enqueue_script(self::$prefix . 'settings_js', 			NNROBOTS_CONTENT_AUDIT_PLUGIN_URL . '/js/settings.js', array('jquery', 'jquery-ui-sortable'));
		wp_localize_script(self::$prefix . 'settings_js', 			'nnr_ca_data', array(
			'prefix'	=> self::$prefix_dash,
		) );

	}

	/**
	 * Hooks into the 'admin_print_scripts-$page' to inlcude the scripts for the settings page
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	static function updates_scripts() {

		wp_enqueue_style(self::$prefix . 'settings_css', 			NNROBOTS_CONTENT_AUDIT_PLUGIN_URL . '/css/settings.css');
		wp_enqueue_style(self::$prefix . 'bootstrap_css', 			NNROBOTS_CONTENT_AUDIT_PLUGIN_URL . '/css/nnr-bootstrap.min.css');
		wp_enqueue_style(self::$prefix . 'fontawesome_css', 		NNROBOTS_CONTENT_AUDIT_PLUGIN_URL . '/css/font-awesome.min.css');

		wp_enqueue_script(self::$prefix . 'updates_js',  			NNROBOTS_CONTENT_AUDIT_PLUGIN_URL . '/js/updates.js', array('jquery'));
	    wp_localize_script(self::$prefix . 'updates_js', 			'nnr_ca_updates_data', array(
			'prefix_dash' 		=> self::$prefix_dash,
			'activate_text'		=> __('Activate', self::$text_domain),
			'deactivate_text'	=> __('Deactivate', self::$text_domain),
			'no_license_key'		=> __('Please add or activate your License Key to get automatic updates.  Without a valid license key you will not receive regular updates. You can find your license key', self::$text_domain) . ' <a href="https://99robots.com/dashboard" target="_blank">here</a>',
			'expired'				=> __('EXPIRED: This license key has expired.  Please renew your license key', self::$text_domain) . ' <a href="https://99robots.com/dashboard" target="_blank">here</a>',
			'activation_error'		=> __('INVALID LICENSE KEY: The license key is not valid please try again. You can find your license key', self::$text_domain) . ' <a href="https://99robots.com/dashboard" target="_blank">here</a>.',
			'activation_valid'		=> __('SUCCESS: Your license key is valid.', self::$text_domain),
			'deactivation_valid'	=> __('SUCCESS: This site has been deactivated.', self::$text_domain),
			'deactivation_error'	=> __('DEACTIVATION FAILED: This site could not be deactivated.', self::$text_domain),
		) );

	}

	/**
	 * This is the main function for the dashboard page
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	static function dashboard() {

		$settings = self::get_settings();

		include_once('views/dashboard.php');

	}

	/**
	 * This is the main function for the settings page
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	static function settings() {

		$settings = self::get_settings();

		// Save the settings

		if ( isset($_POST['submit']) && check_admin_referer(self::$prefix . 'settings') ) {

			$settings['shared_count_api_key'] = isset($_POST[self::$prefix_dash . 'shared-count-api-key']) ? sanitize_text_field($_POST[self::$prefix_dash . 'shared-count-api-key']) : '';
			$settings['use_shared_count'] = isset($_POST[self::$prefix_dash . 'use-shared-count']) && $_POST[self::$prefix_dash . 'use-shared-count'] ? true : false;

			self::update_settings($settings);
		}

		include_once('views/settings.php');
	}

	/**
	 * This is the main function for the updates page
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	static function updates() {

		// Get the settings

		$license_key = self::get_license_key();

		global $wp_version;

		include_once('views/updates.php');
	}

	/**
	 * Add a meta box to the post screen
	 *
	 * @access public
	 * @return void
	 */
	static function add_meta_box() {

		add_meta_box(
			self::$prefix_dash . 'meta-box',
			__('Content Audit', self::$text_domain ),
			array('NNR_Content_Audit', 'meta_box_callback')
		);
	}

	/**
	 * Prints the box content.
	 *
	 * @access public
	 * @param mixed $post
	 * @return void
	 */
	static function meta_box_callback( $post ) {

		echo '<table class="wp-list-table widefat fixed striped posts">';
			echo '<thead>';
				echo '<th style="width:20%">' . __('Attribute', self::$text_domain) . '</th>';
				echo '<th>' . __('Value', self::$text_domain) . '</th>';
			echo '</thead>';
			echo '<tbody>';

			self::show_audit_data($post, 'inline');

			echo '</tbody>';
		echo '</table>';


	}

	/**
	 * Show the audit data for a post
	 *
	 * @access public
	 * @param mixed $post
	 * @param string $format (default: 'table')
	 * @return void
	 */
	static function show_audit_data($post, $settings, $format = 'table' ) {

		$user_info = get_userdata($post->post_author);

		preg_match_all('/<h([1-6])/', $post->post_content, $matches);
		$headings = count($matches[0]);

		preg_match_all('/<h1/', $post->post_content, $matches);
		$h1 = count($matches[0]);

		preg_match_all('/<h2/', $post->post_content, $matches);
		$h2 = count($matches[0]);

		preg_match_all('/<h3/', $post->post_content, $matches);
		$h3 = count($matches[0]);

		preg_match_all('/<h4/', $post->post_content, $matches);
		$h4 = count($matches[0]);

		preg_match_all('/<h5/', $post->post_content, $matches);
		$h5 = count($matches[0]);

		preg_match_all('/<h6/', $post->post_content, $matches);
		$h6 = count($matches[0]);

		preg_match_all("/<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>/siU", $post->post_content, $link_matches, PREG_SET_ORDER);

		$internal_links = 0;
		$external_links = 0;

		if ( isset($link_matches) && is_array($link_matches) ) {
			foreach ( $link_matches as $link ) {
				$url = parse_url($link[2]);
				$site_url = parse_url(get_site_url());

				if ( isset($site_url['host']) && isset($url['host']) && $site_url['host'] == $url['host'] ) {
					$internal_links++;
				} else {
					$external_links++;
				}
			}
		}

		$content = preg_replace("/&#?[a-z0-9]{2,8};/i","", $post->post_content);
		$content = strip_tags($content);
		$content = preg_replace("~(?:\[/?)[^/\]]+/?\]~s", '', $content);

		if ( $post->ID == 303 ) {
			//error_log($content);
		}

		$meta_desc = get_post_meta($post->ID, '_yoast_wpseo_metadesc', true);
		$focuskw = get_post_meta($post->ID, '_yoast_wpseo_focuskw', true);

		$score_label = 'na';
		$score = get_post_meta($post->ID, '_yoast_wpseo_linkdex', true);

		if ( file_exists(WP_PLUGIN_DIR . '/wordpress-seo/inc/class-wpseo-utils.php') ) {
			include_once(WP_PLUGIN_DIR. '/wordpress-seo/inc/class-wpseo-utils.php');

			if ( $score !== '' ) {
				$nr          = WPSEO_Utils::calc( $score, '/', 10, true );
				$score_label = WPSEO_Utils::translate_score( $nr );
				$title       = WPSEO_Utils::translate_score( $nr, false );
				unset( $nr );
			}
		}

		if ( $format == 'table' ) {
			echo '<tr>';
				echo '<td>' . $post->ID . '</td>';
				echo '<td>' . $post->post_title . '</td>';
				echo '<td>' . strlen($post->post_title) . '</td>';
				echo '<td><a href="' . get_permalink($post->ID) . '" target="_blank">/' . $post->post_name . '</a></td>';
				echo '<td data-value="' . strtotime($post->post_date) . '">' . date('M j, Y', strtotime($post->post_date)) . '</td>';
				echo '<td data-value="' . strtotime($post->post_modified) . '">' . date('M j, Y', strtotime($post->post_modified)) . '</td>';
				echo '<td>' . str_word_count($content) . '</td>';
				echo '<td>' . $post->comment_count . '</td>';
				echo '<td><div class="wpseo-score-icon ' . esc_attr( $score_label ) . '"></div></td>';
				echo '<td>' . $focuskw . '</td>';
				echo '<td>' . $meta_desc . '</td>';
				echo '<td>' . strlen($meta_desc) . '</td>';
				echo '<td>' . ( $internal_links + $external_links ) . '</td>';
				echo '<td>' . $internal_links . '</td>';
				echo '<td>' . $external_links . '</td>';

				if ( isset($settings['use_shared_count']) && $settings['use_shared_count'] ) {
					echo '<td class="' . self::$prefix_dash . 'share-count" data-post="' . $post->ID . '"><i class="fa fa-refresh fa-spin"></i></td>';
				}

				echo '<td>' . substr_count($post->post_content, '<img') . '</td>';
				echo '<td>' . $headings . '</td>';
				echo '<td>' . $h1 . '</td>';
				echo '<td>' . $h2 . '</td>';
				echo '<td>' . $h3 . '</td>';
				echo '<td>' . $h4 . '</td>';
				echo '<td>' . $h5 . '</td>';
				echo '<td>' . $h6 . '</td>';
			echo '</tr>';
		} else {

			echo '<tr>';
				echo '<td>' . __('Internal Links', self::$text_domain) . '</td>';
				echo '<td><ul>';
					if ( isset($link_matches) && is_array($link_matches) ) {
						foreach ( $link_matches as $link ) {
							$url = parse_url($link[2]);
							$site_url = parse_url(get_site_url());

							if ( $site_url['host'] == $url['host'] ) {
								echo '<li style="list-style: disc;"><a href="' . $link[2] . '" target="_blank">' . $link[2] . '</a></li>';
							}
						}
					}
				echo '</ul></td>';
			echo '</tr>';

			echo '<tr>';
				echo '<td>' . __('External Links', self::$text_domain) . '</td>';
				echo '<td><ul>';
					if ( isset($link_matches) && is_array($link_matches) ) {
						foreach ( $link_matches as $link ) {
							$url = parse_url($link[2]);
							$site_url = parse_url(get_site_url());

							if ( $site_url['host'] != $url['host'] ) {
								echo '<li style="list-style: disc;"><a href="' . $link[2] . '" target="_blank">' . $link[2] . '</a></li>';
							}
						}
					}
				echo '</ul></td>';
			echo '</tr>';

			echo '<tr>';
				echo '<td>' . __('Images', self::$text_domain) . '</td>';
				echo '<td>' . substr_count($post->post_content, '<img') . '</td>';
			echo '</tr>';

			echo '<tr>';
				echo '<td>' . __('Headings', self::$text_domain) . '</td>';
				echo '<td>' . $headings . '</td>';
			echo '</tr>';
		}

	}

	/**
	 * Show the share count for a specfic post
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	static function share_count_post( $export = false, $post = null ) {

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
				'count'	=> __('No post found', self::$text_domain),
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
			'count'		=> __('No data', self::$text_domain),
		));
		die();

	}

	/**
	 * Test the shared count api
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	static function shared_count_test() {

		// Check if the API key is set

		if ( !isset($_POST['api_key']) ) {

			echo json_encode(array(
			   'status'		=> 'warning',
			   'message'	=> __('API Key was not found', self::$text_domain),
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
		   'message'	=> __('API Key is valid', self::$text_domain),
	   ));

	   die();
	}

	/**
	 * Perform all the actions with a license
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	static function license_action() {

		if ( !isset($_POST['license_key']) ) {
			echo '';
			die();
		}

		// data to send in our API request

		$api_params = array(
			'edd_action'	=> $_POST['license_action'],
			'license' 		=> $_POST['license_key'],
			'item_name' 	=> urlencode( NNROBOTS_CONTENT_AUDIT_ITEM_NAME ), // the name of our product in EDD
			'url'       	=> is_multisite() ? network_home_url() : home_url()
		);

		// Call the custom API.

		$response = wp_remote_post( NNROBOTS_CONTENT_AUDIT_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay

		if ( is_wp_error( $response ) ) {
			echo $response;
			die();
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( $_POST['license_action'] == 'activate_license' ) {
			self::update_license_key($_POST['license_key']);
		} else if ( $_POST['license_action'] == 'deactivate_license' && $license_data->license == 'deactivated') {
			self::update_license_key('');
		}

		// decode the license data

		echo $license_data->license;

		die();

	}

	/**
	 * Check if license key is active
	 *
	 * @access public
	 * @static
	 * @param mixed $license_key
	 * @return void
	 */
	static function is_license_active( $license_key ) {

		if ( !isset( $license_key ) ) {
			return false;
		}

		// data to send in our API request

		$api_params = array(
			'edd_action'	=> 'check_license',
			'license' 		=> $license_key,
			'item_name' 	=> urlencode( NNROBOTS_CONTENT_AUDIT_ITEM_NAME ), // the name of our product in EDD
			'url'       	=> is_multisite() ? network_home_url() : home_url()
		);

		// Call the custom API.

		$response = wp_remote_post( NNROBOTS_CONTENT_AUDIT_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// decode the license data

		if ( isset($license_data->license) && $license_data->license == 'valid' ) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * Get the license key
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	static function get_license_key() {

		// Get the license key from based on WordPress install

		if ( function_exists('is_multisite') && is_multisite() ) {
			$license_key = get_site_option(self::$prefix . 'license_key');
		} else {
			$license_key = get_option(self::$prefix . 'license_key');
		}

		// Check if license key is set

		if ( $license_key === false ) {
			$license_key = '';
		}

		return $license_key;
	}

	/**
	 * Update the license key
	 *
	 * @access public
	 * @static
	 * @param mixed $license_key
	 * @return void
	 */
	static function update_license_key($license_key) {

		if ( isset( $license_key ) ) {

			// Get the license key from based on WordPress install

			if ( function_exists('is_multisite') && is_multisite() ) {

				update_site_option(self::$prefix . 'license_key', $license_key);

			} else {

				update_option(self::$prefix . 'license_key', $license_key);

			}
		}
	}

	/**
	 * Get the settings
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	static function get_settings() {

		$settings = get_option(self::$prefix . 'settings');

		// Check if the setting is not set

		if ( $settings === false ) {
			$settings = array();
		}

		return $settings;
	}

	/**
	 * Update the settings
	 *
	 * @access public
	 * @static
	 * @param mixed $settings
	 * @return void
	 */
	static function update_settings($settings) {

		// Get the setting

		$result = update_option(self::$prefix . 'settings', $settings);

		return $result;
	}
}

if ( !class_exists('EDD_SL_Plugin_Updater') ) {
	include_once( dirname( __FILE__ ) . '/includes/edd-software-licensing-updates/EDD_SL_Plugin_Updater.php' );
}

add_action('wp_ajax_nnr_ca_license_action', array('NNR_Content_Audit', 'license_action'));

$edd_updater = new EDD_SL_Plugin_Updater( NNROBOTS_CONTENT_AUDIT_STORE_URL , __FILE__, array(
		'version' 	=> NNROBOTS_CONTENT_AUDIT_VERSION_NUM,
		'license' 	=> NNR_Content_Audit::get_license_key(),
		'item_name' => NNROBOTS_CONTENT_AUDIT_ITEM_NAME,
		'author' 	=> '99 Robots',
	)
);

endif;