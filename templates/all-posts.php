<?php
/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 10/26/15
 * Package: Template/All Posts
 * File: all-posts.php
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

?>

<h1><?php _e('All Posts', 'msa'); ?></h1>

<form method="get">
	<input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>" />
	<?php
	$all_posts_table = new MSA_All_Posts_Table();
	$all_posts_table->prepare_items();
	$all_posts_table->search_box('Search Posts', 'msa');
	$all_posts_table->display(); ?>
</form>