<?php
/**
 * Manages all the data within the {$wpdb->prefix}msa_audits table
 *
 * @package Model / Audits
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MSA_Audits_Model' ) ) :

	/**
	 * The Audits model class
	 */
	class MSA_Audits_Model {

		/**
		 * Table name
		 *
		 * (default value: 'msa_audits')
		 *
		 * @var string
		 * @access public
		 */
		public $table_name = 'msa_audits';

		/**
		 * Data format
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
		 * @return array $default_data A default audit.
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
		 * @param array $data    A new audit's data.
		 * @return int $audit_id The new audit id.
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
					'args'      => wp_json_encode( $data['args'] ),
				)
			);

			return $wpdb->insert_id;
		}

		/**
		 * Update Audit
		 *
		 * @param int   $id        The audit id.
		 * @param array $data      The new audit data.
		 * @return bool true|false The query result.
		 */
		function update_data( $id = null, $data = array() ) {
			$data = $this->validate_data( $data );

			if ( ! isset( $id ) || empty( $id ) ) {
				return false;
			}

			global $wpdb;

			wp_cache_delete( 'msa_audits_get_data' );

			$result = $wpdb->update(
				$this->get_table_name(),
				array(
					'name'      => $data['name'],
					'date'      => date( $this->data_format, strtotime( $data['date'] ) ),
					'score'     => $data['score'],
					'status'    => $data['status'],
					'user'      => $data['user'],
					'num_posts' => $data['num_posts'],
					'args'      => wp_json_encode( $data['args'] ),
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
		 * @param array $args    The args to filter audits.
		 * @return array $audits The filtered audits.
		 */
		function get_data( $args = array() ) {
			global $wpdb;

			// Create the WHERE clause.
			$where = ' WHERE 1=1 ';

			// Search.
			$search = '';

			if ( isset( $args['s'] ) ) {
				$s = $wpdb->esc_like( $args['s'] );
				$s = '%' . $s . '%';
				$search = $wpdb->prepare( ' AND `name` LIKE %s', $s );
			}

			// Status.
			$status = '';

			if ( isset( $args['status'] ) && 'all' !== $args['status'] ) {
				$status = $wpdb->prepare( ' AND `status` = %s', $args['status'] );
			}

			$where .= $search . $status;

			$data = $wpdb->get_results( 'SELECT * FROM `' . $this->get_table_name() . '` ' . $where . ' ORDER BY `id` DESC;', 'ARRAY_A' ); // WPCS: unprepared SQL ok. // WPCS: cache ok.

			return $this->parse_data( $data );
		}

		/**
		 * Get audit from id
		 *
		 * @param mixed $id           The audit id.
		 * @return mixed $parsed_data The audit if found and null if not.
		 */
		function get_data_from_id( $id ) {
			global $wpdb;

			if ( false === ( $data = wp_cache_get( 'msa_audits_get_data_' . $id ) ) ) {
				$data = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM `' . $this->get_table_name() . '` WHERE `id` = %d', $id ), 'ARRAY_A' ); // WPCS: unprepared SQL ok.
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
		 * @return mixed $parsed_data The audit if found and null if not.
		 */
		function get_latest() {
			global $wpdb;

			if ( false === ( $data = wp_cache_get( 'msa_audits_get_latest' ) ) ) {
				$data = $wpdb->get_results( 'SELECT * FROM `' . $this->get_table_name() . '` WHERE `status` = "completed" ORDER BY `id` DESC LIMIT 1', 'ARRAY_A' ); // WPCS: unprepared SQL ok.
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
		 * @param mixed $id The audit id.
		 * @return void
		 */
		function delete_data( $id ) {
			global $wpdb;

			// $sql = $wpdb->prepare( 'DELETE FROM `' . $this->get_table_name() . '` WHERE `id` = %d', $id );
			$wpdb->delete(
				$this->get_table_name(),
				array( 'id' => $id )
			);

			// Delete all the posts in the Audit Posts Table.
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
		 * @param mixed $data  The audit data to validate.
		 * @return mixed $data The validated data.
		 */
		function validate_data( $data ) {
			return array_merge( $this->default_data(), $data );
		}

		/**
		 * Parse the returned data
		 *
		 * @param mixed $data  The audit data to be parsed.
		 * @return mixed $data The parsed audit data.
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
		 * @param mixed $data  The audit data to clean.
		 * @return mixed $data The cleaned audit data.
		 */
		function clean_data( $data ) {
			return stripcslashes( sanitize_text_field( $data ) );
		}

		/**
		 * Returns the proper table name for Multisies
		 *
		 * @return string $table_name The name of the database table.
		 */
		function get_table_name() {
			global $wpdb;
			return $wpdb->prefix . $this->table_name;
		}
	}

endif;
