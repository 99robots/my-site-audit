<?php

/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 10/22/15
 * Package: Views/Dashboard
 * File: dashboard.php
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

$panels = msa_get_dashboard_panels();?>

<h1><?php _e('Dashboard', 'msa'); ?></h1>

<div id="dashboard-widgets" class="metabox-holder">

	<div id="postbox-container-1" class="postbox-container">

		<div class="meta-box-sortables ui-sortable">

			<?php foreach ( $panels as $key => $panel ) {

				if ( $panel['postbox'] != 1 ) {
					continue;
				} ?>

				<div class="postbox">
					<h3 class="hndle ui-sortable-handle"><span><?php echo $panel['title']; ?></span></h3>
					<div class="inside">
						<?php echo apply_filters('msa_dashboard_panel_content_' . $key,  $panel['content']); ?>
					</div>
				</div>

			<?php } ?>

		</div>

	</div>

	<div id="postbox-container-2" class="postbox-container">

		<div class="meta-box-sortables ui-sortable">

			<?php foreach ( $panels as $key => $panel ) {

				if ( $panel['postbox'] != 2 ) {
					continue;
				} ?>

				<div class="postbox">
					<h3 class="hndle ui-sortable-handle"><span><?php echo $panel['title']; ?></span></h3>
					<div class="inside">
						<?php echo apply_filters('msa_dashboard_panel_content_' . $key,  $panel['content']); ?>
					</div>
				</div>

			<?php } ?>

		</div>

	</div>

</div>

<?php require_once('footer.php');