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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MSA_Audit_Posts_Model' ) ) :

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

			$sql = 'CREATE TABLE IF NOT EXISTS ' . $this->get_table_name() . " (
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
			) " . $wpdb->get_charset_collate() . ';';

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
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

			$wpdb->insert(
				$this->get_table_name(),
				array(
					'audit_id'              => $data['audit_id'],
					'post_id'               => $data['post']->ID,
					'score'                 => $data['data']['score']['score'],
					'post_author'           => $data['post']->post_author,
					'post_date'             => date( $this->data_format, strtotime( $data['post']->post_date ) ),
					'post_date_gmt'         => date( $this->data_format, strtotime( $data['post']->post_date_gmt ) ),
					'post_content'          => $data['post']->post_content,
					'post_title'            => $data['post']->post_title,
					'post_excerpt'          => $data['post']->post_excerpt,
					'post_status'           => $data['post']->post_status,
					'comment_status'        => $data['post']->comment_status,
					'ping_status'           => $data['post']->ping_status,
					'post_password'         => $data['post']->post_password,
					'post_name'             => $data['post']->post_name,
					'to_ping'               => $data['post']->to_ping,
					'pinged'                => $data['post']->pinged,
					'post_modified'         => date( $this->data_format, strtotime( $data['post']->post_modified ) ),
					'post_modified_gmt'     => date( $this->data_format, strtotime( $data['post']->post_modified_gmt ) ),
					'post_content_filtered' => $data['post']->post_content_filtered,
					'post_parent'           => $data['post']->post_parent,
					'guid'                  => $data['post']->guid,
					'menu_order'            => $data['post']->menu_order,
					'post_type'             => $data['post']->post_type,
					'post_mime_type'        => $data['post']->post_mime_type,
					'comment_count'         => $data['post']->comment_count,
					'data'                  => json_encode( $data['data'] ),
				)
			);

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
			if ( ! isset( $id ) || empty( $id ) ) {
				return false;
			}

			global $wpdb;

			wp_cache_delete( 'msa_get_audit_posts_' . $audit_id );

			$wpdb->update(
				$this->get_table_name(),
				array(
					'audit_id'              => $data['audit_id'],
					'post_id'               => $data['post']->ID,
					'score'                 => $data['data']['score']['score'],
					'post_author'           => $data['post']->post_author,
					'post_date'             => date( $this->data_format, strtotime( $data['post']->post_date ) ),
					'post_date_gmt'         => date( $this->data_format, strtotime( $data['post']->post_date_gmt ) ),
					'post_content'          => $data['post']->post_content,
					'post_title'            => $data['post']->post_title,
					'post_excerpt'          => $data['post']->post_excerpt,
					'post_status'           => $data['post']->post_status,
					'comment_status'        => $data['post']->comment_status,
					'ping_status'           => $data['post']->ping_status,
					'post_password'         => $data['post']->post_password,
					'post_name'             => $data['post']->post_name,
					'to_ping'               => $data['post']->to_ping,
					'pinged'                => $data['post']->pinged,
					'post_modified'         => date( $this->data_format, strtotime( $data['post']->post_modified ) ),
					'post_modified_gmt'     => date( $this->data_format, strtotime( $data['post']->post_modified_gmt ) ),
					'post_content_filtered' => $data['post']->post_content_filtered,
					'post_parent'           => $data['post']->post_parent,
					'guid'                  => $data['post']->guid,
					'menu_order'            => $data['post']->menu_order,
					'post_type'             => $data['post']->post_type,
					'post_mime_type'        => $data['post']->post_mime_type,
					'comment_count'         => $data['post']->comment_count,
					'data'                  => json_encode( $data['data'] ),
				),
				array( 'id' => $id ),
				array(
					'%d',
					'%d',
					'%f',
					'%d',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%d',
					'%s',
					'%d',
					'%s',
					'%s',
					'%d',
					'%s',
				)
			);

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
		function get_data( $audit_id, $args = array() ) {
			global $wpdb;

			// Search

			$search = '';

			if ( isset( $args['s'] ) ) {
				$s = $wpdb->esc_like( $args['s'] );
				$s = '%' . $s . '%';
				$search = $wpdb->prepare( 'AND `post_title` LIKE %s', $s );
			}

			if ( false === ( $data = wp_cache_get( 'msa_get_audit_posts_' . $audit_id ) ) ) {
				$data = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM `' . $this->get_table_name() . '` WHERE `audit_id` = %d ', $audit_id ) . $search, 'ARRAY_A' );
				wp_cache_set( 'msa_get_audit_posts_' . $audit_id , $data );
			}

			return $this->parse_data( $data );
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
		function get_data_from_id( $audit_id, $post_id, $args = array() ) {
			global $wpdb;

			if ( false === ( $data = wp_cache_get( 'msa_audit_posts_get_data_' . $audit_id . '_' . $post_id ) ) ) {
				$data = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM `' . $this->get_table_name() . '` WHERE `audit_id` = %d AND `post_id` = %d', $audit_id, $post_id ), 'ARRAY_A' );
				wp_cache_set( 'msa_audi_posts_get_data_' . $audit_id . '_' . $post_id, $data );
			}

			$parsed_data = $this->parse_data( $data );

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
		function get_authors_in_audit( $audit_id, $args = array() ) {
			global $wpdb;

			if ( false === ( $data = wp_cache_get( 'msa_audit_posts_get_authors_' . $audit_id ) ) ) {
				$data = $wpdb->get_results( $wpdb->prepare( 'SELECT DISTINCT `post_author` FROM `' . $this->get_table_name() . '` WHERE `audit_id` = %d', $audit_id ), 'ARRAY_A' );
				wp_cache_set( 'msa_audi_posts_get_authors_' . $audit_id , $data );
			}

			return $data;

		}

		/**
		 * Delete all the posts within an audit data
		 *
		 * @access public
		 * @param mixed $id
		 * @return void
		 */
		function delete_data( $audit_id ) {
			global $wpdb;
			$wpdb->delete(
				$this->get_table_name(),
				array( 'audit_id' => $audit_id )
			);
		}

		/**
		 * Delete the post data
		 *
		 * @access public
		 * @param mixed $id
		 * @return void
		 */
		function delete_post_data( $audit_id, $post_id ) {
			global $wpdb;
			$wpdb->delete(
				$this->get_table_name(),
				array(
					'audit_id' => $audit_id,
					'post_id'  => $post_id,
				)
			);
		}

		/**
		 * Delete the table
		 *
		 * @access public
		 * @return void
		 */
		function delete_table() {
			global $wpdb;
			$sql = 'DROP TABLE `' . $this->get_table_name() . '`';

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
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

			foreach ( $data as $row ) {
				$entry = array();

				$entry['id']           	= $row['id'];
				$entry['audit_id']      = $row['audit_id'];
				$entry['score']      	= $row['score'];

				$post['ID']                      = $row['post_id'];
				$post['post_author']             = $row['post_author'];
				$post['post_date']               = $row['post_date'];
				$post['post_date_gmt']           = $row['post_date_gmt'];
				$post['post_content']            = $row['post_content'];
				$post['post_title']              = $row['post_title'];
				$post['post_excerpt']            = $row['post_excerpt'];
				$post['post_status']             = $row['post_status'];
				$post['comment_status']          = $row['comment_status'];
				$post['ping_status']             = $row['ping_status'];
				$post['post_password']           = $row['post_password'];
				$post['post_name']               = $row['post_name'];
				$post['to_ping']                 = $row['to_ping'];
				$post['pinged']                  = $row['pinged'];
				$post['post_modified']           = $row['post_modified'];
				$post['post_modified_gmt']       = $row['post_modified_gmt'];
				$post['post_content_filtered']   = $row['post_content_filtered'];
				$post['post_parent']             = $row['post_parent'];
				$post['guid']                    = $row['guid'];
				$post['menu_order']              = $row['menu_order'];
				$post['post_type']               = $row['post_type'];
				$post['post_mime_type']          = $row['post_mime_type'];
				$post['comment_count']           = $row['comment_count'];

				$entry['post'] 			= new WP_Post( (object) $post );

				$entry['data']         	= json_decode( $row['data'], true );

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
		function clean_data( $data ) {
			return stripcslashes( sanitize_text_field( $data ) );
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
