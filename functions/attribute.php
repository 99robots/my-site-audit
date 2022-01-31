<?php
/**
 * The file is responsible for managing all audit attributes.  Audit attributes act
 * like post types where you can register your own.
 *
 * @package Functions / Attributes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Change the post sort data based on the sort
 *
 * @access public
 * @param mixed $value   The value of the attribute.
 * @param mixed $array   The entire array.
 * @param mixed $orderby The orderby value.
 * @return mixed $value  The new value based on the sort.
 */
function msa_audit_posts_table_sort_data_attribute( $value, $array, $orderby ) {

	// Author.
	if ( 'post_author' === $orderby ) {
		return $array['post']->post_author;
	}

	return $value;

}
add_filter( 'msa_audit_posts_table_sort_data', 'msa_audit_posts_table_sort_data_attribute', 10, 3 );

/**
 * Create the attribute content
 *
 * @access public
 * @param mixed $content  The original content.
 * @param mixed $item     The audit.
 * @param mixed $name     The name of the attribute.
 * @return mixed $content The new content.
 */
function msa_attribute_table_column_post_author( $content, $item, $name ) {

	// Author.
	if ( 'post_author' === $name ) {
		$author = get_userdata( $item['post']->post_author );
		return $author->display_name;
	}

	return $content;
}
add_filter( 'msa_all_posts_table_column_data', 'msa_attribute_table_column_post_author', 10, 3 );

/**
 * Post Type Attribute options for the filters
 *
 * @access public
 * @param mixed $content  The un-filtered content.
 * @return mixed $content The filtered content.
 */
function msa_filter_attribute_post_type_options( $content ) {

    if(empty($content)) {
        $content = array();
    }

	$audit = -1;
	if ( isset( $_GET['audit'] ) ) { // Input var okay.
		$audit = sanitize_text_field( wp_unslash( $_GET['audit'] ) ); // Input var okay.

		// Get all the post types for this audit.
		$audit_model = new MSA_Audits_Model();
		$audit = $audit_model->get_data_from_id( $audit );

		$form_fields = json_decode( $audit['args']['form_fields'], true );

		foreach ( $form_fields['post-types'] as $post_type ) {

			$content[] = array(
				'name'	=> ucfirst( $post_type ),
				'value'	=> $post_type,
			);
		}
	}

	return $content;
}
add_filter( 'msa_filter_attribute_post-type', 'msa_filter_attribute_post_type_options', 10, 1 );

/**
 * Author Attribute options for the filters
 *
 * @access public
 * @param mixed $content  The un-filtered content.
 * @return mixed $content The filtered content.
 */
function msa_filter_attribute_author_options( $content ) {

	$audit = -1;
	if ( isset( $_GET['audit'] ) ) { // Input var okay.
		$audit = sanitize_text_field( wp_unslash( $_GET['audit'] ) ); // Input var okay.

		// Get all authors within an audit.
		$audit_posts_model = new MSA_Audit_Posts_Model();
		$authors = $audit_posts_model->get_authors_in_audit( $audit );
		$content = array();

		foreach ( $authors as $author ) {

			$author_data = get_userdata( $author['post_author'] );

			$content[] = array(
				'name'	=> $author_data->display_name,
				'value'	=> $author['post_author'],
			);
		}
	}

	return $content;
}
add_filter( 'msa_filter_attribute_post_author', 'msa_filter_attribute_author_options', 10, 1 );

/**
 * Filter all the posts shown by the author
 *
 * @access public
 * @param mixed $items  The original audit items.
 * @param mixed $name   The name of the column to be filtered.
 * @param mixed $value  The value of the column.
 * @return mixed $items The new items.
 */
function msa_filter_by_attribute_author( $items, $name, $value ) {

	// Filter by author.
	if ( 'author' === $name && '' !== $value ) {
		foreach ( $items as $key => $item ) {
			if ( $item['post']->post_author !== $value ) {
				unset( $items[ $key ] );
			}
		}
	}

	return $items;

}
add_filter( 'msa_filter_by_attribute', 'msa_filter_by_attribute_author', 10, 3 );


/**
 * Filter all the posts shown by the post type
 *
 * @access public
 * @param mixed $items  The original audit items.
 * @param mixed $name   The name of the column to be filtered.
 * @param mixed $value  The value of the column.
 * @return mixed $items The new items.
 */
function msa_filter_by_attribute_post_type( $items, $name, $value ) {

	// Filter by author.
	if ( 'post-type' === $name && '' !== $value ) {
		foreach ( $items as $key => $item ) {
			if ( $item['post']->post_type !== $value ) {
				unset( $items[ $key ] );
			}
		}
	}

	return $items;

}
add_filter( 'msa_filter_by_attribute', 'msa_filter_by_attribute_post_type', 10, 3 );

/**
 * Create all inital attributes
 *
 * @access public
 * @return void
 */
function msa_create_initial_attributes() {

	// Post Author.
	msa_register_attribute( 'post_author', array(
		'name' 		=> __( 'Author', 'msa' ),
		'post_data'	=> true,
		'filter'	=> array(
			'label'		=> __( 'Authors', 'msa' ),
			'name'		=> 'author',
			'options'	=> '',
		),
	) );

	// Post Type.
	msa_register_attribute( 'post-type', array(
		'name' 			=> __( 'Post Type', 'msa' ),
		'post_data'		=> true,
		'filter'		=> array(
			'label'		=> __( 'Post Types', 'msa' ),
			'name'		=> 'post-type',
			'options'	=> '',
		),
	) );

	do_action( 'msa_register_attributes' );
}


/**
 * Get all the attributes
 *
 * @access public
 * @return mixed $msa_attributes The MSA attributes.
 */
function msa_get_attributes() {

	global $msa_attributes;

	if ( ! is_array( $msa_attributes ) ) {
		$msa_attributes = array();
	}

	return apply_filters( 'msa_get_attributes', $msa_attributes );
}

/**
 * Register a new attribute
 *
 * @access public
 * @param mixed $attribute The new attribute to be added.
 * @param array $args      The args to the new attribute.
 * @return mixed $args     The args to the new attribute.
 */
function msa_register_attribute( $attribute, $args = array() ) {

	global $msa_attributes;

	if ( ! is_array( $msa_attributes ) ) {
		$msa_attributes = array();
	}

	// Default attribute.
	$default = array(
		'name'			=> __( 'Attribute', 'msa' ),
		'value'        	=> 0,
	);

	$args = array_merge( $default, $args );

	// Add the attribute to the global attributes array.
	$msa_attributes[ $attribute ] = apply_filters( 'msa_register_attribute_args', $args );

	/**
	* Fires after a attribute is registered.
	*
	* @param string $attribute Attribute.
	* @param array $args      Arguments used to register the attribute.
	*/
	do_action( 'msa_registed_attribute', $attribute, $args );

	return $args;
}
