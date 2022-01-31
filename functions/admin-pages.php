<?php
/**
 * This file is responsible for creating the admin pages, and there are hooks for
 * extensibility
 *
 * @package Functions / Admin Pages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Hooks into the 'admin_menu' hook to show the settings page
 *
 * @access public
 * @static
 * @return void
 */
function msa_menu() {

	// Dashboard.
	add_menu_page(
		__( MY_SITE_AUDIT_ITEM_NAME, 'msa' ),
		__( MY_SITE_AUDIT_ITEM_NAME, 'msa' ),
		'edit_posts',
		'msa-dashboard',
		'msa_dashboard',
		'dashicons-analytics'
	);

	// Dashboard.
	$dashboard_page_load = add_submenu_page(
		'msa-dashboard',
		__( 'Dashboard', 'msa' ),
		__( 'Dashboard', 'msa' ),
		'edit_posts',
		'msa-dashboard',
		'msa_dashboard'
	);
	add_action( "admin_print_scripts-$dashboard_page_load", 'msa_dashboard_scripts' );

	// All Audits.
	$all_audits_page_load = add_submenu_page(
		'msa-dashboard',
		__( 'All Audits', 'msa' ),
		__( 'All Audits', 'msa' ),
		'edit_pages',
		'msa-all-audits',
		'msa_all_audits'
	);
	add_action( "admin_print_scripts-$all_audits_page_load", 'msa_all_audits_scripts' );
	add_action( "load-$all_audits_page_load", 'msa_all_audits_load' );

	/**
	 * Allows other developers the ability to add thier own pages
	 */
	do_action( 'msa_before_admin_pages', 'msa-dashboard' );

	// Settings.
	$settings_page_load = add_submenu_page(
		'msa-dashboard',
		__( 'Settings', 'msa' ),
		__( 'Settings', 'msa' ),
		'manage_options',
		'msa-settings',
		'msa_settings'
	);
	add_action( "admin_print_scripts-$settings_page_load" , 'msa_settings_scripts' );

	// Extensions.
	$extensions_page_load = add_submenu_page(
		'msa-dashboard',
		__( 'Extensions', 'msa' ),
		__( 'Extensions', 'msa' ),
		'manage_options',
		'msa-extensions',
		'msa_extensions'
	);
	add_action( "admin_print_scripts-$extensions_page_load" , 'msa_extensions_scripts' );

	/**
	 * Allows other developers the ability to add thier own pages
	 */
	do_action( 'msa_after_admin_pages', 'msa-dashboard' );
}
add_action( 'admin_menu', 'msa_menu' );

/**
 * Hooks into the 'admin_print_scripts-$page' to inlcude the scripts for the dashboard page
 *
 * @access public
 * @static
 * @return void
 */
function msa_dashboard_scripts() {

	msa_include_default_styles();

	// Style.
	wp_enqueue_style( 'msa-dashboard-css', 			MY_SITE_AUDIT_PLUGIN_URL . '/css/dashboard.css' );
	wp_enqueue_style( 'msa-fontawesome-css', 		MY_SITE_AUDIT_PLUGIN_URL . '/includes/font-awesome/css/font-awesome.min.css' );

	// Scripts.
	wp_enqueue_script( 'msa-dashboard-js', 			MY_SITE_AUDIT_PLUGIN_URL . '/js/dashboard.js' );
	wp_localize_script( 'msa-dashboard-js', 'msaDashboardData', array(
		'save_dashboard_panel_order_nonce'	=> wp_create_nonce( 'save_dashboard_panel_order_nonce' ),
	) );
	wp_enqueue_script( 'dashboard' );

}

/**
 * Hooks into the 'admin_print_scripts-$page' to inlcude the scripts for the all audits page
 *
 * @access public
 * @static
 * @return void
 */
function msa_all_audits_scripts() {

	msa_include_default_styles();

	// Style.
	wp_enqueue_style( 'msa-all-audits-css', 				MY_SITE_AUDIT_PLUGIN_URL . '/css/all-audits.css' );
	wp_enqueue_style( 'msa-fontawesome-css', 				MY_SITE_AUDIT_PLUGIN_URL . '/includes/font-awesome/css/font-awesome.min.css' );
	wp_enqueue_style( 'msa-jquery-ui-css', 					MY_SITE_AUDIT_PLUGIN_URL . '/includes/jquery-datepicker/jquery-ui.min.css' );
	wp_enqueue_style( 'msa-jquery-ui-theme-css', 			MY_SITE_AUDIT_PLUGIN_URL . '/includes/jquery-datepicker/jquery-ui.theme.min.css' );
	wp_enqueue_style( 'msa-jquery-daterange-picker-css', 	MY_SITE_AUDIT_PLUGIN_URL . '/includes/jquery-daterange-picker/jquery.comiseo.daterangepicker.css' );
	wp_enqueue_style( 'media-views' );

	// Scripts.
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-datepicker' );

	wp_enqueue_script( 'msa-jquery-daterange-picker-js', 	MY_SITE_AUDIT_PLUGIN_URL . '/includes/jquery-daterange-picker/jquery.comiseo.daterangepicker.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'jquery-ui-button', 'jquery-ui-tabs', 'jquery-ui-menu', 'jquery-ui-widget', 'msa-moment-js' ) );
	wp_enqueue_script( 'msa-moment-js', 					MY_SITE_AUDIT_PLUGIN_URL . '/includes/moment/moment.min.js' );

	wp_enqueue_script( 'msa-all-audits-js', 				MY_SITE_AUDIT_PLUGIN_URL . '/js/all-audits.js' );
	wp_localize_script( 'msa-all-audits-js', 'msa_all_audits_data', array(
		'site_url'					=> get_site_url(),
		'admin_url'					=> get_admin_url(),
		'audit_page'				=> get_admin_url() . 'admin.php?page=msa-all-audits',
		'add_post_to_audit_nonce'	=> wp_create_nonce( 'add_post_to_audit_nonce' ),
		'update_audit_score_nonce'	=> wp_create_nonce( 'update_audit_score_nonce' ),
		'get_post_ids_nonce'		=> wp_create_nonce( 'get_post_ids_nonce' ),
		'info'						=> __( 'Your Audit is now being created and you can monitor its status from the progress bar below.  Please <strong>DO NOT</strong> refresh this page as that will stop the audit. If you want to stop this audit for any reason, then click <a href="' . get_admin_url() . 'admin.php?page=msa-all-audits" class="msa-force-stop">Force Stop</a>.', 'msa' ),
		'success_message'			=> __( 'Your Audit has been created! See it ', 'msa' ),
	));

	if ( false === ( $show_columns = get_option( 'msa_show_columns_' . get_current_user_id() ) ) ) {
		$show_columns = array();
	}

	wp_enqueue_script( 'msa-single-audit-js', 				MY_SITE_AUDIT_PLUGIN_URL . '/js/single-audit.js' );
	wp_localize_script( 'msa-single-audit-js', 'msaSingleAuditData', array(
		'audit_page'			=> isset( $_GET['audit'] ) ? msa_get_single_audit_link( wp_unslash( $_GET['audit'] ) ) : '',
		'show_columns'			=> $show_columns,
		'attribute_title'		=> __( 'Attributes', 'msa' ),
		'conditions'			=> msa_get_conditions(),
		'show_column_nonce'		=> wp_create_nonce( 'msa_show_column' ),
		'condition_categories'	=> msa_get_condition_categories(),
	));

	wp_enqueue_script( 'msa-single-post-js', 				MY_SITE_AUDIT_PLUGIN_URL . '/js/single-post.js' );

}

/**
 * Hooks into the 'admin_print_scripts-$page' to inlcude the scripts for the settings page
 *
 * @access public
 * @static
 * @return void
 */
function msa_settings_scripts() {

	msa_include_default_styles();

	// Style.
	wp_enqueue_style( 'msa-settings-css', 			MY_SITE_AUDIT_PLUGIN_URL . '/css/settings.css' );
	wp_enqueue_style( 'msa-fontawesome-css', 		MY_SITE_AUDIT_PLUGIN_URL . '/includes/font-awesome/css/font-awesome.min.css' );

	// Script.
	wp_enqueue_script( 'msa-settings-js', 			MY_SITE_AUDIT_PLUGIN_URL . '/js/settings.js' );
	wp_enqueue_script( 'msa-licensing-js', 			MY_SITE_AUDIT_PLUGIN_URL . '/js/licensing.js', array( 'jquery' ) );
	wp_localize_script( 'msa-licensing-js', 			'msaLicensingData', array(
		'site_url'				=> get_site_url(),
		'activate_text'			=> __( 'Activate', 'msa' ),
		'deactivate_text'		=> __( 'Deactivate', 'msa' ),
		'no_license_key'		=> __( 'Please add or activate your License Key to get automatic updates.  Without a valid license key you will not receive regular updates. You can find your license key', 'msa' ) . ' <a href="https://draftpress.com/dashboard" target="_blank">here</a>',
		'expired'				=> __( 'EXPIRED LICENSE KEY: This license key has expired.  Please renew your license key', 'msa' ) . ' <a href="https://draftpress.com/dashboard" target="_blank">here</a>',
		'inactive'				=> __( 'INACTIVE LICENSE KEY: The license key is not active for this site.  Please activate it by clicking the Activate button to the right.', 'msa' ),
		'activation_error'		=> __( 'INVALID LICENSE KEY: The license key is not valid please try again. You can find your license key', 'msa' ) . ' <a href="https://draftpress.com/dashboard" target="_blank">here</a>.',
		'activation_valid'		=> __( 'SUCCESS: Your license key is valid.', 'msa' ),
		'deactivation_valid'	=> __( 'SUCCESS: This site has been deactivated.', 'msa' ),
		'deactivation_error'	=> __( 'DEACTIVATION FAILED: This site could not be deactivated.', 'msa' ),
	));

}

/**
 * Hooks into the 'admin_print_scripts-$page' to inlcude the scripts for the extensions page
 *
 * @access public
 * @static
 * @return void
 */
function msa_extensions_scripts() {

	msa_include_default_styles();

	// Style.
	wp_enqueue_style( 'msa-extensions-css', 			MY_SITE_AUDIT_PLUGIN_URL . '/css/extensions.css' );
	wp_enqueue_style( 'msa-fontawesome-css', 			MY_SITE_AUDIT_PLUGIN_URL . '/includes/font-awesome/css/font-awesome.min.css' );

}

/**
 * Include the default styles on all pages
 *
 * @access public
 * @return void
 */
function msa_include_default_styles() {
	wp_enqueue_style( 'msa-common-css', 			MY_SITE_AUDIT_PLUGIN_URL . '/css/common.css' );
	wp_enqueue_style( 'msa-theme-css', 				MY_SITE_AUDIT_PLUGIN_URL . '/css/theme.css' );
}

/**
 * This is the main function for the dashboard page
 *
 * @access public
 * @static
 * @return void
 */
function msa_dashboard() {
	require_once( MY_SITE_AUDIT_PLUGIN_DIR . 'controllers/dashboard.php' );
}

/**
 * This is the main function for the All Audits page
 *
 * @access public
 * @static
 * @return void
 */
function msa_all_audits() {
	require_once( MY_SITE_AUDIT_PLUGIN_DIR . 'controllers/all-audits.php' );
}

/**
 * Show the screen options on the Single Audit page
 *
 * @access public
 * @return void
 */
function msa_all_audits_load() {

	// Single Audit.
	if ( isset( $_GET['audit'] ) && ! isset( $_GET['post'] ) ) { // Input var okay.

		// Screen Options.
		$option = 'per_page';
		$args = array(
			 'label' 	=> 'Posts',
			 'default' 	=> 50,
			 'option' 	=> 'posts_per_page',
		);
		add_screen_option( $option, $args );

		add_filter( 'manage_my-site-audit_page_msa-all-audits_columns', 'msa_all_audits_add_column' );

	}
}

/**
 * Set the per page value
 *
 * @access public
 * @param mixed $status The Status.
 * @param mixed $option The Option.
 * @param mixed $value  The Value.
 * @return mixed $value The Value.
 */
function msa_set_per_page_value( $status, $option, $value ) {
	return $value;
}
add_filter( 'set-screen-option', 'msa_set_per_page_value', 10, 3 );

/**
 * Add all the screen option columns
 *
 * @access public
 * @param mixed $columns  The original columns.
 * @return array $columns The modified columns.
 */
function msa_all_audits_add_column( $columns ) {
	/**
	 * Conditions
	 */

	$condition_categories = msa_get_condition_categories();
	foreach ( $condition_categories as $key => $condition_category ) {
		$conditions = msa_get_conditions_from_category( $key );
		foreach ( $conditions as $key => $condition ) {
			$columns[ $key ] = $condition['name'];
		}
	}

	/**
	 * Attributes
	 */

	$attributes = msa_get_attributes();
	foreach ( $attributes as $slug => $attribute ) {
		if ( isset( $attribute['name'] ) ) {
			$columns[ $slug ] = $attribute['name'];
		}
	}

	return $columns;
}

/**
 * This is the main function for the settings page
 *
 * @access public
 * @static
 * @return void
 */
function msa_settings() {
	require_once( MY_SITE_AUDIT_PLUGIN_DIR . 'controllers/settings.php' );
}

/**
 * This is the main function for the extensions page
 *
 * @access public
 * @static
 * @return void
 */
function msa_extensions() {
	msa_force_redirect( 'https://draftpress.com/products/category/my-site-audit/' );
}
