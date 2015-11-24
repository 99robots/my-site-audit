<?php
/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 10/30/15
 * Package: Functions/Common
 * File: common.php
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

/**
 * Force the redirect to a url
 *
 * @access public
 * @param mixed $url
 * @return void
 */
function msa_force_redirect($url) {
	?><script>
		window.location = "<?php echo $url; ?>";
	</script><?php
}

/**
 * Get the post excerpt
 *
 * @access public
 * @param mixed $post
 * @return void
 */
function msa_get_post_excerpt($post) {

	// Check to see if there is excerpt data

	if ( isset($post->post_excerpt) && $post->post_excerpt != '' ) {
		$the_excerpt = $post->post_excerpt;
	}

	// There is no post excerpt so we need to get part of the post content

	else {

		$the_excerpt = $post->post_content;
	    $the_excerpt = strip_tags(strip_shortcodes($the_excerpt));
	}

	// Truncate the string if its too long

	if ( strlen( $the_excerpt ) > 156 ) {
		$the_excerpt = substr($the_excerpt, 0, 156) . '…';
	}

	return $the_excerpt;

}

/**
 * Add or remove the show columns
 *
 * @access public
 * @return void
 */
function msa_show_column() {

	if ( !isset($_POST['action_needed']) || !isset($_POST['column']) ) {
		echo '';
		die();
	}

	if ( false === ( $show_columns = get_option( 'msa_show_columns_' . get_current_user_id() ) ) ) {
		$show_columns = array();
	}

	if ( $_POST['action_needed'] == 'add' ) {
		$show_columns[] = $_POST['column'];
	} else {

		foreach ( $show_columns as $key => $show_column ) {
			if ( $show_column == $_POST['column'] ) {
				unset($show_columns[$key]);
			}
		}
	}

	update_option( 'msa_show_columns_' . get_current_user_id(), $show_columns );

	echo json_encode($show_columns);
	die();
}
add_action('wp_ajax_msa_show_column', 'msa_show_column');

/**
 * Check to see if we can add a new audit
 *
 * @access public
 * @param mixed $data
 * @return void
 */
function msa_add_new_audit_check($data) {

	$audit_model = new MSA_Audits_Model();
	$audits = $audit_model->get_data();

	if ( count($audits) >= MY_SITE_AUDIT_MAX_AUDITS ) {
		return false;
	}

	return true;
}
add_filter('msa_can_add_new_audit', 'msa_add_new_audit_check', 10, 1);

/**
 * Filters all the audits from the list of audits
 *
 * @access public
 * @param mixed $audit
 * @return void
 */
function msa_save_more_audits_extension($audits) {

	if ( count($audits) >= MY_SITE_AUDIT_MAX_AUDITS ) {

		$audits[] = array(
			'extension'			=> true,
			'extension-link' 	=> 'https://mysiteaudit.com/?post_type=download&p=74&utm_source=plugin&utm_campaign=extension',
			'score'				=> 1,
			'name'				=> __('Want to Save more Audits? Get the Extension!', 'msa'),
			'date'				=> date('Y-m-d H:i:s'),
			'num_posts'			=> '',
			'user'				=> 0,
		);
	}

	return $audits;

}
add_filter('msa_all_audits_table_items', 'msa_save_more_audits_extension', 10, 1);