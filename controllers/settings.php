<?php
/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 10/22/15
 * Package: Controllers/Settings
 * File: settings.php
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

if ( false === ( $settings = get_option('msa_settings') ) ) {
	$settings = array();
}

// Save the settings

if ( isset($_POST['submit']) && check_admin_referer('msa-settings') ) {

	// Pass all the post variables to the save action

	do_action('msa_save_settings', $_POST);

	?><script>
		window.location = "<?php echo get_admin_url() . 'admin.php?page=msa-settings'; ?>";
	</script><?php

/*
	$settings['shared_count_api_key'] = isset($_POST['msa-shared-count-api-key']) ? sanitize_text_field($_POST['msa-shared-count-api-key']) : '';
	$settings['use_shared_count'] = isset($_POST['msa-use-shared-count']) && $_POST['msa-use-shared-count'] ? true : false;

	update_option('msa_settings', $settings);
*/
}

include_once(MY_SITE_AUDIT_PLUGIN_DIR . 'views/settings.php');