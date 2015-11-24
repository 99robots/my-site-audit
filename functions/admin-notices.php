<?php
/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 11/18/15
 * Package: Functions/ Admin Notices
 * File: admin-notices.php
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
 * Show the notice for an audit in progress
 *
 * @access public
 * @return void
 */
function msa_admin_notices() {

	// Unable to create audit

	if ( false !== ( $no_audit = get_transient('msa_unable_to_create_audit') ) ) {

		?>
	    <div class="error">
	        <p><?php echo $no_audit; ?></p>
	    </div>
	    <?php

		delete_transient('msa_unable_to_create_audit');

	}

	// Schedule Audit Notice

	if ( false !== ( $scheduled_audit = get_transient('msa_schedule_audit') ) ) {

		?>
	    <div class="updated">
	        <p><?php _e( 'My Site Audit: An Audit has been scheduled to run.', 'msa' ); ?></p>
	    </div>
	    <?php

	}

	// Running Audit

	if ( false !== ( $in_progress = get_transient('msa_running_audit') ) ) {

		?>
	    <div class="updated">
	        <p><?php _e( 'An Audit is currently being created.  We will email you once its completed and you can refresh this page to check as well.', 'msa' ); ?> <a class="button button-default" href="<?php echo wp_nonce_url(get_admin_url() . 'admin.php?page=msa-all-audits&action=force_stop_audit', 'msa-force-stop-audit'); ?>"><?php _e('Force Stop', 'msa'); ?></a></p>
	    </div>
	    <?php

	}

	// WP Cron

	if ( defined('DISABLE_WP_CRON') && DISABLE_WP_CRON ) {
		?>
	    <div class="error">
	        <p><?php _e('WP Cron is <span style="color:red;font-weight:bold;">DISABLED</span>! My Site Audit needs WP Cron to be enabled in order to create an audit. Please read our', 'msa'); ?> <a href="https://99robots.com/?post_type=doc&p=9799" target="_blank"><?php _e('documentation page', 'msa'); ?></a> <?php _e('about how to enable WP Cron.', 'msa'); ?></p>
	    </div>
	    <?php
	}

	do_action('msa_show_admin_notices');
}
add_action( 'admin_notices', 'msa_admin_notices' );