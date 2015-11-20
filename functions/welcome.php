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

/*
	// About Page

	add_dashboard_page(
		__( 'About My Site Audit', 'msa' ),
		__( 'About My Site Audit', 'msa' ),
		'manage_options',
		'msa-about',
		'msa_welcome_about_page'
	);
*/

	// Changelog Page

/*
	add_dashboard_page(
		__( 'My Site Audit Changelog', 'msa' ),
		__( 'My Site Audit Changelog', 'msa' ),
		'manage_options',
		'msa-changelog',
		'msa_welcome_changelog_page'
	);
*/

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

		<h1 class="msa-welcome-header"><?php _e( 'Welcome to My Site Audit', 'msa' ); ?>
			<span class="msa-badge dashicons dashicons-analytics"></span>
		</h1>

		<div class="about-text"><?php printf( __( 'Thank you for updating to the latest version!', 'msa' ), MY_SITE_AUDIT_VERSION ); ?></div>

		<?php msa_welcome_page_tabs(); ?>

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

		<h1 class="msa-welcome-header"><?php printf( __( 'Welcome to My Site Audit %s', 'msa' ), MY_SITE_AUDIT_VERSION ); ?>
			<span class="msa-badge dashicons dashicons-analytics"></span>
		</h1>

		<div class="about-text"><?php printf( __( 'Thank you for updating to the latest version!', 'msa' ), MY_SITE_AUDIT_VERSION ); ?></div>

		<?php msa_welcome_page_tabs(); ?>

		<div class="feature-section">

			<div class="feature-section-media">
				<img src="<?php echo MY_SITE_AUDIT_PLUGIN_URL; ?>images/welcome/overview.png"/>
			</div>

			<div class="feature-section-content">

				<h3><?php _e('Overview'); ?></h3>

				<p><?php _e('We are very excited to announce the release of My Site Audit.  This plugin was built to make auditing your content extremely easy and fast.  Within minutes you will be able to create an audit of all the posts on your site.'); ?></p>

			</div>

		</div>

		<div class="feature-section">

			<div class="feature-section-media">
				<img src="<?php echo MY_SITE_AUDIT_PLUGIN_URL; ?>images/welcome/quick-setup.png"/>
			</div>

			<div class="feature-section-content">

				<h3><?php _e('Quick Setup'); ?></h3>

				<p><?php _e('Creating an audit is as simple as 1..2..3'); ?></p>

				<h4><?php _e('Post Date Range'); ?></h4>

				<p><?php _e('You will be able to select the date range for posts to audit.  My Site Audit will then audit posts that were published within the given date range.'); ?></p>

				<h4><?php _e('Post Types'); ?></h4>

				<p><?php _e('Select from any post type you have on your site, even custom post types.  You can chose just one or multiple at a time for a single audit.'); ?></p>

				<h4><?php _e('Maximum Posts'); ?></h4>

				<p><?php _e('There is a built-in safe guard to protect you from auditing more posts then you want to.  We give you the option to select the maximum number of posts to audit even if there are more Posts within the date range.'); ?></p>

			</div>

		</div>

		<div class="feature-section">

			<div class="feature-section-media">
				<img src="<?php echo MY_SITE_AUDIT_PLUGIN_URL; ?>images/welcome/sort-filter.png"/>
			</div>

			<div class="feature-section-content">

				<h3><?php _e('Sort and Filter'); ?></h3>

				<p><?php _e('My Site Audit give you the power to sort and filter for posts based on many different criteria.'); ?></p>

				<h4><?php _e('Post Score'); ?></h4>

				<p><?php _e('Just like post stati you can filter posts based on their My Site Audit score.  This is really useful to see exactly what posts need your attention and which ones can wait.'); ?></p>

				<h4><?php _e('Attributes'); ?></h4>

				<p><?php _e('There are a lot of different attributes that you can filter posts by like, word count, comments, images and many more.'); ?></p>

			</div>

		</div>

		<div class="feature-section">

			<div class="feature-section-media">
				<img src="<?php echo MY_SITE_AUDIT_PLUGIN_URL; ?>images/welcome/improve-content.png"/>
			</div>

			<div class="feature-section-content">

				<h3><?php _e('Improve your Content'); ?></h3>

				<p><?php _e('My Site Audit was built to help you improve your content and therefore drive more traffic to your site.'); ?></p>

				<h4><?php _e('Increase User Engagement'); ?></h4>

				<p><?php _e('Increasing your Post Score will help to improve your user engagement.  Higher user engagement can result in lower bounce rates and higher site traffic.'); ?></p>

				<h4><?php _e('Better Indexing'); ?></h4>

				<p><?php _e('By editing your content based on the its audit you will increase the ability for it to be indexed better.'); ?></p>

			</div>

		</div>

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

		<h1 class="msa-welcome-header"><?php _e( 'My Site Audit Changelog', 'msa' ); ?>
			<span class="msa-badge dashicons dashicons-analytics"></span>
		</h1>

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

		<h1 class="msa-welcome-header"><?php _e( 'Creators of My Site Audit', 'msa' ); ?>
			<span class="msa-badge dashicons dashicons-analytics"></span>
		</h1>

		<div class="about-text"><?php printf( __( 'Thank you for updating to the latest version!', 'msa' ), MY_SITE_AUDIT_VERSION ); ?></div>

		<?php msa_welcome_page_tabs(); ?>

		<p class="about-description"><?php _e( 'My Site Audit is created by the great people at 99 Robots.  We are looking for contributors all the time so please', 'msa' ); ?> <a href="https://99robots.com/contact/" target="_blank"><?php _e('Contact Us', 'msa'); ?></a> <?php _e( 'if you want to become a contributor.', 'msa' ); ?></p>

		<?php echo msa_display_contributors(); ?>

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

<!--
		<a class="nav-tab <?php echo $selected == 'msa-about' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'msa-about' ), 'index.php' ) ) ); ?>">
			<?php _e( "What's New", 'msa' ); ?>
		</a>
-->

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
		wp_safe_redirect( admin_url( 'index.php?page=msa-getting-started' ) );
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
	//remove_submenu_page( 'index.php', 'msa-changelog' );
	remove_submenu_page( 'index.php', 'msa-credits' );

	?>
	<style>
		.about-wrap h1.msa-welcome-header {
			position: relative;
			margin-right: 0;
		}

		.msa-badge {
			position: absolute;
			top: 0;
			right: 0;
			width: 100px;
			font-size: 100px;
		}
	</style>
	<?php

}

add_action( 'admin_head', 'msa_hide_welcome_dashboard_pages' );

/**
 * Display a list of contributors
 *
 * @access public
 * @return void
 */
function msa_display_contributors() {

	$contributors = msa_get_contributors();

	if ( empty( $contributors ) ) {
		return '';
	}

	$contributor_list = '<ul class="wp-people-group">';

	foreach ( $contributors as $contributor ) {

		$contributor_list .= '<li class="wp-person">';
		$contributor_list .= sprintf( '<a href="%s" title="%s">',
			esc_url( 'https://github.com/' . $contributor->login ),
			esc_html( sprintf( __( 'View %s', 'edd' ), $contributor->login ) )
		);

		$contributor_list .= sprintf( '<img src="%s" width="64" height="64" class="gravatar" alt="%s" />', esc_url( $contributor->avatar_url ), esc_html( $contributor->login ) );
		$contributor_list .= '</a>';
		$contributor_list .= sprintf( '<a class="web" href="%s">%s</a>', esc_url( 'https://github.com/' . $contributor->login ), esc_html( $contributor->login ) );
		$contributor_list .= '</a>';
		$contributor_list .= '</li>';

	}

	$contributor_list .= '</ul>';

	return $contributor_list;
}

/**
 * Get the list of contributors who have worked on My Site Audit
 *
 * @access public
 * @return void
 */
function msa_get_contributors() {

	$contributors = get_transient( 'msa_contributors' );

	if ( false !== $contributors ) {
		return $contributors;
	}

	$response = wp_remote_get( 'https://api.github.com/repos/99robots/my-site-audit/contributors', array( 'sslverify' => false ) );

	if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) ) {
		return array();
	}

	$contributors = json_decode( wp_remote_retrieve_body( $response ) );

	if ( ! is_array( $contributors ) ) {
		return array();
	}

	set_transient( 'msa_contributors', $contributors, 3600 );

	return $contributors;
}