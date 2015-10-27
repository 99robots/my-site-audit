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
				`post_title` longtext,
				`post_name` longtext,
				`post_content` longtext,
				`post_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`post_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`post_type` varchar(20),
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
				`post_title`,
				`post_name`,
				`post_content`,
				`post_date`,
				`post_modified`,
				`post_type`,
				`comment_count`,
				`data`
			) VALUES (%d, %d, %s, %s, %s, %s, %s, %s, %d, %s)",
				$data['audit_id'],
				$data['post']->ID,
				$data['post']->post_title,
				$data['post']->post_name,
				$data['post']->post_content,
				date($this->data_format, strtotime($data['post']->post_date)),
				date($this->data_format, strtotime($data['post']->post_modified)),
				$data['post']->post_type,
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
				`audit_id` = %s,
				`post_id` = %s,
				`post_title` = %d,
				`post_name` = %d,
				`post_content` = %d,
				`post_date` = %d,
				`post_modified` = %s,
				`post_type` = %s,
				`comment_count` = %d,
				`data` = %s
			WHERE id = %d",
				$data['audit_id'],
				$data['post']->ID,
				$data['post']->post_title,
				$data['post']->post_name,
				$data['post']->post_content,
				date($this->data_format, strtotime($data['post']->post_date)),
				date($this->data_format, strtotime($data['post']->post_modified)),
				$data['post']->post_type,
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

			$post["ID"]        		= $row["post_id"];
			$post["post_title"]     = $row["post_title"];
			$post["post_name"]     	= $row["post_name"];
			$post["post_content"]   = $row["post_content"];
			$post["post_date"]      = $row["post_date"];
			$post["post_modified"]  = $row["post_modified"];
			$post["post_type"]      = $row["post_type"];
			$post["comment_count"]  = $row["comment_count"];

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