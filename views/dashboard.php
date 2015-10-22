<div class="nnr-wrap">

	<?php require_once('header.php'); ?>

	<div class="nnr-container">

		<?php // Display any messaages

		if (isset($_GET['message_text']) && $_GET['message_text'] != '') {

			$status = isset($_GET['message_status']) ? $_GET['message_status'] : 'warning';

			?>
			<div class="alert alert-<?php echo $status; ?> alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<?php echo stripcslashes(esc_attr($_GET['message_text'])); ?>
			</div>
			<?php

		} ?>

		<table class="<?php echo self::$prefix_dash; ?>table table table-responsive table-striped sortable">

			<thead>
				<th data-defaultsort="desc"><?php _e("ID", self::$text_domain); ?></th>
				<th><?php _e("Title", self::$text_domain); ?></th>
				<th><?php _e("Title Length", self::$text_domain); ?></th>
				<th><?php _e("Slug", self::$text_domain); ?></th>
				<th><?php _e("Published Date", self::$text_domain); ?></th>
				<th><?php _e("Modified Date", self::$text_domain); ?></th>
				<th><?php _e("Word Count", self::$text_domain); ?></th>
				<th><?php _e("Comments", self::$text_domain); ?></th>
				<th><?php _e("SEO", self::$text_domain); ?></th>
				<th><?php _e("Focus Keyword", self::$text_domain); ?></th>
				<th><?php _e("Meta Description", self::$text_domain); ?></th>
				<th><?php _e("Meta Description Length", self::$text_domain); ?></th>
				<th><?php _e("Links", self::$text_domain); ?></th>
				<th><?php _e("Internal Links", self::$text_domain); ?></th>
				<th><?php _e("External Links", self::$text_domain); ?></th>

				<?php if ( isset($settings['use_shared_count']) && $settings['use_shared_count'] ) { ?>
					<th><?php _e("Shares", self::$text_domain); ?></th>
				<?php } ?>

				<th><?php _e("Images", self::$text_domain); ?></th>
				<th><?php _e("Headings", self::$text_domain); ?></th>
				<th><?php _e("H1", self::$text_domain); ?></th>
				<th><?php _e("H2", self::$text_domain); ?></th>
				<th><?php _e("H3", self::$text_domain); ?></th>
				<th><?php _e("H4", self::$text_domain); ?></th>
				<th><?php _e("H5", self::$text_domain); ?></th>
				<th><?php _e("H6", self::$text_domain); ?></th>
			</thead>

			<?php $posts = get_posts(array(
				'public' 			=> true,
				'posts_per_page' 	=> -1,
			)); ?>

			<tbody>

				<?php foreach ( $posts as $post ) {

					self::show_audit_data($post, $settings);

				} ?>

			</tbody>

		</table>

	</div>

	<?php require_once('footer.php'); ?>

</div>