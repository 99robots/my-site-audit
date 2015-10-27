<?php
/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 10/22/15
 * Package: Views/Settings
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

require_once('header.php'); ?>

<h1><?php _e("Settings", 'msa'); ?></h1>

<form method="post" class="msa-form">

	<div class="msa-vertical-tabs-menu" role="navigation">

		<ul class="msa-vertical-tabs-list">

			<li class="msa-vertical-tabs-item msa-vertical-tabs-current">
				<a href="#settings"><?php _e("Settings", 'msa'); ?></a>
			</li>
			<li class="msa-vertical-tabs-item">
				<a href="#conditions"><?php _e("Conditions", 'msa'); ?></a>
			</li>
			<li class="msa-vertical-tabs-item">
				<a href="#user-roles"><?php _e("User Roles", 'msa'); ?></a>
			</li>

		</ul>

	</div>

	<div class="msa-vertical-tabs-content">

		<div id="settings" class="msa-vertical-tabs-content-item msa-vertical-tabs-content-current">

			<h3><?php _e("Settings", 'msa'); ?></h3>

			<table class="form-table">
				<tbody>

					<!-- Shared Count API Key -->

					<tr>
						<th scope="row"><label for="msa-shared-count-api-key"><?php _e("Shared Count API Key", 'msa'); ?></label></th>
						<td>
							<input type="text" class="regular-text msa-shared-count-api-key" id="msa-shared-count-api-key" name="msa-shared-count-api-key" value="<?php echo isset($settings['shared_count_api_key']) ? esc_attr($settings['shared_count_api_key']) : ''; ?>"/>
							<p class="description"><?php _e('Input your', 'msa'); ?> <a href="https://admin.sharedcount.com/admin/user/home.php" target="_blank"><?php _e("Shared Count API Key", 'msa'); ?></a> <?php _e('in order to get the', 'msa'); ?> <a href="https://admin.sharedcount.com/faq.php" target="_blank"><?php _e("share count data", 'msa'); ?></a> <?php _e('for your site\'s posts.', 'msa'); ?></p>
						</td>
					</tr>

					<!-- Test Shared Count -->

					<tr>
						<th scope="row"><label for="msa-shared-count-test"></label></th>
						<td>
							<button class="button msa-shared-count-test"><?php _e("Test", 'msa'); ?></button>
						</td>
					</tr>

					<!-- Use Shared Count -->

					<tr>
						<th scope="row"><label for="msa-use-shared-count"><?php _e("Use Shared Count", 'msa'); ?></label></th>
						<td>
							<input type="checkbox" class="msa-use-shared-count" id="msa-use-shared-count" name="msa-use-shared-count" <?php echo isset($settings['use_shared_count']) && $settings['use_shared_count'] ? 'checked="checked"' : ''; ?>>
							<p class="description"><?php _e("Do you want to use Shared Count Data with your author stats?", 'msa'); ?></p>
						</td>
					</tr>

				</tbody>

			</table>

		</div>

		<div id="conditions" class="msa-vertical-tabs-content-item">
			<h3><?php _e("Conditions", 'msa'); ?></h3>
		</div>

		<div id="user-roles" class="msa-vertical-tabs-content-item">
			<h3><?php _e("User Roles", 'msa'); ?></h3>
		</div>

		<?php submit_button(); ?>

		<?php wp_nonce_field('msa-settings'); ?>

	</div>

</form>

<?php require_once('footer.php'); ?>