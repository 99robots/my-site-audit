<?php
/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 10/30/15
 * Package: Functions/Welcome
 * File: welcome.php
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
 * Create the menus on the welcome page
 *
 * @access public
 * @return void
 */
function msa_welcome_menus() {

	// Getting Started Page

	add_dashboard_page(
		__( 'Getting Started with My Site Audit', 'msa' ),
		__( 'Getting Started with My Site Audit', 'msa' ),
		'manage_options',
		'msa-getting-started',
		'msa_welcome_getting_started_page'
	);

	// About Page

	add_dashboard_page(
		__( 'About My Site Audit', 'msa' ),
		__( 'About My Site Audit', 'msa' ),
		'manage_options',
		'msa-about',
		'msa_welcome_about_page'
	);

	// Changelog Page

	add_dashboard_page(
		__( 'My Site Audit Changelog', 'msa' ),
		__( 'My Site Audit Changelog', 'msa' ),
		'manage_options',
		'msa-changelog',
		'msa_welcome_changelog_page'
	);

	// Credits Page

	add_dashboard_page(
		__( 'Creators of My Site Audit', 'msa' ),
		__( 'Creators of My Site Audit', 'msa' ),
		'manage_options',
		'msa-credits',
		'msa_welcome_credits_page'
	);

}

add_action( 'admin_menu', 'msa_welcome_menus' );

/**
 * The contents of the Welcome About Page
 *
 * @access public
 * @return void
 */
function msa_welcome_about_page() {

	?><div class="wrap about-wrap">

		<h1><?php _e( 'About My Site Audit', 'msa' ); ?></h1>

		<div class="about-text"><?php printf( __( 'Thank you for updating to the latest version!', 'msa' ), MY_SITE_AUDIT_VERSION ); ?></div>

		<?php msa_welcome_page_tabs(); ?>

		<div class="return-to-dashboard">
			<a href="<?php echo get_admin_url() . 'admin.php?page=msa-settings'; ?>"><?php _e( 'Go to My Site Audit Settings', 'msa' ); ?></a>
		</div>
	</div><?php
}

/**
 * The content for the getting started page
 *
 * @access public
 * @return void
 */
function msa_welcome_getting_started_page() {

	?><div class="wrap about-wrap">

		<h1><?php printf( __( 'Welcome to My Site Audit %s', 'msa' ), MY_SITE_AUDIT_VERSION ); ?></h1>

		<div class="about-text"><?php printf( __( 'Thank you for updating to the latest version!', 'msa' ), MY_SITE_AUDIT_VERSION ); ?></div>

		<?php msa_welcome_page_tabs(); ?>

		<div class="return-to-dashboard">
			<a href="<?php echo get_admin_url() . 'admin.php?page=msa-settings'; ?>"><?php _e( 'Go to My Site Audit Settings', 'msa' ); ?></a>
		</div>
	</div><?php

}

/**
 * The contents of the Changelog Page
 *
 * @access public
 * @return void
 */
function msa_welcome_changelog_page() {

	?><div class="wrap about-wrap">

		<h1><?php _e( 'My Site Audit Changelog', 'msa' ); ?></h1>

		<div class="about-text"><?php printf( __( 'Thank you for updating to the latest version!', 'msa' ), MY_SITE_AUDIT_VERSION ); ?></div>

		<?php msa_welcome_page_tabs(); ?>

		<div class="return-to-dashboard">
			<a href="<?php echo get_admin_url() . 'admin.php?page=msa-settings'; ?>"><?php _e( 'Go to My Site Audit Settings', 'msa' ); ?></a>
		</div>
	</div><?php
}

/**
 * The contents of the Credits Page
 *
 * @access public
 * @return void
 */
function msa_welcome_credits_page() {

	?><div class="wrap about-wrap">

		<h1><?php _e( 'Creators of My Site Audit', 'msa' ); ?></h1>

		<div class="about-text"><?php printf( __( 'Thank you for updating to the latest version!', 'msa' ), MY_SITE_AUDIT_VERSION ); ?></div>

		<?php msa_welcome_page_tabs(); ?>

		<div class="return-to-dashboard">
			<a href="<?php echo get_admin_url() . 'admin.php?page=msa-settings'; ?>"><?php _e( 'Go to My Site Audit Settings', 'msa' ); ?></a>
		</div>
	</div><?php
}

/**
 * Show the Welcome page tabs
 *
 * @access public
 * @return void
 */
function msa_welcome_page_tabs() {

	$selected = isset( $_GET['page'] ) ? $_GET['page'] : 'msa-about';

	?>
	<h2 class="nav-tab-wrapper">

		<a class="nav-tab <?php echo $selected == 'msa-about' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'msa-about' ), 'index.php' ) ) ); ?>">
			<?php _e( "What's New", 'msa' ); ?>
		</a>

		<a class="nav-tab <?php echo $selected == 'msa-getting-started' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'msa-getting-started' ), 'index.php' ) ) ); ?>">
			<?php _e( 'Getting Started', 'msa' ); ?>
		</a>

		<a class="nav-tab <?php echo $selected == 'msa-credits' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'msa-credits' ), 'index.php' ) ) ); ?>">
			<?php _e( 'Credits', 'msa' ); ?>
		</a>

	</h2>
	<?php
}

/**
 * Redirect to Welcome page from activation or update
 *
 * @access public
 * @return void
 */
function msa_welcome_page_redirect() {

	// Bail if no activation redirect

	if ( ! get_transient( '_msa_activation_redirect' ) ) {
		return;
	}

	// Delete the redirect transient

	delete_transient( '_msa_activation_redirect' );

	// Bail if activating from network, or bulk

	if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
		return;
	}

	$upgrade = get_option( 'msa_version_upgraded_from' );

	// First time install

	if( ! $upgrade ) {
		wp_safe_redirect( admin_url( 'index.php?page=msa-getting-started' ) );
		exit;
	}

	// Update

	else {
		wp_safe_redirect( admin_url( 'index.php?page=msa-about' ) );
		exit;
	}

}

add_action( 'admin_init', 'msa_welcome_page_redirect');

/**
 * Hide all the welcome pages from the Dashboard submenu
 *
 * @access public
 * @return void
 */
function msa_hide_welcome_dashboard_pages() {

	remove_submenu_page( 'index.php', 'msa-about' );
	remove_submenu_page( 'index.php', 'msa-getting-started' );
	remove_submenu_page( 'index.php', 'msa-changelog' );
	remove_submenu_page( 'index.php', 'msa-credits' );

}

add_action( 'admin_head', 'msa_hide_welcome_dashboard_pages' );