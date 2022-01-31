<?php
/**
 * The class that is responsible for diplaying all of the posts within an Audit
 * using the WP_List_Table.
 *
 * @package Classes / Audit Posts Table
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if ( ! class_exists( 'MSA_All_Posts_Table' ) ) :

	/**
	 * The Audit Posts Table class
	 */
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
				'ajax'     => false,
			) );

		}

		/**
		 * Display this message when there are no posts found
		 *
		 * @access public
		 * @return void
		 */
		public function no_items() {
			esc_attr_e( 'No Posts found.', 'msa' );
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

			$this->_column_headers = array( $columns, $hidden, $sortable );

			// Get the audit data.
			$audit_posts_model 	= new MSA_Audit_Posts_Model();
			$per_page     		= $this->get_items_per_page( 'posts_per_page', 50 );
			$current_page 		= $this->get_pagenum();
			$args = array(
				'per_page'		=> $per_page,
				'current_page'	=> $current_page,
			);

			/**
			 * Search term
			 */

			if ( isset( $_POST['s'] ) && check_admin_referer( 'msa-all-audit-posts-table' ) ) { // Input var okay.
				$args['s'] = sanitize_text_field( wp_unslash( $_POST['s'] ) ); // Input var okay.
			}

			/**
			 * Get Posts
			 */

			$audit = -1;
			if ( isset( $_GET['audit'] ) ) { // Input var okay.
				$audit = sanitize_text_field( wp_unslash( $_GET['audit'] ) ); // Input var okay.
			}

			$posts = $audit_posts_model->get_data( $audit, $args );

			foreach ( $posts as $post ) {
				$this->items[] = array( 'score' => $post['score'], 'post' => $post['post'], 'data' => $post['data'] );
			}

			/**
			 * Filter Posts
			 */

			$this->items = msa_filter_posts( $this->items );

			/**
			 * Sort Posts
			 */

			if ( count( $this->items ) > 0 ) {
				usort( $this->items, array( &$this, 'usort_reorder' ) );
			}

			$total_items = count( $this->items );

			$this->set_pagination_args( array(
				'total_items' => $total_items, 	// We have to calculate the total number of items.
				'per_page'    => $per_page, 	// We have to determine how many items to show on a page.
			) );

			if ( is_array( $this->items ) && count( $this->items ) > 0 ) {
				$this->items = array_slice( $this->items, ( ( $current_page - 1 ) * $per_page ), $per_page );
			}
		}

		/**
		 * Get all the columns that we want to display
		 *
		 * @access public
		 */
		function get_columns() {
			$columns['score'] = __( 'Score', 'msa' );
			$condition_categories = msa_get_condition_categories();

			foreach ( $condition_categories as $key => $condition_category ) {
				$columns[ $key ] = $condition_category['name'];
				$conditions = msa_get_conditions_from_category( $key );

				foreach ( $conditions as $key => $condition ) {
					$columns[ $key ] = $condition['name'];
				}
			}

			$attributes = msa_get_attributes();

			foreach ( $attributes as $key => $attribute ) {
				$columns[ $key ] = $attribute['name'];
			}

			return $columns;
		}

		/**
		 * Get the sortable columns for the table
		 *
		 * @access public
		 */
		function get_sortable_columns() {
			$sortable_columns['score'] = array( 'score', false );
			$condition_categories = msa_get_condition_categories();

			foreach ( $condition_categories as $key => $condition_category ) {
				$sortable_columns[ $key ] = array( $key, false );
			}

			$conditions = msa_get_conditions();

			foreach ( $conditions as $key => $condition ) {
				if ( 'score' === $key ) {
					$sortable_columns[ $key ] = array( $key, true );
				} else {
					$sortable_columns[ $key ] = array( $key, false );
				}
			}

			$attributes = msa_get_attributes();

			foreach ( $attributes as $key => $attribute ) {
				if ( isset( $attribute['sort'] ) && ! $attribute['sort'] ) {
					continue;
				}
				$sortable_columns[ $key ] = array( $key, false );
			}

			return $sortable_columns;
		}

		/**
		 * Sort the data
		 *
		 * @access public
		 * @param mixed $a The first element to sort.
		 * @param mixed $b The second element to sort.
		 */
		function usort_reorder( $a, $b ) {

			$orderby = 'score';
			if ( ! empty( $_GET['orderby'] ) ) { // Input var okay.
				$orderby = sanitize_text_field( wp_unslash( $_GET['orderby'] ) ); // Input var okay.
			}

			$order = 'desc';
			if ( ! empty( $_GET['order'] ) ) { // Input var okay.
				$order = sanitize_text_field( wp_unslash( $_GET['order'] ) ); // Input var okay.
			}

			$a_data = '';
			$b_data = '';

			if ( isset( $a['data']['values'][ $orderby ] ) ) {
				$a_data = $a['data']['values'][ $orderby ];
			}

			if ( isset( $b['data']['values'][ $orderby ] ) ) {
				$b_data = $b['data']['values'][ $orderby ];
			}

			$condition_categories = msa_get_condition_categories();

			if ( isset( $condition_categories[ $orderby ] ) ) {

				if ( isset( $a['data']['score']['data'][ $orderby ] ) ) {
					$a_data = $a['data']['score']['data'][ $orderby ];
				}

				if ( isset( $b['data']['score']['data'][ $orderby ] ) ) {
					$b_data = $b['data']['score']['data'][ $orderby ];
				}
			}

			$a_sort = apply_filters( 'msa_audit_posts_table_sort_data', $a_data, $a, $orderby );
			$b_sort = apply_filters( 'msa_audit_posts_table_sort_data', $b_data, $b, $orderby );

			// Determine sort order.
			$result = ( $a_sort < $b_sort ) ? -1 : 1;

			// Send final sort direction to usort.
			return ( 'asc' === $order ) ? $result : -$result;
		}

		/**
		 * Default Column Value
		 *
		 * @access public
		 * @param mixed $item        The audit post.
		 * @param mixed $column_name The column that is displayed.
		 */
		public function column_default( $item, $column_name ) {

			$audit = -1;
			if ( isset( $_GET['audit'] ) ) { // Input var okay.
				$audit = sanitize_text_field( wp_unslash( $_GET['audit'] ) ); // Input var okay.
			}

			$score = $item['data']['score'];
			$values = $item['data']['values'];

			$condition_categories = msa_get_condition_categories();
			$conditions = msa_get_conditions();
			$attributes = msa_get_attributes();

			switch ( $column_name ) {

				case 'score':
					$data = '<span>' . round( 100 * $score['score'] ) . '%</span><br/> <a href="' . msa_get_single_audit_post_link( $audit, $item['post']->ID ) . '">' . $item['post']->post_title . '</a>';
				break;

				case 'modified_date':
					$data = date( 'M j, Y', strtotime( $item['post']->post_modified ) );
				break;

				case 'comment_count':
					$data = $item['post']->comment_count;
				break;

				case 'post-type':
					$data = $item['post']->post_type;
				break;

				default:

					if ( isset( $values[ $column_name ] ) && ( isset( $conditions[ $column_name ] ) || isset( $attributes[ $column_name ] ) ) ) {
						$data = $values[ $column_name ];
					} else {
						$data = '';
					}

				break;
			}

			// Invalid Data.
			if ( 'missing_alt_tag' === $column_name ||
				 'broken_images' === $column_name ||
				 'broken_links' === $column_name ||
				 'invalid_headings' === $column_name ) {

				if ( 9999 === $values[ $column_name ] ) {
					$data = 'N/A';
				} else {
					$data = $values[ $column_name ];
				}
			}

			// Check if this is a condition category.
			if ( isset( $condition_categories[ $column_name ] ) ) {
				$data = '<span class="msa-post-status-text-' . msa_get_score_status( $score['data'][ $column_name ] ) . '">' . round( $score['data'][ $column_name ] * 100 ) . '%</span>';
			}

			return apply_filters( 'msa_all_posts_table_column_data', $data, $item, $column_name );
		}

		/**
		 * Generates content for a single row of the table
		 *
		 * @param object $item The current item.
		 */
		public function single_row( $item ) {

			echo '<tr class="msa-post-status msa-post-status-' . esc_attr__( msa_get_score_status( $item['data']['score']['score'] ) ) .'">';
			$this->single_row_columns( $item );
			echo '</tr>';
		}

		/**
		 * Extra controls to be displayed between bulk actions and pagination
		 *
		 * @access protected
		 *
		 * @param mixed $which Which tablenav the top or bottom.
		 */
		protected function extra_tablenav( $which ) {
			$conditions = msa_get_conditions();
			$attributes = msa_get_attributes();

			if ( 'top' === $which ) {
				?><div class="alignleft actions bulkactions"><?php

foreach ( $conditions as $key => $condition ) {
	if ( isset( $condition['filter'] ) ) {

		$value = '';
		if ( isset( $_GET[ $condition['filter']['name'] ] ) ) { // Input var okay.
			$value = sanitize_text_field( wp_unslash( $_GET[ $condition['filter']['name'] ] ) ); // Input var okay.
		}

		$options = '';

		$condition = apply_filters( 'msa_audit_posts_filter_' . $key, $condition );

		if ( 1 === $condition['comparison'] ) {

			$options .= '<option value="less-' . $condition['min'] . '" ' . selected( 'less-' . $condition['min'], $value, false ) . '>' . __( 'Less than ', 'msa' ) . ' ' . $condition['min'] . ' ' . $condition['units'] . '</option>';
			$options .= '<option value="more-' . $condition['min'] . '" ' . selected( 'more-' . $condition['min'], $value, false ) . '>' . __( 'More than ', 'msa' ) . ' ' . $condition['min'] . ' ' . $condition['units'] . '</option>';

		} else if ( 2 === $condition['comparison'] ) {

			$options .= '<option value="less-' . $condition['max'] . '" ' . selected( 'less-' . $condition['max'], $value, false ) . '>' . __( 'Less than ', 'msa' ) . ' ' . $condition['max'] . ' ' . $condition['units'] . '</option>';
			$options .= '<option value="more-' . $condition['max'] . '" ' . selected( 'more-' . $condition['max'], $value, false ) . '>' . __( 'More than ', 'msa' ) . ' ' . $condition['max'] . ' ' . $condition['units'] . '</option>';

		} else if ( 3 === $condition['comparison'] ) {

			$options .= '<option value="less-' . $condition['max'] . '" ' . selected( 'less-' . $condition['max'], $value, false ) . '>' . __( 'Less than ', 'msa' ) . ' ' . $condition['max'] . ' ' . $condition['units'] . '</option>';
			$options .= '<option value="more-' . $condition['max'] . '" ' . selected( 'more-' . $condition['max'], $value, false ) . '>' . __( 'More than ', 'msa' ) . ' ' . $condition['max'] . ' ' . $condition['units'] . '</option>';

		}

		?><div class="msa-filter-container msa-filter-conditions-container filter-<?php esc_attr_e( $key ); ?>">
			<!-- <label class="msa-filter-label"><?php esc_attr_e( $condition['filter']['label'] ); ?></label> -->
			<select class="msa-filter" name="<?php esc_attr_e( $condition['filter']['name'] ); ?>">
				<option value="" <?php selected( '', $value, true ); ?>><?php esc_attr_e( 'All ' . $condition['filter']['label'], 'msa' ); ?></option>
				<?php echo( $options ); // WPCS: XSS ok. ?>
			</select>
		</div> <?php
	}
}

foreach ( $attributes as $key => $attribute ) {

	if ( isset( $attribute['filter'] ) ) {

		$value = '';
		if ( isset( $_GET[ $attribute['filter']['name'] ] ) ) { // Input var okay.
			$value = sanitize_text_field( wp_unslash( $_GET[ $attribute['filter']['name'] ] ) );  // Input var okay.
		}

		$attribute['filter']['options'] = apply_filters( 'msa_filter_attribute_' . $key, $attribute['filter']['options'], $key ); ?>

		<div class="msa-filter-container msa-filter-attributes-container filter-<?php esc_attr_e( $key ); ?>">
			<!-- <label class="msa-filter-label"><?php esc_attr_e( $attribute['filter']['label'] ); ?></label> -->
			<select class="msa-filter" name="<?php esc_attr_e( $attribute['filter']['name'] ); ?>">
				<option value="" <?php selected( '', $value, true ); ?>><?php esc_attr_e( 'All ' . $attribute['filter']['label'], 'msa' ); ?></option>
				<?php foreach ( $attribute['filter']['options'] as $option ) { ?>
					<option value="<?php esc_attr_e( $option['value'] ); ?>" <?php selected( $option['value'], $value, true ); ?>><?php esc_attr_e( $option['name'] ); ?></option>
				<?php } ?>
			</select>
		</div>

	<?php }
}

				?><button class="msa-filter-button button"><?php esc_attr_e( 'Filter', 'msa' ); ?></button>
				<button class="msa-clear-filters-button button"><?php esc_attr_e( 'Clear Filters', 'msa' ); ?></button>
				</div><?php

			}

			// Output stlying for the condition categories.
			$condition_categories = msa_get_condition_categories();

			?><style>
			<?php foreach ( $condition_categories as $key => $condition_category ) {
				?>th#<?php esc_attr_e( $key ); ?>.manage-column.column-<?php esc_attr_e( $key ); ?>,
				.<?php esc_attr_e( $key ); ?>.column-<?php esc_attr_e( $key ); ?> {
					font-weight: bold;
					border-left: 1px solid #dfdfdf;
					background: linear-gradient(rgba(0, 0, 0, 0.02), rgba(0, 0, 0, 0.02));
				}
			<?php } ?>
			</style><?php
		}

		/**
		 * Get a list of CSS classes for the list table table tag.
		 *
		 * @access protected
		 *
		 * @return array List of CSS classes for the table tag.
		 */
		protected function get_table_classes() {

			$classes = array( 'widefat', 'fixed', 'striped', $this->_args['plural'] );

			if ( 0 === count( $this->items ) ) {
				foreach ( $classes as $key => $class ) {
					if ( 'fixed' === $class ) {
						unset( $classes[ $key ] );
					}
				}
			}

			return $classes;
		}
	}

endif;
