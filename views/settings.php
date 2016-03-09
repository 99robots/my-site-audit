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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once( 'header.php' );

$settings_tabs = msa_get_settings_tabs(); ?>

<h1><?php esc_attr_e( 'Settings', 'msa' ); ?></h1>

<form method="post" class="msa-form">

	<div class="msa-vertical-tabs-menu" role="navigation">

		<ul class="msa-vertical-tabs-list">

			<?php foreach ( $settings_tabs as $key => $settings_tab ) { ?>

			<li class="msa-vertical-tabs-item <?php esc_attr_e( isset( $settings_tab['current'] ) && $settings_tab['current'] ? 'msa-vertical-tabs-current' : '' ); ?>">
				<a href="#<?php esc_attr_e( $key ); ?>"><?php esc_attr_e( $settings_tab['tab'] ); ?></a>
			</li>

			<?php } ?>

		</ul>

	</div>

	<div class="msa-vertical-tabs-content">

		<?php foreach ( $settings_tabs as $key => $settings_tab ) { ?>

			<div id="<?php esc_attr_e( $key ); ?>" class="msa-vertical-tabs-content-item <?php esc_attr_e( isset( $settings_tab['current'] ) && $settings_tab['current'] ? 'msa-vertical-tabs-content-current' : '' ); ?>">
				<?php echo ( apply_filters( 'msa_settings_tab_content_' . $key, $settings_tab['content'] ) ); ?>
			</div>

		<?php } ?>

		<?php submit_button(); ?>

		<?php wp_nonce_field( 'msa-settings' ); ?>

	</div>

</form>

<?php require_once( 'footer.php' );
