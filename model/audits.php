<?php
/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 10/26/15
 * Package: Model/Audits
 * File: audits.php
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

if ( ! class_exists( 'MSA_Audits_Model' ) ) :

	class MSA_Audits_Model {

		/**
		 * table_name
		 *
		 * (default value: 'msa_audits')
		 *
		 * @var string
		 * @access public
		 */
		public $table_name = 'msa_audits';

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
		 * The default audit
		 *
		 * @access public
		 * @return void
		 */
		function default_data() {
			return array(
				'name'  => __( 'My Audit', 'msa' ),
				'date'  => '',
				'score' => '',
				'args'  => array(),
			);
		}

		/**
		 * Create a table for the audits
		 *
		 * @access public
		 * @return void
		 */
		function create_table() {
			global $wpdb;

			$sql = 'CREATE TABLE IF NOT EXISTS ' . $this->get_table_name() . " (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`name` varchar(60) NOT NULL DEFAULT '',
				`date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`score` decimal(10,10) NOT NULL DEFAULT '.0',
				`status` varchar(60),
				`user` int(11),
				`num_posts` int(11),
				`args` longtext,
				PRIMARY KEY (`id`)
			) " . $wpdb->get_charset_collate();

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
			$data = $this->validate_data( $data );

			global $wpdb;

			$wpdb->insert(
				$this->get_table_name(),
				array(
					'name'      => $data['name'],
					'date'      => date( $this->data_format, strtotime( $data['date'] ) ),
					'score'     => $data['score'],
					'status'    => $data['status'],
					'user'      => $data['user'],
					'num_posts' => $data['num_posts'],
					'args'      => json_encode( $data['args'] ),
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
			$data = $this->validate_data( $data );

			if ( ! isset( $id ) || empty( $id ) ) {
				return false;
			}

			global $wpdb;

			wp_cache_delete( 'msa_audits_get_data' );

			$wpdb->update(
				$this->get_table_name(),
				array(
					'name'      => $data['name'],
					'date'      => date( $this->data_format, strtotime( $data['date'] ) ),
					'score'     => $data['score'],
					'status'    => $data['status'],
					'user'      => $data['user'],
					'num_posts' => $data['num_posts'],
					'args'      => json_encode( $data['args'] ),
				),
				array( 'id' => $id ),
				array(
					'%s',
					'%s',
					'%f',
					'%s',
					'%d',
					'%d',
					'%s',
				)
			);

			return $result;
		}

		/**
		 * Get audits
		 *
		 * @access public
		 * @param array $args (default: array())
		 * @return void
		 */
		function get_data( $args = array() ) {
			global $wpdb;

			// Create the WHERE clause

			$where = ' WHERE 1=1 ';

			// Search

			$search = '';

			if ( isset( $args['s'] ) ) {
				$s = $wpdb->esc_like( $args['s'] );
				$s = '%' . $s . '%';
				$search = $wpdb->prepare( ' AND `name` LIKE %s', $s );
			}

			// Status

			$status = '';

			if ( isset( $args['status'] ) && 'all' !== $args['status'] ) {
				$status = $wpdb->prepare( ' AND `status` = %s', $args['status'] );
			}

			$where .= $search . $status;

			wp_cache_get( 'msa_audits_get_data' );
			$data = $wpdb->get_results( 'SELECT * FROM `' . $this->get_table_name() . '` ' . $where . ' ORDER BY `id` DESC;', 'ARRAY_A' );
			wp_cache_set( 'msa_audits_get_data', $data );

			return $this->parse_data( $data );
		}

		/**
		 * Get audit from id
		 *
		 * @access public
		 * @param mixed $id
		 * @return void
		 */
		function get_data_from_id( $id ) {
			global $wpdb;

			if ( false === ( $data = wp_cache_get( 'msa_audits_get_data_' . $id ) ) ) {
				$data = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM `' . $this->get_table_name() . '` WHERE `id` = %d', $id ), 'ARRAY_A' );
				wp_cache_set( 'msa_audits_get_data_' . $id, $data );
			}

			$parsed_data = $this->parse_data( $data );

			if ( isset( $parsed_data[0] ) ) {
				return $parsed_data[0];
			}

			return null;
		}

		/**
		 * Get the latest audit
		 *
		 * @access public
		 * @return void
		 */
		function get_latest() {
			global $wpdb;

			if ( false === ( $data = wp_cache_get( 'msa_audits_get_latest' ) ) ) {
				$data = $wpdb->get_results( 'SELECT * FROM `' . $this->get_table_name() . '` WHERE `status` = "completed" ORDER BY `id` DESC LIMIT 1', 'ARRAY_A' );
				wp_cache_set( 'msa_audits_get_latest', $data );
			}

			$parsed_data = $this->parse_data( $data );

			if ( isset( $parsed_data[0] ) ) {
				return $parsed_data[0];
			}

			return null;
		}

		/**
		 * Delete some data
		 *
		 * @access public
		 * @param mixed $id
		 * @return void
		 */
		function delete_data( $id ) {

			global $wpdb;
			//$sql = $wpdb->prepare( 'DELETE FROM `' . $this->get_table_name() . '` WHERE `id` = %d', $id );

			$wpdb->delete(
				$this->get_table_name(),
				array( 'id' => $id )
			);

			// Delete all the posts in the Audit Posts Table

			$audit_posts_model = new MSA_Audit_Posts_Model();
			$audit_posts_model->delete_data( $id );
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
		 * Validate that the data is in the correct format
		 *
		 * @access public
		 * @param mixed $data
		 * @return void
		 */
		function validate_data( $data ) {
			return array_merge( $this->default_data(), $data );
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

				$entry['id']           			= $row['id'];
				$entry['name']         			= $row['name'];
				$entry['date']        			= $row['date'];
				$entry['score']        			= $row['score'];
				$entry['status']        		= $row['status'];
				$entry['user']        			= $row['user'];
				$entry['num_posts']        		= $row['num_posts'];
				$entry['args']         			= json_decode( $row['args'], true );

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
