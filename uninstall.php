<?php
/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 10/22/15
 * Package: Uninstall
 * File: uninstall.php
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

// if uninstall not called from WordPress exit

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit ();

// Delete all existence of this plugin

require_once( plugin_dir_path( __FILE__ ) . 'model/audits.php' );
require_once( plugin_dir_path( __FILE__ ) . 'model/audit-posts.php' );

// Delete the audits table

$audit_model = new MSA_Audits_Model();
$audit_model->delete_table();

// Delete the audit posts table

$audit_posts_model = new MSA_Audit_Posts_Model();
$audit_posts_model->delete_table();