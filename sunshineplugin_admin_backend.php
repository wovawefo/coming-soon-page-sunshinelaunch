<?php
	/*	SunshinePlugin
		sunshineplugin_admin_backend.php
		Funktionen für Administration
		Copyright 2015 - SunshineSites
	*/

	if ( !function_exists('add_action') ) {
		die( 'ERROR: Unauthorised access.' );
	}

	/// returns update/info notifications in backend
	function ssp_admin_notice( $text, $class = 'updated' ) {
		if ( $class == 'info' ) { $class='updated ssp_backend_info'; }
		$output = '<div class="'.$class.'">
						<p>'.$text.'</p>
					</div>';
		return $output;
	}

	// process status_messages
	function ssp_get_backend_notifications() {

		if ( $_GET['ok'] == 1 ) {
			$output =  ssp_admin_notice( __('Options saved.', 'sunshineplugin') );
		}

		return $output;
	}

	function ssp_create_top_buttons( $name, $title, $display_on_start, $is_debug, $is_extended ) {
		if ( $is_debug && !SSP_DEBUG ) {
			$output = '';
		} else {
			$id = 'ssp_backend_'.$name;
			$buttonid = 'ssp_backend_h_'.$name;
			$active = ( $display_on_start ) ? ' pfb_h3_active' : '';
			$extended = ( $is_extended ) ? ' ssp_extended' : '';

			$output = '	<h3 id="'.$buttonid.'" class="ssp_h3'.$active.$extended.'" onclick="ssp_toggle_accordion( \''.$name.'\' );">'.$title.'</h3>';
		}

		return $output;
	}

	function ssp_create_backend_option_set( $name, $title, $main_help, $content, $display_on_start, $is_debug, $is_extended ) {
		if ( $is_debug && !SSP_DEBUG ) {
			$output = '';
		} else {
			$id = 'ssp_backend_'.$name;
			$display = ( $display_on_start ) ? '' : ' style="display: none;"';
 			$more_lnk = ( strpos($content, 'aside' ) !== false ) ? ' <a onclick="jQuery(\'#'.$id.' a\').hide(); jQuery(\'#'.$id.' aside\').slideToggle( \'fast\' );" href="javascript:void(0)">'.__('Mehr Informationen', 'sunshineplugin').'...</a>' : '';

			$output = '<div class="ssp_backend_wrapper" id="'.$id.'"'.$display.'><div>'.$main_help.$more_lnk.'</div>'.$content.'</div>';
		}

		return $output;
	}

	// select for mailchimp-lists
	function ssp_get_mc_select( ) {
		if ( SSP_MC_API != '' ) {
			$MailChimp = new \Drewm\MailChimp( SSP_MC_API );

			$mc_overview = $MailChimp->call('lists/list');
			if ( $mc_overview['total'] > 0 ) {
				$mc_lists = $mc_overview['data'];
				$select .= '<select id="ssp_mc_list" name="ssp_mc_list">';
				foreach ( $mc_lists as $mc_list )
				{
					$selected = ( SSP_MC_LID == $mc_list['id'] ) ? ' selected' : '';

					$select .= '<option value="'.$mc_list['id'].'"'.$selected.'>'.$mc_list['name'].'</option>';
				}
				$select .= '</select>';
			} else {
				$select = __( 'No list available.', 'shunshineplugin');
			}
		} else {
			$select = __( 'Type in your Mailchimp key an save the options.', 'shunshineplugin');
		}

		return $select;
	}

	// select for Fonts
	function getFontSelect( $id, $standard ) {
		$gfonts = ssp_get_custom_fonts( 'array' );
		$sfonts = array( 'serif', 'sans-serif', 'cursive', 'monospace', 'Verdana', 'Helvetica' );
		if ( !empty( $gfonts ) ) {
			$fonts = array_merge ( $gfonts, $sfonts );
		} else {
			$fonts = $sfonts;
		}

		$select .= '<select id="'.$id.'" name="'.$id.'" onchange="ssp_change_font(\''.$id.'\');">';
		foreach ( $fonts as $font )
		{
			$selected = ( $standard == $font ) ? ' selected' : '';

			$select .= '<option value="'.$font.'"'.$selected.'>'.$font.'</option>';
		}
		$select .= '</select>';

		return $select;
	}

	function ssp_get_template( $subject ) {

		$files = scandir( realpath( '../wp-content/plugins/'.SSP_FOLDER.'/'.SSP_TEMPLATE_FOLDER.'/' ) );
		$activation_url = admin_url( 'admin.php?page='.SSP_FOLDER.'/sunshineplugin_admin_backend.php&amp;template=' );
		if ( $subject == 'amount' ) {
			$result = 0;
		} elseif ( $subject == 'menu' ) {
			$result = '';
		} else {
			$result = false;
		}

		foreach( $files as $file ) :
			$fileinfo = explode(".", $file);
			if ( $fileinfo[1] == 'ini' ) {
				if ( $subject == 'amount' ) {
					$result += 1;
				} elseif ( $subject == 'menu' ) {
					$preview = $fileinfo[0]. '.prev.jpg';
					$ini_array = parse_ini_file( realpath('../wp-content/plugins/'.SSP_FOLDER.'/'.SSP_TEMPLATE_FOLDER.'/'.$file), TRUE);
					$name = $ini_array[ 'template' ][ 'name' ];

					if ( file_exists( '../wp-content/plugins/'.SSP_FOLDER.'/'.SSP_TEMPLATE_FOLDER.'/' . $preview ) ) {
						$image = '<img src="'.plugins_url().'/'.SSP_FOLDER.'/'.SSP_TEMPLATE_FOLDER.'/'. $preview .'">';
					} else {
						$image = '<img src="'.plugins_url().'/'.SSP_FOLDER.'/graphics/nopreview.jpg">';
					}

					if ( $name == SSP_TEMPLATE ) {
						$active = ' ssp_template_active';
					} else {
						$active = '';
					}
					$overlay = '';
					$html = 'a';
					$link = ' href="'.$activation_url.$fileinfo[0].'" title="'.sprintf( __('Load template %s.', 'sunshineplugin'), $ini_array[ 'template' ][ 'name' ] ).'"';
					if ( $ini_array[ 'template' ][ 'demo' ] == 1 ) {
						$overlay = '<span class="ssp_overlay">
										<a href="http://www.sunshinesites.com/coming-soon-plugin/" target="_blank" class="button-primary">Get Pro</a>
									</span>';
						$html = 'span';
						$link = '';
					}

					$result .= '<'.$html.$link.' class="ssp_template '.$active.$cf2.'">'.$image.$overlay.'</'.$html.'> ';
				}
			}
		endforeach;

		return $result;
	}

	/// builds menu for backend
	function ssp_AddSubMenu() {
		global $template_error;

		if ( $template_error != false ) {
			echo ssp_admin_notice( $template_error[0], $template_error[1] );
		}

		wp_enqueue_script( 'jscolor' );
		wp_enqueue_style( 'ssp_style_backend' );
		$ssp_colors = get_option( 'ssp_colors' );

		$filter_disabled = '';

		// deactivate filter_select
		if ( SSP_HIDE_FILTER == 1 ) {
			$filter_disabled = ' disabled="disabled"';
		}

		$logo_radio_id = SSP_LOGO_RADIO;
		$logo_radio_arr[1] = $logo_radio_arr[2] = '';
		$logo_radio_arr[$logo_radio_id] = ' checked="checked"';

		$font_radio_id = SSP_FONT_RADIO;
		$font_radio_arr[1] = $font_radio_arr[2] = '';
		$font_radio_arr[$font_radio_id] = ' checked="checked"';

		$use_background = ( SSP_BACKGROUND != '' );

		$cf1 = ' ssp_hf';
		$cf2 = '';
		if ( SSP_FREE ) {
			$cf2 = $cf1;
			$cf1 = '';
		}

		$setting_group_array = array(

					array( 'templates',
							__('Templates', 'sunshineplugin'),
							'',
							'
							<h4>'.__('Templates', 'sunshineplugin').'</h4>
							<span class="ssp_notice">
							'.sprintf(__('Choose one of %d templates.', 'sunshineplugin'), ssp_get_template( 'amount' ) ).'
							</span><br>
							'.ssp_get_template( 'menu' ) .'
							',
							true, false, false),

					array( 'access',
							__('Access', 'sunshineplugin'),
							'',
							'
							<h4>Access control</h4>

							<table class="ssp_table_primary">
								<tr>
									<td>'.__('Allow access for', 'sunshineplugin').':</td>
									<td>
										<input type="checkbox" name="ssp_ac_all" id="ssp_ac_all" style="margin: 0" ' . checked(1, SSP_AC_ALL, false) .' onchange="ssp_check_ac_all()" /> <label for="ssp_ac_all">'.__('Logged in', 'sunshineplugin').'</label><br>
										<input type="checkbox" name="ssp_ac_4" id="ssp_ac_4" style="margin: 0" ' . checked(1, SSP_AC_4, false) .' onchange="ssp_uncheck_ac_all(\'ssp_ac_4\')" /> <label for="ssp_ac_4">'.translate_user_role('Subscriber').'</label><br>
										<input type="checkbox" name="ssp_ac_3" id="ssp_ac_3" style="margin: 0" ' . checked(1, SSP_AC_3, false) .' onchange="ssp_uncheck_ac_all(\'ssp_ac_3\')" /> <label for="ssp_ac_3">'.translate_user_role('Contributor').'</label><br>
										<input type="checkbox" name="ssp_ac_2" id="ssp_ac_2" style="margin: 0" ' . checked(1, SSP_AC_2, false) .' onchange="ssp_uncheck_ac_all(\'ssp_ac_2\')" /> <label for="ssp_ac_2">'.translate_user_role('Author').'</label><br>
										<input type="checkbox" name="ssp_ac_1" id="ssp_ac_1" style="margin: 0" ' . checked(1, SSP_AC_1, false) .' onchange="ssp_uncheck_ac_all(\'ssp_ac_1\')" /> <label for="ssp_ac_1">'.translate_user_role( 'Editor' ).'</label><br>
										<input type="checkbox" name="ssp_ac_0" id="ssp_ac_0" style="margin: 0" ' . checked(1, SSP_AC_0, false) .' onchange="ssp_uncheck_ac_all(\'ssp_ac_0\')" /> <label for="ssp_ac_0">'.translate_user_role( 'Administrator' ).'</label>
									</td>
								</tr>
							</table>
							',
							false, false, false),

					array( 'settings',
							__('Settings', 'sunshineplugin'),
							'',
							'
							<h4>'.__('Email Settings', 'sunshineplugin').'</h4>
							
							<span class="ssp_notice'.$cf1.'">
								<a href="http://www.sunshinesites.com/coming-soon-plugin/" target="_blank">Connect to Mailchimp in PRO.</a>
							</span>

							<table class="ssp_table_primary">
								<tr>
									<td>'.__('Activate email collection', 'sunshineplugin').'</td>
									<td><input type="checkbox" name="ssp_email_active" id="ssp_email_active" style="margin: 0" ' . checked(1, SSP_EMAIL_ACTIVE, false) .'/> <label for="ssp_email_active">'.__('Activate', 'sunshineplugin').'</label></td>
								</tr>
							</table>
							<table class="ssp_table_primary ssp_hide_email">
								<tr class="'.$cf1.'">
									<td>'.__('Your email adress', 'sunshineplugin').':</td>
									<td><input type="text" name="ssp_owner_mail" value="'.SSP_OWNER_MAIL.'" /></td>
								</tr>
								<tr>
									<td>'.__('Input placeholder text', 'sunshineplugin').': </td>
									<td><input type="text" name="ssp_t_subs_input" value="'.SSP_T_SUBS_INPUT.'" /></td>
								</tr>
								<tr>
									<td>'.__('Buton text', 'sunshineplugin').': </td>
									<td><input type="text" name="ssp_t_subs_btn" value="'.SSP_T_SUBS_BTN.'" /></td>
								</tr>
								<tr>
									<td>'.__('Error text', 'sunshineplugin').': </td>
									<td><input type="text" name="ssp_t_error" value="'.SSP_T_ERROR.'" /></td>
								</tr>
							</table><br />

							<h4>'.__('Countdown', 'sunshineplugin').'</h4>

							<span class="ssp_notice'.$cf1.'">
								<a href="http://www.sunshinesites.com/coming-soon-plugin/" target="_blank">Only available in PRO.</a>
							</span>

							<h4>'.__('Social Networks', 'sunshineplugin').'</h4>
							<table class="ssp_table_primary">
								<tr>
									<td>'.__('Facebook', 'sunshineplugin').': </td>
									<td><input type="text" name="ssp_sm_fb" value="'.SSP_SM_FB.'" /></td>
								</tr>
								<tr>
									<td>'.__('Twitter', 'sunshineplugin').': </td>
									<td><input type="text" name="ssp_sm_tw" value="'.SSP_SM_TW.'" /></td>
								</tr>
								<tr>
									<td>'.__('Google Plus', 'sunshineplugin').': </td>
									<td><input type="text" name="ssp_sm_gp" value="'.SSP_SM_GP.'" /></td>
								</tr>
								<tr>
									<td>'.__('Pinterest', 'sunshineplugin').': </td>
									<td><input type="text" name="ssp_sm_pi" value="'.SSP_SM_PI.'" /></td>
								</tr>
								<tr>
									<td>'.__('tumblr', 'sunshineplugin').': </td>
									<td><input type="text" name="ssp_sm_t" value="'.SSP_SM_T.'" /></td>
								</tr>
								<tr>
									<td>'.__('linkedin', 'sunshineplugin').': </td>
									<td><input type="text" name="ssp_sm_in" value="'.SSP_SM_IN.'" /></td>
								</tr>
								<tr>
									<td>'.__('Vimeo', 'sunshineplugin').': </td>
									<td><input type="text" name="ssp_sm_v" value="'.SSP_SM_V.'" /></td>
								</tr>
								<tr>
									<td>'.__('YouTube', 'sunshineplugin').': </td>
									<td><input type="text" name="ssp_sm_yt" value="'.SSP_SM_YT.'" /></td>
								</tr>
								<tr>
									<td>'.__('Instagram', 'sunshineplugin').': </td>
									<td><input type="text" name="ssp_sm_ig" value="'.SSP_SM_IG.'" /></td>
								</tr>
							</table>

							<h4>'.__('Scripts', 'sunshineplugin').'</h4>
							<span class="ssp_notice'.$cf1.'">
								<a href="http://www.sunshinesites.com/coming-soon-plugin/" target="_blank">Only available in PRO.</a>
							</span>
							',
							false, false, false),

					array( 'content',
							__('Content', 'sunshineplugin'),
							__('', 'sunshineplugin'),
							'
							<h4>'.__('Logo', 'sunshineplugin').'</h4>

							<table class="ssp_table_secondary">
								<tr>
									<td>'.__('Logo type', 'sunshineplugin').':</td>
									<td>
										<input type="radio" id="ssp_logo_radio1" name="ssp_logo_radio" onclick="ssp_logo_radio_toggle(this)" value="1" '.$logo_radio_arr[1].'><label for="ssp_logo_radio1">'.__('Text', 'sunshineplugin').'</label>
									</td>
									<td>
										<input type="radio" id="ssp_logo_radio2" name="ssp_logo_radio" onclick="ssp_logo_radio_toggle(this)" value="2" '.$logo_radio_arr[2].'><label for="ssp_logo_radio2">'.__('Image', 'sunshineplugin').'</label>
									</td>
								</tr>
							</table><br>
							<table class="ssp_table_secondary" id="ssp_logo_img">
								<tr>
									<td>'.__('Logo image', 'sunshineplugin').':</td>
									<td>
										<input type="file" name="ssp_logo_upload_img" id="ssp_logo_upload_img" onchange="ssp_save_settings();javascript:this.form.submit();"/><label for="ssp_logo_upload_img" title="'.__('select and upload image', 'sunshineplugin').'" class="button-primary">'.__('upload', 'sunshineplugin').'</label>
									</td>
								</tr>
								<tr class="ssp_desc">
									<td></td>
									<td>'.__('Allowed filetypes: PNG, JPG', 'sunshineplugin').'<br>'.__('The logo area allows pictures with 50px height maximum.', 'sunshineplugin').'</td>
								</tr>
							</table>

							<table  class="ssp_table_primary" id="ssp_logo_text">
								<tr>
									<td>'.__('Logo-Text', 'sunshineplugin').':</td>
									<td><textarea id="ssptlogo" name="ssptlogo"  />'.esc_html( SSP_T_LOGO ).'</textarea></td>
								</tr>
							</table>

							<h4>'.__('Header', 'sunshineplugin').'</h4>
							<table class="ssp_table_primary">
								<tr>
									<td>'.__('Headline', 'sunshineplugin').': </td>
									<td><textarea id="ssptheadline" name="ssptheadline" />'.esc_html( SSP_T_HEADLINE ).'</textarea></td>
								</tr>
							</table><br />
							<h4>'.__('Content', 'sunshineplugin').'</h4>

							<table class="ssp_table_primary">
								<tr>
									<td>'.__('Message', 'sunshineplugin').': </td>
									<td><textarea id="ssptmessage" name="ssptmessage" />'.esc_html( SSP_T_MESSAGE ).'</textarea></td>
								</tr>
								<tr style="display: none;">
									<td>'.__('Submessage', 'sunshineplugin').': </td>
									<td><textarea id="ssptemailnotification" name="ssptemailnotification" />'.esc_html( SSP_T_EMAIL_NOTIFICATION ).'</textarea></td>
								</tr>
								<tr>
									<td>'.__('Footer', 'sunshineplugin').': </td>
									<td><textarea id="ssptfooter" name="ssptfooter" />'.esc_html( SSP_T_FOOTER ).'</textarea></td>
								</tr>
							</table><br />
							<span class="ssp_hide_email">
								<h4>'.__('Thank You Page', 'sunshineplugin').'</h4>

								<table class="ssp_table_primary">
									<tr>
										<td>'.__('Headline', 'sunshineplugin').': </td>
										<td><input type="text" name="sspttyheadline" id="sspttyheadline" value="'.esc_html( SSP_T_TY_HEADLINE ).'" /></td>
									</tr>
									<tr>
										<td>'.__('Message', 'sunshineplugin').': </td>
										<td><textarea id="sspttymessage" name="sspttymessage"  />'.esc_html( SSP_T_TY_MESSAGE ).'</textarea></td>
									</tr>
									<tr >
										<td>'.__('Social sharing', 'sunshineplugin').': </td>
										<td><span class="ssp_notice'.$cf1.'"><a href="http://www.sunshinesites.com/coming-soon-plugin/" target="_blank">Only available in PRO.</a></span></td>
									</tr>
								</table><br />
							</span>
							<h4>'.__('Footer Page', 'sunshineplugin').' 1</h4>
							<span class="ssp_notice">
							'.__('Use it for Imprint, Privacy or Terms & Conditions.', 'sunshineplugin').'<br>'.home_url('/?page=impr').'<br>
							</span>
							<table class="ssp_table_primary">
								<tr>
									<td>'.__('Headline', 'sunshineplugin').': </td>
									<td><input type="text" name="ssptimprheadline" id="ssptimprheadline" value="'.esc_html( SSP_T_IMPR_HEADLINE ).'" /></td>
								</tr>
								<tr>
									<td>'.__('Message', 'sunshineplugin').': </td>
									<td><textarea id="ssptimprmessage" name="ssptimprmessage" />'.esc_html( SSP_T_IMPR_MESSAGE ).'</textarea></td>
								</tr>
							</table><br />

							<h4>'.__('Footer Page', 'sunshineplugin').' 2</h4>
							<span class="ssp_notice">
							'.__('Use it for Imprint, Privacy or Terms & Conditions.', 'sunshineplugin').'<br>'.home_url('/?page=priv').'<br>
							</span>
							<table class="ssp_table_primary">
								<tr>
									<td>'.__('Headline', 'sunshineplugin').': </td>
									<td><input type="text" name="ssptprivheadline" id="ssptprivheadline" value="'.esc_html( SSP_T_PRIV_HEADLINE ).'" /></td>
								</tr>
								<tr>
									<td>'.__('Message', 'sunshineplugin').': </td>
									<td><textarea id="ssptprivmessage" name="ssptprivmessage" style="width: 525px; height: 100px;" />'.esc_html( SSP_T_PRIV_MESSAGE ).'</textarea></td>
								</tr>
							</table>',
							 false, false, false),

					array(	'design',
							__('Design', 'sunshineplugin'),
							__('', 'sunshineplugin'),
							'
							<h4>'.__('Background', 'sunshineplugin').'</h4>

							<table class="ssp_table_secondary" id="ssp_bg_image_table">
								<tr>
									<td>'.__('Background image', 'sunshineplugin').':</td>
									<td><input type="checkbox" name="ssp_bg_image" id="ssp_bg_image" style="margin: 0" ' . checked(1, $use_background, false) .'/> <label for="ssp_bg_image">'.__('Activate', 'sunshineplugin').'</label></td>
									<td colspan="2"><input type="file" name="ssp_background_upload_img" id="ssp_background_upload_img" onchange="ssp_save_settings();javascript:this.form.submit();"/><label for="ssp_background_upload_img" title="'.__('select and upload image', 'sunshineplugin').'" class="button-primary">'.__('upload', 'sunshineplugin').'</label></td>
								</tr>
								<tr class="ssp_desc">
									<td></td>
									<td></td>
									<td colspan="2">'.__('Allowed filetype: JPG', 'sunshineplugin').'</td>
								</tr>
								<tr>
									<td>'.__('Overlay color', 'sunshineplugin').':</td>
									<td><input type="text" name="ssp_input_color_0" class="color" value="'.ssp_rgb2hex($ssp_colors[0]).'" /></td>
									<td><input type="number" name="ssp_input_transparency_0" min="0" max="100" value="'.( ( 1 - ssp_rgb2hex($ssp_colors[0], false) ) * 100 ).'" />%</td>
								</tr>
							</table>

							<h4>'.__('Tile', 'sunshineplugin').'</h4>

							<span>

							</span><br>

							<table class="ssp_table_secondary">
								<tr>
									<td>'.__('Activate tile', 'sunshineplugin').':</td>
									<td><input type="checkbox" name="ssp_tile_active" id="ssp_tile_active" style="margin: 0" ' . checked(1, SSP_TILE_ACTIVE, false) .'/> <label for="ssp_tile_active">'.__('Activate tile', 'sunshineplugin').'</label></td>
								</tr>
							</table>
							<table class="ssp_table_secondary" id="ssp_tile_table">
								<tr>
									<td>'.__('tile color', 'sunshineplugin').':</td>
									<td><input type="text" name="ssp_input_color_13" class="color" value="'.ssp_rgb2hex($ssp_colors[13]).'" /></td>
									<td><input type="number" name="ssp_input_transparency_13" min="0" max="100" value="'.( ( 1 - ssp_rgb2hex($ssp_colors[13], false) ) * 100 ).'" />%</td>
								</tr>
							</table>

							<h4>'.__('Colors', 'sunshineplugin').'</h4>

							<table class="ssp_table_secondary" id="ssp_color_table">
								<tr>
									<td>'.__('Normal text', 'sunshineplugin').': </td>
									<td><input type="text" name="ssp_input_color_1" class="color" value="'.ssp_rgb2hex($ssp_colors[1]).'" /></td>
									<td><input type="number" name="ssp_input_transparency_1" min="0" max="100" value="'.( ( 1 - ssp_rgb2hex($ssp_colors[1], false) ) * 100 ).'" />%</td>
								</tr>
								<tr>
									<td>'.__('Links', 'sunshineplugin').': </td>
									<td><input type="text" name="ssp_input_color_12" class="color" value="'.ssp_rgb2hex($ssp_colors[12]).'" /></td>
									<td><input type="number" name="ssp_input_transparency_12" min="0" max="100" value="'.( ( 1 - ssp_rgb2hex($ssp_colors[12], false) ) * 100 ).'" />%</td>
								</tr>
								<tr>
									<td>'.__('Logo', 'sunshineplugin').': </td>
									<td><input type="text" name="ssp_input_color_14" class="color" value="'.ssp_rgb2hex($ssp_colors[14]).'" /></td>
									<td><input type="number" name="ssp_input_transparency_14" min="0" max="100" value="'.( ( 1 - ssp_rgb2hex($ssp_colors[14], false) ) * 100 ).'" />%</td>
								</tr>
								<tr>
									<td>'.__('Headline', 'sunshineplugin').': </td>
									<td><input type="text" name="ssp_input_color_2" class="color" value="'.ssp_rgb2hex($ssp_colors[2]).'" /></td>
									<td><input type="number" name="ssp_input_transparency_2" min="0" max="100" value="'.( ( 1 - ssp_rgb2hex($ssp_colors[2], false) ) * 100 ).'" />%</td>
								</tr>
								<tr>
									<td>'.__('Message', 'sunshineplugin').': </td>
									<td><input type="text" name="ssp_input_color_3" class="color" value="'.ssp_rgb2hex($ssp_colors[3]).'" /></td>
									<td><input type="number" name="ssp_input_transparency_3" min="0" max="100" value="'.( ( 1 - ssp_rgb2hex($ssp_colors[3], false) ) * 100 ).'" />%</td>
								</tr>
								<tr style="display:none">
									<td>'.__('Submessage', 'sunshineplugin').': </td>
									<td><input type="text" name="ssp_input_color_4" class="color" value="'.ssp_rgb2hex($ssp_colors[4]).'" /></td>
									<td><input type="number" name="ssp_input_transparency_4" min="0" max="100" value="'.( ( 1 - ssp_rgb2hex($ssp_colors[4], false) ) * 100 ).'" />%</td>
								</tr>
								<tr>
									<td>'.__('Countdown', 'sunshineplugin').': </td>
									<td><input type="text" name="ssp_input_color_5" class="color" value="'.ssp_rgb2hex($ssp_colors[5]).'" /></td>
									<td><input type="number" name="ssp_input_transparency_5" min="0" max="100" value="'.( ( 1 - ssp_rgb2hex($ssp_colors[5], false) ) * 100 ).'" />%</td>
								</tr>
								<tr>
									<td>'.__('Email input background', 'sunshineplugin').': </td>
									<td><input type="text" name="ssp_input_color_15" class="color" value="'.ssp_rgb2hex($ssp_colors[15]).'" /></td>
									<td><input type="number" name="ssp_input_transparency_15" min="0" max="100" value="'.( ( 1 - ssp_rgb2hex($ssp_colors[15], false) ) * 100 ).'" />%</td>
								</tr>
								<tr>
									<td>'.__('Email input text', 'sunshineplugin').': </td>
									<td><input type="text" name="ssp_input_color_16" class="color" value="'.ssp_rgb2hex($ssp_colors[16]).'" /></td>
									<td><input type="number" name="ssp_input_transparency_16" min="0" max="100" value="'.( ( 1 - ssp_rgb2hex($ssp_colors[16], false) ) * 100 ).'" />%</td>
								</tr>
								<tr>
									<td>'.__('Email button background', 'sunshineplugin').': </td>
									<td><input type="text" name="ssp_input_color_6" class="color" value="'.ssp_rgb2hex($ssp_colors[6]).'" /></td>
									<td><input type="number" name="ssp_input_transparency_6" min="0" max="100" value="'.( ( 1 - ssp_rgb2hex($ssp_colors[6], false) ) * 100 ).'" />%</td>
								</tr>
								<tr>
									<td>'.__('Email button text', 'sunshineplugin').': </td>
									<td><input type="text" name="ssp_input_color_7" class="color" value="'.ssp_rgb2hex($ssp_colors[7]).'" /></td>
									<td><input type="number" name="ssp_input_transparency_7" min="0" max="100" value="'.( ( 1 - ssp_rgb2hex($ssp_colors[7], false) ) * 100 ).'" />%</td>
								</tr>
								<tr>
									<td>'.__('Social media symbols', 'sunshineplugin').': </td>
									<td><input type="text" name="ssp_input_color_8" class="color" value="'.ssp_rgb2hex($ssp_colors[8]).'" /></td>
									<td><input type="number" name="ssp_input_transparency_8" min="0" max="100" value="'.( ( 1 - ssp_rgb2hex($ssp_colors[8], false) ) * 100 ).'" />%</td>
								</tr>
								<tr>
									<td>'.__('Thank You Page headline', 'sunshineplugin').': </td>
									<td><input type="text" name="ssp_input_color_9" class="color" value="'.ssp_rgb2hex($ssp_colors[9]).'" /></td>
									<td><input type="number" name="ssp_input_transparency_9" min="0" max="100" value="'.( ( 1 - ssp_rgb2hex($ssp_colors[9], false) ) * 100 ).'" />%</td>
								</tr>
								<tr>
									<td>'.__('Thank You Page message', 'sunshineplugin').': </td>
									<td><input type="text" name="ssp_input_color_10" class="color" value="'.ssp_rgb2hex($ssp_colors[10]).'" /></td>
									<td><input type="number" name="ssp_input_transparency_10" min="0" max="100" value="'.( ( 1 - ssp_rgb2hex($ssp_colors[10], false) ) * 100 ).'" />%</td>
								</tr>
								<tr>
									<td>'.__('Error message', 'sunshineplugin').': </td>
									<td><input type="text" name="ssp_input_color_11" class="color" value="'.ssp_rgb2hex($ssp_colors[11]).'" /></td>
									<td><input type="number" name="ssp_input_transparency_11" min="0" max="100" value="'.( ( 1 - ssp_rgb2hex($ssp_colors[11], false) ) * 100 ).'" />%</td>
								</tr>
							</table>

							<h4>'.__('Fonts', 'sunshineplugin').'</h4>


							<table class="ssp_table_secondary" id="ssp_logo_img">
								<tr>
									<td>'.__('Set fonts', 'sunshineplugin').':</td>
									<td>
										<input type="radio" id="ssp_font_radio1" name="ssp_font_radio" onclick="ssp_font_radio_toggle(this)" value="1" '.$font_radio_arr[1].'><label for="ssp_font_radio1">'.__('Global', 'sunshineplugin').'</label>
									</td>
									<td>
										<input type="radio" id="ssp_font_radio2" name="ssp_font_radio" onclick="ssp_font_radio_toggle(this)" value="2" '.$font_radio_arr[2].'><label for="ssp_font_radio2">'.__('Seperate', 'sunshineplugin').'</label>
									</td>
								</tr>
							</table><br>

							<table class="ssp_table_secondary" id="ssp_global_fonts">
								<tr>
									<td>'.__('Global', 'sunshineplugin').':</td>
									<td>'.getFontSelect( 'ssp_font_0', SSP_FONT_0 ).'</td>
									<td><span id="ssp_font_0_example" style="font-family: '.SSP_FONT_0.'">ABCDEFGHIJKLMNOPQRSTUVWXYZ abcdefghijklmnopqrstuvwxyz 0123456789</span></td>
								</tr>
							</table>
							<table class="ssp_table_secondary" id="ssp_all_fonts">
								<tr>
									<td>'.__('Normal text', 'sunshineplugin').':</td>
									<td>'.getFontSelect( 'ssp_font_1', SSP_FONT_1 ).'</td>
									<td><span id="ssp_font_1_example" style="font-family: '.SSP_FONT_1.'">ABCDEFGHIJKLMNOPQRSTUVWXYZ abcdefghijklmnopqrstuvwxyz 0123456789</span></td>
								</tr>
								<tr>
									<td>'.__('Headline', 'sunshineplugin').':</td>
									<td>'.getFontSelect( 'ssp_font_2', SSP_FONT_2 ).'</td>
									<td><span id="ssp_font_2_example" style="font-family: '.SSP_FONT_2.'">ABCDEFGHIJKLMNOPQRSTUVWXYZ abcdefghijklmnopqrstuvwxyz 0123456789</span></td>
								</tr>
								<tr>
									<td>'.__('Message', 'sunshineplugin').':</td>
									<td>'.getFontSelect( 'ssp_font_3', SSP_FONT_3 ).'</td>
									<td><span id="ssp_font_3_example" style="font-family: '.SSP_FONT_3.'">ABCDEFGHIJKLMNOPQRSTUVWXYZ abcdefghijklmnopqrstuvwxyz 0123456789</span></td>
								</tr>
								<tr style="display:none">
									<td>'.__('Submessage', 'sunshineplugin').':</td>
									<td>'.getFontSelect( 'ssp_font_4', SSP_FONT_4 ).'</td>
									<td><span id="ssp_font_4_example" style="font-family: '.SSP_FONT_4.'">ABCDEFGHIJKLMNOPQRSTUVWXYZ abcdefghijklmnopqrstuvwxyz 0123456789</span></td>
								</tr>
								<tr>
									<td>'.__('Countdown', 'sunshineplugin').':</td>
									<td>'.getFontSelect( 'ssp_font_5', SSP_FONT_5 ).'</td>
									<td><span id="ssp_font_5_example" style="font-family: '.SSP_FONT_5.'">ABCDEFGHIJKLMNOPQRSTUVWXYZ abcdefghijklmnopqrstuvwxyz 0123456789</span></td>
								</tr>
								<tr>
									<td>'.__('Email placeholder', 'sunshineplugin').':</td>
									<td>'.getFontSelect( 'ssp_font_6', SSP_FONT_6 ).'</td>
									<td><span id="ssp_font_6_example" style="font-family: '.SSP_FONT_6.'">ABCDEFGHIJKLMNOPQRSTUVWXYZ abcdefghijklmnopqrstuvwxyz 0123456789</span></td>
								</tr>
								<tr>
									<td>'.__('Email button', 'sunshineplugin').':</td>
									<td>'.getFontSelect( 'ssp_font_7', SSP_FONT_7 ).'</td>
									<td><span id="ssp_font_7_example" style="font-family: '.SSP_FONT_7.'">ABCDEFGHIJKLMNOPQRSTUVWXYZ abcdefghijklmnopqrstuvwxyz 0123456789</span></td>
								</tr>
								<tr>
									<td>'.__('Thank You Page headline', 'sunshineplugin').':</td>
									<td>'.getFontSelect( 'ssp_font_8', SSP_FONT_8 ).'</td>
									<td><span id="ssp_font_8_example" style="font-family: '.SSP_FONT_8.'">ABCDEFGHIJKLMNOPQRSTUVWXYZ abcdefghijklmnopqrstuvwxyz 0123456789</span></td>
								</tr>
								<tr>
									<td>'.__('Thank You Page message', 'sunshineplugin').':</td>
									<td>'.getFontSelect( 'ssp_font_9', SSP_FONT_9 ).'</td>
									<td><span id="ssp_font_9_example" style="font-family: '.SSP_FONT_9.'">ABCDEFGHIJKLMNOPQRSTUVWXYZ abcdefghijklmnopqrstuvwxyz 0123456789</span></td>
								</tr>
								<tr>
									<td>'.__('Error message', 'sunshineplugin').':</td>
									<td>'.getFontSelect( 'ssp_font_10', SSP_FONT_10 ).'</td>
									<td><span id="ssp_font_10_example" style="font-family: '.SSP_FONT_10.'">ABCDEFGHIJKLMNOPQRSTUVWXYZ abcdefghijklmnopqrstuvwxyz 0123456789</span></td>
								</tr>
								<tr>
									<td>'.__('Logo', 'sunshineplugin').':</td>
									<td>'.getFontSelect( 'ssp_font_11', SSP_FONT_11 ).'</td>
									<td><span id="ssp_font_11_example" style="font-family: '.SSP_FONT_11.'">ABCDEFGHIJKLMNOPQRSTUVWXYZ abcdefghijklmnopqrstuvwxyz 0123456789</span></td>
								</tr>
							</table>

							<div style="display: none;">
							<h4>'.__('Custom Fonts', 'sunshineplugin').'</h4>

								<table class="ssp_table_primary">
									<tr>
										<td>'.__('Google Fonts', 'sunshineplugin').':</td>
										<td><textarea id="ssp_custom_fonts" name="ssp_custom_fonts" />'.SSP_CUSTOM_FONTS.'</textarea></td>
									</tr>
									<tr class="ssp_desc">
										<td></td>
										<td>'.__('You can add custom fonts from www.google.com/fonts.', 'sunshineplugin').'<br>'.__('Seperate the font names with ",".', 'sunshineplugin').'</td>
									</tr>
								</table>
							</div>
							',
							false, false, false),

					array( 'custom',
							__('Custom code', 'sunshineplugin'),
							__('', 'sunshineplugin'),
							'
							<h4>'.__('Custom code', 'sunshineplugin').'</h4>

							<span class="ssp_notice'.$cf1.'">
								<a href="http://www.sunshinesites.com/coming-soon-plugin/" target="_blank">Only available in PRO.</a>
							</span><br />
							<h4>'.__('Thank You Page', 'sunshineplugin').'</h4>

							<span class="ssp_notice'.$cf1.'">
								<a href="http://www.sunshinesites.com/coming-soon-plugin/" target="_blank">Only available in PRO.</a>
							</span><br />
							<h4>'.__('Custom CSS', 'sunshineplugin').'</h4>
							<span class="ssp_notice'.$cf1.'">
								<a href="http://www.sunshinesites.com/coming-soon-plugin/" target="_blank">Only available in PRO.</a>
							</span>',
							false, false, true ),

					array( 'preview',
							__('Preview', 'sunshineplugin'),
							__('', 'sunshineplugin'),
							'
							<h4>Main Page</h4>
							<div class="ssp_preview_wrapper">
								<iframe src="'.home_url('/?ssp_preview=1').'" class="ssp_preview" name="preview">
									'.__('Your Browser does not support iFrames. Please use the following Link:', 'sunshineplugin').'<br> <a href="'.home_url('/?ssp_preview=1').'">'.__('Preview', 'sunshineplugin').'</a></p>
								</iframe>
							</div>
							<span class="ssp_hide_email">
								<h4>Thank You Page</h4>
								<div class="ssp_preview_wrapper">
									<iframe src="'.home_url('/?ssp_preview=1&ssp_ty=1').'" class="ssp_preview" name="preview">
										'.__('Your Browser does not support iFrames. Please use the following Link:', 'sunshineplugin').'<br> <a href="'.home_url('/?ssp_preview=1&ssp_ty=1').'">'.__('Preview', 'sunshineplugin').'</a></p>
									</iframe>
								</div>
							</span>',
							false, false, false)
			); // end of array

		// css output for backend
		$css = '<style type="text/css" media="screen">
					@import url(http://fonts.googleapis.com/css?family='.ssp_get_custom_fonts().');
				</style>';


		// js output
		$js =  "<script type=\"text/javascript\">

					function ssp_toggle_accordion( name ) {
						activeClass = 'pfb_h3_active';
						if ( jQuery( '#ssp_backend_' + name ).is(':hidden') ) {
							jQuery( '.' + activeClass ).removeClass( activeClass );
							jQuery('.ssp_backend_wrapper').hide( );
							jQuery( '#ssp_backend_' + name ).toggle( );
							jQuery( '#ssp_backend_h_' + name ).addClass( activeClass );
						}
						jQuery( '#ssp_active_option' ).val( name );
					}

					function ssp_hide_show_on_checkbox( id_checkbox, id_a, id_b ) {
						if ( jQuery( '#' + id_checkbox ).prop( 'checked' ) ) {
							if ( id_b != '' ) { jQuery( '#' + id_b ).show(); }
							jQuery( '#' + id_a ).hide();
						} else {
							jQuery( '#' + id_a ).show();
							if ( id_b != '' ) { jQuery( '#' + id_b ).hide(); }
						}
					}

					function ssp_disable_on_checkbox(id_checkbox, id_input) {
						if ( jQuery( '#' + id_checkbox ).prop( 'checked' ) ) {
							jQuery( '#' + id_input ).prop('disabled', false);
						} else {
							jQuery( '#' + id_input ).prop('disabled', true);
						}
					}

					function ssp_check_ac_all() {
						if ( jQuery( '#ssp_ac_all' ).prop( 'checked' ) ) {
							jQuery( '#ssp_ac_0' ).prop('checked', true);
							jQuery( '#ssp_ac_1' ).prop('checked', true);
							jQuery( '#ssp_ac_2' ).prop('checked', true);
							jQuery( '#ssp_ac_3' ).prop('checked', true);
							jQuery( '#ssp_ac_4' ).prop('checked', true);
						}
					}

					function ssp_uncheck_ac_all( id ) {
						if ( jQuery( '#' + id ).prop( 'checked' ) == false ) {
							jQuery( '#ssp_ac_all' ).prop('checked', false);
						}
					}

					function ssp_change_font( id ) {
						fontName = jQuery( '#' + id ).val();
						var isSpecialFont = (fontName.indexOf(' ') >= 0);
						if ( isSpecialFont ) {
							fontName = '\"' + fontName + '\"';
						}
						jQuery( '#' + id + '_example' ).css( 'font-family', fontName );
					}

					function ssp_font_radio_toggle( opt ) {
						val = opt.value;
						ssp_toggle_font_table( val )
					}

					function ssp_toggle_font_table( val ) {
						if ( val == 1 ) {
							jQuery('#ssp_all_fonts').hide();
							jQuery('#ssp_global_fonts').show();
						} else if ( val == 2 ) {
							jQuery('#ssp_global_fonts').hide();
							jQuery('#ssp_all_fonts').show();
						}
					}
					ssp_toggle_font_table( ".$font_radio_id." );

					function ssp_logo_radio_toggle( opt ) {
						val = opt.value;
						ssp_toggle_logo_table( val )
					}

					function ssp_toggle_logo_table( val ) {
						if ( val == 1 ) {
							jQuery('#ssp_logo_img').hide();
							jQuery('#ssp_logo_text').show();
						} else if ( val == 2 ) {
							jQuery('#ssp_logo_text').hide();
							jQuery('#ssp_logo_img').show();
						}
					}
					ssp_toggle_logo_table( ".$logo_radio_id." );

					jQuery('#ssp_form').on('submit', function () {
						ssp_save_settings()
					});
					function ssp_save_settings() {
						jQuery('#ssp_form').hide();
						jQuery('#ssp_saving').fadeIn();
					}

					function ssp_tile_click() {
						if ( jQuery( '#ssp_tile_active' ).prop( 'checked' ) ) {
							jQuery( '#ssp_tile_table' ).show();
						} else {
							jQuery( '#ssp_tile_table' ).hide();
						}
					}
					jQuery( '#ssp_tile_active' ).click(function() {
						 ssp_tile_click();
					});
					ssp_tile_click();

					function ssp_activate_ssp_click() {
						if ( jQuery( '#ssp_active' ).prop( 'checked' ) ) {
							jQuery( '#ssp_active_cb' ).show();
							jQuery( '#ssp_inactive_cb' ).hide();
						} else {
							jQuery( '#ssp_inactive_cb' ).show();
							jQuery( '#ssp_active_cb' ).hide();
						}
					}
					jQuery( '#ssp_active' ).click(function() {
						 ssp_activate_ssp_click();
					});
					ssp_activate_ssp_click();
					
					function ssp_activate_email_click() {
						if ( jQuery( '#ssp_email_active' ).prop( 'checked' ) ) {
							jQuery( '.ssp_hide_email' ).show();
						} else {
							jQuery( '.ssp_hide_email' ).hide();
						}
					}
					jQuery( '#ssp_email_active' ).click(function() {
						 ssp_activate_email_click();
					});
					ssp_activate_email_click();
				</script>";


		if ( isset($_GET['ssp_active_option']) ) {
			$active_option = $_GET['ssp_active_option'];
		} else {
			$active_option = '';
		}

		foreach ( $setting_group_array as $sg ) {
			if ( $active_option != '' ) {
				if ( $sg[0] == $active_option ) {
					$sg[4] = true;
				} else {
					$sg[4] = false;
				}
			} elseif ( $sg[4] ) {
				$active_option = $sg[0];
			}
			$setting_group_head .= ssp_create_top_buttons( $sg[0], $sg[1], $sg[4], $sg[5], $sg[6] );
			$setting_group_body .= ssp_create_backend_option_set( $sg[0], $sg[1], $sg[2], $sg[3], $sg[4], $sg[5], $sg[6] );
		}



		// html output

		$output = ssp_get_backend_notifications() . $css;
		if ( SSP_FREE ) {
			$output .= '<a href="http://www.sunshinesites.com/coming-soon-plugin/" taget="_blank" class="button-primary ssp_upgrade">'.__('Upgrade to PRO', 'sunshineplugin').'</a>';
		}
		$output .= '<h1>'.SSP_NAME.'</h1>
					<div id="ssp_version"><a href="'.SSP_URI.'" target="_blank">www.sunshinesites.com</a> '.__('Version', 'sunshineplugin').' '.SSP_VERSION.'</div>
					<div id="ssp_saving">'.__('Saving settings...', 'sunshineplugin').'</div>
					<form method="post" action="admin-post.php" enctype="multipart/form-data" id="ssp_form">
						<span id="ssp_extend_span">
							<input type="hidden" name="action" value="ssp_save_option" />
							<input type="hidden" id="ssp_active_option" name="ssp_active_option" value="'.$active_option.'" />

							<input type="checkbox" name="ssp_active" id="ssp_active" ' . checked(1, SSP_ACTIVE, false) .' onchange="ssp_save_settings();javascript:this.form.submit();"/>
							<label for="ssp_active">
								<span id="ssp_inactive_cb">
									<span class="dashicons dashicons-yes"></span>'.__('Activate plugin', 'sunshineplugin').'
								</span>
								<span id="ssp_active_cb">
									<span class="dashicons dashicons-no-alt"></span>'.__('Deactivate plugin', 'sunshineplugin').'
								</span>
							</label>

						</span><br>
						'.wp_nonce_field( 'ssp_verify', '_wpnonce', true, false );

		$output .= '<span id="ssp_menu_wrapper">'.$setting_group_head.'</span>'.$setting_group_body;

		$output .= '	<br />
						<input type="submit" value="'.__('Save', 'sunshineplugin').'" class="button-primary"/>
					</form>';


		// $output .= '<span class="ssp_backend_inline">
						// <h2>'.__('Drittanbieterpakete', 'sunshineplugin').'</h2>
						// <a href="http://fortawesome.github.io/Font-Awesome/" target="_blank">Font Awesome</a>, MIT-Licence<br />
						// <a href="http://jscolor.com/" target="_blank">JSColor</a> LGPL<br />
						// <a href="https://github.com/drewm/mailchimp-api" target="_blank">MailChimp API v2 wrapper</a>, MIT-Licence<br />
						// <a href="http://hilios.github.io/jQuery.countdown/" target="_blank">The Final Countdown</a>, MIT-Licence
					// </span>';
		$output .= $js;

		echo $output;
		echo '<div style="display:none">';
		$settings = array(
						'media_buttons' => false,
						'quicktags' => false,
						'wpautop' => false
					);

		wp_editor( '', 'ssptlogo', $settings );
		wp_editor( '', 'ssptheadline', $settings );
		wp_editor( '', 'sspttyheadline', $settings );
		wp_editor( '', 'ssptprivheadline', $settings );
		wp_editor( '', 'ssptimprheadline', $settings );
		wp_editor( '', 'ssptprivmessage', $settings );
		wp_editor( '', 'ssptimprmessage', $settings );
		wp_editor( '', 'sspttymessage', $settings );
		wp_editor( '', 'ssptmessage', $settings );
		wp_editor( '', 'ssptfooter', $settings );
		wp_editor( '', 'ssptcustomhtml', $settings );
		wp_editor( '', 'sspttycustomhtml', $settings );

		echo '</div>';
	}

	function ssp_AddMenu() {
		add_menu_page('SunshinePlugin', 'SunshinePlugin', 'manage_options', __FILE__, 'ssp_AddSubMenu',SSP_GRAPH_URL.'Ico.png');
	}
	add_action( 'admin_menu', 'ssp_AddMenu' );

	// set admin defined options
	function process_ssp_options()
	{
		// some security checks
		if ( ( get_current_user_role() != 'Administrator') )
		{
			wp_die( __('FEHLER', 'sunshineplugin').': '.__('Unerlaubter Zugriff', 'sunshineplugin') );
		}

		check_admin_referer( 'ssp_verify' );

		////// set options
		$ssp_settings = get_option( 'ssp_settings' );
		$ssp_settings_b4 = $ssp_settings;

		if ( isset( $_POST['ssp_bg_image'] ) ) {
			if ( $_FILES['ssp_background_upload_img']['name'] != '' ) {
				if ( strtolower( pathinfo( $_FILES['ssp_background_upload_img']['name'], PATHINFO_EXTENSION ) ) == 'jpg' ) {
					$attachment_id = media_handle_upload( 'ssp_background_upload_img', 0 );
					if ( is_wp_error( $attachment_id ) ) {
						wp_die( $attachment_id );
						$ul_status = 4;
					} else {
						$ssp_settings['background_aid'] = $attachment_id;
						$ul_status = 1;
					}
				} else $ul_status = 2;
			} else $ul_status = 3;
			if ( ( $ssp_settings['background_aid'] == 0 ) && ( $ssp_settings['background_url'] == '' ) ) {
				$ssp_settings['background_url'] = SSP_GLOBAL_BACKGROUND;
			}
		} else {
			$ssp_settings['background_url'] = '';
		}

		if ( $_FILES['ssp_logo_upload_img']['name'] != '' ) {
			$filetype = strtolower( pathinfo( $_FILES['ssp_logo_upload_img']['name'], PATHINFO_EXTENSION ) );
			if ( ( $filetype == 'jpg' ) || ( $filetype == 'png' ) ) {
				$attachment_id = media_handle_upload( 'ssp_logo_upload_img', 0 );
				if ( is_wp_error( $attachment_id ) ) {
					wp_die( $attachment_id );
					$ul_status = 4;
				} else {
					$ssp_settings['logo_aid'] = $attachment_id;
					$ul_status = 1;
				}
			} else $ul_status = 2;
		} else $ul_status = 3;

		$ssp_settings['active']			= ( isset( $_POST['ssp_active'] ) )	? true : false;

		$ssp_settings['email_active']		= ( isset( $_POST['ssp_email_active'] ) )	? true : false;
		$ssp_settings['tile_active']	= ( isset( $_POST['ssp_tile_active'] ) )	? true : false;

		$ssp_settings['ac_all']			= ( isset( $_POST['ssp_ac_all'] ) )	? true : false;
		if ( $ssp_settings['ac_all'] ) {
			$ssp_settings['ac_4'] = $ssp_settings['ac_3'] = $ssp_settings['ac_2'] = $ssp_settings['ac_1'] = $ssp_settings['ac_0'] = true;
		} else {
			$ssp_settings['ac_4']		= ( isset( $_POST['ssp_ac_4'] ) )	? true : false;
			$ssp_settings['ac_3']		= ( isset( $_POST['ssp_ac_3'] ) )	? true : false;
			$ssp_settings['ac_2']		= ( isset( $_POST['ssp_ac_2'] ) )	? true : false;
			$ssp_settings['ac_1']		= ( isset( $_POST['ssp_ac_1'] ) )	? true : false;
			$ssp_settings['ac_0']		= ( isset( $_POST['ssp_ac_0'] ) )	? true : false;
		}

		$ssp_settings['logo_radio']		= $_POST['ssp_logo_radio'];

		$ssp_settings['t_logo']					= $_POST['ssptlogo'];
		$ssp_settings['t_headline']				= $_POST['ssptheadline'];
		$ssp_settings['t_message']				= $_POST['ssptmessage'];
		// $ssp_settings['t_email_notification']	= $_POST['ssptemailnotification'];
		$ssp_settings['t_ty_headline']			= $_POST['sspttyheadline'];
		$ssp_settings['t_ty_message']			= $_POST['sspttymessage'];
		$ssp_settings['t_impr_headline']		= $_POST['ssptimprheadline'];
		$ssp_settings['t_impr_message']			= $_POST['ssptimprmessage'];
		$ssp_settings['t_priv_headline']		= $_POST['ssptprivheadline'];
		$ssp_settings['t_priv_message']			= $_POST['ssptprivmessage'];
		$ssp_settings['t_subs_input']			= sanitize_text_field( $_POST['ssp_t_subs_input'] );
		$ssp_settings['t_subs_btn']				= sanitize_text_field( $_POST['ssp_t_subs_btn'] );
		$ssp_settings['t_error']				= sanitize_text_field( $_POST['ssp_t_error'] );
		$ssp_settings['t_footer']				= $_POST['ssptfooter'];
		$ssp_settings['t_sharing_message']				= sanitize_text_field( $_POST['ssp_t_sharing_message'] );


		$ssp_settings['t_custom_html']		= $_POST['ssptcustomhtml'];
		$ssp_settings['t_ty_custom_html']	= $_POST['sspttycustomhtml'];
		$ssp_settings['t_custom_css']		= $_POST['ssp_t_custom_css'];
		$ssp_settings['t_custom_js']		= $_POST['ssp_t_custom_js'];
		$ssp_settings['t_ty_custom_js']		= $_POST['ssp_t_ty_custom_js'];

		$ssp_settings['sm_fb']		= sanitize_text_field( $_POST['ssp_sm_fb'] );
		$ssp_settings['sm_tw']		= sanitize_text_field( $_POST['ssp_sm_tw'] );
		$ssp_settings['sm_gp']		= sanitize_text_field( $_POST['ssp_sm_gp'] );
		$ssp_settings['sm_pi']		= sanitize_text_field( $_POST['ssp_sm_pi'] );
		$ssp_settings['sm_t']		= sanitize_text_field( $_POST['ssp_sm_t'] );
		$ssp_settings['sm_in']		= sanitize_text_field( $_POST['ssp_sm_in'] );
		$ssp_settings['sm_v']		= sanitize_text_field( $_POST['ssp_sm_v'] );
		$ssp_settings['sm_yt']		= sanitize_text_field( $_POST['ssp_sm_yt'] );
		$ssp_settings['sm_ig']		= sanitize_text_field( $_POST['ssp_sm_ig'] );

		$ssp_settings['share']			= ( isset( $_POST['ssp_share'] ) )	? true : false;

		////// mailchimp
		$ssp_settings['mc_api']		= sanitize_text_field( $_POST['ssp_mc_api'] );
		if ( $ssp_settings['mc_api'] != '' ) {
			if ( isset( $_POST['ssp_mc_list'] ) && ( $_POST['ssp_mc_list'] != '' ) )  {
				$ssp_settings['mc_lid'] = sanitize_text_field( $_POST['ssp_mc_list'] );
			} else {
				$MailChimp = new \Drewm\MailChimp( $ssp_settings['mc_api'] );
				$mc_overview = $MailChimp->call('lists/list');
				if ( $mc_overview['total'] > 0 ) {
					$mc_lists = $mc_overview['data'];
					$mc_list = $mc_lists[0];
					$ssp_settings['mc_lid'] = $mc_list['id'];

				} else {
					$ssp_settings['mc_lid'] = '';
				}
			}
		} else {
			$ssp_settings['mc_lid'] = '';
		}

		$ssp_settings['owner_mail']		= sanitize_email( $_POST['ssp_owner_mail'] );

		$ssp_settings['cd_active']		= ( isset( $_POST['ssp_cd_active'] ) )	? true : false;

		$ssp_settings['cd_date']		= sanitize_text_field( $_POST['ssp_cd_date'] );
		$ssp_settings['cd_format']		= sanitize_text_field( $_POST['ssp_cd_format'] );

		$ssp_settings['font_radio']		= $_POST['ssp_font_radio'];
		$ssp_settings['custom_fonts']	= trim( trim( trim( sanitize_text_field( $_POST['ssp_custom_fonts'] ) ), ',' ) );

		////// set fonts
		$ssp_fonts = get_option( 'ssp_fonts' );
		$ssp_fonts_b4 = $ssp_fonts;
		$ssp_fonts[0]		= $_POST['ssp_font_0'];
		if ( $ssp_settings['font_radio'] == 1 ) {
			for ($i = 1; $i < count( $ssp_fonts ); $i++) {
				$ssp_fonts[ $i ]		= $_POST[ 'ssp_font_0' ];
			}
		} else {
			for ($i = 1; $i < count( $ssp_fonts ); $i++) {
				$ssp_fonts[ $i ]		= $_POST[ 'ssp_font_'.$i ];
			}
		}
		update_option( 'ssp_fonts', $ssp_fonts );

		////// set colors
		$ssp_colors = get_option( 'ssp_colors' );
		$ssp_colors_b4 = $ssp_colors;

		////// transparency colors
		for ($i = 0; $i <= 15; $i++) {
			$transparency = 1;
			if ( isset( $_POST['ssp_input_transparency_'.$i] ) ) $transparency = 1 - ( intval( $_POST['ssp_input_transparency_'.$i] ) / 100 );
			if ( isset( $_POST['ssp_input_color_'.$i] ) ) $ssp_colors[$i] = ssp_hex2rgb( sanitize_text_field($_POST['ssp_input_color_'.$i]), $transparency  );
		}
		update_option( 'ssp_colors', $ssp_colors );

		////// check for a change in template
		if ( ( $ssp_settings_b4['tile_active'] != $ssp_settings['tile_active'] ) ||
			 ( $ssp_settings_b4['font_radio'] != $ssp_settings['font_radio'] ) ||
			 ( $ssp_settings_b4['background_url'] != $ssp_settings['background_url'] ) ||
			 ( $ssp_settings_b4['background_aid'] != $ssp_settings['background_aid'] ) ||
			 ( $ssp_fonts_b4 != $ssp_fonts ) ||
			 ( $ssp_colors_b4 != $ssp_colors )
		   ) {
			$ssp_settings['template'] = '';
		}

		update_option( 'ssp_settings', $ssp_settings );

		////// get last option position
		if ( isset( $_POST['ssp_active_option'] ) ) {
			$active_option = '&ssp_active_option='.$_POST['ssp_active_option'];
		}

		// redirect to adminpanel
		wp_redirect( admin_url( 'admin.php?page='.SSP_FOLDER.'/sunshineplugin_admin_backend.php&ok=1&upload='.$ul_status.$active_option ) );
		exit;
	}
	add_action( 'admin_post_ssp_save_option', 'process_ssp_options' );
?>