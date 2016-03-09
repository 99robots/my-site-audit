<?php
/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 11/19/15
 * Package: Functions/ Notifications
 * File: notifications.php
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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Notifiy people that the audit has been completed
 *
 * @access public
 * @return void
 */
function msa_notifiy_audit_is_completed( $audit_id, $audit_name ) {

	// Check if we have data already saved

	if ( false === ( $settings = get_option( 'msa_settings' ) ) ) {
		$settings = array();
	}

	// Set to the default admin email

	if ( ! isset( $settings['notification_emails'] ) ) {
		$settings['notification_emails'] = get_option( 'admin_email' );
	}

	// Send email

	if ( '' !== $settings['notification_emails'] ) {

		$site_name = bloginfo( 'description' );

		if ( ! isset( $site_name ) || '' === $site_name ) {
			$site_name = get_site_url();
		}

		$subject = $audit_name . __( ' Completed for site: ', 'msa' ) . $site_name;
		$message = __( 'Your Audit has been completed for site: ', 'msa' ) . get_site_url() . "\r\n\r\n" . __( 'View: ', 'msa' ) . get_admin_url() . 'admin.php?page=msa-all-audits&audit=' . $audit_id;
		$headers = '';
		//$headers .= "MIME-Version: 1.0\r\n";
		//$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

		$emails  = apply_filters( 'msa_notify_emails', $settings['notification_emails'], $audit_id );
		$subject = apply_filters( 'msa_notify_subject', $subject, $audit_id );
		$message = apply_filters( 'msa_notify_message', $message, $audit_id );
		$headers = apply_filters( 'msa_notify_headers', $headers, $audit_id );

		$result = wp_mail( $emails, $subject, $message, $headers );
	}
}
add_action( 'msa_audit_completed', 'msa_notifiy_audit_is_completed', 10, 2 );
