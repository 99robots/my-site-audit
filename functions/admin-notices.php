<?php
/**
 * This file is responsible for all of the admin notices
 *
 * @package Functions / Admin Noticies
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Show the notice for an audit in progress
 *
 * @access public
 * @return void
 */
function msa_admin_notices() {

	// Unable to create audit.
	if ( false !== ( $no_audit = get_transient( 'msa_unable_to_create_audit' ) ) ) {
		?>
		<div class="error">
			<p><?php echo( $no_audit ); // WPCS: XSS ok. ?></p>
		</div>
		<?php

		delete_transient( 'msa_unable_to_create_audit' );
	}

	// Schedule Audit Notice.
	if ( false !== ( $scheduled_audit = get_transient( 'msa_schedule_audit' ) ) ) {
		?>
		<div class="updated">
			<p><?php esc_attr_e( 'My Site Audit: An Audit has been scheduled to run.', 'msa' ); // WPCS: XSS ok. ?></p>
		</div>
		<?php
	}

	// Running Audit.
	if ( false !== ( $in_progress = get_transient( 'msa_running_audit' ) ) ) {
		?>
		<div class="updated">
			<p><?php esc_attr_e( 'An Audit is currently being created.  We will email you once its completed and you can refresh this page to check as well.', 'msa' ); ?> <a class="button button-default" href="<?php echo ( wp_nonce_url( get_admin_url() . 'admin.php?page=msa-all-audits&action=force_stop_audit', 'msa-force-stop-audit' ) ); // WPCS: XSS ok. ?>"><?php esc_attr_e( 'Force Stop', 'msa' ); ?></a></p>
		</div>
		<?php
	}

	// WP Cron.
	if ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) {
		?>
		<div class="error">
			<p><?php esc_attr_e( 'WP Cron is <span style="color:red;font-weight:bold;">DISABLED</span>! My Site Audit needs WP Cron to be enabled in order to create an audit. Please read our', 'msa' ); ?> <a href="https://draftpress.com/docs/how-to-enable-wp-cron/" target="_blank"><?php esc_attr_e( 'documentation page', 'msa' ); ?></a> <?php esc_attr_e( 'about how to enable WP Cron.', 'msa' ); ?></p>
		</div>
		<?php
	}

	do_action( 'msa_show_admin_notices' );
}
add_action( 'admin_notices', 'msa_admin_notices' );
