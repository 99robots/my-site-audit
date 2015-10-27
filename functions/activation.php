<?php
/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 10/22/15
 * Package: Functions/Activation
 * File: activation.php
 * Author: Kyle Benk
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
 * The plugin is activated
 *
 * @access public
 * @return void
 */
function msa_activation() {

	// Add the version number to the database

	if ( function_exists('is_multisite') && is_multisite() ) {
		update_site_option('msa-version', MY_SITE_AUDIT_VERSION);
	} else {
		update_option('msa-version', MY_SITE_AUDIT_VERSION);
	}

	// Create the audits table

	$audit_model = new MSA_Audits_Model();
	$audit_model->create_table();

	// Create the audit posts table

	$audit_posts_model = new MSA_Audit_Posts_Model();
	$audit_posts_model->create_table();

}

register_activation_hook( MY_SITE_AUDIT_PLUGIN_FILE, 'msa_activation' );