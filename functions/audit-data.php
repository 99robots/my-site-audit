<?php
/**
 * This file handles all of the actual auditing.  After the content is audited the
 * data is then returned.
 *
 * @package Functions / Audit Data
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Calculate the score of a post
 *
 * @access public
 * @param object $post  A WP_Post obejct.
 * @param array  $data  The relevant audit data.
 * @return array $array The score and audit data.
 */
function msa_calculate_score( $post, $data ) {

	$score_data = array();
	$score = 0;
	$weight = 0;
	$conditions = msa_get_conditions();

	foreach ( $conditions as $key => $condition ) {

		// Greater Than.
		if ( 1 === $condition['comparison'] && isset( $condition['min'] ) ) {
			if ( 0 !== $condition['min'] ) {
				$value = min( $data[ $key ] / $condition['min'], 1 );
			} else {
				$value = 1;
			}
		}

		// Less Than.
		if ( 2 === $condition['comparison'] && isset( $condition['max'] ) ) {
			if ( 0 !== $condition['max'] ) {
				$value = 1 - min( $data[ $key ] / $condition['max'], 1 );
			} else {
				$value = 0;
			}
		}

		// Range.
		if ( 3 === $condition['comparison'] && isset( $condition['max'] ) && isset( $condition['min'] ) ) {

			$range = $condition['max'] - $condition['min'] + 2;
			$mean = ($condition['max'] + $condition['min']) / 2;

			$value = ( 1 -  min( floor( abs( $data[ $key ] - $mean ) ) / floor( ( $range / 2 ) ) , 1 ) );

		}

		// Convert to bool if needed.
		$value = 1 === $condition['value'] && 0 !== $value ? 1 : $value;

		$score_data[ $key ] = $value;
		$score += $value * $condition['weight'];
		$weight += $condition['weight'];

	}

	// Get the score data for condition categories.
	$condition_categories = msa_get_condition_categories();

	foreach ( $condition_categories as $key => $condition_category ) {
		$score_data[ $key ] = msa_get_condition_catergory_score( $key, $score_data );
	}

	$score = 0 !== $weight ? round( $score / $weight , 2 ) : 0;

	return array(
		'score'	=> $score,
		'data'	=> $score_data,
	);
}

/**
 * Get all the audit data for a specific post
 *
 * @access public
 * @param object $post A WP_Post object.
 * @return array $data The audi data.
 */
function msa_get_post_audit_data( $post ) {

	$data = array();

	/**
	 * Content
	 */

	$content   = preg_replace( '/&#?[a-z0-9]{2,8};/i', '', $post->post_content );
	$content   = strip_tags( $content );
	$content   = preg_replace( '~(?:\[/?)[^/\]]+/?\]~s', '', $content );

	$data['title_length']      = strlen( $post->post_title );
	$data['excerpt_length']    = strlen( $post->post_excerpt );
	$data['modified_date']     = max( time() - strtotime( $post->post_modified ), 0 );
	$data['word_count']        = str_word_count( $content );
	$data['comment_count']     = $post->comment_count;

	/**
	 * Links
	 */

	preg_match_all( "/<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>/siU" , $post->post_content, $data['link_matches'], PREG_SET_ORDER );

	$data['internal_links'] = 0;
	$data['external_links'] = 0;
	$data['broken_links'] = 0;

	$data['internal_links_data'] = array();
	$data['external_links_data'] = array();
	$data['broken_links_data'] = array();

	if ( isset( $data['link_matches'] ) && is_array( $data['link_matches'] ) ) {

		foreach ( $data['link_matches'] as $key => $link ) {

			$url = wp_parse_url( $link[2] );
			$site_url = wp_parse_url( get_site_url() );

			// Internal Link - External Link.
			if ( '#' === substr( $link[2], 0 , 1 ) || ( isset( $site_url['host'] ) && isset( $url['host'] ) && $site_url['host'] === $url['host'] ) ) {
				$data['internal_links_data'][] = array(
					'url'		=> $link[2],
				);
				$data['internal_links']++;
			} else {
				$data['external_links_data'][] = array(
					'url'		=> $link[2],
				);
				$data['external_links']++;
			}

			// Check if link is broken.
			if ( msa_is_link_broken( $link[2] ) ) {
				$data['broken_links_data'][] = array(
					'url'		=> $link[2],
				);
				$data['broken_links']++;
			}
		}
	}

	// Set the broken links to a high number if there are no links.
	if ( 0 === $data['internal_links'] + $data['external_links'] ) {
		$data['broken_links'] = 9999;
	}

	/**
	 * Images
	 */

	$data['missing_alt_tag'] = 0;
	$data['broken_images'] = 0;
	$data['broken_images_data'] = array();
	$data['image_count'] = substr_count( $post->post_content, '<img' );

	preg_match_all( '/(<img[^>]+>)/i', $post->post_content, $img_matches, PREG_SET_ORDER );

	foreach ( $img_matches as $match ) {

		// Check for broken image.
		preg_match( '/src="([^"]*)"/i', $match[0], $link );
		$link = $link[1];

		if ( msa_is_link_broken( $link ) ) {
			$data['broken_images_data'][] = array(
				'url'		=> $link,
			);
			$data['broken_images']++;
		}

		// Check for an alt tag.
		preg_match( '/alt="([^"]*)"/i', $match[0], $alt );
		$alt = $alt[1];

		if ( empty( $alt ) || '' === $alt ) {
			$data['missing_alt_tag']++;
		}
	}

	// Set the missing alt tags to a fail if there are no images.
	if ( 0 === $data['image_count'] ) {
		$data['missing_alt_tag'] = 9999; // We will set this to some high number so that it fails the test.
		$data['broken_images'] = 9999;
	}

	/**
	 * Headings
	 */

	$data['h1_tag'] = substr_count( $post->post_content, '<h1' );

	preg_match_all( '|<\s*h[1-6](?:.*)>(.*)</\s*h[1-6]>|Ui', $post->post_content, $headings_matches, PREG_SET_ORDER );

	$data['invalid_headings'] = 0;
	$data['invalid_headings_data'] = array();

	foreach ( $headings_matches as $match ) {
		if ( '' === strip_tags( $match[1], '<h1><h2><h3><h4><h5><h6>' ) ) {
			$data['invalid_headings']++;
			$data['invalid_headings_data'][] = array(
				'html'		=> $match[0],
				'text'		=> $match[1],
			);
		}
	}

	preg_match_all( '/<h([1-6])/', $post->post_content, $matches );
	$data['heading_count'] = count( $matches[0] );

	// Set the invalid headins to a high number if there are no headings.
	if ( 0 === $data['heading_count'] ) {
		$data['invalid_headings'] = 9999;
	}

	// Return the data.
	return apply_filters( 'msa_get_post_audit_data', $data, $post );
}

/**
 * Check if a link is broken
 *
 * @access public
 * @param mixed $url       The URL to be checked.
 * @return bool true|false Whether or not the URL is broken.
 */
function msa_is_link_broken( $url ) {

	return false;

	// Check if user wants to perform long task.
	if ( false === ( $settings = get_option( 'msa_settings' ) ) ) {
		$settings = array();
	}

	if ( ! isset( $settings['use_slow_conditions'] ) || ( isset( $settings['use_slow_conditions'] ) && ! $settings['use_slow_conditions'] ) ) {
		return false;
	}

	// Check if this is hash link.
	if ( '#' === substr( $url, 0, 1 ) ) {
		return false;
	}

	// Check if this is valid URL before we begin the query.
	if ( is_string( $url ) && preg_match( '/^http(s)?:\/\/[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(\/.*)?$/i', $url ) ) {
		// @todo add wp_remote_get support
		return false;
	} else {
		return true;
	}

	if ( isset( $httpcode ) && ( 404 === $httpcode || 0 === $httpcode ) ) {
		return true;
	}

	return false;
}

/**
 * Show all the links
 *
 * @access public
 * @param mixed $data     The audit data.
 * @param mixed $key      The key to access specfic parts of the data.
 * @return string $output The HTML of the links to be shown.
 */
function msa_show_links( $data, $key ) {

	if ( isset( $data[ $key . '_data' ] ) && is_array( $data[ $key . '_data' ] ) ) {

		$links = $data[ $key . '_data' ];
		$matches = 0;
		$output = '<ol class="msa-link-list msa-link-list-' . $key . '">';

		// Get all the broken links.
		$broken_links = array();

		foreach ( $data['broken_links_data'] as $broken_link ) {
			$broken_links[] = $broken_link['url'];
		}

		// Valid links.
		foreach ( $links as $link ) {

			if ( ! in_array( $link['url'], $broken_links, true ) ) {
				$output .= '<li class="msa-link"><a href="' . $link['url'] . '" target="_blank">' . $link['url'] . '</a></li>';
			} else {
				$output .= '<li class="msa-link msa-broken-link"><a href="' . $link['url'] . '" target="_blank">' . $link['url'] . '</a></li>';
			}

			$matches++;
		}

		$output .= '</ol></p>';

		if ( 0 === $matches ) {
			return __( 'No Links', 'msa' );
		}
	} else {
		return __( 'No Links', 'msa' );
	}

	return $output;
}

/**
 * Show the images for a post
 *
 * @access public
 * @param mixed $content  The post content.
 * @return string $output The HTML output of the images to be shown.
 */
function msa_show_images( $content ) {

	preg_match_all( '/(<img[^>]+>)/i', $content, $matches, PREG_SET_ORDER );

	$images = 0;

	$output = __( 'Count: ' . count( $matches ), 'msa' );
	$output .= '<div class="msa-images">';

	// Has Alt tag.
	foreach ( $matches as $match ) {

		// Get the scr URL.
		$link = array();
		preg_match( '/src="([^"]*)"/i', $match[0], $link );

		if ( isset( $link[1] ) ) {
			$link = $link[1];
		} else {
			$link = '';
		}

		// Check for an alt tag.
		preg_match( '/alt="([^"]*)"/i', $match[0], $alt );

		if ( isset( $alt[1] ) ) {
			$alt = $alt[1];
		} else {
			$alt = '';
		}

		$class = '';

		if ( ! isset( $alt ) || ( isset( $alt ) && '' === $alt ) ) {
			$class = 'msa-missing-alt-tag';
		}

		$output .= '<div class="attachment ' . $class . '">
			<div class="attachment-preview">
				<div class="thumbnail">
					<a class="centered" href="' . $link . '" target="_blank">' . $match[0] . '</a>
				</div>
			</div>
		</div>';
		$images++;
	}

	$output .= '</div>';

	if ( 0 === $images ) {
		$output = __( 'No Images', 'msa' );
	}

	return $output;
}

/**
 * Show the images without an alt tag
 *
 * @access public
 * @param mixed $content  The post content.
 * @return string $output The HTML output of the images to be shown.
 */
function msa_show_images_without_alt( $content ) {

	preg_match_all( '/(<img[^>]+>)/i', $content, $matches, PREG_SET_ORDER );

	$images = 0;
	$output = '<div class="msa-images">';

	// Does not have alt tag.
	foreach ( $matches as $match ) {

		// Get the scr URL.
		$link = array();
		preg_match( '/src="([^"]*)"/i', $match[0], $link );

		if ( isset( $link[1] ) ) {
			$link = $link[1];
		} else {
			$link = '';
		}

		// Check for an alt tag.
		preg_match( '/alt="([^"]*)"/i', $match[0], $alt );

		if ( isset( $alt[1] ) ) {
			$alt = $alt[1];
		} else {
			$alt = '';
		}

		if ( empty( $alt ) || '' === $alt ) {
			$output .= '<div class="attachment">
				<div class="attachment-preview msa-no-alt-tag">
					<div class="thumbnail">
						<a class="centered" href="' . $link . '" target="_blank">' . $match[0] . '</a>
					</div>
				</div>
			</div>';
			$images++;
		}
	}

	$output .= '</div>';

	if ( 0 === $images ) {
		$output = __( 'No Images', 'msa' );
	}

	return '<p>' . __( 'Count: ', 'msa' ) . $images . '</p>' . $output;
}

/**
 * Show the H1 Tags for a post
 *
 * @access public
 * @param mixed $content  The post content.
 * @param mixed $data     The audit data.
 * @return string $output The HTML output of the headings to be shown.
 */
function msa_show_h1_tags( $content, $data ) {

	preg_match_all( '|<\s*h[1-6](?:.*)>(.*)</\s*h[1-6]>|Ui', $content, $matches, PREG_SET_ORDER );

	$headings = 0;
	$output = '';
	$invalid_headings = array();

	if ( isset( $data['invalid_headings_data'] ) && count( $data['invalid_headings_data'] ) > 0 ) {
		foreach ( $data['invalid_headings_data'] as $invalid_heading ) {
			$invalid_headings[] = $invalid_heading['html'];
		}
	}

	foreach ( $matches as $match ) {

		// Continue if the heading is invalid.
		if ( in_array( $match[0], $invalid_headings, true ) ) {
			continue;
		}

		// H1.
		if ( substr_count( $match[0], '<h1' ) > 0 ) {
			$output .= '<div class="msa-headings-item">';
				$output .= '<span class="msa-heading-h1">h1</span>';
				$output .= strip_tags( $match[1], '<h1><h2><h3><h4><h5><h6>' );
			$output .= '</div>';
			$headings++;
		}
	}

	$output .= '</div>';

	if ( 0 === $headings ) {
		$output = __( 'No H1 Tags', 'msa' );
	}

	return '<div class="msa-headings"><p>' .  __( 'Count: ' . $headings, 'msa' ) . '</p>' . $output;
}

/**
 * Show the headings for a post
 *
 * @access public
 * @param mixed $content  The post content.
 * @param mixed $data     The audit data.
 * @return string $output The HTML output of the headings to be shown.
 */
function msa_show_headings( $content, $data ) {

	preg_match_all( '|<\s*h[1-6](?:.*)>(.*)</\s*h[1-6]>|Ui', $content, $matches, PREG_SET_ORDER );

	$headings = 0;
	$output = '';
	$invalid_headings = array();

	if ( isset( $data['invalid_headings_data'] ) && count( $data['invalid_headings_data'] ) > 0 ) {
		foreach ( $data['invalid_headings_data'] as $invalid_heading ) {
			$invalid_headings[] = $invalid_heading['html'];
		}
	}

	foreach ( $matches as $match ) {

		$output .= '<div class="msa-headings-item">';

		// Continue if the heading is invalid.
		if ( in_array( $match[0], $invalid_headings, true ) ) {
			$output .= '<div class="msa-headings-item msa-invalid-heading">';
		}

		// Headings.
		if ( substr_count( $match[0], '<h1' ) > 0 ) {
			$output .= '<span class="msa-heading-h1">h1</span>';
		} else if ( substr_count( $match[0], '<h2' ) > 0 ) {
			$output .= '<span class="msa-heading-h2">h2</span>';
		} else if ( substr_count( $match[0], '<h3' ) > 0 ) {
			$output .= '<span class="msa-heading-h3">h3</span>';
		} else if ( substr_count( $match[0], '<h4' ) > 0 ) {
			$output .= '<span class="msa-heading-h4">h4</span>';
		} else if ( substr_count( $match[0], '<h5' ) > 0 ) {
			$output .= '<span class="msa-heading-h5">h5</span>';
		} else {
			$output .= '<span class="msa-heading-h6">h6</span>';
		}

		$output .= $match[1];
		$output .= '</div>';
		$headings++;
	}

	$output .= '</div>';

	if ( 0 === $headings ) {
		$output = __( 'No Headings', 'msa' );
	}

	return '<div class="msa-headings"><p>' .  __( 'Count: ' . $headings, 'msa' ) . '</p>' . $output;
}

/**
 * Show the headings for a post
 *
 * @access public
 * @param mixed $invalid_headings An array of invalid headings to show.
 * @return string $ouput          The HTML output of the invalid headings.
 */
function msa_show_invalid_headings( $invalid_headings ) {

	$headings = 0;
	$output = '';

	foreach ( $invalid_headings as $match ) {

		$output .= '<div class="msa-headings-item">';

		// Headings.
		if ( substr_count( $match['html'], '<h1' ) > 0 ) {
			$output .= '<span class="msa-heading msa-heading-h1">h1</span>';
		} else if ( substr_count( $match['html'], '<h2' ) > 0 ) {
			$output .= '<span class="msa-heading msa-heading-h2">h2</span>';
		} else if ( substr_count( $match['html'], '<h3' ) > 0 ) {
			$output .= '<span class="msa-heading msa-heading-h3">h3</span>';
		} else if ( substr_count( $match['html'], '<h4' ) > 0 ) {
			$output .= '<span class="msa-heading msa-heading-h4">h4</span>';
		} else if ( substr_count( $match['html'], '<h5' ) > 0 ) {
			$output .= '<span class="msa-heading msa-heading-h5">h5</span>';
		} else {
			$output .= '<span class="msa-heading msa-heading-h6">h6</span>';
		}

		$output .= $match['text'];
		$output .= '</div>';
		$headings++;
	}

	$output .= '</div>';

	if ( 0 === $headings ) {
		$output = __( 'No Invalid Headings', 'msa' );
	}

	return '<div class="msa-invalid-headings"><p>' .  __( 'Count: ' . $headings, 'msa' ) . '</p>' . $output;
}

/**
 * Filter the posts for the all posts table
 *
 * @access public
 * @param mixed $posts  The original array of WP_Post objects.
 * @return mixed $posts The filtered array of WP_Post objects.
 */
function msa_filter_posts( $posts ) {

	// Score.
	if ( !empty( $_GET['score-low'] ) && !empty( $_GET['score-high'] ) ) { // Input var okay.

		$score_low = floatval( sanitize_text_field( wp_unslash( $_GET['score-low'] ) ) ); // Input var okay.
		$score_high = floatval( sanitize_text_field( wp_unslash( $_GET['score-high'] ) ) ); // Input var okay.

		foreach ( $posts as $key => $item ) {

			if ( $item['data']['values']['score'] < $score_low || $item['data']['values']['score'] > $score_high ) {
				unset( $posts[ $key ] );
			}
		}
	}

	// Conditions.
	$conditions = msa_get_conditions();

	foreach ( $conditions as $condition ) {

		if ( isset( $condition['filter'] ) && isset( $_GET[ $condition['filter']['name'] ] ) && '' !== $_GET[ $condition['filter']['name'] ] ) { // Input var okay.

			$name = $condition['filter']['name'];

			$atts = array();
			if ( isset( $_GET[ $name ] ) ) { // Input var okay.
				$atts = explode( '-', sanitize_text_field( wp_unslash( $_GET[ $name ] ) ) ); // Input var okay.
			}

			$compare = $atts[0];
			$value = $atts[1];

			foreach ( $posts as $key => $item ) {

				$post_value = $item['data']['values'][ $name ];

				// Compare.
				if ( 'more' === $compare  ) {
					if ( isset( $post_value ) && $post_value < $value ) {
						unset( $posts[ $key ] );
					}
				} else if ( 'less' === $compare ) {
					if ( isset( $post_value ) && $post_value > $value ) {
						unset( $posts[ $key ] );
					}
				} else if ( 'equal' === $compare ) {
					if ( isset( $post_value ) && $post_value !== $value ) {
						unset( $posts[ $key ] );
					}
				} else if ( 'notequal' === $compare ) {
					if ( isset( $post_value ) && $post_value === $value ) {
						unset( $posts[ $key ] );
					}
				}
			}
		}
	}

	// Attributes.
	$attributes = msa_get_attributes();

	foreach ( $attributes as $attribute ) {

		if ( isset( $attribute['filter'] ) && !empty( $_GET[ $attribute['filter']['name'] ] ) ) { // Input var okay.
			$name = $attribute['filter']['name'];

			$get_name = null;
			if ( isset( $_GET[ $name ] ) ) { // Input var okay.
				$get_name = sanitize_text_field( wp_unslash( $_GET[ $name ] ) ); // Input var okay.
			}
			$posts = apply_filters( 'msa_filter_by_attribute', $posts, $name, $get_name );
		}
	}

	return $posts;
}
