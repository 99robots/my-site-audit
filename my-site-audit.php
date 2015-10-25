<?php
/*
Plugin Name: My Site Audit
Plugin URI:
Description:
version: 1.0.0
Author: 99 Robots
Author URI: https://99robots.com
License: GPL2
*/

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

if ( !class_exists('NNR_Content_Audit') ) :

/**
 * My_Site_Audit class.
 */
final class My_Site_Audit {

	/**
	 * Holds the My_Site_Audit object and is the only way to obtain it
	 *
	 * @var mixed
	 * @access private
	 * @static
	 */
	private static $instance;

	/**
	 * Creates or retrieves the Get_Notified instance
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function instance() {

		// No object is created yet so lets create one

		if ( !isset(self::$instance) && !(self::$instance instanceof My_Site_Audit) ) {

			self::$instance = new My_Site_Audit;
			self::$instance->setup_constants();
			self::$instance->includes();

			add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
		}

		// Return the Get_Notified object

		return self::$instance;
	}

	/**
	 * Throw an error if this class is cloned
	 *
	 * @access public
	 * @return void
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'You cannot __clone an instance of the My_Site_Audit class.', MY_SITE_AUDIT_TEXT_DOMAIN ), '1.6' );
	}

	/**
	 * Throw an error if this class is unserialized
	 *
	 * @access public
	 * @return void
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'You cannot __wakeup an instance of the My_Site_Audit class.', MY_SITE_AUDIT_TEXT_DOMAIN ), '1.6' );
	}

	/**
	 * Sets up the constants we will use throughout the plugin
	 *
	 * @access private
	 * @return void
	 */
	private function setup_constants() {

		// Item Name

		if ( !defined('MY_SITE_AUDIT_ITEM_NAME') ) {
			define('MY_SITE_AUDIT_ITEM_NAME', 'My Site Audit');
		}

		// Store URL

		if ( !defined('MY_SITE_AUDIT_STORE_URL') ) {
			define('MY_SITE_AUDIT_STORE_URL', 'https://mysiteaudit.com');
		}

		// Minimum PHP version

		if ( !defined('MY_SITE_AUDIT_MIN_PHP_VERSION') ) {
			define('MY_SITE_AUDIT_MIN_PHP_VERSION', '5.4.0');
		}

		// Plugin prefix

		if ( ! defined( 'MY_SITE_AUDIT_PREFIX' ) ) {
			define( 'MY_SITE_AUDIT_PREFIX', 'msa-' );
		}

		// Plugin text domain

		if ( ! defined( 'MY_SITE_AUDIT_TEXT_DOMAIN' ) ) {
			define( 'MY_SITE_AUDIT_TEXT_DOMAIN', 'msa' );
		}

		// Plugin version

		if ( ! defined( 'MY_SITE_AUDIT_VERSION' ) ) {
			define( 'MY_SITE_AUDIT_VERSION', '1.0.0' );
		}

		// Plugin Folder Path

		if ( ! defined( 'MY_SITE_AUDIT_PLUGIN_DIR' ) ) {
			define( 'MY_SITE_AUDIT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin Folder URL

		if ( ! defined( 'MY_SITE_AUDIT_PLUGIN_URL' ) ) {
			define( 'MY_SITE_AUDIT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Plugin Root File

		if ( ! defined( 'MY_SITE_AUDIT_PLUGIN_FILE' ) ) {
			define( 'MY_SITE_AUDIT_PLUGIN_FILE', __FILE__ );
		}

		// Make sure CAL_GREGORIAN is defined

		if ( ! defined( 'CAL_GREGORIAN' ) ) {
			define( 'CAL_GREGORIAN', 1 );
		}

		// Score Increment

		if ( ! defined( 'MY_SITE_AUDIT_SCORE_INCREMENT' ) ) {
			define( 'MY_SITE_AUDIT_SCORE_INCREMENT', 0.1667 );
		}

		date_default_timezone_set(timezone_name_from_abbr(null, (int) get_option('gmt_offset') * 3600 , true));
	}

	/**
	 * Include required files
	 *
	 * @access private
	 * @since 1.4
	 * @return void
	 */
	private function includes() {

		// Model

		// Views

		// Controllers

		require_once( MY_SITE_AUDIT_PLUGIN_DIR . 'controllers/all-posts-table.php' );

		// Functions

		require_once( MY_SITE_AUDIT_PLUGIN_DIR . 'functions/activation.php' );
		require_once( MY_SITE_AUDIT_PLUGIN_DIR . 'functions/admin-pages.php' );
		require_once( MY_SITE_AUDIT_PLUGIN_DIR . 'functions/post-meta-box.php' );
		require_once( MY_SITE_AUDIT_PLUGIN_DIR . 'functions/audit-data.php' );

		// Includes

		require_once( MY_SITE_AUDIT_PLUGIN_DIR . 'includes/sharedcount/sharedcount.php' );

	}

	/**
	 * Load the text domain for translation
	 *
	 * @access public
	 * @return void
	 */
	public function load_textdomain() {
		load_textdomain( MY_SITE_AUDIT_TEXT_DOMAIN , dirname( plugin_basename( MY_SITE_AUDIT_PLUGIN_FILE ) ) . '/languages/' );
	}
}

endif;

/**
 * This is the function you will use in order to obtain an instance
 * of the My_Site_Audit class.
 *
 * Example: <?php $msa = MSA(); ?>
 *
 * @access public
 * @return void
 */
function MSA() {
	return My_Site_Audit::instance();
}

// Get the class loaded up and running

MSA();