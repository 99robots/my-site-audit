<?php
/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 10/27/15
 * Package: Model/Audit Posts
 * File: audit-posts.php
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

if ( !class_exists('MSA_Audit_Posts_Model') ) :

class MSA_Audit_Posts_Model {

	/**
	 * table_name
	 *
	 * (default value: 'msa_audit_posts')
	 *
	 * @var string
	 * @access public
	 */
	public $table_name = 'msa_audit_posts';

	/**
	 * data_format
	 *
	 * (default value: 'Y-m-d H:i:s')
	 *
	 * @var string
	 * @access public
	 */
	public $data_format = 'Y-m-d H:i:s';

	/**
	 * Create a table for the audits
	 *
	 * @access public
	 * @return void
	 */
	function create_table() {

		global $wpdb;

		$result = $wpdb->query("CREATE TABLE IF NOT EXISTS `" . $this->get_table_name() . "` (
				`id` bigint(20) NOT NULL AUTO_INCREMENT,
				`audit_id` bigint(20) NOT NULL DEFAULT 1,
				`post_id` bigint(20) NOT NULL DEFAULT 1,
				`score` decimal(10,10) NOT NULL DEFAULT '.0',
				`post_author` bigint(20) NOT NULL DEFAULT 0,
				`post_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`post_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`post_content` longtext NOT NULL,
				`post_title` text NOT NULL,
				`post_excerpt` text NOT NULL,
				`post_status` varchar(20) NOT NULL DEFAULT 'publish',
				`comment_status` varchar(20) NOT NULL DEFAULT 'open',
				`ping_status` varchar(20) NOT NULL DEFAULT 'open',
				`post_password` varchar(20) NOT NULL,
				`post_name` varchar(200) NOT NULL,
				`to_ping` text NOT NULL,
				`pinged` text NOT NULL,
				`post_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`post_modified_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`post_content_filtered` longtext NOT NULL,
				`post_parent` bigint(20) NOT NULL DEFAULT 0,
				`guid` varchar(255) NOT NULL,
				`menu_order` int(11) NOT NULL DEFAULT 0,
				`post_type` varchar(20) NOT NULL DEFAULT 'post',
				`post_mime_type` varchar(100) NOT NULL DEFAULT '',
				`comment_count` bigint(20),
				`data` longtext,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1;");

		return $result;
	}

	/**
	 * Add a new audit
	 *
	 * @access public
	 * @param array $data (default: array())
	 * @return void
	 */
	function add_data( $data = array() ) {

		global $wpdb;

		$result = $wpdb->query( $wpdb->prepare("INSERT INTO `" . $this->get_table_name() . "` (
				`audit_id`,
				`post_id`,
				`score`,
				`post_author`,
				`post_date`,
				`post_date_gmt`,
				`post_content`,
				`post_title`,
				`post_excerpt`,
				`post_status`,
				`comment_status`,
				`ping_status`,
				`post_password`,
				`post_name`,
				`to_ping`,
				`pinged`,
				`post_modified`,
				`post_modified_gmt`,
				`post_content_filtered`,
				`post_parent`,
				`guid`,
				`menu_order`,
				`post_type`,
				`post_mime_type`,
				`comment_count`,
				`data`
			) VALUES (%d, %d, %f, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d, %s, %d, %s, %s, %d, %s)",
				$data['audit_id'],
				$data['post']->ID,
				$data['data']['score']['score'],
				$data['post']->post_author,
				date($this->data_format, strtotime($data['post']->post_date)),
				date($this->data_format, strtotime($data['post']->post_date_gmt)),
				$data['post']->post_content,
				$data['post']->post_title,
				$data['post']->post_excerpt,
				$data['post']->post_status,
				$data['post']->comment_status,
				$data['post']->ping_status,
				$data['post']->post_password,
				$data['post']->post_name,
				$data['post']->to_ping,
				$data['post']->pinged,
				date($this->data_format, strtotime($data['post']->post_modified)),
				date($this->data_format, strtotime($data['post']->post_modified_gmt)),
				$data['post']->post_content_filtered,
				$data['post']->post_parent,
				$data['post']->guid,
				$data['post']->menu_order,
				$data['post']->post_type,
				$data['post']->post_mime_type,
				$data['post']->comment_count,
				json_encode($data['data'])
		) );

		// Return the recently created id for this entry

		return $wpdb->insert_id;

	}

	/**
	 * Update Audit
	 *
	 * @since 1.0.0
	 *
	 * @param	data to be updated
	 * @return	false if error, otherwise nothing
	 */
	function update_data( $id = null, $data = array() ) {

		if ( !isset($id) || empty($id) ) {
			return false;
		}

		global $wpdb;

		$result = $wpdb->query( $wpdb->prepare(
			"UPDATE `" . $this->get_table_name() . "` SET
				`audit_id` = %d,
				`post_id` = %d,
				`score` = %f,
				`post_author` = %d,
				`post_date` = %s,
				`post_date_gmt` = %s,
				`post_content` = %s,
				`post_title` = %s,
				`post_excerpt` = %s,
				`post_status` = %s,
				`comment_status` = %s,
				`ping_status` = %s,
				`post_password` = %s,
				`post_name` = %s,
				`to_ping` = %s,
				`pinged` = %s,
				`post_modified` = %s,
				`post_modified_gmt` = %s,
				`post_content_filtered` = %s,
				`post_parent` = %d,
				`guid` = %s,
				`menu_order` = %d,
				`post_type` = %s,
				`post_mime_type` = %s,
				`comment_count` = %d,
				`data` = %s
			WHERE id = %d",
				$data['audit_id'],
				$data['post']->ID,
				$data['score'],
				$data['post']->post_author,
				date($this->data_format, strtotime($data['post']->post_date)),
				date($this->data_format, strtotime($data['post']->post_date_gmt)),
				$data['post']->post_content,
				$data['post']->post_title,
				$data['post']->post_excerpt,
				$data['post']->post_status,
				$data['post']->comment_status,
				$data['post']->ping_status,
				$data['post']->post_password,
				$data['post']->post_name,
				$data['post']->to_ping,
				$data['post']->pinged,
				date($this->data_format, strtotime($data['post']->post_modified)),
				date($this->data_format, strtotime($data['post']->post_modified_gmt)),
				$data['post']->post_content_filtered,
				$data['post']->post_parent,
				$data['post']->guid,
				$data['post']->menu_order,
				$data['post']->post_type,
				$data['post']->post_mime_type,
				$data['post']->comment_count,
				json_encode($data['data']),
				$id
		) );

		return $result;
	}

	/**
	 * Get all posts in an audit
	 *
	 * @access public
	 * @param mixed $audit_id
	 * @param array $args (default: array())
	 * @return void
	 */
	function get_data($audit_id, $args = array()) {

		global $wpdb;

		// Search

		$search = '';

		if ( isset($args['s']) ) {
			$s = $wpdb->esc_like($args['s']);
			$s = '%' . $s . '%';
			$search = $wpdb->prepare("AND `post_title` LIKE %s", $s);
		}

		$data = $wpdb->get_results( $wpdb->prepare("SELECT * FROM `" . $this->get_table_name() . "` WHERE `audit_id` = %d ", $audit_id) . $search, 'ARRAY_A');

		return $this->parse_data($data);
	}

	/**
	 * Get post from audit
	 *
	 * @access public
	 * @param mixed $audit_id
	 * @param mixed $post_id
	 * @param array $args (default: array())
	 * @return void
	 */
	function get_data_from_id($audit_id, $post_id, $args = array()) {

		global $wpdb;

		$data = $wpdb->get_results( $wpdb->prepare("SELECT * FROM `" . $this->get_table_name() . "` WHERE `audit_id` = %d AND `post_id` = %d", $audit_id, $post_id), 'ARRAY_A');

		$parsed_data = $this->parse_data($data);

		return $parsed_data[0];
	}

	/**
	 * Get an array of author ids from the audit
	 *
	 * @access public
	 * @param mixed $audit_id
	 * @param array $args (default: array())
	 * @return void
	 */
	function get_authors_in_audit($audit_id, $args = array()) {

		global $wpdb;

		$data = $wpdb->get_results( $wpdb->prepare("SELECT DISTINCT `post_author` FROM `" . $this->get_table_name() . "` WHERE `audit_id` = %d", $audit_id), 'ARRAY_A');

		return $data;

	}

	/**
	 * Delete all the posts within an audit data
	 *
	 * @access public
	 * @param mixed $id
	 * @return void
	 */
	function delete_data($audit_id) {

		global $wpdb;

		$result = $wpdb->query( $wpdb->prepare("DELETE FROM `" . $this->get_table_name() . "` WHERE `audit_id` = %d", $audit_id ) );

		return $result;

	}

	/**
	 * Delete the post data
	 *
	 * @access public
	 * @param mixed $id
	 * @return void
	 */
	function delete_post_data($audit_id, $post_id) {

		global $wpdb;

		$result = $wpdb->query( $wpdb->prepare("DELETE FROM `" . $this->get_table_name() . "` WHERE `audit_id` = %d AND `post_id` = %d", $audit_id, $post_id ) );

		return $result;

	}

	/**
	 * Delete the table
	 *
	 * @access public
	 * @return void
	 */
	function delete_table() {

		global $wpdb;

		$result = $wpdb->query("DROP TABLE `" . $this->get_table_name() . "`");

		return $result;

	}

	/**
	 * Parse the returned data
	 *
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
	function parse_data( $data ) {

		$parsed_data = array();

		foreach ($data as $row) {

			$entry = array();

			$entry["id"]           	= $row["id"];
			$entry["audit_id"]      = $row["audit_id"];
			$entry["score"]      	= $row["score"];

			$post["ID"]                      = $row["post_id"];
			$post["post_author"]             = $row["post_author"];
			$post["post_date"]               = $row["post_date"];
			$post["post_date_gmt"]           = $row["post_date_gmt"];
			$post["post_content"]            = $row["post_content"];
			$post["post_title"]              = $row["post_title"];
			$post["post_excerpt"]            = $row["post_excerpt"];
			$post["post_status"]             = $row["post_status"];
			$post["comment_status"]          = $row["comment_status"];
			$post["ping_status"]             = $row["ping_status"];
			$post["post_password"]           = $row["post_password"];
			$post["post_name"]               = $row["post_name"];
			$post["to_ping"]                 = $row["to_ping"];
			$post["pinged"]                  = $row["pinged"];
			$post["post_modified"]           = $row["post_modified"];
			$post["post_modified_gmt"]       = $row["post_modified_gmt"];
			$post["post_content_filtered"]   = $row["post_content_filtered"];
			$post["post_parent"]             = $row["post_parent"];
			$post["guid"]                    = $row["guid"];
			$post["menu_order"]              = $row["menu_order"];
			$post["post_type"]               = $row["post_type"];
			$post["post_mime_type"]          = $row["post_mime_type"];
			$post["comment_count"]           = $row["comment_count"];

			$entry['post'] 			= new WP_Post((object) $post);

			$entry['data']         	= json_decode($row['data'], true);

			$parsed_data[] = $entry;
		}

		return $parsed_data;

	}

	/**
	 * Clean the data given to us
	 *
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
	function clean_data($data) {
		return stripcslashes(sanitize_text_field($data));
	}

	/**
	 * Returns the proper table name for Multisies
	 *
	 * @access public
	 * @param mixed $table_name
	 * @return void
	 */
	function get_table_name() {

		global $wpdb;

		return $wpdb->prefix . $this->table_name;
	}

}

endif;