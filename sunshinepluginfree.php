<?php
/*
Plugin Name: SunshinePlugin
Plugin URI: http://www.sunshinesites.com/coming-soon-plugin
Description: SunshinePlugin is a beautiful Coming Soon & Under Construction plugin for WordPress.
Author: SunshineSites
Version: 1.0.0
*/
	if ( !function_exists('add_action') ) {
		die( 'ERROR: Unauthorised access.' );
	}

	////// -> load translation
	load_plugin_textdomain('sunshineplugin', false, dirname( plugin_basename( __FILE__ ) ) . '/lang');

	////// -> path definitions
	define( 'SSP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
	$location = explode( '/', plugin_basename( __FILE__ ) );
	define( 'SSP_FOLDER', $location[0] );
	define( 'SSP_GRAPH_URL', plugins_url().'/'.SSP_FOLDER.'/graphics/' );

	$path_array  = wp_upload_dir();
	define( 'SSP_MAIN_FILE', $location[1] );
	define( 'SSP_PIC_DIR', str_replace('\\', '/', $path_array['basedir']).'/'.SSP_FOLDER );
	define( 'SSP_PIC_URL', $path_array['baseurl'].'/sunshineplugin');
	if ( file_exists( SSP_PLUGIN_DIR."MailChimp.php" ) ) {
		define( 'SSP_FREE',			false );
		define( 'SSP_TEMPLATE_FOLDER','templates' );
	} else {
		define( 'SSP_FREE',			true );
		define( 'SSP_TEMPLATE_FOLDER', 'templates_free' );
	}
	define( 'SSP_TEMPLATE_URL', plugin_dir_path( __FILE__ ).SSP_TEMPLATE_FOLDER.'/' );


	////// -> configuration, functions
	require_once( SSP_PLUGIN_DIR."sunshineplugin_api.php" );
	require_once( SSP_PLUGIN_DIR."sunshineplugin_mail.php" );
	require_once( SSP_PLUGIN_DIR."sunshineplugin_config.php" );
	require_once( SSP_PLUGIN_DIR."sunshineplugin_uninstall.php" );

	////// -> admin
	require_once( SSP_PLUGIN_DIR."sunshineplugin_admin_backend.php" );

	////// -> frontend
	require_once( SSP_PLUGIN_DIR."sunshineplugin_ajax.php" );
	require_once( SSP_PLUGIN_DIR."sunshineplugin_css.php" );

	////// -> WP-hooks and shortcodes
	register_uninstall_hook( __FILE__, 'ssp_uninstall' );
	add_shortcode( 'sunshineplugin', 'ssp_frontend_start' );
	add_filter( 'template_include', 'ssp_load_overlay' );

	// remove all foreign stylesheets
	add_action('wp_head', 'ssp_kill_styles', 1);
?>
