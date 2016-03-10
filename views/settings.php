<?php
/**
 * This file is responsible for showing the data within the Settings Page.
 *
 * @package Views / Settings
 */

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
				<?php echo ( apply_filters( 'msa_settings_tab_content_' . $key, $settings_tab['content'] ) ); // WPCS: XSS ok. ?>
			</div>

		<?php } ?>

		<?php submit_button(); ?>

		<?php wp_nonce_field( 'msa-settings' ); ?>

	</div>

</form>

<?php require_once( 'footer.php' );
