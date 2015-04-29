<?php
	/*	Sunshineplugin
		shunshineplugin-css.php
		Funktionen zur Anzeige des Frontends
		Copyright 2015 - SunshineSites
	*/

	if ( !function_exists('add_action') ) {
		die( 'ERROR: Unauthorised access.' );
	}

	function ssp_parse_special_fonts( $font_name ) {
		if ( strpos($font_name, ' ') !== false ) {
			$font_name = '"'.$font_name.'"';
		}
		return $font_name;
	}


	function ssp_project_css( ) {
		$ssp_colors = get_option( 'ssp_colors' );

		$wrapper_padding = '3em';

		if ( SSP_TILE_ACTIVE ) {
			$wrapper = '
				#ssp_wrapper_tile { background-color: '.$ssp_colors[13].'; padding: '.$wrapper_padding.' }
				header { margin-top: 20pt; }
				footer { margin-left: -'.$wrapper_padding.'; }
				@media screen and (max-width : 720px) {
					footer { margin-left: -1em; }
				}';
		} else {
			$wrapper = '
				header { margin-top: 25pt; }
				#ssp_headline { margin-top: 25pt; }
				#main { width: 70%; }
				footer { margin-left: 0 }';
		}

		//Output
		$output =
		'<style type="text/css" media="screen">
			body { background-color: '.$ssp_colors[0].'; color: '.$ssp_colors[1].'; font-family: '.ssp_parse_special_fonts( SSP_FONT_1 ).';}
			a, a:hover, a:visited { color: '.$ssp_colors[12].'; }
			#ssp_headline { color: '.$ssp_colors[2].'; font-family: '.ssp_parse_special_fonts( SSP_FONT_2 ).'; }
			#ssp_logo_image { max-height: 50px; }
			#ssp_message { color: '.$ssp_colors[3].'; font-family: '.ssp_parse_special_fonts( SSP_FONT_3 ).'; }
			#ssp_email_notification { color: '.$ssp_colors[4].'; font-family: '.ssp_parse_special_fonts( SSP_FONT_4).'; }
			#ssp_countdown { color: '.$ssp_colors[5].'; font-family: '.ssp_parse_special_fonts( SSP_FONT_5 ).'; }
			#ssp_email_input { font-family: '.ssp_parse_special_fonts( SSP_FONT_6 ).'; background-color: '.$ssp_colors[15].'; color: '.$ssp_colors[16].'; }
			#ssp_email_btn { background-color: '.$ssp_colors[6].'; color: '.$ssp_colors[7].'; font-family: '.ssp_parse_special_fonts( SSP_FONT_7 ).'; }
			#ssp_social_media a, #ssp_social_media a:hover, #ssp_social_media a:visited { color: '.$ssp_colors[8].'; }
			#ssp_thx { color: '.$ssp_colors[9].'; font-family: '.ssp_parse_special_fonts( SSP_FONT_8 ).'; }
			#ssp_share_txt { color: '.$ssp_colors[10].'; font-family: '.ssp_parse_special_fonts( SSP_FONT_9 ).';  }
			#ssp_error { color: '.$ssp_colors[11].'; font-family: '.ssp_parse_special_fonts( SSP_FONT_10 ).';  }
			header { color: '.$ssp_colors[14].'; font-family: '.ssp_parse_special_fonts( SSP_FONT_11 ).'; }
			'.$wrapper.'
			';
		$output .= SSP_T_CUSTOM_CSS;
		$output .= '</style>';
		echo $output;
	}
?>
