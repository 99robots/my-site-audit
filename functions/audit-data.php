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

			$value = min( $condition['max'] / $data[$key], 1 );

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
 * Get the conditions
 *
 * @access public
 * @return void
 */
function msa_get_conditions() {

	// If there are no conditions than go to default values

	if ( false === ( $conditions = get_option('msa_conditions') ) ) {

		/*
		 * Comparsion:
		 *
		 * 0 = greater than some number
		 * 1 = less than some number
		 * 2 = in between some numbers
		 *
		 */

		/*
		 * Value:
		 *
		 * 0 = boolean result (i.e pass or fail)
		 * 1 = ratio (i.e. .123)
		 *
		 */

		$conditions = array(
			'title'          => array(
				'weight'        => 2,
				'comparison'	=> 1,
				'value'			=> 0,
				'max'           => 60,
			),
			'modified_date'  => array(
				'weight'        => 3,
				'comparison'	=> 1,
				'value'			=> 1,
				'max'           => DAY_IN_SECONDS * 90,
			),
			'word_count'     => array(
				'weight'        => 10,
				'comparison'	=> 0,
				'value'			=> 1,
				'min'           => 750,
			),
			'comment_count'  => array(
				'weight'        => 1,
				'comparison'	=> 0,
				'value'			=> 1,
				'min'           => 5,
			),
			'internal_links' => array(
				'weight'        => 5,
				'comparison'	=> 2,
				'value'			=> 1,
				'min'           => 2,
				'max'           => 6,
			),
			'external_links' => array(
				'weight'        => 5,
				'comparison'	=> 2,
				'value'			=> 1,
				'min'           => 1,
				'max'           => 14,
			),
			'images'         => array(
				'weight'        => 3,
				'comparison'	=> 0,
				'value'			=> 0,
				'min'           => 2,
			),
			'headings'       => array(
				'weight'        => 6,
				'comparison'	=> 0,
				'value'			=> 0,
				'min'           => 5,
			),
		);

	}

	return $conditions;
}

/**
 * Show the Audit Data for a specific post
 *
 * @access public
 * @param mixed $post
 * @param mixed $settings
 * @param string $format (default: 'table')
 * @return void
 */
function msa_show_audit_data($post, $settings, $format = 'table' ) {

	// Get the settings

	if ( false === ( $settings = get_option('msa_settings') ) ) {
		$settings = array();
	}

	$data = msa_get_post_audit_data($post);

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

	$score = msa_calculate_score($post, $data);
	$data['score'] = $score['score'];

	if ( $format == 'table' ) {

		if ( isset($_GET['post']) ) {

			msa_show_audit_data_single($post, $data);

		} else {

			msa_show_audit_data_all($post, $data);

		}

	} else {

		return msa_show_audit_data_single_meta($post, $data);

	}

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

	$data['content']           = preg_replace("/&#?[a-z0-9]{2,8};/i","", $post->post_content);
	$data['content']           = strip_tags($data['content']);
	$data['content']           = preg_replace("~(?:\[/?)[^/\]]+/?\]~s", '', $data['content']);
	$data['title']             = strlen($post->post_title);
	$data['name']              = strlen($post->post_name);
	$data['modified_date']     = time() - strtotime($post->post_modified);
	$data['word_count']        = str_word_count($data['content']);
	$data['comment_count']     = $post->comment_count;
	$data['images'] = substr_count($post->post_content, '<img');

	// Headings

	preg_match_all('/<h([1-6])/', $post->post_content, $matches);
	$data['headings'] = count($matches[0]);

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

	return $data;

}

/**
 * Get the status of a score
 *
 * @access public
 * @param mixed $score
 * @return void
 */
function msa_get_score_status($score) {

	if ( $score >= msa_get_score_increment() * 5 ) {
		$status = 'great';
	} else if ( $score >= msa_get_score_increment() * 4 ) {
		$status = 'good';
	} else if ( $score >= msa_get_score_increment() * 3 ) {
		$status = 'ok';
	} else if ( $score >= msa_get_score_increment() * 2 ) {
		$status = 'poor';
	} else {
		$status = 'bad';
	}

	return $status;

}

/**
 * Get the letter grade for this score
 *
 * @access public
 * @param mixed $score
 * @return void
 */
function msa_get_letter_grade($score) {

	if ( $score >= .96667 ) {
		$grade = 'A+';
	} else if ( $score >= .93334 ) {
		$grade = 'A';
	} else if ( $score >= .90 ) {
		$grade = 'A-';
	} else if ( $score >= .86667 ) {
		$grade = 'B+';
	} else if ( $score >= .83334 ) {
		$grade = 'B';
	} else if ( $score >= .80 ) {
		$grade = 'B-';
	} else if ( $score >= .76667 ) {
		$grade = 'C+';
	} else if ( $score >= .73334 ) {
		$grade = 'C';
	} else if ( $score >= .70 ) {
		$grade = 'C-';
	} else if ( $score >= .66667 ) {
		$grade = 'D+';
	} else if ( $score >= .63334 ) {
		$grade = 'D';
	} else if ( $score >= .60 ) {
		$grade = 'D-';
	} else {
		$grade = 'F';
	}

	return $grade;

}

/**
 * Get the score increment
 *
 * @access public
 * @return void
 */
function msa_get_score_increment() {

	return MY_SITE_AUDIT_SCORE_INCREMENT;
}

/**
 * Show all the internal links
 *
 * @access public
 * @param mixed $link_matches
 * @return void
 */
function msa_show_internal_links( $link_matches ) {

	$output = '<ul>';

	if ( isset($link_matches) && is_array($link_matches) ) {
		foreach ( $link_matches as $link ) {
			$url = parse_url($link[2]);
			$site_url = parse_url(get_site_url());

			if ( isset($site_url['host']) && isset($url['host']) && $site_url['host'] == $url['host'] ) {
				$output .= '<li style="list-style: disc;margin: 0;"><a href="' . $link[2] . '" target="_blank">' . $link[2] . '</a></li>';
			}
		}
	}

	$output .= '</ul>';

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

	$output = '<ul>';

	if ( isset($link_matches) && is_array($link_matches) ) {
		foreach ( $link_matches as $link ) {
			$url = parse_url($link[2]);
			$site_url = parse_url(get_site_url());

			if ( isset($site_url['host']) && isset($url['host']) && $site_url['host'] != $url['host'] ) {
				$output .= '<li style="list-style: disc;margin: 0;"><a href="' . $link[2] . '" target="_blank">' . $link[2] . '</a></li>';
			}
		}
	}

	$output .= '</ul>';

	return $output;

}

/**
 * Show data for all posts page
 *
 * @access public
 * @param mixed $post
 * @param mixed $data
 * @return void
 */
function msa_show_audit_data_all($post, $data) {

	$score = msa_calculate_score($post, $data);

	$output = '<tr class="msa-post-status msa-post-status-' . msa_get_score_status($score['score']) .'">';

		$output .= '<td>' . (100 * $data['score']) . '%</td>';

		$output .= '<td><i class="fa fa-caret-' . ( $score['data']['title'] >= .5 ? 'up' : 'down' ) . ' msa-post-status-text-' . msa_get_score_status($score['data']['title']) . '"></i> <a href="' . get_admin_url() . 'admin.php?page=msa-dashboard&post=' . $post->ID . '">' . $post->post_title . '</a></td>';

		$output .= '<td><i class="fa fa-caret-' . ( $score['data']['modified_date'] >= .5 ? 'up' : 'down' ) . ' msa-post-status-text-' . msa_get_score_status($score['data']['modified_date']) . '"></i> ' . date('M j, Y', strtotime($post->post_modified)) . '</td>';

		$output .= '<td><i class="fa fa-caret-' . ( $score['data']['word_count'] >= .5 ? 'up' : 'down' ) . ' msa-post-status-text-' . msa_get_score_status($score['data']['word_count']) . '"></i> ' . str_word_count($data['content']) . '</td>';

		$output .= '<td><i class="fa fa-caret-' . ( $score['data']['comment_count'] >= .5 ? 'up' : 'down' ) . ' msa-post-status-text-' . msa_get_score_status($score['data']['comment_count']) . '"></i> ' . $data['comment_count'] . '</td>';

		$output .= '<td><i class="fa fa-caret-' . ( $score['data']['internal_links'] >= .5 ? 'up' : 'down' ) . ' msa-post-status-text-' . msa_get_score_status($score['data']['internal_links']) . '"></i> ' . $data['internal_links'] . '</td>';

		$output .= '<td><i class="fa fa-caret-' . ( $score['data']['external_links'] >= .5 ? 'up' : 'down' ) . ' msa-post-status-text-' . msa_get_score_status($score['data']['external_links']) . '"></i> ' . $data['external_links'] . '</td>';

		$output .= '<td><i class="fa fa-caret-' . ( $score['data']['images'] >= .5 ? 'up' : 'down' ) . ' msa-post-status-text-' . msa_get_score_status($score['data']['images']) . '"></i> ' . $data['images'] . '</td>';

		$output .= '<td><i class="fa fa-caret-' . ( $score['data']['headings'] >= .5 ? 'up' : 'down' ) . ' msa-post-status-text-' . msa_get_score_status($score['data']['headings']) . '"></i> ' . $data['headings'] . '</td>';

	$output .= '</tr>';

	echo $output;
}

/**
 * Show data for single post
 *
 * @access public
 * @param mixed $post
 * @param mixed $data
 * @return void
 */
function msa_show_audit_data_single($post, $data) {

	$score = msa_calculate_score($post, $data);

	$output = '<tr class="msa-post-status-bg msa-post-status-bg-' . msa_get_score_status($score['score']) . '">';
		$output .= '<td>' . __('Score', 'msa') . '</td>';
		$output .= '<td>' . 100 * $score['score'] . '%</td>';
	$output .= '</tr>';

	$output .= '<tr>';
		$output .= '<td>' . __('Published Date', 'msa') . '</td>';
		$output .= '<td>' . date('M j, Y', strtotime($post->post_date)) . '</td>';
	$output .= '</tr>';

	$output .= '<tr>';
		$output .= '<td>' . __('ID', 'msa') . '</td>';
		$output .= '<td>' . $post->ID . '</td>';
	$output .= '</tr>';

	$output .= '<tr>';
		$output .= '<td>' . __('Slug', 'msa') . '</td>';
		$output .= '<td><a href="' . get_permalink($post->ID) . '" target="_blank">/' . $post->post_name . '</a></td>';
	$output .= '</tr>';

	$output .= '<tr>';
		$output .= '<td>' . __('Title', 'msa') . '</td>';
		$output .= '<td>' . $post->post_title . '</td>';
	$output .= '</tr>';

	$output .= '<tr class="msa-post-status msa-post-status-' . msa_get_score_status($score['data']['title']) .'">';
		$output .= '<td>' . __('Title Length', 'msa') . '</td>';
		$output .= '<td>' . strlen($post->post_title) . '</td>';
	$output .= '</tr>';

	$output .= '<tr class="msa-post-status msa-post-status-' . msa_get_score_status($score['data']['modified_date']) .'">';
		$output .= '<td>' . __('Modified Date', 'msa') . '</td>';
		$output .= '<td>' . date('M j, Y', strtotime($post->post_modified)) . '</td>';
	$output .= '</tr>';

	$output .= '<tr class="msa-post-status msa-post-status-' . msa_get_score_status($score['data']['word_count']) .'">';
		$output .= '<td>' . __('Word Count', 'msa') . '</td>';
		$output .= '<td>' . str_word_count($data['content']) . '</td>';
	$output .= '</tr>';

	$output .= '<tr class="msa-post-status msa-post-status-' . msa_get_score_status($score['data']['comment_count']) .'">';
		$output .= '<td>' . __('Comment Count', 'msa') . '</td>';
		$output .= '<td>' . $data['comment_count'] . '</td>';
	$output .= '</tr>';

	// Yoast

/*
	if ( file_exists(WP_PLUGIN_DIR . '/wordpress-seo/inc/class-wpseo-utils.php') && is_plugin_active('wordpress-seo/wp-seo.php') ) {

		$output .= '<tr>';
			$output .= '<td>' . __('SEO Score', 'msa') . '</td>';
			$output .= '<td><div class="wpseo-score-icon ' . esc_attr( $data['yoast-seo-score-label'] ) . '"></div></td>';
		$output .= '</tr>';

		$output .= '<tr>';
			$output .= '<td>' . __('Focus Keyword', 'msa') . '</td>';
			$output .= '<td>' . $data['yoast-seo-focuskw'] . '</td>';
		$output .= '</tr>';

		$output .= '<tr>';
			$output .= '<td>' . __('Meta Description', 'msa') . '</td>';
			$output .= '<td>' . $data['yoast-seo-meta-desc'] . '</td>';
		$output .= '</tr>';

		$output .= '<tr>';
			$output .= '<td>' . __('Meta Description Length', 'msa') . '</td>';
			$output .= '<td>' . strlen($data['yoast-seo-meta-desc']) . '</td>';
		$output .= '</tr>';

	}
*/

	$output .= '<tr class="msa-post-status msa-post-status-' . msa_get_score_status($score['data']['internal_links']) .'">';
		$output .= '<td>' . __('Internal Links', 'msa') . '</td>';
		$output .= '<td>';
			$output .= msa_show_internal_links($data['link_matches']);
		$output .= '</td>';
	$output .= '</tr>';

	$output .= '<tr class="msa-post-status msa-post-status-' . msa_get_score_status($score['data']['external_links']) .'">';
		$output .= '<td>' . __('External Links', 'msa') . '</td>';
		$output .= '<td>';
			$output .= msa_show_external_links($data['link_matches']);
		 $output .= '</td>';
	$output .= '</tr>';

/*
	// Share Count

	if ( isset($settings['use_shared_count']) && $settings['use_shared_count'] ) {
		$output .= '<tr>';
			$output .= '<td>' . __('Share Count', 'msa') . '</td>';
			$output .= '<td class="msa-share-count" data-post="' . $post->ID . '"><i class="fa fa-refresh fa-spin"></i></td>';
		$output .= '</tr>';
	}
*/

	$output .= '<tr class="msa-post-status msa-post-status-' . msa_get_score_status($score['data']['images']) .'">';
		$output .= '<td>' . __('Image Count', 'msa') . '</td>';
		$output .= '<td>' . $data['images'] . '</td>';
	$output .= '</tr>';

	$output .= '<tr class="msa-post-status msa-post-status-' . msa_get_score_status($score['data']['headings']) .'">';
		$output .= '<td>' . __('Heading Count', 'msa') . '</td>';
		$output .= '<td>' . $data['headings'] . '</td>';
	$output .= '</tr>';

	$output .= '<tr>';
		$output .= '<td>' . __('Heading 1 Count', 'msa') . '</td>';
		$output .= '<td>' . $data['h1'] . '</td>';
	$output .= '</tr>';

	$output .= '<tr>';
		$output .= '<td>' . __('Heading 2 Count', 'msa') . '</td>';
		$output .= '<td>' . $data['h2'] . '</td>';
	$output .= '</tr>';

	$output .= '<tr>';
		$output .= '<td>' . __('Heading 3 Count', 'msa') . '</td>';
		$output .= '<td>' . $data['h3'] . '</td>';
	$output .= '</tr>';

	$output .= '<tr>';
		$output .= '<td>' . __('Heading 4 Count', 'msa') . '</td>';
		$output .= '<td>' . $data['h4'] . '</td>';
	$output .= '</tr>';

	$output .= '<tr>';
		$output .= '<td>' . __('Heading 5 Count', 'msa') . '</td>';
		$output .= '<td>' . $data['h5'] . '</td>';
	$output .= '</tr>';

	$output .= '<tr>';
		$output .= '<td>' . __('Heading 6 Count', 'msa') . '</td>';
		$output .= '<td>' . $data['h6'] . '</td>';
	$output .= '</tr>';

	echo $output;

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