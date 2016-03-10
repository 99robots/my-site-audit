<?php
/**
 *  The controller of the Dashboard Page.
 *
 * @package Controllers / Dashboard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( false === ( $settings = get_option( 'msa_settings' ) ) ) {
	$settings = array();
}

include_once( MY_SITE_AUDIT_PLUGIN_DIR . 'views/dashboard.php' );
