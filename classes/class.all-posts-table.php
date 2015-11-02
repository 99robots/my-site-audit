<?php
/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 10/23/15
 * Package: Controller/All Posts Table
 * File: all-posts-table.php
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

if ( !class_exists('MSA_All_Posts_Table') ) :

add_filter('msa_single_audit_posts', array('MSA_All_Posts_Table', 'posts_from_filters'), 10, 2);

class MSA_All_Posts_Table extends WP_List_Table {

	/**
	 * Initialize the table object
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		parent::__construct( array(
			'singular' => __( 'Post', 'msa' ),
			'plural'   => __( 'Posts', 'msa' ),
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
		_e( 'No Posts found.', 'msa' );
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

		// Get the audit data

		$audit_posts_model 	= new MSA_Audit_Posts_Model();
		$per_page     		= 20;
		$current_page 		= $this->get_pagenum();
		$args = array(
			'per_page'		=> $per_page,
			'current_page'	=> $current_page,
		);

		/* =========================================================================
		 *
		 * Search term
		 *
		 ========================================================================= */

		if ( isset($_POST['s']) ) {
			$args['s'] = $_POST['s'];
		}

		/* =========================================================================
		 *
		 * Get Posts
		 *
		 ========================================================================= */

		$posts = $audit_posts_model->get_data($_GET['audit'], $args);

		foreach ( $posts as $post ) {
			$this->items[] = array('score' => $post['score'], 'post' => $post['post'], 'data' => $post['data']);
		}

		/* =========================================================================
		 *
		 * Filter Posts
		 *
		 ========================================================================= */

		$this->items = msa_filter_posts($this->items);

		/* =========================================================================
		 *
		 * Sort Posts
		 *
		 ========================================================================= */

		usort( $this->items, array( &$this, 'usort_reorder' ) );

		$total_items = count($this->items);

		$this->set_pagination_args( array(
			'total_items' => $total_items, 	// We have to calculate the total number of items
			'per_page'    => $per_page 		// We have to determine how many items to show on a page
		) );

		$this->items = array_slice($this->items,(($current_page-1)*$per_page),$per_page);
	}

	/**
	 * Get all the columns that we want to display
	 *
	 * @access public
	 * @return void
	 */
	function get_columns() {

		$columns['score'] = __('Score', 'msa');

		// Conditions

		$conditions = msa_get_conditions();

		foreach ( $conditions as $slug => $condition ) {
			$columns[$slug] = $condition['name'];
		}

		// Attributes

		$attributes = msa_get_attributes();

		foreach ( $attributes as $slug => $attribute ) {
			$columns[$slug] = $attribute['name'];
		}

		return $columns;
	}

	/**
	 * Get the sortable columns for the table
	 *
	 * @access public
	 * @return void
	 */
	function get_sortable_columns() {

		$sortable_columns['score'] = array('score', false);

		// Conditions

		$conditions = msa_get_conditions();

		foreach ( $conditions as $slug => $condition ) {
			$sortable_columns[$slug] = array($slug, true);
		}

		// Attributes

		$attributes = msa_get_attributes();

		foreach ( $attributes as $slug => $attribute ) {
			$sortable_columns[$slug] = array($slug, true);
		}

		return $sortable_columns;
	}

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

		if ( isset($a['data'][$orderby]) ) {
			$a_data = $a['data'][$orderby];
		} else {
			$a_data = '';
		}

		if ( isset($b['data'][$orderby]) ) {
			$b_data = $b['data'][$orderby];
		} else {
			$b_data = '';
		}

		$a_sort = apply_filters('msa_audit_posts_table_sort_data', $a_data, $a, $orderby);
		$b_sort = apply_filters('msa_audit_posts_table_sort_data', $b_data, $b, $orderby);

		// Determine sort order
		$result = ( $a_sort < $b_sort ) ? -1 : 1;

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

		$score = msa_calculate_score($item['post'], $item['data']);
		$caret = '';

		// Conditions

		$conditions = msa_get_conditions();

		// Attributes

		$attributes = msa_get_attributes();

		if ( isset($conditions[$column_name]) ) {
			$caret = '<i class="fa fa-caret-' . ( $score['data'][$column_name] >= .5 ? 'up' : 'down' ) . ' msa-post-status-text-' . msa_get_score_status($score['data'][$column_name]) . '"></i> ';
		}

		switch( $column_name ) {

			case 'score':
				$data = round(100 * $item['score']) . '%';
				$caret = '';
			break;

			case 'title':
				$data = '<a href="' . get_admin_url() . 'admin.php?page=msa-all-audits&audit=' . $_GET['audit'] . '&post=' . $item['post']->ID . '">' . $item['post']->post_title . '</a>';
			break;

			case 'modified_date':
				$data = date('M j, Y', strtotime($item['post']->post_modified));
			break;

			case 'comment_count':
				$data = $item['post']->comment_count;
			break;

			default:

				if ( isset($item['data'][$column_name]) && ( isset($conditions[$column_name]) || isset($attributes[$column_name]) ) ) {
					$data = $item['data'][$column_name];
				} else {
					$data = '';
				}

			break;

		}

		return apply_filters('msa_all_posts_table_column_data', $caret . $data, $item, $column_name);

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

		$score = msa_calculate_score($item['post'], $item['data']);

		echo '<tr class="msa-post-status msa-post-status-' . msa_get_score_status($score['score']) .'">';
		$this->single_row_columns( $item );
		echo '</tr>';
	}

	/**
	 * Extra controls to be displayed between bulk actions and pagination
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @param string $which
	 */
	protected function extra_tablenav( $which ) {

		// Get all the registerd conditions

		$conditions = msa_get_conditions();

		// Get all the registerd attributes

		$attributes = msa_get_attributes();

		if ( $which == 'top' ) {

			?><div class="alignleft actions bulkactions">

				<?php

				// Conditions

				foreach ( $conditions as $condition ) {

					if ( isset($condition['filter']) ) {

						if ( isset($_GET[$condition['filter']['name']]) ) {
							$value = $_GET[$condition['filter']['name']];
						} else {
							$value = '';
						} ?>

						<div style="display: inline-block;">
							<label class="msa-filter-label"><?php echo $condition['filter']['label']; ?></label>
							<select class="msa-filter" name="<?php echo $condition['filter']['name']; ?>">
								<option value="" <?php selected("", $value, true); ?>><?php _e('All', 'msa'); ?></option>
								<?php foreach ( $condition['filter']['options'] as $option ) { ?>
									<option value="<?php echo $option['value']; ?>" <?php selected($option['value'], $value, true); ?>><?php echo $option['name']; ?></option>
								<?php } ?>
							</select>
						</div>

					<?php }

				}

				// Attributes

				foreach ( $attributes as $key => $attribute ) {

					if ( isset($attribute['filter']) ) {

						if ( isset($_GET[$attribute['filter']['name']]) ) {
							$value = $_GET[$attribute['filter']['name']];
						} else {
							$value = '';
						}

						$attribute['filter']['options'] = apply_filters('msa_filter_attribute_' . $key, $attribute['filter']['options'])?>

						<div style="display: inline-block;">
							<label class="msa-filter-label"><?php echo $attribute['filter']['label']; ?></label>
							<select class="msa-filter" name="<?php echo $attribute['filter']['name']; ?>">
								<option value="" <?php selected("", $value, true); ?>><?php _e('All', 'msa'); ?></option>
								<?php foreach ( $attribute['filter']['options'] as $option ) { ?>
									<option value="<?php echo $option['value']; ?>" <?php selected($option['value'], $value, true); ?>><?php echo $option['name']; ?></option>
								<?php } ?>
							</select>
						</div>

					<?php }

				}

			?><button class="msa-filter-button button"><?php _e('Filter', 'msa'); ?></button>
			<button class="msa-clear-filters-button button"><?php _e('Clear Filters', 'msa'); ?></button>
			</div>
			<script>
				jQuery(document).ready(function($){

					// Add Filters

					$('.msa-filter-button').click(function(e) {
						e.preventDefault();

						var parameters = '';

						$('.msa-filter').each(function(index, value) {

							if ( $(value).length != 0 ) {
								parameters += "&" + $(value).attr('name') + "=" + $(value).val();
							}
						});

					    window.location += parameters;
					});

					// Clear Filters

					$('.msa-clear-filters-button').click(function(e){
						e.preventDefault();
						window.location = "<?php echo get_admin_url() . 'admin.php?page=msa-all-audits&audit=' . $_GET['audit']; ?>";
					});
				});
			</script><?php

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
		return array( 'widefat', 'fixed', 'striped', $this->_args['plural'] );
	}
}

endif;