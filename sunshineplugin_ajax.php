<?php
	/*	Sunshineplugin
		shunshineplugin-ajax.php
		AJAX-Funktionen
		Copyright 2015 - SunshineSites
	*/

	if ( !function_exists('add_action') ) {
		die( 'ERROR: Unauthorised access.' );
	}

	function ssp_ajax_add_email_callback() {
		$response = 0;

		check_ajax_referer( 'my-special-string', 'security' );

		$email = sanitize_email( $_POST['email'] );
		if ( $email != '' ) {
			if ( SSP_FREE ) {
				$response = ssp_compute_free_mail( $email );
			} else {
				$response_array = ssp_add_subscriber( $email );
				if ( $response_array['email'] == $email ) {
					$response = 1;
				}
			}
		}

		echo $response;
		die();
	}
	add_action( 'wp_ajax_ssp_ajax_add_email', 'ssp_ajax_add_email_callback' );
	add_action( 'wp_ajax_nopriv_ssp_ajax_add_email', 'ssp_ajax_add_email_callback' );
?>