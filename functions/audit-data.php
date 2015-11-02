<?php
/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 10/22/15
 * Package: Functions/Audit Data
 * File: audit-data.php
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
 * Calculate the score of a post
 *
 * @access public
 * @param mixed $post
 * @param mixed $data
 * @return void
 */
function msa_calculate_score($post, $data) {

	$score_data = array();
	$score = 0;
	$weight = 0;
	$conditions = msa_get_conditions();

	foreach ( $conditions as $key => $condition ) {

		// Greater Than

		if ( $condition['comparison'] == 0 && isset($condition['min']) ) {

			$value = min( $data[$key] / $condition['min'], 1 );

		}

		// Less Than

		else if ( $condition['comparison'] == 1 && isset($condition['max']) ) {

			$value = 1 - min( $data[$key] / $condition['max'], 1 );

		}

		// Range

		else {

			$range = $condition['max'] - $condition['min'];
			$mean = ($condition['max'] + $condition['min']) / 2;

			$value = ( 1 -  min( floor( abs( $data[$key] - $mean ) ) / floor( ( $range / 2 ) ) , 1 ) );

		}

		$score_data[$key] = $condition['value'] == 0 && $value != 0 ? 1 : $value;

		$value *= $condition['weight'];

		// Convert the ratio into a bool

		if ( $condition['value'] == 0 && $value != 0 ) {
			$value = $condition['weight'];
		}

		$score += $value;
		$weight += $condition['weight'];

	}

	$score = round($score / $weight, 2);

	return array(
		'score'	=> $score,
		'data'	=> $score_data,
	);
}

/**
 * Get all the audit data for a specific post
 *
 * @access public
 * @param mixed $post
 * @return void
 */
function msa_get_post_audit_data($post) {

	$data = array();

	$content   = preg_replace("/&#?[a-z0-9]{2,8};/i","", $post->post_content);
	$content   = strip_tags($content);
	$content   = preg_replace("~(?:\[/?)[^/\]]+/?\]~s", '', $content);

	$data['title']             = strlen($post->post_title);
	$data['name']              = strlen($post->post_name);
	$data['modified_date']     = max(time() - strtotime($post->post_modified), 0);
	$data['word_count']        = str_word_count($content);
	$data['comment_count']     = $post->comment_count;
	$data['images'] 		   = substr_count($post->post_content, '<img');

	// Headings

	preg_match_all('/<h([1-6])/', $post->post_content, $matches);
	$data['headings'] = count($matches[0]);

/*
	preg_match_all('/<h1/', $post->post_content, $matches);
	$data['h1'] = count($matches[0]);

	preg_match_all('/<h2/', $post->post_content, $matches);
	$data['h2'] = count($matches[0]);

	preg_match_all('/<h3/', $post->post_content, $matches);
	$data['h3'] = count($matches[0]);

	preg_match_all('/<h4/', $post->post_content, $matches);
	$data['h4'] = count($matches[0]);

	preg_match_all('/<h5/', $post->post_content, $matches);
	$data['h5'] = count($matches[0]);

	preg_match_all('/<h6/', $post->post_content, $matches);
	$data['h6'] = count($matches[0]);
*/

	// Links

	preg_match_all("/<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>/siU", $post->post_content, $data['link_matches'], PREG_SET_ORDER);

	$data['internal_links'] = 0;
	$data['external_links'] = 0;
	$data['broken_links'] = 0;

	if ( isset($data['link_matches']) && is_array($data['link_matches']) ) {

		foreach ( $data['link_matches'] as $key => $link ) {

			$url = parse_url($link[2]);
			$site_url = parse_url(get_site_url());

			if ( isset($site_url['host']) && isset($url['host']) && $site_url['host'] == $url['host'] ) {
				$data['internal_links']++;
			} else {
				$data['external_links']++;
			}

			$handle = curl_init($link[2]);
			curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
			$response = curl_exec($handle);
			$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
			curl_close($handle);

			if ( $httpCode == 404 || $httpCode == 0 ) {
				$data['link_matches'][$key]['broken'] = true;
				$data['broken_links']++;
			} else {
				$data['link_matches'][$key]['broken'] = false;
			}


		}
	}

	$data['links'] = $data['internal_links'] + $data['external_links'];

	// Check for images with missing alt tags

	$data['missing_alt_tag'] = 0;

	preg_match_all('|<img(?:.*)/>|Ui', $post->post_content, $img_matches, PREG_SET_ORDER);

	foreach ( $img_matches as $match ) {

		// Check for an alt tag

		preg_match( '/alt="([^"]*)"/i', $match[0], $alt );
		$alt = $alt[1];

		if ( empty($alt) || $alt == '' ) {
			$data['missing_alt_tag']++;
		}
	}

	// Score

	$score = msa_calculate_score($post, $data);
	$data['score'] = $score['score'];

	/*
	// Yoast Score

	if ( file_exists(WP_PLUGIN_DIR . '/wordpress-seo/inc/class-wpseo-utils.php') && is_plugin_active('wordpress-seo/wp-seo.php') ) {

		include_once(WP_PLUGIN_DIR. '/wordpress-seo/inc/class-wpseo-utils.php');

		$data['yoast-seo-meta-desc'] = get_post_meta($post->ID, '_yoast_wpseo_metadesc', true);
		$data['yoast-seo-focuskw'] = get_post_meta($post->ID, '_yoast_wpseo_focuskw', true);

		$data['yoast-seo-score_label'] = 'na';
		$data['yoast-seo-score'] = get_post_meta($post->ID, '_yoast_wpseo_linkdex', true);

		if ( $data['yoast-seo-score'] !== '' ) {
			$nr = WPSEO_Utils::calc( $data['yoast-seo-score'], '/', 10, true );
			$data['yoast-seo-score-label'] 	= WPSEO_Utils::translate_score( $nr );
			unset( $nr );
		}
	}
*/

	return $data;

}

/**
 * Show all the internal links
 *
 * @access public
 * @param mixed $link_matches
 * @return void
 */
function msa_show_internal_links( $link_matches ) {

	if ( isset($link_matches) && is_array($link_matches) ) {

		$output = '<p>' . __('Valid Links') . '<ol style="margin:0;">';

		// Valid links

		foreach ( $link_matches as $link ) {
			$url = parse_url($link[2]);
			$site_url = parse_url(get_site_url());

			if ( isset($site_url['host']) && isset($url['host']) && $site_url['host'] == $url['host'] && ( !isset($link['broken']) || ( isset($link['broken']) && !$link['broken'] ) ) ) {
				$output .= '<li style="margin: 0;"><a href="' . $link[2] . '" target="_blank">' . $link[2] . '</a></li>';
				$matches++;
			}
		}

		$output .= '</ol></p>';

		// Broken Links

		$output .= '<p>' . __('Broken Links') . '<ol style="margin:0;">';

		// Valid links

		foreach ( $link_matches as $link ) {
			$url = parse_url($link[2]);
			$site_url = parse_url(get_site_url());

			if ( isset($site_url['host']) && isset($url['host']) && $site_url['host'] == $url['host'] && isset($link['broken']) && $link['broken'] ) {
				$output .= '<li style="margin: 0;" class="msa-broken-link"><a href="' . $link[2] . '" target="_blank">' . $link[2] . '</a></li>';
				$matches++;
			}
		}

		$output .= '</ol></p>';

		if ( $matches == 0 ) {
			return __('No Links', 'msa');
		}

	} else {
		return __('No Links', 'msa');
	}

	return $output;

}

/**
 * Show all the external links
 *
 * @access public
 * @param mixed $link_matches
 * @return void
 */
function msa_show_external_links( $link_matches ) {

	if ( isset($link_matches) && is_array($link_matches) ) {

		$matches = 0;

		$output = '<p>' . __('Valid Links') . '<ol style="margin:0;">';

		// Valid links

		foreach ( $link_matches as $link ) {
			$url = parse_url($link[2]);
			$site_url = parse_url(get_site_url());

			if ( isset($site_url['host']) && isset($url['host']) && $site_url['host'] != $url['host'] && ( !isset($link['broken']) || ( isset($link['broken']) && !$link['broken'] ) ) ) {
				$output .= '<li style="margin: 0;"><a href="' . $link[2] . '" target="_blank">' . $link[2] . '</a></li>';
				$matches++;
			}
		}

		$output .= '</ol></p>';

		// Broken Links

		$output .= '<p>' . __('Broken Links') . '<ol style="margin:0;">';

		// Valid links

		foreach ( $link_matches as $link ) {
			$url = parse_url($link[2]);
			$site_url = parse_url(get_site_url());

			if ( isset($site_url['host']) && isset($url['host']) && $site_url['host'] != $url['host'] && isset($link['broken']) && $link['broken'] ) {
				$output .= '<li style="margin: 0;" class="msa-broken-link"><a href="' . $link[2] . '" target="_blank">' . $link[2] . '</a></li>';
				$matches++;
			}
		}

		$output .= '</ol></p>';

		if ( $matches == 0 ) {
			return __('No Links', 'msa');
		}

	} else {
		return __('No Links', 'msa');
	}

	return $output;

}

/**
 * Show the images for a post
 *
 * @access public
 * @param mixed $content
 * @return void
 */
function msa_show_images($content) {

	preg_match_all('|<img(?:.*)/>|Ui', $content, $matches, PREG_SET_ORDER);

	$images = 0;

	$output = __('Count: ' . count($matches), 'msa');
	$output .= '<div class="msa-images"><p>' . __('Has Alt tag', 'msa') . '</p>';

	// Has Alt tag

	foreach ( $matches as $match ) {

		// Get the scr URL

		$link = array();
		preg_match( '/src="([^"]*)"/i', $match[0], $link );
		$link = $link[1];

		// Check for an alt tag

		preg_match( '/alt="([^"]*)"/i', $match[0], $alt );
		$alt = $alt[1];

		if ( isset($alt) && $alt != '' ) {
			$output .= '<a href="' . $link . '" target="_blank">' . $match[0] . '</a>';
			$images++;
		}
	}

	$output .= '</div>';

	$output .= '<div class="msa-images"><p>' . __('Does not have Alt tag', 'msa') . '</p>';

	// Does not have alt tag

	foreach ( $matches as $match ) {

		// Get the scr URL

		$link = array();
		preg_match( '/src="([^"]*)"/i', $match[0], $link );
		$link = $link[1];

		// Check for an alt tag

		preg_match( '/alt="([^"]*)"/i', $match[0], $alt );
		$alt = $alt[1];

		if ( empty($alt) || $alt == '' ) {
			$output .= '<a class="msa-no-alt-tag" href="' . $link . '" target="_blank">' . $match[0] . '</a>';
			$images++;
		}
	}

	$output .= '</div>';

	if ( $images == 0 ) {
		$output = __('No Images', 'msa');
	}

    return $output;

}

/**
 * Show the headings for a post
 *
 * @access public
 * @param mixed $content
 * @return void
 */
function msa_show_headings($content) {

	preg_match_all('|<\s*h[1-6](?:.*)>(.*)</\s*h[1-6]>|Ui', $content, $matches, PREG_SET_ORDER);

	$headings = 0;

	$output = __('Count: ' . count($matches), 'msa');

	foreach ( $matches as $match ) {

		$output .= strip_tags($match[0], '<h1><h2><h3><h4><h5><h6>');
		$headings++;
	}

	if ( $headings == 0 ) {
		$output = __('No Headings', 'msa');
	}

    return $output;

}

/**
 * Filter the posts for the all posts table
 *
 * @access public
 * @param mixed $posts
 * @return void
 */
function msa_filter_posts($posts) {

	// Score

	if ( isset($_GET['score-low']) && $_GET['score-low'] != '' && isset($_GET['score-high']) && $_GET['score-high'] != '' ) {

		$score_low = floatval($_GET['score-low']);
		$score_high = floatval($_GET['score-high']);

		foreach ( $posts as $key => $item ) {

			if ( $item['data']['score'] < $_GET['score-low'] || $item['data']['score'] > $_GET['score-high'] ) {
				unset($posts[$key]);
			}
		}
	}

	// Conditions

	$conditions = msa_get_conditions();

	foreach ( $conditions as $condition ) {

		if ( isset($condition['filter']) && isset($_GET[$condition['filter']['name']]) && $_GET[$condition['filter']['name']] != '' ) {

			$name = $condition['filter']['name'];

			$atts = explode('-', $_GET[$name]);
			$compare = $atts[0];
			$value = $atts[1];

			foreach ( $posts as $key => $item ) {

				// Greater Than

				if ( $compare == 'more' ) {

					if ( $item['data'][$name] < $value ) {
						unset($posts[$key]);
					}

				}

				// Less Than

				else if ( $compare == 'less' ) {

					if ( $item['data'][$name] > $value ) {
						unset($posts[$key]);
					}

				}

				// Equal To

				else if ( $compare === 'equal' ) {

					if ( $item['data'][$name] != $value ) {
						unset($posts[$key]);
					}

				}

				// NOT Equal To

				else if ( $compare === 'notequal' ) {

					if ( $item['data'][$name] == $value ) {
						unset($posts[$key]);
					}

				}
			}
		}
	}

	// Attributes

	$attributes = msa_get_attributes();

	foreach ( $attributes as $attribute ) {

		if ( isset($attribute['filter']) && isset($_GET[$attribute['filter']['name']]) && $_GET[$attribute['filter']['name']] != '' ) {

			$name = $attribute['filter']['name'];

			$posts = apply_filters('msa_filter_by_attribute', $posts, $name, $_GET[$name]);
		}
	}

	return $posts;
}

/**
 * Show the post data withint the meta box
 *
 * @access public
 * @param mixed $post
 * @param mixed $data
 * @return void
 */
function msa_show_audit_data_single_meta($post, $data) {

	$output = '<tr>';
		$output .= '<td>' . __('Internal Links', 'msa') . '</td>';
		$output .= '<td><ul style="margin: 0;">';
			$output .= msa_show_internal_links($data['link_matches']);
		$output .= '</ul></td>';
	$output .= '</tr>';

	$output .= '<tr>';
		$output .= '<td>' . __('External Links', 'msa') . '</td>';
		$output .= '<td><ul style="margin: 0;">';
			$output .= msa_show_external_links($data['link_matches']);
		$output .= '</ul></td>';
	$output .= '</tr>';

	$output .= '<tr>';
		$output .= '<td>' . __('Images', 'msa') . '</td>';
		$output .= '<td>' . substr_count($post->post_content, '<img') . '</td>';
	$output .= '</tr>';

	$output .= '<tr>';
		$output .= '<td>' . __('Headings', 'msa') . '</td>';
		$output .= '<td>' . $data['headings'] . '</td>';
	$output .= '</tr>';

	return $output;
}