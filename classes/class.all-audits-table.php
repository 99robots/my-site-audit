<?php
/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 10/26/15
 * Package: Controllers/All Audits Table
 * File: all-aduits-table.php
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

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if ( !class_exists('MSA_All_Audits_Table') ) :

class MSA_All_Audits_Table extends WP_List_Table {

	/**
	 * Initialize the table object
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		parent::__construct( array(
			'singular' => __( 'Audit', 'msa' ),
			'plural'   => __( 'Audits', 'msa' ),
			'ajax'     => false
		) );

	}

	/**
	 * Display this message when there are no posts found
	 *
	 * @access public
	 * @return void
	 */
	public function no_items() {
		_e( 'No Audits found.', 'msa' );
	}

	/**
	 * Prepare all the items to be displayed
	 *
	 * @access public
	 * @return void
	 */
	public function prepare_items() {

		$columns = $this->get_columns();
		$sortable = $this->get_sortable_columns();
		$hidden = array();

		$this->_column_headers = array($columns, $hidden, $sortable);

		$per_page     = 20;
		$current_page = $this->get_pagenum();
		$args = array();

		/* =========================================================================
		 *
		 * Search term
		 *
		 ========================================================================= */

		if ( isset($_POST['s']) ) {
			$args['s'] = $_POST['s'];
		}

		if ( isset($_GET['s']) ) {
			$args['s'] = $_GET['s'];
		}

		/* =========================================================================
		 *
		 * Get Audits
		 *
		 ========================================================================= */

		$audit_model = new MSA_Audits_Model();
		$this->items = $audit_model->get_data($args);

		/* =========================================================================
		 *
		 * Sort Posts
		 *
		 ========================================================================= */

		if ( count($this->items) > 0 ) {
			usort( $this->items, array( &$this, 'usort_reorder' ) );
		}

		$total_items = count($this->items);

		$this->set_pagination_args( array(
			'total_items' => $total_items, 	// We have to calculate the total number of items
			'per_page'    => $per_page 		// We have to determine how many items to show on a page
		) );

		$this->items = array_slice($this->items,(($current_page-1)*$per_page),$per_page);

		$this->items = array_reverse($this->items);

		// Add a new row to show that to get more audits they need to purchase an add-on

		if ( count($this->items) > 0 ) {

			$this->items[] = array(
				'extension'			=> true,
				'extension-link' 	=> 'https://mysiteaudit.com',
				'score'				=> 1,
				'name'				=> __('Want to Save more Audits? Get the Extension!', 'msa'),
				'date'				=> date('Y-m-d H:i:s'),
				'num_posts'			=> '',
				'user'				=> 0,
			);

			$this->items = apply_filters('msa_all_audits_table_items', $this->items);

		}
	}

	/**
	 * Get all the columns that we want to display
	 *
	 * @access public
	 * @return void
	 */
	function get_columns() {

		$columns['score']         = __('Score', 'msa');
		$columns['name']          = __('Name', 'msa');
		$columns['date']          = __('Created On', 'msa');
		$columns['user']          = __('Created By', 'msa');
		$columns['num_posts']     = __('Number of Posts', 'msa');
		$columns['post_types']    = __('Post Types', 'msa');

		return apply_filters('msa_all_audits_table_columns', $columns);
	}

	/**
	 * Get the sortable columns for the table
	 *
	 * @access public
	 * @return void
	 */
/*
	function get_sortable_columns() {

		$sortable_columns['score']        = array('score', false);
		$sortable_columns['name']         = array('name', false);
		$sortable_columns['date']         = array('date', false);
		$sortable_columns['num_posts']    = array('num_posts', false);
		$sortable_columns['user']         = array('user', false);

		return $sortable_columns;
	}
*/

	/**
	 * Sort the data
	 *
	 * @access public
	 * @param mixed $a
	 * @param mixed $b
	 * @return void
	 */
	function usort_reorder( $a, $b ) {

		// If no sort, default to title
		$orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'score';

		// If no order, default to asc
		$order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';

		// Determine sort order
		$result = ($a['args'][$orderby] < $b['args'][$orderby]) ? -1 : 1;

		// Send final sort direction to usort
		return ( $order === 'asc' ) ? $result : -$result;
	}

	/**
	 * Default Column Value
	 *
	 * @access public
	 * @param mixed $item
	 * @param mixed $column_name
	 * @return void
	 */
	public function column_default( $item, $column_name ) {

		if ( isset($item['extension']) && $item['extension'] ) {
			return '';
		}

		$output = '';

		switch( $column_name ) {

			case 'score':
				$output = round(100 * $item[$column_name]) . '%';
			break;

			case 'date':
				$output = date('M d Y, h:i:s', strtotime($item[$column_name]));
			break;

			case 'user':
				$user = get_userdata($item[$column_name]);
				$output = $user->display_name;
			break;

			case 'post_types':
				$output = implode('<br />', $item['args']['post_types']);
			break;

			default:
				$output = $item[$column_name];
			break;

		}

		return apply_filters('msa_all_audits_table_column_default', $output);

	}

	/**
	 * Return the output for the Name column
	 *
	 * @access public
	 * @param mixed $item
	 * @return void
	 */
	public function column_name($item) {

		if ( !isset($item['extension']) ) {

			$audit_model = new MSA_Audits_Model();
			$audit = $audit_model->get_data_from_id($item['id']);

			$condition = $audit['args']['conditions'];

			$actions = array();

			$actions['delete'] = '<a href="' . wp_nonce_url(get_admin_url() . 'admin.php?page=msa-all-audits&action=delete&audit=' . $item['id'], 'msa-delete-audit') . '">' . __('Delete', 'msa') . '</a>';

			$condition_modal = '<a href="#" class="msa-audit-conditions-button" data-id="' . $item['id'] . '">' . __('Conditions', 'msa') . '</a>
			<div class="msa-audit-conditions-modal" data-id="' . $item['id'] . '">
				<div class="msa-audit-conditions-modal-container">

					<h3 class="msa-audit-conditions-modal-heading">' . __('Conditions', 'msa') . '</h3>

					<div class="msa-audit-conditions">
						<table class="wp-list-table widefat striped fixed">

							<thead>
								<tr>
									<th scope="col">' . __('Name', 'msa') . '</th>
									<th scope="col">' . __('Weight', 'msa') . '</th>
									<th scope="col">' . __('Comparison', 'msa') . '</th>
									<th scope="col">' . __('Value', 'msa') . '</th>
									<th scope="col">' . __('Minimum', 'msa') . '</th>
									<th scope="col">' . __('Maximum', 'msa') . '</th>
								</tr>
							</thead>

							<tbody>';

								foreach( json_decode($audit['args']['conditions'], true) as $condition ) {

									if ( $condition['comparison'] == 1 ) {
										$comparison = __('Greater Than', 'msa');
									} else if ( $condition['comparison'] == 2 ) {
										$comparison = __('Less Than', 'msa');
									} else if ( $condition['comparison'] == 3 ) {
										$comparison = __('In Between', 'msa');
									}

									$value = __('Pass or Fail', 'msa');

									if ( $condition['value'] == 2 ) {
										$value = __('Precentage', 'msa');
									}

									$condition_modal .= '<tr>
										<td>' . $condition['name'] . '</td>
										<td>' . $condition['weight'] . '</td>
										<td>' . $comparison . '</td>
										<td>' . $value . '</td>
										<td>' . (isset($condition['min_display_val']) ? $condition['min_display_val'] : '') . '</td>
										<td>' . (isset($condition['max_display_val']) ? $condition['max_display_val'] : '') . '</td>
									</tr>';

								}

							$condition_modal .= '</tbody>
						</table>
					</div>
				</div>
			</div>';

			$actions['edit'] = $condition_modal;

			return apply_filters('msa_all_audits_table_column_name', sprintf('%1$s %2$s', '<a href="' . get_admin_url() . 'admin.php?page=msa-all-audits&audit=' . $item['id'] . '">' . $item['name'] . '</a><small style="opacity:0.5;padding-left:4px;">id:(' . $item['id'] . ')</small>', $this->row_actions($actions) ) );

		}

		return apply_filters('msa_all_audits_table_column_name_extension', '<a href="' . $item['extension-link'] . '" target="_blank">' . $item['name'] . '</a>');

	}

	/**
	 * Generates content for a single row of the table
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @param object $item The current item
	 */
	public function single_row( $item ) {

		// Check if this row is for an extension

		if ( isset($item['extension']) && $item['extension'] ) {

			echo '<tr class="msa-extension-row">';
			$this->single_row_columns( $item );
			echo '</tr>';

		} else {
			echo '<tr class="msa-post-status msa-post-status-' . msa_get_score_status($item['score']) .'">';
			$this->single_row_columns( $item );
			echo '</tr>';
		}
	}

	/**
	 * Get a list of CSS classes for the list table table tag.
	 *
	 * @access protected
	 *
	 * @return array List of CSS classes for the table tag.
	 */
	protected function get_table_classes() {
		return array( 'widefat', 'striped', $this->_args['plural'] );
	}
}

endif;