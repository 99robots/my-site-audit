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
		return $post->post_excerpt;
	}

	// There is no post excerpt so we need to get part of the post content

	else {

		$the_excerpt = $post->post_content;
	    $the_excerpt = strip_tags(strip_shortcodes($the_excerpt));
	    $words = explode(' ', $the_excerpt, 35 + 1);

	    if ( count($words) > 35 ) {
			array_pop($words);
	        array_push($words, '…');
	        $the_excerpt = implode(' ', $words);
	    }

	    return $the_excerpt;

	}

}

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

	if ( count($audits) > 0 ) {
		return false;
	}

	return true;
}
add_filter('msa_can_add_new_audit', 'msa_add_new_audit_check', 10, 1);