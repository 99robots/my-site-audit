<?php
/**
 * This file gives the developer common functions to use within MSA.
 *
 * @package Functions / Common
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Force the redirect to a url
 *
 * @access public
 * @param mixed $url The URL to redirect to .
 * @return void
 */
function msa_force_redirect( $url ) {
	?><script>
		window.location = "<?php esc_attr_e( $url ); ?>";
	</script><?php
}

/**
 * Returns a safe link to a single audit
 *
 * @param int $audit_id The audit id.
 * @return string $url  The url to an audit with a nonce.
 */
function msa_get_single_audit_link( $audit_id ) {
	return wp_nonce_url( get_admin_url() . 'admin.php?page=msa-all-audits&audit=' . $audit_id, 'msa-single-audit' );
}

/**
 * Returns a safe link to a single audit post
 *
 * @param int $audit_id The audit id.
 * @param int $post_id  The post id.
 * @return string $url  The url to an audit with a nonce.
 */
function msa_get_single_audit_post_link( $audit_id, $post_id ) {
	return wp_nonce_url( get_admin_url() . 'admin.php?page=msa-all-audits&audit=' . $audit_id . '&post=' . $post_id, 'msa-single-audit-post' );
}

/**
 * Get the post excerpt
 *
 * @access public
 * @param object $post         A WP_Post object.
 * @return string $the_excerpt The post excerpt.
 */
function msa_get_post_excerpt( $post ) {

	// Check to see if there is excerpt data.
	if ( isset( $post->post_excerpt ) && '' !== $post->post_excerpt ) {
		$the_excerpt = $post->post_excerpt;
	} else {
		$the_excerpt = $post->post_content;
		$the_excerpt = strip_tags( strip_shortcodes( $the_excerpt ) );
	}

	// Truncate the string if its too long.
	if ( strlen( $the_excerpt ) > 156 ) {
		$the_excerpt = substr( $the_excerpt, 0, 156 ) . '…';
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

	if ( ! isset( $_POST['action_needed'] ) || ! isset( $_POST['column'] ) ) { // Input var okay. // WPCS: CSRF ok.
		echo '';
		die();
	}

	if ( false === ( $show_columns = get_option( 'msa_show_columns_' . get_current_user_id() ) ) ) {
		$show_columns = array();
	}

	if ( 'add' === $_POST['action_needed'] && isset( $_POST['column'] ) ) { // Input var okay. // WPCS: CSRF ok.
		$show_columns[] = sanitize_text_field( wp_unslash( $_POST['column'] ) ); // Input var okay. // WPCS: CSRF ok.
	} else {

        $post_column = sanitize_text_field( wp_unslash( $_POST['column'] ) );

		foreach ( $show_columns as $key => $show_column ) {
			if ( $show_column === $post_column ) { // Input var okay. // WPCS: CSRF ok.
				unset( $show_columns[ $key ] );
			}
		}
	}

	update_option( 'msa_show_columns_' . get_current_user_id(), $show_columns );

	echo wp_json_encode( $show_columns );
	die();
}
add_action( 'wp_ajax_msaShowColumn', 'msa_show_column' );

/**
 * Check to see if we can add a new audit
 *
 * @access public
 * @param mixed $data      The audit data.
 * @return bool true|false Determine if the audit can be added.
 */
function msa_add_new_audit_check( $data ) {

	$audit_model = new MSA_Audits_Model();
	$audits = $audit_model->get_data();

	if ( count( $audits ) >= msa_get_max_audits() ) {
		return false;
	}

	return true;
}
add_filter( 'msa_can_add_new_audit', 'msa_add_new_audit_check', 10, 1 );

/**
 * Filters all the audits from the list of audits
 *
 * @access public
 * @param mixed $audits  All of the audits.
 * @return mixed $audits All of the audits filered.
 */
function msa_save_more_audits_extension( $audits ) {

	if ( count( $audits ) >= msa_get_max_audits() ) {

		$audits[] = array(
			'extension'			=> true,
			'extension-link' 	=> 'https://draftpress.com/products/msa-conditions-control?utm_source=plugin&utm_campaign=extension',
			'score'				=> 1,
			'name'				=> __( 'Want to Save more Audits? Get the Extension!', 'msa' ),
			'date'				=> date( 'Y-m-d H:i:s' ),
			'num_posts'			=> '',
			'user'				=> 0,
		);
	}

	return $audits;
}
add_filter( 'msa_all_audits_table_items', 'msa_save_more_audits_extension', 10, 1 );

/**
 * Get the maximum number of audits a user is allowed to have
 *
 * @access public
 * @return int $msa_get_max_audits The maximum number of audits.
 */
function msa_get_max_audits() {
	return apply_filters( 'msa_get_max_audits', MY_SITE_AUDIT_MAX_AUDITS );
}
