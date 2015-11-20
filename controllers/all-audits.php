<?php
/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 10/26/15
 * Package: Controllers/All Audits
 * File: all-audits.php
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

// Create a new Audit

if ( isset($_POST['submit']) && check_admin_referer('msa-add-audit') ) {

	// Check if they can add a new audit

	if ( apply_filters('msa_can_add_new_audit', true) ) {

		/**
		 *
		 * This is the main action for creating an audit
		 *
		 */
		do_action('msa_create_audit', $_POST);

		$_POST['user'] = get_current_user_id();

		$date_range = json_decode(stripcslashes($_POST['date-range']), true);

		$_POST['after-date'] = $date_range['start'];
		$_POST['before-date'] = $date_range['end'];

		msa_add_audit_to_queue($_POST);

	} else {

		set_transient('msa_unable_to_create_audit', __('Unable to create your audit because you reached the maximum number of audits you can have.  In order to add a new audit you will first need to delete one.  If you want want to increase the amount of audits you can have please ') . '<a href="' . MY_SITE_AUDIT_EXT_URL . '" target="_blank">' . __('download the extension') . '</a>' );
	}

	msa_force_redirect(get_admin_url() . 'admin.php?page=msa-all-audits');
}

// Delete an audit

if ( isset($_GET['action']) && $_GET['action'] == 'delete' && check_admin_referer('msa-delete-audit')) {

	$audit_model = new MSA_Audits_Model();
	$audit_model->delete_data($_GET['audit']);

	msa_force_redirect( get_admin_url() . 'admin.php?page=msa-all-audits' );

}

// Force Stop an audit

if ( isset($_GET['action']) && $_GET['action'] == 'force_stop_audit' && check_admin_referer('msa-force-stop-audit')) {

	delete_transient('msa_running_audit');

	$audit_model = new MSA_Audits_Model();
	$audits = $audit_model->get_data(array('status' => 'in-progress'));

	if ( isset($audits[0]) ) {
		$audit = $audits[0];
		$audit['status'] = 'completed';
		$audit_model->update_data($audit['id'], $audit);
	}

	msa_force_redirect( get_admin_url() . 'admin.php?page=msa-all-audits' );

}

include_once(MY_SITE_AUDIT_PLUGIN_DIR . 'views/all-audits.php');