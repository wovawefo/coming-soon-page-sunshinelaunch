<?php
	/*	Sunshineplugin
		shunshineplugin-mail.php
		AJAX-Funktionen
		Copyright 2015 - SunshineSites
	*/

	if ( !function_exists('add_action') ) {
		die( 'PhotoMark ERROR: Unauthorised access.' );
	}

	function ssp_send_email( $to, $subject, $msg, $from )
	{
		$type = 'plain';
		$header  = 'MIME-Version: 1.0' . "\r\n";
		$header .= 'Content-Type: text/'.$type.'; charset=UTF-8' . "\r\n";
		$header .= 'Content-Transfer-Encoding: quoted-printable' . "\r\n";
		$header .= 'From: '.$from."\r\n";
		return mail( $to, $subject, $msg, $header );
	}

	function ssp_compute_free_mail( $newmail )
	{
		$owner_mail	= SSP_OWNER_MAIL;
		if ( $owner_mail != '' ) {
			$find			= array( '%SITE%', '%SUBSCRIBERMAIL%', '%lt%','%gt%','%BR%', '\"');
			$replace		= array( home_url('/'), $newmail, '<', '>', "\r\n", '"');

			$subject 		= __( 'New subscriber', 'sunshineplugin');
			$text			= __( 'You have a new subscriber on %SITE% :%BR%%BR%%SUBSCRIBERMAIL%', 'sunshineplugin');
			$text 			= str_replace( $find, $replace, $text );
			$text			= str_replace( '=', '=3D', $text );

			return ssp_send_email( $owner_mail, $subject, $text, $newmail );
		} else {
			return 0;
		}
	}
?>