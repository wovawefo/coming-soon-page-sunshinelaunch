<?php
	/*	Sunshineplugin
		sunshineplugin-frontend.php
		Funktionen zur Anzeige des Frontends
		Copyright 2015 - SunshineSites
	*/

	if ( !function_exists('add_action') ) {
		die( 'ERROR: Unauthorised access.' );
	}
	
	// From http://bavotasan.com/2011/convert-hex-color-to-rgb-using-php/
	function ssp_hex2rgb($hex, $transparency = 1) {
		$hex = str_replace("#", "", $hex);

		if(strlen($hex) == 3) {
		  $r = hexdec(substr($hex,0,1).substr($hex,0,1));
		  $g = hexdec(substr($hex,1,1).substr($hex,1,1));
		  $b = hexdec(substr($hex,2,1).substr($hex,2,1));
		} else {
		  $r = hexdec(substr($hex,0,2));
		  $g = hexdec(substr($hex,2,2));
		  $b = hexdec(substr($hex,4,2));
		}
		$rgb = array($r, $g, $b);
		return 'rgba('.implode(",", $rgb).','.$transparency.')'; // returns the rgb values separated by commas
		// return $rgb; // returns an array with the rgb values
	}
	
	// From http://bavotasan.com/2011/convert-hex-color-to-rgb-using-php/
	function ssp_rgb2hex($rgb_string, $get_hex = true) {
		$rgb_string = str_replace("rgba(", "", str_replace(")", "", $rgb_string));
		$rgb = explode(',', $rgb_string);
		
		$hex = "#";
		$hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
		$hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
		$hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);

		if ( $get_hex ) {
			return $hex;
		} else { 
			return $rgb[3];
		}
	}
	
	function ssp_user_is( $role, $id = NULL ) {
		$roles = array(
			'admin'		=> 'manage_options',
			'author'	=> 'upload_files',
		);
		if ( is_null($id) ) {
			return current_user_can( $roles[ $role ] );
		} else {
			return user_can( $id, $roles[ $role ] );
		}
	}
	
	function get_current_user_role() {
		global $wp_roles;
		$current_user = wp_get_current_user();
		$roles = $current_user->roles;
		$role = array_shift($roles);
		return isset($wp_roles->role_names[$role]) ? $wp_roles->role_names[$role] : false;
	}
	
	//returns true if overlay has to be shown
	function ssp_check_access() {
		$result = false;
		
		// show manually
		if ( isset($_GET['ssp_preview']) && ( $_GET['ssp_preview'] == 1 ) ) {
			$result = true;
		}
		
		// show depending on users role
		if  ( SSP_ACTIVE ) {
			if ( is_user_logged_in() ) {
				$userrole = get_current_user_role();
				
				$allowedroles = array(
					'Subscriber'	=> SSP_AC_4,
					'Contributor'	=> SSP_AC_3,
					'Author'		=> SSP_AC_2,
					'Editor'		=> SSP_AC_1,
					'Administrator'	=> SSP_AC_0
				);
				
				if ( !$allowedroles[ $userrole ] ) {
					$result = true;
				}
			} else {
				$result = true;
			}
		}
		
		return $result;		
	}

	function ssp_load_overlay( $template )
	{
		if ( ssp_check_access() ) {
			$template = plugin_dir_path( __FILE__ ) . 'sunshineplugin_frontend.php';
		}
		
		return $template;
	}
	
	function ssp_kill_styles()
	{
		if( ssp_check_access() ) {
			global $wp_styles; 
			global $wp_scripts;
			foreach( $wp_styles->registered as $object ) :
				if ( wp_style_is( $object->handle ) ) {
					if( 'dashicons' != $object->handle && 'admin-bar' != $object->handle ) {
						wp_dequeue_style( $object->handle );
					}
				}
			endforeach;
			foreach( $wp_scripts->registered as $object ) :
				if ( wp_script_is( $object->handle ) ) {
					wp_dequeue_script( $object->handle );
				}
			endforeach;
			ssp_load_scripts();
		}
	}
	
	function ssp_add_subscriber( $email ) {
		$result = '';
		return $result;
	}
	
	function ssp_get_custom_fonts( $as = 'css_full' ) {
		$string = '';
		$ssp_settings = get_option( 'ssp_settings' );
		$gfonts = array_map( 'trim', explode( ',', $ssp_settings['custom_fonts'] ) );
		
		// $as:
		// array = returns an array of all googlefonts
		// css_short = returns stylesheet for frontend
		// css_full = returns fontstring for backend ( @import )
		if ( $as == 'array' ) {
			return $gfonts;
		} elseif ( $as == 'css_short' ) {
			
			$ssp_fonts = get_option( 'ssp_fonts' );
			// SSP_FONT_RADIO: 1 = global, 2 = seperatly
			if ( SSP_FONT_RADIO == 1 ) {
				$usedfonts = array( $ssp_fonts[0] );
			} else {
				unset( $ssp_fonts[0] );	// remove global setting
				$usedfonts = array_unique( $ssp_fonts );
			}
			foreach( $usedfonts as $font ) :
				if ( $font != '' ) {	// remove empty entry
					foreach( $gfonts as $gfont ) :
						if ( $font == $gfont  ) { // select only google fonts
							$fontstring .= $delimiter.str_replace( ' ', '+', $font);
							$delimiter = '|';
						}
					endforeach;
				}
			endforeach;
			if ( $fontstring != '' ) {
				$string = '<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family='.$fontstring.'" media="screen">';
			}
			return $string;
		} else {
			foreach( $gfonts as $font ) :
				$fontstring .= $delimiter.str_replace( ' ', '+', $font);
				$delimiter = '|';
			endforeach;
			return $fontstring;
		}
	}
	
	function ssp_get_logo() {
		if ( ( SSP_LOGO_RADIO == 1 ) || ( SSP_LOGO == '' ) ) {
			return SSP_T_LOGO;
		} else {
			return '<img src="'.SSP_LOGO.'" id="ssp_logo_image" />';
		}
	}
	
	function ssp_load_template( $template_id ) {
		// if ( is_user_logged_in() )  {
			$template_id = sanitize_text_field( $template_id );
			
			if ( preg_match("#^[a-zA-Z0-9]+$#", $template_id) ) {
				$filename = $template_id . '.ini';
				$image =  $template_id . '.jpg';
				if ( file_exists( SSP_TEMPLATE_URL . $filename ) ) {
					$ini_array = parse_ini_file( realpath('../wp-content/plugins/'.SSP_FOLDER.'/'.SSP_TEMPLATE_FOLDER.'/'.$filename), TRUE);
					if (!$ini_array) {
						$error[0] = __('Can\'t load template!', 'sunshineplugin');
						$error[1] = 'error';
					} elseif( SSP_FREE && ( $template_id != '01' ) ) {
						$error[0] = __('Upgrade to PRO to load themes.', 'sunshineplugin');
						$error[1] = 'error';
					} elseif ( $ini_array[ 'template' ]['demo'] == 1 ) {
						$error[0] = sprintf( __('Can\'t load demo template "%s".', 'sunshineplugin'), $ini_array[ 'template' ][ 'name' ] );
						$error[1] = 'error';
					} else {
						$ssp_settings = get_option( 'ssp_settings' );
						
						$ssp_colors = get_option( 'ssp_colors' );
						if ( $ini_array['colors'][0] != '' ) {
							for ($i = 0; $i < count( $ssp_colors ); $i++) {
								$ssp_colors[ $i ]		= $ini_array['colors'][$i];
							}
						}
						update_option( 'ssp_colors', $ssp_colors );
						
						$ssp_fonts = get_option( 'ssp_fonts' );
						if ( $ini_array['fonts'][0] != '' ) {
							for ($i = 0; $i < count( $ssp_fonts ); $i++) {
								$ssp_fonts[ $i ]		= $ini_array['fonts'][0];
							}
							$ssp_settings['font_radio'] = 1;
						} else {
							for ($i = 1; $i < count( $ssp_fonts ); $i++) {
								if  ( $ini_array['fonts'][$i] != '' ) {
									$ssp_fonts[ $i ]	= $ini_array['fonts'][$i];
								}
							}
						}
						update_option( 'ssp_fonts', $ssp_fonts );
						
						// search for new gfonts and add them to the list
						$delimiter = '';
						$gfonts = ssp_get_custom_fonts( 'array' );
						$fontlist  = array();
						for ($i = 0; $i < count( $ssp_fonts ); $i++) {
							array_push($fontlist, $ssp_fonts[$i]);
						}
						$fonts = array_unique( array_merge ( $gfonts, $fontlist ) );
						for ($i = 0; $i < count( $fonts ); $i++) {
							$fontstring .= $delimiter.$fonts[$i];
							$delimiter = ', ';
						}
						$ssp_settings[ 'custom_fonts' ] = $fontstring;
						
						$ssp_settings[ 'template' ] = $ini_array[ 'template' ][ 'name' ];
						$ssp_settings[ 'tile_active' ] = ( $ini_array[ 'template' ][ 'tile' ] == 0 ) ? false : true;
						
						if ( file_exists( SSP_TEMPLATE_URL . $image ) ) {
							$ssp_settings[ 'background_url' ] = plugins_url().'/'.SSP_FOLDER.'/'.SSP_TEMPLATE_FOLDER.'/'. $image;
						}
						
						update_option( 'ssp_settings', $ssp_settings );
						
						$error[0] = sprintf( __('Template "%s" loaded successfully.', 'sunshineplugin'), $ini_array[ 'template' ][ 'name' ] );
						$error[1] = 'updated';
					}
				} else {
					$error[0] = __('File '.$file.' does not exist.', 'sunshineplugin');
					$error[1] = 'error';
				}
			}
		// }
		return $error;
	}
	
	function ssp_load_scripts() {
		if( ssp_check_access() ) {
			wp_enqueue_style( 'font-awesome' );
			wp_enqueue_style( 'ssp_style' );
			
			/// sonstige Scripts/styles
			if ( ssp_user_is( 'author' ) ) {
				wp_enqueue_script( 'plupload' );
				wp_enqueue_script( 'plupload-html5' );
			}
			wp_enqueue_script( 'sunshineplugin_js' );
			wp_localize_script( 'sunshineplugin_js', 'MyAjax', array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'security' => wp_create_nonce( 'my-special-string' )
			));
		}
	}
?>