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
	$data['images'] = substr_count($post->post_content, '<img');

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

	if ( isset($data['link_matches']) && is_array($data['link_matches']) ) {
		foreach ( $data['link_matches'] as $link ) {
			$url = parse_url($link[2]);
			$site_url = parse_url(get_site_url());

			if ( isset($site_url['host']) && isset($url['host']) && $site_url['host'] == $url['host'] ) {
				$data['internal_links']++;
			} else {
				$data['external_links']++;
			}
		}
	}

	$data['links'] = $data['internal_links'] + $data['external_links'];

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

	$output = '<ol style="margin:0;">';

	if ( isset($link_matches) && is_array($link_matches) ) {

		$matches = 0;

		foreach ( $link_matches as $link ) {
			$url = parse_url($link[2]);
			$site_url = parse_url(get_site_url());

			if ( isset($site_url['host']) && isset($url['host']) && $site_url['host'] == $url['host'] ) {
				$output .= '<li style="margin: 0;"><a href="' . $link[2] . '" target="_blank">' . $link[2] . '</a></li>';
				$matches++;
			}
		}

		if ( $matches == 0 ) {
			$output .= '<li style="margin: 0;">' . __('No Links', 'msa') . '</li>';
		}

	} else {
		$output .= '<li style="margin: 0;">' . __('No Links', 'msa') . '</li>';
	}

	$output .= '</ol>';

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

	$output = '<ol style="margin:0;">';

	if ( isset($link_matches) && is_array($link_matches) ) {

		$matches = 0;

		foreach ( $link_matches as $link ) {
			$url = parse_url($link[2]);
			$site_url = parse_url(get_site_url());

			if ( isset($site_url['host']) && isset($url['host']) && $site_url['host'] != $url['host'] ) {
				$output .= '<li style="margin: 0;"><a href="' . $link[2] . '" target="_blank">' . $link[2] . '</a></li>';
				$matches++;
			}
		}

		if ( $matches == 0 ) {
			$output .= '<li style="margin: 0;">' . __('No Links', 'msa') . '</li>';
		}

	} else {
		$output .= '<li style="margin: 0;">' . __('No Links', 'msa') . '</li>';
	}

	$output .= '</ol>';

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
	$output .= '<div class="msa-images">';

	foreach ( $matches as $match ) {

		$link = array();
		preg_match( '/src="([^"]*)"/i', $match[0], $link ) ;
		$link = $link[1];

		$output .= '<a href="' . $link . '" target="_blank">' . $match[0] . '</a>';
		$images++;
	}

	if ( $images == 0 ) {
		$output .= __('No Images', 'msa');
	}

	$output .= '</div>';

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