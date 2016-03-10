<?php
/**
 * This file is responsible for showing the data on the Dashboard Page.
 *
 * @package Views / Dashboard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once( 'header.php' );

$panels = msa_get_dashboard_panels(); ?>

<h1><?php esc_attr_e( 'Dashboard', 'msa' ); ?>
	<a href="<?php esc_attr_e( get_admin_url() . 'index.php?page=msa-getting-started' ); ?>" class="page-title-action"><?php esc_attr_e( 'Getting Started', 'msa' ); ?></a>
</h1>

<div id="dashboard-widgets" class="metabox-holder">

	<div id="postbox-container-1" class="postbox-container">

		<div class="meta-box-sortables meta-box-sortables-left ui-sortable">

			<?php
			$dashboard_panel_order = get_option( 'msa_dashboard_panel_order_' . get_current_user_id() );
			$show_panels = array();

			foreach ( $dashboard_panel_order['left'] as $panel_order ) {

				if ( isset( $panels[ $panel_order ] ) ) {
					$show_panels[ $panel_order ] = $panels[ $panel_order ];
				}
			}

			foreach ( $show_panels as $key => $panel ) { ?>

				<div class="postbox" id="<?php esc_attr_e( $key ); ?>">
					<div class="handlediv" title="Click to toggle"><br></div>
					<h2 class="hndle ui-sortable-handle"><span><?php esc_attr_e( $panel['title'] ); ?></span></h2>
					<div class="inside">
						<?php echo ( apply_filters( 'msa_dashboard_panel_content_' . $key, $panel['content'] ) ); // WPCS: XSS ok. ?>
					</div>
				</div>

			<?php } ?>

		</div>

	</div>

	<div id="postbox-container-2" class="postbox-container">

		<div class="meta-box-sortables meta-box-sortables-right ui-sortable">

			<?php
			$dashboard_panel_order = get_option( 'msa_dashboard_panel_order_' . get_current_user_id() );
			$show_panels = array();

			foreach ( $dashboard_panel_order['right'] as $panel_order ) {

				if ( isset( $panels[ $panel_order ] ) ) {
					$show_panels[ $panel_order ] = $panels[ $panel_order ];
				}
			}

			foreach ( $show_panels as $key => $panel ) { ?>

				<div class="postbox" id="<?php esc_attr_e( $key ); ?>">
					<div class="handlediv" title="Click to toggle"><br></div>
					<h3 class="hndle ui-sortable-handle"><span><?php esc_attr_e( $panel['title'] ); ?></span></h3>
					<div class="inside">
						<?php echo ( apply_filters( 'msa_dashboard_panel_content_' . $key, $panel['content'] ) ); // WPCS: XSS ok. ?>
					</div>
				</div>

			<?php } ?>

		</div>

	</div>

</div>

<?php require_once( 'footer.php' );
