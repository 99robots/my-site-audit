<?php
/**
 * This file is responsible for handling the conditions of an audit.  Audit conditions
 * are used to calcuate the score of a post.
 *
 * @package Functions / Conditions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Show the content for the condition values that need custom implementation.
 *
 * @access public
 * @param mixed  $value   The value of the condition.
 * @param mixed  $data    The audit data.
 * @param object $post    The WP_Post object.
 * @param mixed  $key     The condition key.
 * @return string $output The HTML output of the condition.
 */
function msa_condition_category_content_value( $value, $data, $post, $key ) {

	// Invalid Data.
	if ( 'broken_links' === $key || 'broken_images' === $key || 'invalid_headings' === $key ) {
		if ( $value >= 9999 ) {
			return __( 'Count: 0', 'msa' );
		}
	}

	// Excerpt Length.
	if ( 'excerpt_length' === $key ) {
		return strlen( $post->post_excerpt );
	}

	// Modified Date.
	if ( 'modified_date' === $key ) {
		return date( 'M j, Y', strtotime( $post->post_modified ) );
	}

	// Links.
	if ( 'internal_links' === $key || 'external_links' === $key || 'broken_links' === $key ) {
		return msa_show_links( $data, $key );
	}

	// Images.
	if ( 'image_count' === $key ) {
		return msa_show_images( $post->post_content );
	}

	// Missing Alt Tag.
	if ( 'missing_alt_tag' === $key ) {
		return msa_show_images_without_alt( $post->post_content );
	}

	// H1 Tags.
	if ( 'h1_tag' === $key ) {
		return msa_show_h1_tags( $post->post_content, $data );
	}

	// Invalid Headings.
	if ( 'invalid_headings' === $key ) {
		return msa_show_invalid_headings( $data['invalid_headings_data'] );
	}

	// Headings.
	if ( 'heading_count' === $key ) {
		return msa_show_headings( $post->post_content, $data );
	}

	return $value;

}
add_filter( 'msa_condition_category_content_value', 'msa_condition_category_content_value', 10, 4 );

/**
 * Filter the goal for the condition
 *
 * @access public
 * @param mixed $goal       The goal value to achieve.
 * @param mixed $data       The audit data.
 * @param mixed $post       The WP_Post object.
 * @param mixed $key        The condition key.
 * @param mixed $condition  The condition attributes.
 * @return string $output   The HTML output of the condition goal.
 */
function msa_condition_category_content_goal( $goal, $data, $post, $key, $condition ) {

	// Modified Date.
	if ( 'modified_date' === $key ) {
		if ( 1 === $condition['comparison'] ) {
			$goal = __( 'Greater Than ' . round( $condition['min'] / DAY_IN_SECONDS ) . ' Days', 'msa' );
		} else if ( 2 === $condition['comparison'] ) {
			$goal = __( 'Less Than ' . round( $condition['max'] / DAY_IN_SECONDS ) . ' Days', 'msa' );
		} else if ( 3 === $condition['comparison'] ) {
			$goal = __( 'In Between ' . round( $condition['min'] / DAY_IN_SECONDS ) . ' - ' . round( $condition['max'] / DAY_IN_SECONDS ) . ' Days', 'msa' );
		}
	}

	return $goal;
}
add_filter( 'msa_condition_category_content_goal', 'msa_condition_category_content_goal', 10, 5 );

/**
 * Output the correct data within the filter dropdown.
 *
 * @param array $condition  The original condition.
 * @return array $condition The new condition.
 */
function msa_audit_posts_filter_modified_date( $condition ) {

	$condition['units'] = __( 'Days', 'msa' );
	$condition['min'] = round( $condition['min'] / DAY_IN_SECONDS );
	$condition['max'] = round( $condition['max'] / DAY_IN_SECONDS );

	return $condition;
}
add_filter( 'msa_audit_posts_filter_modified_date', 'msa_audit_posts_filter_modified_date', 10, 1 );

/**
 * Create initial conidtions
 *
 * @access public
 * @return void
 */
function msa_create_initial_conditions() {

	/**
	 * Comparsion:
	 *
	 * 1 = greater than some number
	 * 2 = less than some number
	 * 3 = in between some numbers
	 */
	/**
	 * Value:
	 *
	 * 1 = boolean result (i.e pass or fail)
	 * 2 = ratio (i.e. .123)
	 */

	/**
	 * Content
	 */

	// Title.
	msa_register_condition('title_length', array(
		'name' 				=> __( 'Title Length', 'msa' ),
		'description' 		=> __( 'The length of your title should be less than 60 characters so that the title summarizes your post succinctly and is easily read quickly.  Google also displays only 60 characters so having a longer title may mean that a portion of the title will get cut of in search results and other sources.', 'msa' ),
		'weight'        	=> 5,
		'comparison'		=> 2,
		'value'				=> 1,
		'max'           	=> 60,
		'units'				=> 'characters',
		'max_display_val'	=> __( '60 Characters', 'msa' ),
		'category'			=> 'content',
		'show_column'		=> true,
		'settings'		=> array(
			'id'				=> 'msa-condition-title_length',
			'class'				=> 'msa-condition-title_length',
			'name'				=> 'msa-condition-title_length',
			'description-max'	=> __( 'The maximum number of characters the Title can be.', 'msa' ),
		),
		'filter'		=> array(
			'label'		=> __( 'Title Lengths', 'msa' ),
			'name'		=> 'title_length',
		),
	));

	// Excerpt Length.
	msa_register_condition('excerpt_length', array(
		'name' 				=> __( 'Excerpt Length', 'msa' ),
		'description' 		=> __( 'The length of your post excerpt should be within the limits of most search engines, including Google and therefore should be between 2 and 155 characters.  Please make sure to edit the excerpt to fit within these bounds so that the description is succinct and does not get cut off.', 'msa' ),
		'weight'        	=> 5,
		'comparison'		=> 3,
		'value'				=> 1,
		'min'           	=> 1,
		'max'				=> 156,
		'units'				=> 'characters',
		'category'			=> 'content',
		'show_column'		=> true,
		'settings'		=> array(
			'id'				=> 'msa-condition-excerpt_length',
			'class'				=> 'msa-condition-excerpt_length',
			'name'				=> 'msa-condition-excerpt_length',
			'description-max'	=> __( 'The number of characters the excerpt can be.', 'msa' ),
		),
		'filter'		=> array(
			'label'		=> __( 'Excerpt Length', 'msa' ),
			'name'		=> 'excerpt_length',
		),
	));

	// Modified Date.
	msa_register_condition('modified_date', array(
		'name' 				=> __( 'Modified Date', 'msa' ),
		'description' 		=> __( 'Your content should be updated continually to ensure that it contains the most up-to-date information within the article and does not contain any broken links for pages that may have been retired or expired.  Search engines look at when a post has been last updated and rank items that have been updated recently higher.', 'msa' ),
		'weight'        	=> 5,
		'comparison'		=> 2,
		'value'				=> 2,
		'units'				=> 'seconds',
		'max'          		=> DAY_IN_SECONDS * 180,
		'max_display_val'	=> __( '90 Days', 'msa' ),
		'category'			=> 'content',
		'show_column'		=> true,
		'settings'		=> array(
			'id'				=> 'msa-condition-modified_date',
			'class'				=> 'msa-condition-modified_date',
			'name'				=> 'msa-condition-modified_date',
			'description-max'	=> __( 'The maximum number of seconds your posts can go without being modified.', 'msa' ),
		),
		'filter'		=> array(
			'label'		=> __( 'Modified Dates', 'msa' ),
			'name'		=> 'modified_date',
		),
	));

	// Word Count.
	msa_register_condition('word_count', array(
		'name' 				=> __( 'Word Count', 'msa' ),
		'description' 		=> __( 'You want to have a good balance between providing your readers with enough information and background about the topic at hand as well as giving enough information to Search engines to properly index and rank your page.  750 words is a good starting point towards providing both.', 'msa' ),
		'weight'        	=> 15,
		'comparison'		=> 1,
		'value'				=> 2,
		'min'           	=> 750,
		'units'				=> 'words',
		'min_display_val'	=> __( '750 Words', 'msa' ),
		'category'			=> 'content',
		'show_column'		=> true,
		'settings'		=> array(
			'id'				=> 'msa-condition-word_count',
			'class'				=> 'msa-condition-word_count',
			'name'				=> 'msa-condition-word_count',
			'description-min'	=> __( 'The minimum number of words each post should have.', 'msa' ),
		),
		'filter'		=> array(
			'label'		=> __( 'Word Counts', 'msa' ),
			'name'		=> 'word_count',
		),
	));

	// Comment Count.
	msa_register_condition('comment_count', array(
		'name' 				=> __( 'Comment Count', 'msa' ),
		'description' 		=> __( 'Creating an environment for constructive discussion is an important part of increasing awareness and attention to the post and your site in general.  You should aim to create content that facilitates discussion and also take the time to respond to any readers that may have questions or comments about the post.', 'msa' ),
		'weight'        	=> 5,
		'comparison'		=> 1,
		'value'				=> 2,
		'min'           	=> 5,
		'units'				=> 'comments',
		'min_display_val'	=> __( '5 Comments', 'msa' ),
		'category'			=> 'content',
		'show_column'		=> true,
		'settings'		=> array(
			'id'				=> 'msa-condition-comment_count',
			'class'				=> 'msa-condition-comment_count',
			'name'				=> 'msa-condition-comment_count',
			'description-min'	=> __( 'The minimum number of comments each post should have.', 'msa' ),
		),
		'filter'		=> array(
			'label'		=> __( 'Comment Counts', 'msa' ),
			'name'		=> 'comment_count',
		),
	));

	/**
	 * Links
	 */

	// Internal Links.
	msa_register_condition('internal_links', array(
		'name' 				=> __( 'Internal Links', 'msa' ),
		'description' 		=> __( 'Linking to other portions of your site to explain things within your post or cross-promote other stories to keep users on your site is important.  Each post should provide the user some way of finding out more information or allow the reader to explore other areas of your site.', 'msa' ),
		'weight'        	=> 8,
		'comparison'		=> 1,
		'value'				=> 2,
		'min'           	=> 3,
		'units'				=> 'links',
		'min_display_val'	=> __( '3 Internal Links', 'msa' ),
		'category'			=> 'links',
		'show_column'		=> true,
		'settings'		=> array(
			'id'				=> 'msa-condition-internal_links',
			'class'				=> 'msa-condition-internal_links',
			'name'				=> 'msa-condition-internal_links',
			'description-min'	=> __( 'The minimum number of Internal Links each post should have.', 'msa' ),
			'description-max'	=> __( 'The maximum number of Internal Links each post should have.', 'msa' ),
		),
		'filter'		=> array(
			'label'		=> __( 'Internal Links', 'msa' ),
			'name'		=> 'internal_links',
		),
	));

	// External Links.
	msa_register_condition('external_links', array(
		'name' 				=> __( 'External Links', 'msa' ),
		'description' 		=> __( 'Linking to other sites that have a good domain authority or ranking will improve your ranking.  Also, these links will have the user quickly navigate to an external source.', 'msa' ),
		'weight'        	=> 8,
		'comparison'		=> 1,
		'value'				=> 2,
		'min'           	=> 6,
		'units'				=> 'links',
		'min_display_val'	=> __( '6 External Links', 'msa' ),
		'category'			=> 'links',
		'show_column'		=> true,
		'settings'		=> array(
			'id'				=> 'msa-condition-external_links',
			'class'				=> 'msa-condition-external_links',
			'name'				=> 'msa-condition-external_links',
			'description-min'	=> __( 'The minimum number of External Links each post should have.', 'msa' ),
			'description-max'	=> __( 'The maximum number of External Links each post should have.', 'msa' ),
		),
		'filter'		=> array(
			'label'		=> __( 'External Links', 'msa' ),
			'name'		=> 'external_links',
		),
	));

	/**
	 * Images
	 */

	// Images.
	msa_register_condition('image_count', array(
		'name' 				=> __( 'Images', 'msa' ),
		'description' 		=> __( 'Images enhance the reading experience and break up the monotony of lots of text as well as improve your ranking in search engines, try to have at least 3 images!', 'msa' ),
		'weight'        	=> 12,
		'comparison'		=> 1,
		'value'				=> 2,
		'min'           	=> 3,
		'units'				=> 'images',
		'min_display_val'	=> __( '2 Images', 'msa' ),
		'category'			=> 'images',
		'show_column'		=> true,
		'settings'		=> array(
			'id'				=> 'msa-condition-image_count',
			'class'				=> 'msa-condition-image_count',
			'name'				=> 'msa-condition-image_count',
			'description-min'	=> __( 'The minimum number of images each post should have.', 'msa' ),
		),
		'filter'		=> array(
			'label'		=> __( 'Images', 'msa' ),
			'name'		=> 'image_count',
		),
	));

	// Missing Alt Tag.
	msa_register_condition('missing_alt_tag', array(
		'name' 				=> __( 'Missing Alt Tag', 'msa' ),
		'description' 		=> __( 'Image alt tags are very useful to search engines when they index your images.  If image do not have an alt tab then you are not presenting your images in the more optimal way.', 'msa' ),
		'weight'        	=> 8,
		'comparison'		=> 2,
		'value'				=> 1,
		'max'           	=> 1,
		'units'				=> 'missing alt tags',
		'max_display_val'	=> __( '1 Missing Alt Tag', 'msa' ),
		'category'			=> 'images',
		'show_column'		=> true,
		'settings'		=> array(
			'id'				=> 'msa-condition-missing_alt_tag',
			'class'				=> 'msa-condition-missing_alt_tag',
			'name'				=> 'msa-condition-missing_alt_tag',
			'description-max'	=> __( 'The maximum number of images without an alt tag allowed per post.', 'msa' ),
		),
		'filter'		=> array(
			'label'		=> __( 'Missing Alt Tags', 'msa' ),
			'name'		=> 'missing_alt_tag',
		),
	));

	/**
	 * Headings
	 */

	// Headings.
	msa_register_condition('heading_count', array(
		'name' 				=> __( 'Headings', 'msa' ),
		'description' 		=> __( 'Having multiple headings helps to summarize and section your post into logical parts.  It also lets google index your page and provides additional information to what the article is about.  You should try to have at least 5 headings for a post of 750 characters.', 'msa' ),
		'weight'        	=> 13,
		'comparison'		=> 1,
		'value'				=> 2,
		'min'           	=> 5,
		'units'				=> 'headings',
		'min_display_val'	=> __( '5 Headings', 'msa' ),
		'category'			=> 'headings',
		'show_column'		=> true,
		'settings'		=> array(
			'id'				=> 'msa-condition-heading_count',
			'class'				=> 'msa-condition-heading_count',
			'name'				=> 'msa-condition-heading_count',
			'description-min'	=> __( 'The minimum number of headings each post should have.', 'msa' ),
		),
		'filter'		=> array(
			'label'		=> __( 'Headings', 'msa' ),
			'name'		=> 'heading_count',
		),
	));

	// No h1 tag.
	msa_register_condition('h1_tag', array(
		'name' 				=> __( 'Has H1 Tags', 'msa' ),
		'description' 		=> __( 'Your post should only have 1 H1 tag, which should not be located within the content of the page.  The H1 tag should be displayed for the title of post.', 'msa' ),
		'weight'        	=> 3,
		'comparison'		=> 2,
		'value'				=> 1,
		'max'           	=> 1,
		'units'				=> 'h1 tags',
		'max_display_val'	=> __( '1 H1 Tag', 'msa' ),
		'category'			=> 'headings',
		'show_column'		=> true,
		'settings'		=> array(
			'id'				=> 'msa-condition-h1-tag',
			'class'				=> 'msa-condition-h1-tag',
			'name'				=> 'msa-condition-h1-tag',
			'description-max'	=> __( 'The minimum number of H1 tags allowed for each post.', 'msa' ),
		),
		'filter'		=> array(
			'label'		=> __( 'H1 Tags', 'msa' ),
			'name'		=> 'h1_tag',
		),
	));

	// Invalid Headings.
	msa_register_condition('invalid_headings', array(
		'name' 				=> __( 'Invalid Headings', 'msa' ),
		'description' 		=> __( 'Headings should only have text as content.  They should not contain links or other HTML tags or formatting within them.', 'msa' ),
		'weight'        	=> 4,
		'comparison'		=> 2,
		'value'				=> 1,
		'max'           	=> 1,
		'units'				=> 'invalid headings',
		'max_display_val'	=> __( '1 Invalid Heading', 'msa' ),
		'category'			=> 'headings',
		'show_column'		=> true,
		'settings'		=> array(
			'id'				=> 'msa-condition-invalid-headings',
			'class'				=> 'msa-condition-invalid-headings',
			'name'				=> 'msa-condition-invalid-headings',
			'description-max'	=> __( 'The minimum number of invalid headings allowed for each post.', 'msa' ),
		),
		'filter'		=> array(
			'label'		=> __( 'Invalid Headings', 'msa' ),
			'name'		=> 'invalid_headings',
		),
	));

	do_action( 'msa_register_conditions' );
}

/**
 * Get total weight of all registered conditions
 *
 * @access public
 * @return int $weight The total condition weight.
 */
function msa_get_total_conditions_weight() {

	$conditions = msa_get_conditions();
	$weight = 0;

	foreach ( $conditions as $condition ) {
		$weight += $condition['weight'];
	}

	return $weight;
}

/**
 * Get total weight for specific conditions
 *
 * @access public
 * @param array $conditions All conditions.
 * @return int $weight      The total weight for all conditions.
 */
function msa_get_total_weight_for_conditions( $conditions ) {
	$weight = 0;

	foreach ( $conditions as $condition ) {
		$weight += $condition['weight'];
	}

	return $weight;
}

/**
 * Get all the conditions
 *
 * @access public
 * @return array $msa_conditions The conditions array.
 */
function msa_get_conditions() {

	global $msa_conditions;

	if ( ! is_array( $msa_conditions ) ) {
		$msa_conditions = array();
	}

	return apply_filters( 'msa_get_conditions', $msa_conditions );
}

/**
 * Filter the conditions
 *
 * @access public
 * @param mixed $conditions  All conditions.
 * @return mixed $conditions Filtered conditions.
 */
function msa_get_conditions_filter( $conditions ) {

	$deregistered_conditions = msa_get_deregistered_conditions();

	foreach ( $deregistered_conditions as $key ) {
		unset( $conditions[ $key ] );
	}

	return $conditions;
}
add_filter( 'msa_get_conditions', 'msa_get_conditions_filter', 10, 1 );

/**
 * Register a new condition
 *
 * @access public
 * @param mixed $condition  The slug for the new condition.
 * @param array $args       The args for the new condition.
 * @return array $args      The args for the new condition.
 */
function msa_register_condition( $condition, $args = array() ) {

	// Make sure this is not already a condition category.
	$condition_categories = msa_get_condition_categories();

	if ( isset( $condition_categories[ $condition ] ) ) {
		return false;
	}

	global $msa_conditions;

	if ( ! is_array( $msa_conditions ) ) {
		$msa_conditions = array();
	}

	// Default condition.
	$default = array(
		'name'			=> __( 'Condition', 'msa' ),
		'weight'        => 1,
		'comparison'	=> 0,
		'value'			=> 0,
		'max'           => 0,
		'min'			=> 0,
	);

	$args = array_merge( $default, $args );

	// Add the condition to the global conditions array.
	$msa_conditions[ $condition ] = apply_filters( 'msa_register_condition_args', $args );

	/**
	* Fires after a condition is registered.
	*
	* @param string $condition Condition.
	* @param array $args      Arguments used to register the condition.
	*/
	do_action( 'msa_registed_condition', $condition, $args );

	return $args;
}

/**
 * De-register Condition
 *
 * @access public
 * @param mixed $condition The condition to de-register.
 * @return void
 */
function msa_deregister_condition( $condition ) {

	global $msa_deregisterd_conditions;

	if ( ! is_array( $msa_deregisterd_conditions ) ) {
		$msa_deregisterd_conditions = array();
	}

	$msa_deregisterd_conditions[] = $condition;
}

/**
 * Get the de-registered Conditions
 *
 * @access public
 * @return array $msa_deregisterd_conditions All de-registered conditions.
 */
function msa_get_deregistered_conditions() {

	global $msa_deregisterd_conditions;

	if ( ! is_array( $msa_deregisterd_conditions ) ) {
		$msa_deregisterd_conditions = array();
	}

	return apply_filters( 'msa_get_deregistered_conditions', $msa_deregisterd_conditions );
}
