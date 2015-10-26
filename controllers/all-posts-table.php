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

		$per_page     = 20;
		$current_page = $this->get_pagenum();

		$args = array(
			'public' 			=> true,
			'posts_per_page'	=> -1,
		);

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
		 * Get Posts
		 *
		 ========================================================================= */

		$posts = get_posts($args);

		foreach ( $posts as $post ) {
			$this->items[] = array('post' => $post, 'data' => msa_get_post_audit_data($post));
		}

		/* =========================================================================
		 *
		 * Filter Posts
		 *
		 ========================================================================= */

		// Score

		if ( isset($_GET['score']) ) {

			$score_value = floatval($_GET['score']);
			$score_range = msa_get_score_increment();

			if ( $score_value < msa_get_score_increment() * 2 ) {
				$score_value = 0;
				$score_range = msa_get_score_increment() * 2;
			}

			foreach ( $this->items as $key => $item ) {

				$score = msa_calculate_score($item['post'], $item['data']);

				if ( $score['score'] < $score_value || $score['score'] > ( $score_value + $score_range ) ) {
					unset($this->items[$key]);
				}
			}
		}

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

		$columns = array(
			'score'          => __( 'Score', 'msa'),
			'title'          => __( 'Title', 'msa'),
			'modified_date'  => __( 'Modified Date', 'msa'),
			'word_count'     => __( 'Word Count', 'msa'),
			'comment_count'  => __( 'Comments', 'msa'),
			'internal_links' => __( 'Internal Links', 'msa'),
			'external_links' => __( 'External Links', 'msa'),
			'images'         => __( 'Images', 'msa'),
			'headings'       => __( 'Headings', 'msa'),
		);

		return $columns;
	}

	/**
	 * Get the sortable columns for the table
	 *
	 * @access public
	 * @return void
	 */
	function get_sortable_columns() {

		$sortable_columns = array(
			'score'          => array('score', true),
			'title'          => array('title', false),
			'modified_date'  => array('modified_date', false),
			'word_count'     => array('word_count', false),
			'comment_count'  => array('comment_count', false),
			'internal_links' => array('internal_links', false),
			'external_links' => array('external_links', false),
			'images'         => array('images', false),
			'headings'       => array('headings', false),
		);

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

		// Determine sort order
		$result = ($a['data'][$orderby] < $b['data'][$orderby]) ? -1 : 1;

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
		$score['data']['score'] = $score['score'];

		// Default

		$caret = '<i class="fa fa-caret-' . ( $score['data'][$column_name] >= .5 ? 'up' : 'down' ) . ' msa-post-status-text-' . msa_get_score_status($score['data'][$column_name]) . '"></i> ';

		switch( $column_name ) {

			case 'score':
				$data = round(100 * $item['data']['score']) . '%';
				$caret = '';
			break;

			case 'title':
				$data = '<a href="' . get_admin_url() . 'admin.php?page=msa-dashboard&post=' . $item['post']->ID . '">' . $item['post']->post_title . '</a>';
			break;

			case 'modified_date':
				$data = date('M j, Y', strtotime($item['post']->post_modified));
			break;

			case 'word_count':
				$data = str_word_count($item['data']['content']);
			break;

			case 'comment_count':
				$data = $item['post']->comment_count;
			break;

			default:
				$data = $item['data'][$column_name];
			break;

		}

		return $caret . $data;

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
	 * Generate the table navigation above or below the table
	 *
	 * @since 3.1.0
	 * @access protected
	 * @param string $which
	 */
	protected function display_tablenav( $which ) {

		if ( 'top' == $which )
			wp_nonce_field( 'bulk-' . $this->_args['plural'] );

		// Show Score Filter

		$score = '';

		if ( isset($_GET['score']) ) {
			$score = $_GET['score'];
		}

		?><div class="tablenav <?php echo esc_attr( $which ); ?>">

			<div class="alignleft actions bulkactions">
				<div>
					<label class="msa-filter-label"><?php _e('Score:', 'msa'); ?></label>
					<select name="score">
						<option value="<?php echo msa_get_score_increment() * 1; ?>" <?php selected( msa_get_score_increment() * 1 , $score, true); ?>><?php _e('Bad', 'msa'); ?></option>
						<option value="<?php echo msa_get_score_increment() * 2; ?>" <?php selected( msa_get_score_increment() * 2, $score, true); ?>><?php _e('Poor', 'msa'); ?></option>
						<option value="<?php echo msa_get_score_increment() * 3; ?>" <?php selected( msa_get_score_increment() * 3 , $score, true); ?>><?php _e('Ok', 'msa'); ?></option>
						<option value="<?php echo msa_get_score_increment() * 4; ?>" <?php selected( msa_get_score_increment() * 4 , $score, true); ?>><?php _e('Good', 'msa'); ?></option>
						<option value="<?php echo msa_get_score_increment() * 5; ?>" <?php selected( msa_get_score_increment() * 5 , $score, true); ?>><?php _e('Great', 'msa'); ?></option>
					</select>
				</div>
			</div>
			<?php
			$this->extra_tablenav( $which );
			$this->pagination( $which );
			?>
			<br class="clear" />
		</div><?php
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