<?php
	/*	Sunshineplugin
		shunshineplugin_uninstall.php
		Deinstallationsscript
		Copyright 2015 - SunshineSites
	*/

	if ( !function_exists('add_action') ) {
		die( 'ERROR: Unauthorised access.' );
	}

	function ssp_uninstall() {

		global $wpdb;

		// delete WordPress settings
		delete_option( 'ssp_settings' );
		delete_option( 'ssp_colors' );
		delete_option( 'ssp_fonts' );
	}

?>
