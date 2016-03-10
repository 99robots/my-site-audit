<?php
/**
 * This file is responsible for showing the data on the Extensions Page.
 *
 * @package Views / Extensions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once( 'header.php' );
$extensions = array();

// Get all the remote extensions.
$remote_extensions = msa_get_remote_extensions();
$extensions = array_merge( $remote_extensions, $extensions ); ?>

<h1><?php esc_attr_e( 'Extensions', 'msa' ); ?>
	<a href="<?php esc_attr_e( MY_SITE_AUDIT_EXT_URL ); ?>" class="page-title-action" target="_blank"><?php esc_attr_e( 'Browse All Extensions', 'msa' ); ?></a>
</h1>

<div id="dashboard-widgets" class="metabox-holder">

	<?php foreach ( $extensions as $key => $extension ) { ?>

	<div class="postbox-container">

		<div class="meta-box-sortables">

			<div class="postbox">
				<h2 class="hndle" style="cursor: auto;"><span><?php esc_attr_e( $extension['title'] ); ?></span></h2>
				<div class="inside">
					<img src="<?php esc_attr_e( MY_SITE_AUDIT_PLUGIN_URL . 'images/' . ( isset( $extension['image'] ) && '' !== $extension['image'] ? $extension['image'] : 'coming-soon.png' ) ); ?>"/>
					<a href="<?php esc_attr_e( ( isset( $extension['link'] ) && '' !== $extension['link'] ? $extension['link'] : MY_SITE_AUDIT_EXT_URL ) ); ?>" target="_blank"><?php esc_attr_e( 'Learn More', 'msa' ); ?></a>
					<!-- <p><?php esc_attr_e( $extension['description'] ); ?></p> -->
					<?php esc_attr_e( apply_filters( 'msa_extension_content_' . $key, $extension['content'] ) ); ?>
				</div>
			</div>

		</div>

	</div>

	<?php } ?>

</div>

<?php require_once( 'footer.php' );
