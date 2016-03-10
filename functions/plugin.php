<?php
/**
 * This file is responsible for all of the WordPress admin plugin actions.
 *
 * @package Functions / Plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add the actions rows for the all plugins page
 *
 * @access public
 * @param mixed $links  The current action links.
 * @param mixed $file   The current file.
 * @return mixed $links The new action links.
 */
function msa_plugin_action_links( $links, $file ) {

	$settings_link = '<a href="' . admin_url( 'admin.php?page=msa-settings' ) . '">' . esc_html__( 'General Settings', 'msa' ) . '</a>';

	// Remove the action if we are on our own page.
	if ( 'my-site-audit/my-site-audit.php' === $file ) {
		array_unshift( $links, $settings_link );
	}

	return $links;
}
add_filter( 'plugin_action_links', 'msa_plugin_action_links', 10, 2 );

/**
 * Add the row meta data for the all plugins page
 *
 * @access public
 * @param mixed $input  The current plugin row meta.
 * @param mixed $file   The current file.
 * @return mixed $input The new plugin row meta.
 */
function msa_plugin_row_meta( $input, $file ) {

	// Remove the action if we are on our own page.
	if ( 'my-site-audit/my-site-audit.php' === $file ) {
		return $input;
	}

	$msa_link = esc_url( add_query_arg( array(
		'utm_source'   => 'plugins-page',
		'utm_medium'   => 'plugin-row',
		'utm_campaign' => 'admin',
	), MY_SITE_AUDIT_EXT_URL ) );

	$links = array(
		'<a href="' . admin_url( 'index.php?page=msa-getting-started' ) . '">' . esc_html__( 'Getting Started', 'msa' ) . '</a>',
		'<a href="' . $msa_link . '">' . esc_html__( 'Extensions', 'msa' ) . '</a>',
	);

	$input = array_merge( $input, $links );

	return $input;
}
add_filter( 'plugin_row_meta', 'msa_plugin_row_meta', 10, 2 );
