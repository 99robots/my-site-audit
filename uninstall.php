<?php

// if uninstall not called from WordPress exit

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit ();

// Delete all existence of this plugin

$version_name = 'nnr_custom_image_sizes_version';
$settings_name = 'nnr_custom_image_sizes_settings';

if ( !is_multisite() ) {

	// Delete blog options

	delete_option($version_name);
	delete_option($settings_name);

} else {

	// Delete site options

	delete_site_option($version_name);

	 foreach ( $blog_ids as $blog_id ) {

        switch_to_blog( $blog_id );

        delete_option($settings_name);
    }

}