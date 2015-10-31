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

	/**
	 *
	 * This is the main action for creating an audit
	 *
	 */
	do_action('msa_create_audit', $_POST);

}

// Delete an audit

if ( isset($_GET['action']) && $_GET['action'] == 'delete' && check_admin_referer('msa-delete-audit')) {

	$audit_model = new MSA_Audits_Model();
	$audit_model->delete_data($_GET['audit']);

	msa_force_redirect( get_admin_url() . 'admin.php?page=msa-all-audits' );

}

include_once(MY_SITE_AUDIT_PLUGIN_DIR . 'views/all-audits.php');