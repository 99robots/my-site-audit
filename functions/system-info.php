<?php
/**
 * This file is responsible for handling the output of the settings tab System Info.
 *
 * @package Functions / System Info
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * System Info Tab
 *
 * @access public
 * @param mixed $content   The original HTML output.
 * @return  mixed $content The new HTML output.
 */
function msa_settings_tab_system_info_content( $content ) {

	$content = '';
	$ok = 0;
	$bad = 0;

	$system_status = '<h3 class="msa-settings-heading">' . __( 'System Status' ) . '</h3>';

	$system_status_table = '<div class="msa-system-status-table">
		<table class="wp-list-table widefat fixed striped">
			<tbody>';

	// System Status.
	if ( version_compare( phpversion(), MY_SITE_AUDIT_MIN_PHP_VERSION ) < 1 ) {
		$system_status_table .= '<tr>
			<td class="msa-system-status-row bad"><strong>' . __( 'PHP Version', 'msa' ) . '</strong></td>
			<td>' . __( 'The required minimum version of PHP is <strong>v' . MY_SITE_AUDIT_MIN_PHP_VERSION . '</strong> and your version is <strong>v' . phpversion() . '</strong>. Your version of PHP is outdated and we strongly recommend that you <a href="https://draftpress.com/docs/how-to-update-your-php-version/?utm_source=plugin&utm_medium_system_info" target="_blank">update</a> your version of PHP to at least <strong>v' . MY_SITE_AUDIT_MIN_PHP_VERSION . '</strong>.', 'msa' ) . '</td>
			</tr>';
		$bad++;
	} else {
		$system_status_table .= '<tr>
			<td class="msa-system-status-row ok"><strong>' . __( 'PHP Version', 'msa' ) . '</strong></td>
			<td>' . __( 'OK', 'msa' ) . '</td>
			</tr>';
		$ok++;
	}

	if ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) {
		$system_status_table .= '<tr>
			<td class="msa-system-status-row bad"><strong>' . __( 'WP Cron', 'msa' ) . '</strong></td>
			<td>' . __( 'WP Cron is <span style="color:red;font-weight:bold;">DISABLED</span>! My Site Audit needs WP Cron to be enabled in order to create an audit. Please read our', 'msa' ); ?> <a href="https://draftpress.com/docs/how-to-enable-wp-cron/" target="_blank"><?php esc_attr_e( 'documentation page', 'msa' ); ?></a> <?php esc_attr_e( 'about how to enable WP Cron.', 'msa' ) . '</td>
			</tr>';
			$bad++;
	} else {
		$system_status_table .= '<tr>
			<td class="msa-system-status-row ok"><strong>' . __( 'WP Cron', 'msa' ) . '</strong></td>
			<td>' . __( 'OK', 'msa' ) . '</td>
			</tr>';
		$ok++;
	}

	$system_status_table .= '</tbody>
		</table>
	</div>';

	$content .= $system_status . ( $bad > 0 ?  '<p>' . $bad . __( ' parts of your system have failed.' ) . '</p>' : '' ) . $system_status_table;
	$content .= '<h3 class="msa-settings-heading">' . __( 'System Information', 'msa' ) . '</h3>';

	do_action( 'msa_before_system_info' );

	$break_line = "======================================================================\n\n";
	$white_space = '                              ';
	$content .= '<textarea class="msa-system-info-text" rows="10">';
	$system_info = msa_get_system_info();

	foreach ( $system_info as $group ) {
		$content .= $break_line;

		foreach ( $group as $item ) {
			$content .= $item['name'] . substr( $white_space, strlen( $item['name'] ) ) . $item['value'] . "\n";
		}

		$content .= "\n";
	}

	do_action( 'msa_after_system_info' );
	$content .= '</textarea>';

	return $content;
}
add_filter( 'msa_settings_tab_content_system_info', 'msa_settings_tab_system_info_content', 10, 1 );

/**
 * Get the information about this wordpress install
 *
 * @access public
 * @return string $output The HTMl output.
 */
function msa_get_system_info() {

	global $wp_version;

	if ( get_bloginfo( 'version' ) < '3.4' ) {
		$theme_data = get_theme_data( get_stylesheet_directory() . '/style.css' );
	} else {
		$theme_data = wp_get_theme();
	}

	$white_space = '                              ';

	$plugins = get_plugins();
	$active_plugins = get_option( 'active_plugins', array() );

	$inactive_plugins_data = array();
	$active_plugins_data = array();
	$network_activated_plugins_data = array();

	foreach ( $plugins as $plugin_path => $plugin ) {

		if ( ! in_array( $plugin_path, $active_plugins, true ) ) {
			$inactive_plugins_data[] = $plugin['Name'] . ': ' . $plugin['Version'];
		} else {
			$active_plugins_data[] = $plugin['Name'] . ': ' . $plugin['Version'];
		}
	}

	if ( is_multisite() ) {

		$plugins = wp_get_active_network_plugins();
		$active_plugins = get_site_option( 'active_sitewide_plugins', array() );

		foreach ( $plugins as $plugin_path ) {

			$plugin_base = plugin_basename( $plugin_path );

			if ( ! array_key_exists( $plugin_base, $active_plugins ) ) {
				continue;
			}
			$plugin = get_plugin_data( $plugin_path );

			$network_activated_plugins_data[] = $plugin['Name'] . ' :' . $plugin['Version'];
		}
	}

	$web_server_info = '';
	if ( isset( $_SERVER['SERVER_SOFTWARE'] ) ) { // Input var okay.
		$web_server_info = sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ); // Input var okay.
	}

	return array(
		array(
			array(
				'name'	=> 'Site URL',
				'value'	=> site_url(),
			),
			array(
				'name'	=> 'Home URL',
				'value'	=> home_url(),
			),
			array(
				'name'	=> 'Admin URL',
				'value'	=> admin_url(),
			),
		),
		array(
			array(
				'name'	=> 'WordPress Version',
				'value'	=> $wp_version,
			),
			array(
				'name'	=> 'Permalink Structure',
				'value'	=> get_option( 'permalink_structure' ),
			),
			array(
				'name'	=> 'WP_DEBUG',
				'value'	=> defined( 'WP_DEBUG' ) ? WP_DEBUG ? 'Enabled' : 'Disabled' : 'Not set',
			),
			array(
				'name'	=> 'Registered Post Stati',
				'value'	=> implode( "\n" . $white_space, get_post_stati() ),
			),
			array(
				'name'	=> 'Active Plugins',
				'value'	=> implode( "\n" . $white_space, $active_plugins_data ),
			),
			array(
				'name'	=> 'Inactive Plugins',
				'value'	=> implode( "\n" . $white_space, $inactive_plugins_data ),
			),
			array(
				'name'	=> 'Network Activated Plugins',
				'value'	=> implode( "\n" . $white_space, $network_activated_plugins_data ),
			),
		),
		array(
			array(
				'name'	=> 'My Site Audit Version',
				'value'	=> MY_SITE_AUDIT_VERSION,
			),
		),
		array(
			array(
				'name'	=> 'PHP Version',
				'value'	=> PHP_VERSION,
			),
			array(
				'name'	=> 'Web Server Info',
				'value'	=> $web_server_info,
			),
			array(
				'name'	=> 'WordPress Memory Limit',
				'value'	=> WP_MEMORY_LIMIT,
			),
			array(
				'name'	=> 'PHP Safe Mode',
				'value'	=> ini_get( 'safe_mode' ) ? __( 'Yes', 'msa' ) : __( 'No', 'msa' ),
			),
			array(
				'name'	=> 'PHP Memory Limit',
				'value'	=> ini_get( 'memory_limit' ),
			),
			array(
				'name'	=> 'PHP Upload Max Size',
				'value'	=> ini_get( 'upload_max_filesize' ),
			),
			array(
				'name'	=> 'PHP Post Max Size',
				'value'	=> ini_get( 'post_max_size' ),
			),
			array(
				'name'	=> 'PHP Upload Max File-size',
				'value'	=> ini_get( 'upload_max_filesize' ),
			),
			array(
				'name'	=> 'PHP Time Limit',
				'value'	=> ini_get( 'max_execution_time' ),
			),
			array(
				'name'	=> 'PHP Max Input Vars',
				'value'	=> ini_get( 'max_input_vars' ),
			),
			array(
				'name'	=> 'PHP Arg Separator',
				'value'	=> ini_get( 'arg_separator.output' ),
			),
			array(
				'name'	=> 'PHP Allow URL File Open',
				'value'	=> ini_get( 'allow_url_fopen' ) ? __( 'Yes', 'msa' ) : __( 'No', 'msa' ),
			),
		),
	);
}
