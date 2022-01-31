<?php
/**
 * Plugin Name: My Site Audit
 * Plugin URI: https://draftpress.com/products/my-site-audit
 * Description: The ultimate way to audit your site's content to drive more traffic to your site and enhance your user engagement.
 * version: 1.2.5
 * Author: DraftPress
 * Author URI: https://draftpress.com
 * License: GPL2
 *
 * @package Main File
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'My_Site_Audit' ) ) :

	/**
	 * My_Site_Audit class.
	 */
	final class My_Site_Audit {

		/**
		 * Holds the My_Site_Audit object and is the only way to obtain it
		 *
		 * @var mixed
		 * @access private
		 * @static
		 */
		private static $instance;

		/**
		 * Creates or retrieves the My_Site_Audit instance
		 *
		 * @access public
		 * @static
		 */
		public static function instance() {

			// No object is created yet so lets create one.
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof My_Site_Audit ) ) {

				self::$instance = new My_Site_Audit;
				self::$instance->setup_constants();
				self::$instance->includes();

				add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
				add_action( 'init', array( self::$instance, 'init' ) );

			}

			// Return the My_Site_Audit object.
			return self::$instance;
		}

		/**
		 * Throw an error if this class is cloned
		 *
		 * @access public
		 * @return void
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, esc_attr__( 'You cannot __clone an instance of the My_Site_Audit class.', 'msa' ), '1.6' );
		}

		/**
		 * Throw an error if this class is unserialized
		 *
		 * @access public
		 * @return void
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, esc_attr__( 'You cannot __wakeup an instance of the My_Site_Audit class.', 'msa' ), '1.6' );
		}

		/**
		 * Sets up the constants we will use throughout the plugin
		 *
		 * @access private
		 * @return void
		 */
		private function setup_constants() {

			/**
			 * Item Name
			 */

			if ( ! defined( 'MY_SITE_AUDIT_ITEM_NAME' ) ) {
				define( 'MY_SITE_AUDIT_ITEM_NAME', 'My Site Audit' );
			}

			/**
			 * Store URL
			 */

			if ( ! defined( 'MY_SITE_AUDIT_STORE_URL' ) ) {
				define( 'MY_SITE_AUDIT_STORE_URL', 'https://draftpress.com' );
			}

			/**
			 * Extensions URL
			 */

			if ( ! defined( 'MY_SITE_AUDIT_EXT_URL' ) ) {
				define( 'MY_SITE_AUDIT_EXT_URL' , 'https://draftpress.com/products/category/my-site-audit/?&utm_source=plugin_pages' );
			}

			/**
			 * Minimum PHP version
			 */

			if ( ! defined( 'MY_SITE_AUDIT_MIN_PHP_VERSION' ) ) {
				define( 'MY_SITE_AUDIT_MIN_PHP_VERSION', '5.4.0' );
			}

			/**
			 * Plugin prefix
			 */

			if ( ! defined( 'MY_SITE_AUDIT_PREFIX' ) ) {
				define( 'MY_SITE_AUDIT_PREFIX', 'msa-' );
			}

			/**
			 * Plugin version
			 */

			if ( ! defined( 'MY_SITE_AUDIT_VERSION' ) ) {
				define( 'MY_SITE_AUDIT_VERSION', '1.2.5' );
			}

			/**
			 * Plugin Folder Path
			 */

			if ( ! defined( 'MY_SITE_AUDIT_PLUGIN_DIR' ) ) {
				define( 'MY_SITE_AUDIT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			/**
			 * Plugin Folder URL
			 */

			if ( ! defined( 'MY_SITE_AUDIT_PLUGIN_URL' ) ) {
				define( 'MY_SITE_AUDIT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			/**
			 * Plugin Root File
			 */

			if ( ! defined( 'MY_SITE_AUDIT_PLUGIN_FILE' ) ) {
				define( 'MY_SITE_AUDIT_PLUGIN_FILE', __FILE__ );
			}

			/**
			 * Max audits
			 */

			if ( ! defined( 'MY_SITE_AUDIT_MAX_AUDITS' ) ) {
				define( 'MY_SITE_AUDIT_MAX_AUDITS', 1 );
			}

			/**
			 * Make sure CAL_GREGORIAN is defined
			 */

			if ( ! defined( 'CAL_GREGORIAN' ) ) {
				define( 'CAL_GREGORIAN', 1 );
			}

			/* date_default_timezone_set( timezone_name_from_abbr( null, (int) get_option( 'gmt_offset' ) * 3600 , true ) ); */
		}

		/**
		 * Include required files
		 *
		 * @access private
		 * @since 1.4
		 * @return void
		 */
		function includes() {

			// Load all the admin files.
			if ( is_admin() ) {

				/**
				 * Includes
				 */

				// Model.
				require_once( MY_SITE_AUDIT_PLUGIN_DIR . 'model/audits.php' );
				require_once( MY_SITE_AUDIT_PLUGIN_DIR . 'model/audit-posts.php' );

				// Functions.
				require_once( MY_SITE_AUDIT_PLUGIN_DIR . 'functions/activation.php' );
				require_once( MY_SITE_AUDIT_PLUGIN_DIR . 'functions/admin-pages.php' );
				require_once( MY_SITE_AUDIT_PLUGIN_DIR . 'functions/admin-notices.php' );
				require_once( MY_SITE_AUDIT_PLUGIN_DIR . 'functions/condition.php' );
				require_once( MY_SITE_AUDIT_PLUGIN_DIR . 'functions/condition-category.php' );
				require_once( MY_SITE_AUDIT_PLUGIN_DIR . 'functions/attribute.php' );
				require_once( MY_SITE_AUDIT_PLUGIN_DIR . 'functions/score-status.php' );
				require_once( MY_SITE_AUDIT_PLUGIN_DIR . 'functions/dashboard-panel.php' );
				require_once( MY_SITE_AUDIT_PLUGIN_DIR . 'functions/settings-tab.php' );
				require_once( MY_SITE_AUDIT_PLUGIN_DIR . 'functions/extension.php' );
				require_once( MY_SITE_AUDIT_PLUGIN_DIR . 'functions/licensing.php' );
				require_once( MY_SITE_AUDIT_PLUGIN_DIR . 'functions/system-info.php' );

				require_once( MY_SITE_AUDIT_PLUGIN_DIR . 'functions/audit-data.php' );
				require_once( MY_SITE_AUDIT_PLUGIN_DIR . 'functions/common.php' );
				require_once( MY_SITE_AUDIT_PLUGIN_DIR . 'functions/create-audit.php' );
				require_once( MY_SITE_AUDIT_PLUGIN_DIR . 'functions/notifications.php' );
				require_once( MY_SITE_AUDIT_PLUGIN_DIR . 'functions/post-meta-box.php' );
				require_once( MY_SITE_AUDIT_PLUGIN_DIR . 'functions/plugin.php' );
				require_once( MY_SITE_AUDIT_PLUGIN_DIR . 'functions/welcome.php' );

				// Classes.
				require_once( MY_SITE_AUDIT_PLUGIN_DIR . 'classes/class.all-posts-table.php' );
				require_once( MY_SITE_AUDIT_PLUGIN_DIR . 'classes/class.all-audits-table.php' );

			}
		}

		/**
		 * Setup the plugin
		 *
		 * @access public
		 * @return void
		 */
		public function init() {

			/**
			 * Registers
			 */

			if ( function_exists( 'msa_create_initial_conditions' ) ) {
				msa_create_initial_conditions();
			}

			if ( function_exists( 'msa_create_initial_condition_categories' ) ) {
				msa_create_initial_condition_categories();
			}

			if ( function_exists( 'msa_create_initial_attributes' ) ) {
				msa_create_initial_attributes();
			}

			if ( function_exists( 'msa_create_initial_score_statuses' ) ) {
				msa_create_initial_score_statuses();
			}

			if ( function_exists( 'msa_create_initial_dashboard_panels' ) ) {
				msa_create_initial_dashboard_panels();
			}

			if ( function_exists( 'msa_create_initial_settings_tabs' ) ) {
				msa_create_initial_settings_tabs();
			}

			if ( function_exists( 'msa_create_initial_extensions' ) ) {
				msa_create_initial_extensions();
			}

			/**
			 * Deregisters
			 */

			if ( function_exists( 'msa_deregister_condition' ) ) {

				if ( false === ( $settings = get_option( 'msa_settings' ) ) ) {
					$settings = array();
				}

				if ( ! isset( $settings['use_slow_conditions'] ) || ( isset( $settings['use_slow_conditions'] ) && ! $settings['use_slow_conditions'] ) ) {
					msa_deregister_condition( 'broken_links' );
					msa_deregister_condition( 'broken_images' );
				}
			}
		}

		/**
		 * Load the text domain for translation
		 *
		 * @access public
		 * @return void
		 */
		public function load_textdomain() {
			load_textdomain( 'msa' , dirname( plugin_basename( MY_SITE_AUDIT_PLUGIN_FILE ) ) . '/languages/' );
		}
	}

endif;

/**
 * This is the function you will use in order to obtain an instance
 * of the My_Site_Audit class.
 *
 * Example: <?php $msa = MSA(); ?>
 *
 * @access public
 */
function my_site_audit_instance() {
	return My_Site_Audit::instance();
}

// Get the class loaded up and running.
my_site_audit_instance();

/**
 * Create an Audit
 *
 * @access public
 * @param  array $audit_data The attributes for an audit.
 * @return void
 */
function msa_create_audit( $audit_data ) {

	// Set the transient to say that we are running an audit.
	set_transient( 'msa_running_audit', true );
	delete_transient( 'msa_schedule_audit' );

	// Include all the files we need.
	require_once( plugin_dir_path( __FILE__ ) . 'functions/common.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'model/audits.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'model/audit-posts.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'functions/audit-data.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'functions/condition.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'functions/condition-category.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'functions/attribute.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'functions/score-status.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'functions/create-audit.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'functions/notifications.php' );

	msa_create_initial_conditions();
	msa_create_initial_condition_categories();

	$audit_model = new MSA_Audits_Model();
	$audit_posts_model = new MSA_Audit_Posts_Model();

	// Get all the data from the user.
	$audit = array();

	$audit['name']                = $audit_data['name'];
	$audit['score']               = 0;
	$audit['date']                = date( 'Y-m-d H:i:s' );
	$audit['status']              = 'in-progress';
	$audit['user']                = $audit_data['user'];
	$audit['num_posts']           = 0;
	$audit['args']['conditions']  = wp_json_encode( msa_get_conditions() );
	$audit['args']['before_date'] = $audit_data['after-date'];
	$audit['args']['before_date'] = $audit_data['before-date'];
	$audit['args']['post_types']  = $audit_data['post-types'];
	$audit['args']['max_posts']   = $audit_data['max-posts'];
	$audit_data['after-date']     = '' !== $audit_data['after-date']  ? strip_tags( $audit_data['after-date'] ) : date( 'm/d/Y', strtotime( '-1 years' ) );
	$audit_data['before-date']    = '' !== $audit_data['before-date'] ? strip_tags( $audit_data['before-date'] ) : date( 'm/d/Y', strtotime( 'today' ) );
	$audit['args']['form_fields'] = wp_json_encode( $audit_data );

	// Get all the posts that we are going to perform an audit on.
	$page = 1;
	$posts_per_page = 25;
	$total_posts = 0;
	$audit_score = 0;

	$args = array(
		'public' 			=> true,
		'date_query' 		=> array(
			array(
				'after'	 => $audit_data['after-date'],
				'before'	=> $audit_data['before-date'],
				'inclusive' => true,
			),
		),
		'post_type'			=> $audit_data['post-types'],
		'posts_per_page'	=> $posts_per_page, // $audit_data['max-posts'],
		'paged'				=> $page,
	);

	$posts = get_posts( $args );

	// Create the audit.
	if ( count( $posts ) > 0 ) {
		$audit_id = $audit_model->add_data( $audit );
	}

	// Only perform the audit if there are posts to perform the audit on.
	while ( count( $posts ) > 0 ) {
		if ( $audit_id ) {
			foreach ( $posts as $post ) {
				if ( -1 !== $audit_data['max-posts'] && $total_posts >= $audit_data['max-posts'] ) {
					break 2;
				}

				$data = msa_get_post_audit_data( $post );
				$score = msa_calculate_score( $post, $data );
				$data['score'] = $score['score'];

				// Add a new record in the audit posts table.
				$audit_posts_model->add_data( array(
					'audit_id' 	=> $audit_id,
					'post'		=> $post,
					'data'		=> array(
					'score'		=> $score,
					'values'	=> $data,
					),
				) );

				$audit_score += $score['score'];
				$total_posts++;
			}
		}

		$page++;

		$args = array(
			'public' 			=> true,
			'date_query' 		=> array(
				array(
					'after'	 => $audit_data['after-date'],
					'before'	=> $audit_data['before-date'],
					'inclusive' => true,
				),
			),
			'post_type'			=> $audit_data['post-types'],
			'posts_per_page'	=> $posts_per_page,
			'paged'				=> $page,
		);

		$posts = get_posts( $args );
	}

	$audit_score = round( $audit_score / $total_posts, 10 );
	$audit['num_posts'] = $total_posts;
	$audit['score'] = round( $audit_score, 10 );
	$audit['status'] = 'completed';
	$audit_model->update_data( $audit_id, $audit );

	// Remove the transient once we are done with the audit.
	delete_transient( 'msa_running_audit' );

	/**
	 * Runs when the audit is completed
	 *
	 * @param int    $audit_id      The Audit ID.
	 * @param string $audit['name'] The Audit name.
	 */
	do_action( 'msa_audit_completed', $audit_id, $audit['name'] );
}
add_action( 'msa_run_audit_background', 'msa_create_audit', 10, 1 );
