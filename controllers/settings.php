<?php
/**
 *  The controller of the Settings Page.
 *
 * @package Controllers / Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( false === ( $settings = get_option( 'msa_settings' ) ) ) {
	$settings = array();
}

/**
 *  Save the settings
 */

if ( isset( $_POST['submit'] ) && check_admin_referer( 'msa-settings' ) ) { // Input var okay.

	/**
	 *
	 * Pass all the post variables to the save action
	 *
	 * @param mixed $_POST All of the settings from the form including any custom
	 *                     settings that were added by extensions.
	 */
	do_action( 'msa_save_settings', $_POST ); // Input var okay.
}

include_once( MY_SITE_AUDIT_PLUGIN_DIR . 'views/settings.php' );
