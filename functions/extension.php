<?php
/**
 * This file is responsible for managning all of the extensions.  Extensions are
 * other plugins that hooks into MSA to give it more functionality.
 *
 * @package Functions / Extensions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Create the initial settings extensions
 *
 * @access public
 * @return void
 */
function msa_create_initial_extensions() {
	do_action( 'msa_register_extension' );
}

/**
 * Get all the remote extensions
 *
 * @access public
 * @return array $remote_extensions All the remote extensions
 */
function msa_get_remote_extensions() {

	$remote_extensions = array();
	$response = vip_safe_wp_remote_get( 'https://draftpress.com/msa-extensions.json' );

	if ( is_array( $response ) ) {
		$remote_extensions = json_decode( $response['body'], true );
	}

	return $remote_extensions;
}

/**
 * Get the extensions
 *
 * @access public
 * @return array $msa_extensions The extensions
 */
function msa_get_extensions() {

	global $msa_extensions;

	if ( ! is_array( $msa_extensions ) ) {
		$msa_extensions = array();
	}

	return apply_filters( 'msa_get_extensions', $msa_extensions );
}

/**
 * Register a new Extension
 *
 * @access public
 * @param mixed $extension  The slug of a new extension.
 * @param array $args       The args of a new extension.
 * @return array $args      The args of a new extension.
 */
function msa_register_extension( $extension, $args = array() ) {

	global $msa_extensions;

	if ( ! is_array( $msa_extensions ) ) {
		$msa_extensions = array();
	}

	// Default extension.
	$default = array(
		'title' => __( 'Extension', 'msa' ),
	);

	$args = array_merge( $default, $args );

	// Add the extension to the global extensions array.
	$msa_extensions[ $extension ] = apply_filters( 'msa_register_extension_args', $args );

	/**
	* Fires after a dashboard extension is registered.
	*
	* @param string $extension 	  Extension.
	* @param array $args      Arguments used to register the extension.
	*/
	do_action( 'msa_registed_extension', $extension, $args );

	return $args;
}
