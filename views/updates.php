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

		<h1 id="nnr-heading"><?php _e("Updates", self::$text_domain); ?></h1>

		<div class="form-horizontal" role="form">

			<div class="form-group has-feedback <?php echo self::$prefix_dash; ?>license-key-feedback">
				<label class="control-label col-sm-3"><?php _e("License Key", self::$text_domain); ?></label>

				<div class="col-sm-9">
					<div class="col-sm-8" style="padding-left:0px;">
						<input type="text" class="form-control <?php echo self::$prefix_dash; ?>license-key" value="<?php echo $license_key; ?>" placeholder="<?php _e('Enter your License Key', self::$text_domain); ?>"/>
					</div>
				</div>

			</div>

			<!-- Check PHP Version -->

			<?php if ( version_compare(phpversion(), NNROBOTS_CONTENT_AUDIT_MIN_PHP_VERSION) < 1 ) { ?>

				<div class="form-group has-feedback has-error">
					<label class="col-sm-3 control-label"><?php _e("PHP Version Info", self::$text_domain); ?></label>
					<div class="col-sm-9">
						<p class="form-control-static" style="color:#a94442;"><?php _e('The required minimum version of PHP is <strong>v' . NNROBOTS_CONTENT_AUDIT_MIN_PHP_VERSION . '</strong> and your version is <strong>v' . phpversion() . '</strong>. Your version of PHP is outdated and we strongly recommend that you <a href="https://99robots.com/?p=7356" target="_blank">update</a> your version of PHP to at least <strong>v' . NNROBOTS_CONTENT_AUDIT_MIN_PHP_VERSION . '</strong>.', self::$text_domain); ?></p>
					</div>
				</div>

			<?php } else { ?>

				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e("PHP Version Info", self::$text_domain); ?></label>
					<div class="col-sm-9">
						<p class="form-control-static"><?php _e('The required minimum version of PHP is <strong>v' . NNROBOTS_CONTENT_AUDIT_MIN_PHP_VERSION . '</strong> and your version is <strong>v' . phpversion() . '</strong>. You have a sufficient version of a PHP.', self::$text_domain); ?></p>
					</div>
				</div>

			<?php } ?>

			<!-- Display WordPress and Plguin Version -->

			<div class="form-group">
				<label class="col-sm-3 control-label"><?php _e("Version Info", self::$text_domain); ?></label>
				<div class="col-sm-9">
					<p class="form-control-static"><?php _e("You are currently running " . NNROBOTS_CONTENT_AUDIT_ITEM_NAME . "  <strong>v" . NNROBOTS_CONTENT_AUDIT_VERSION_NUM . "</strong> on WordPress <strong>v" . $wp_version . '</strong>', self::$text_domain); ?></p>
				</div>
			</div>

		</div>

		</div>

	</div>

	<?php require_once('footer.php'); ?>

</div>