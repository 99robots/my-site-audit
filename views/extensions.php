<?php
/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 10/22/15
 * Package: Views/Extensions
 * File: extensions.php
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

require_once('header.php');

$extensions = msa_get_extensions();

// Get all the remote extensions

$remote_extensions = msa_get_remote_extensions();

$extensions = array_merge($remote_extensions, $extensions); ?>

<h1><?php _e("Extensions", 'msa'); ?>
	<a href="<?php echo MY_SITE_AUDIT_EXT_URL; ?>" class="page-title-action" target="_blank"><?php _e('Browse All Extensions', 'msa'); ?></a>
</h1>

<div id="dashboard-widgets" class="metabox-holder">

	<?php foreach ( $extensions as $key => $extension ) { ?>

	<div class="postbox-container">

		<div class="meta-box-sortables">

			<div class="postbox">
				<h3 class="hndle" style="cursor: auto;"><span><?php echo $extension['title']; ?></span></h3>
				<div class="inside">
					<?php echo apply_filters('msa_extension_content_' . $key, $extension['content']); ?>
				</div>
			</div>

		</div>

	</div>

	<?php } ?>

</div>

<?php require_once('footer.php'); ?>