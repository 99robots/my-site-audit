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

		<h1 id="nnr-heading"><?php _e("Settings", self::$text_domain); ?></h1>

		<form method="post" class="form-horizontal">

			<div class="form-group">
				<label for="<?php echo self::$prefix_dash; ?>shared-count-api-key" class="col-sm-3 control-label"><?php _e("Shared Count API Key", self::$text_domain); ?></label>
				<div class="col-sm-9">
					<input type="text" class="form-control <?php echo self::$prefix_dash; ?>shared-count-api-key" id="<?php echo self::$prefix_dash; ?>shared-count-api-key" name="<?php echo self::$prefix_dash; ?>shared-count-api-key" value="<?php echo isset($settings['shared_count_api_key']) ? esc_attr($settings['shared_count_api_key']) : ''; ?>" placeholder="">
					<em class="help-block"><?php _e('Input your', self::$text_domain); ?> <a href="https://admin.sharedcount.com/admin/user/home.php" target="_blank"><?php _e("Shared Count API Key", self::$text_domain); ?></a> <?php _e('in order to get the', self::$text_domain); ?> <a href="https://admin.sharedcount.com/faq.php" target="_blank"><?php _e("share count data", self::$text_domain); ?></a> <?php _e('for your site\'s posts.', self::$text_domain); ?></em>
				</div>
			</div>

			<div class="form-group">
				<label for="<?php echo self::$prefix_dash; ?>shared-count-test" class="col-sm-3 control-label"></label>
				<div class="col-sm-9">
					<button type="submit-settings" class="btn btn-default <?php echo self::$prefix_dash; ?>shared-count-test"><?php _e("Test", self::$text_domain); ?></button>
				</div>
			</div>

			<div class="form-group">
				<label for="<?php echo self::$prefix_dash; ?>use-shared-count" class="col-sm-3 control-label"><?php _e("Use Shared Count", self::$text_domain); ?></label>
				<div class="col-sm-9">
					<input type="checkbox" class="<?php echo self::$prefix_dash; ?>use-shared-count" id="<?php echo self::$prefix_dash; ?>use-shared-count" name="<?php echo self::$prefix_dash; ?>use-shared-count" <?php echo isset($settings['use_shared_count']) && $settings['use_shared_count'] ? 'checked="checked"' : ''; ?>>
					<em class="help-block"><?php _e("Do you want to use Shared Count Data with your author stats?", self::$text_domain); ?></em>
				</div>
			</div>

			<button type="submit" name="submit" class="btn btn-info">
				<i class="fa fa-hdd-o"></i> <?php _e("Save", self::$text_domain); ?>
			</button>

			<?php wp_nonce_field(self::$prefix . 'settings'); ?>

		</form>

	</div>

	<?php require_once('footer.php'); ?>

</div>