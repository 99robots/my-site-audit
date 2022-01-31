<?php
/**
 * This class manages the All Audits table using the WP_List_Table class.
 *
 * @package Classes / All Audit Table
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if ( ! class_exists( 'MSA_All_Audits_Table' ) ) :

	/**
	 * The All Audit Table class
	 */
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
			esc_attr_e( 'No Audits found.', 'msa' );
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

			$per_page     = 20;
			$current_page = $this->get_pagenum();
			$args = array();

			/**
			 * Search Term
			 */

			if ( isset( $_POST['s'] ) && check_admin_referer( 'msa-all-audits-table' ) ) { // Input var okay.

				if ( isset( $_POST['s'] ) ) { // Input var okay.
					$args['s'] = sanitize_text_field( wp_unslash( $_POST['s'] ) ); // Input var okay.
				}

				if ( isset( $_GET['s'] ) ) { // Input var okay.
					$args['s'] = sanitize_text_field( wp_unslash( $_GET['s'] ) ); // Input var okay.
				}

				if ( isset( $_GET['audit_status'] ) ) { // Input var okay.
					$args['status'] = sanitize_text_field( wp_unslash( $_GET['audit_status'] ) ); // Input var okay.
				}
			}

			/**
			 * Get Audits
			 */

			$audit_model = new MSA_Audits_Model();
			$this->items = $audit_model->get_data( $args );

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

			$this->items = array_slice( $this->items,( ( $current_page - 1 ) * $per_page ), $per_page );
			$this->items = apply_filters( 'msa_all_audits_table_items', $this->items );
		}

		/**
		 * Get all the columns that we want to display
		 *
		 * @access public
		 */
		function get_columns() {

			$columns['score']             = __( 'Score', 'msa' );
			$columns['name']              = __( 'Name', 'msa' );
			$columns['status']            = __( 'Status', 'msa' );
			$columns['date']              = __( 'Created On', 'msa' );
			$columns['user']              = __( 'Created By', 'msa' );
			$columns['num_posts']         = __( 'Number of Posts', 'msa' );
			$columns['post_date_range']   = __( 'Post Date Range', 'msa' );
			$columns['post_types']        = __( 'Post Types', 'msa' );

			return apply_filters( 'msa_all_audits_table_columns', $columns );
		}

		/**
		 * Get the sortable columns for the table
		 *
		 * @access public
		 */
		function get_sortable_columns() {

			$sortable_columns['date']         = array( 'date', true );
			$sortable_columns['score']        = array( 'score', false );

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

			$order = 'asc';
			if ( ! empty( $_GET['order'] ) ) { // Input var okay.
				$order = sanitize_text_field( wp_unslash( $_GET['order'] ) ); // Input var okay.
			}

			// Determine sort order.
			$result = ( $a[ $orderby ] < $b[ $orderby ] ) ? -1 : 1;

			// Send final sort direction to usort.
			return ( 'asc' === $order ) ? $result : -$result;
		}

		/**
		 * Default Column Value
		 *
		 * @access public
		 * @param mixed $item        The audit.
		 * @param mixed $column_name The column that is being displayed.
		 */
		public function column_default( $item, $column_name ) {

			if ( isset( $item['extension'] ) && $item['extension'] ) {
				return '';
			}

			$output = '';

			switch ( $column_name ) {

				case 'score':

					if ( 'completed' === $item['status'] ) {
						$output = round( 100 * $item[ $column_name ] ) . '%';
					} else {
						$output = __( '<span class="msa-spinner" style="padding-left:0;"><img src="' . get_site_url() . '/wp-admin/images/spinner-2x.gif"/></span>', 'msa' );
					}

				break;

				case 'Status':
					$output = ucfirst( str_replace( '-', ' ', $item[ $column_name ] ) );
				break;

				case 'date':
					$output = date( 'M d Y', strtotime( $item[ $column_name ] ) );
				break;

				case 'user':
					$user = get_userdata( $item[ $column_name ] );
					$output = $user->display_name;
				break;

				case 'post_types':
					$output = implode( '<br />', $item['args']['post_types'] );
				break;

				case 'post_date_range':

					$form_fields = json_decode( $item['args']['form_fields'], true );

					$output = date( 'M d Y', strtotime( $form_fields['after-date'] ) );
					$output .= ' - ' . date( 'M d Y', strtotime( $form_fields['before-date'] ) );
				break;

				default:
					$output = $item[ $column_name ];
				break;

			}

			return apply_filters( 'msa_all_audits_table_column_default', $output );

		}

		/**
		 * Return the output for the Name column
		 *
		 * @access public
		 * @param mixed $item The Audit.
		 */
		public function column_name( $item ) {

			if ( !empty( $item['status'] ) && 'in-progress' === $item['status'] ) {
				return apply_filters( 'msa_all_audits_table_column_name_extension', $item['name'] );
			}

			if ( ! isset( $item['extension'] ) ) {

				$audit_model = new MSA_Audits_Model();
				$audit = $audit_model->get_data_from_id( $item['id'] );

				$condition = $audit['args']['conditions'];

				$actions = array();

				$actions['delete'] = '<a href="' . wp_nonce_url( get_admin_url() . 'admin.php?page=msa-all-audits&action=delete&audit=' . $item['id'], 'msa-delete-audit' ) . '">' . __( 'Delete', 'msa' ) . '</a>';

				$condition_modal = '<a href="#" class="msa-audit-conditions-button" data-id="' . $item['id'] . '">' . __( 'Conditions', 'msa' ) . '</a>
				<div class="msa-audit-conditions-modal" data-id="' . $item['id'] . '">
					<div class="msa-audit-conditions-modal-container">

						<h3 class="msa-audit-conditions-modal-heading">' . __( 'Conditions', 'msa' ) . '</h3>

						<div class="msa-audit-conditions">
							<table class="wp-list-table widefat striped fixed">

								<thead>
									<tr>
										<th scope="col">' . __( 'Name', 'msa' ) . '</th>
										<th scope="col">' . __( 'Weight', 'msa' ) . '</th>
										<th scope="col">' . __( 'Comparison', 'msa' ) . '</th>
										<th scope="col">' . __( 'Value', 'msa' ) . '</th>
										<th scope="col">' . __( 'Minimum', 'msa' ) . '</th>
										<th scope="col">' . __( 'Maximum', 'msa' ) . '</th>
									</tr>
								</thead>

								<tbody>';

				foreach ( json_decode( $audit['args']['conditions'], true ) as $condition ) {

					$min = isset( $condition['units'] ) ? $condition['min'] . ' ' . $condition['units'] : $condition['min'];
					$max = isset( $condition['units'] ) ? $condition['max'] . ' ' . $condition['units'] : $condition['max'];

					if ( 1 === $condition['comparison'] ) {
						$comparison = __( 'Greater Than', 'msa' );
						$max = '';
					} else if ( 2 === $condition['comparison'] ) {
						$comparison = __( 'Less Than', 'msa' );
						$min = '';
					} else if ( 3 === $condition['comparison'] ) {
						$comparison = __( 'In Between', 'msa' );
					}

					$value = __( 'Pass or Fail', 'msa' );

					if ( 2 === $condition['value'] ) {
						$value = __( 'Percentage', 'msa' );
					}

					$condition_modal .= '<tr>
						<td>' . ( isset( $condition['name'] ) ? $condition['name'] : '' ) . '</td>
						<td>' . ( isset( $condition['weight'] ) ? $condition['weight'] : '' ) . '</td>
						<td>' . $comparison . '</td>
						<td>' . $value . '</td>
						<td>' . $min . '</td>
						<td>' . $max . '</td>
					</tr>';

				}

								$condition_modal .= '</tbody>
							</table>
						</div>
					</div>
				</div>';

				$actions['edit'] = $condition_modal;

				return apply_filters( 'msa_all_audits_table_column_name', sprintf( '%1$s %2$s', '<a href="' . msa_get_single_audit_link( $item['id'] ) . '">' . ($item['name']) . '</a><small style="opacity:0.5;padding-left:4px;">id:(' . $item['id'] . ')</small>', $this->row_actions( $actions ) ) );

			}

			return apply_filters( 'msa_all_audits_table_column_name_extension', '<a href="' . $item['extension-link'] . '" target="_blank">' . $item['name'] . '</a>' );
		}

		/**
		 * Generates content for a single row of the table
		 *
		 * @access public
		 * @param array $item The Audit.
		 */
		public function single_row( $item ) {

			// Check if this row is for an extension.
			if ( isset( $item['extension'] ) && $item['extension'] ) {

				echo '<tr class="msa-extension-row">';
				$this->single_row_columns( $item );
				echo '</tr>';

			} else {

				$class = '';

				if ( 'completed' === $item['status'] ) {
					$class = 'msa-post-status msa-post-status-' . msa_get_score_status( $item['score'] );
				}

				echo '<tr class="' . esc_attr( $class ) . '">';
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
			return array( 'widefat', 'msa-all-audits', 'striped', $this->_args['plural'] );
		}
	}

endif;
