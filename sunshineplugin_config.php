<?php
	/*	Sunshineplugin
		sunshineplugin_config.php
		Grundeinstellungen
		Copyright 2015 - SunshineSites
	*/

	if ( !function_exists('add_action') ) {
		die( 'ERROR: Unauthorised access.' );
	}

	////// reset user settings
	function ssp_reset_plugin_options() {

		$ssp_colors = array(
			 0 => 'rgba(0,0,0,0.5)',		// background
			 1 => 'rgba(255,255,255,0.5)',	// normal text
			 2 => 'rgba(255,255,255,1)',	// headline
			 3 => 'rgba(255,255,255,1)',	// message
			 4 => 'rgba(255,255,255,1)',	// submessage
			 5 => 'rgba(255,255,255,1)',	// countdown
			 6 => 'rgba(198,130,0,1)',		// email button background
			 7 => 'rgba(255,255,255,1)',	// email button text
			 8 => 'rgba(255,255,255,1)',	// social media symbols
			 9 => 'rgba(255,255,255,1)',	// "thank you page" headline
			10 => 'rgba(255,255,255,1)',	// "thank you page" message
			11 => 'rgba(222,0,0,1)',		// error message
			12 => 'rgba(255,255,255,0.25)',	// links
			13 => 'rgba(255,255,255,0.5)',	// tile background
			14 => 'rgba(255,255,255,0.5)',	// logo
			15 => 'rgba(255,255,255,1)',	// email input background
			16 => 'rgba(20,20,20,1)'		// email input text

		);
		if ( get_option( 'ssp_settings' ) === false ) add_option( 'ssp_colors', $ssp_colors );
		else update_option( 'ssp_colors', $ssp_colors );

		$ssp_fonts = array(
			0 => 'Open Sans',	// font for everything
			1 => 'Open Sans',	// normal text
			2 => 'Open Sans',	// headline
			3 => 'Open Sans',	// message
			4 => 'Open Sans',	// submessage
			5 => 'Open Sans',	// countdown
			6 => 'Open Sans',	// email placeholder
			7 => 'Open Sans',	// email button text
			8 => 'Open Sans',	// "thank you page" headline
			9 => 'Open Sans',	// "thank you page" message
			10 => 'Open Sans',	// error message
			11 => 'Open Sans'	// logo
		);
		if ( get_option( 'ssp_settings' ) === false ) add_option( 'ssp_fonts', $ssp_fonts );
		else update_option( 'ssp_fonts', $ssp_fonts );

		$ssp_settings = array(
			'active' => false,
			'tile_active' => false,
			'email_active' => true,
			'extend_opt' => false,

			'ac_all' => false,
			'ac_4' => false,
			'ac_3' => false,
			'ac_2' => false,
			'ac_1' => true,
			'ac_0' => true,

			'background_aid' => 0,
			'background_url' => '',
			'logo_aid' => 0,
			'logo_radio' => 1,

			't_logo' => __('SunshineSites', 'sunshineplugin'),
			't_headline' => '<h1><b>'.__('Something Awesome Is Coming Soon', 'sunshineplugin').'</b></h1>',
			't_message' => __('Your Message', 'sunshineplugin'), //add free in free version
			't_ty_headline' => '<h1><b>'.__('Thank You.', 'sunshineplugin').'</b></h1>',
			't_ty_message' => __('Tell your friends about it.', 'sunshineplugin'),
			't_impr_headline' => '<h1><b>'.__('Imprint', 'sunshineplugin').'</b></h1>',
			't_impr_message' => __('Max Mustermann<br>Musterstraße 0<br>12345 Berlin', 'sunshineplugin'),
			't_priv_headline' => '<h1><b>'.__('Privacy', 'sunshineplugin').'</b></h1>',
			't_priv_message' => __('privacy-message for customers', 'sunshineplugin'),
			't_footer' => __('Copyright 2015 - <a href="'.home_url('/?page=impr').'">Imprint</a> - <a href="'.home_url('/?page=priv').'">Privacy</a>', 'sunshineplugin'),
			't_subs_input' => __('Enter your email', 'sunshineplugin'),
			't_subs_btn' => __('Get Updates', 'sunshineplugin'),
			't_error' => __('There was an error. Please check your input and retry.', 'sunshineplugin'),
			't_sharing_message' => __('Look, something awesome is coming! ', 'sunshineplugin'),
			// 't_email_notification'	=> __('Enter your email adress below & get notified when we launch.', 'sunshineplugin'),

			'share' => true,

			'mc_api' => '',
			'mc_lid' => '',
			'owner_email' => 'no.one@example.com',

			'cd_active' => true,
			'cd_date' => '12/31/2016 12:00:00',
			'cd_format' => '%D d. %H h. %M m. %S s.',

			'font_radio' => 1,
			'custom_fonts' => 'Open Sans, Raleway, PT Sans, Source Sans Pro, Nova Mono',

			't_custom_html' => '',
			't_ty_custom_html' => '',
			't_custom_css' => '',
			't_custom_js' => '',
			't_ty_custom_js' => '',

			'template' => '',
			'load_fontawesome' => 1
		);
		if ( get_option( 'ssp_settings' ) === false ) add_option( 'ssp_settings', $ssp_settings );
		else update_option( 'ssp_settings', $ssp_settings );
		ssp_load_template( '01' );
	}


	////// loading config
	function ssp_load_config() {
		////// setting up template before constant definition!
		global $template_error;
		$template_error = false;
		if ( isset( $_GET['template'] ) && is_admin() ) {
			$template_error = ssp_load_template( $_GET['template'] );
		}

		$default_headers = array(
			'Name' => 'Plugin Name',
			'PluginURI' => 'Plugin URI',
			'Version' => 'Version',
		);
		$plugin_data = get_file_data( SSP_PLUGIN_DIR.SSP_MAIN_FILE, $default_headers, 'plugin' );
		define( 'SSP_NAME', $plugin_data['Name'] );
		define( 'SSP_URI', $plugin_data['PluginURI'] );
		define( 'SSP_VERSION', $plugin_data['Version'] );

		global $wpdb;

		// manual debug option
		define( 'SSP_DEBUG', false );
		if ( SSP_DEBUG ) $wpdb->show_errors();

		////// register extra javascript files
		wp_register_script( 'sunshineplugin_js', plugins_url( 'js/sunshineplugin_script.js', __FILE__ ), array('jquery'), '1.0.0', true );
		wp_register_script( 'jscolor', plugins_url( 'js/jscolor/jscolor.js', __FILE__), NULL, '1.0.0', true );

		////// register extra css files
		wp_register_style( 'ssp_style', plugins_url( 'css/sunshineplugin.css', __FILE__), array(), SSP_VERSION );
		wp_register_style( 'ssp_style_backend', plugins_url( 'css/sunshineplugin_backend.css', __FILE__), array(), SSP_VERSION );
		wp_register_style( 'font-awesome', plugins_url( 'font-awesome/css/font-awesome.min.css', __FILE__), array(), '4.2.0' );

		////// check for first run
		if ( ( get_option( 'ssp_colors' ) === false ) || ( get_option( 'ssp_settings' ) === false ) )
		{
			ssp_reset_plugin_options();
		}

		////// load colors
		$ssp_colors = get_option( 'ssp_colors' );
		define( 'SSP_BGCOLOR', $ssp_colors[0] );

		////// load fonts
		$ssp_fonts = get_option( 'ssp_fonts' );
		for ($i = 0; $i < count( $ssp_fonts ); $i++) {
			define( 'SSP_FONT_'.$i, $ssp_fonts[$i] );
		}

		////// load settings
		$ssp_settings = get_option( 'ssp_settings' );

		define( 'SSP_ACTIVE',		$ssp_settings['active'] );

		define( 'SSP_EXTEND_OPT',	$ssp_settings['extend_opt'] );
		
		define( 'SSP_EMAIL_ACTIVE',	$ssp_settings['email_active'] );
		define( 'SSP_TILE_ACTIVE',	$ssp_settings['tile_active'] );

		define( 'SSP_AC_ALL',		$ssp_settings['ac_all'] );
		define( 'SSP_AC_4',			$ssp_settings['ac_4'] );
		define( 'SSP_AC_3',			$ssp_settings['ac_3'] );
		define( 'SSP_AC_2',			$ssp_settings['ac_2'] );
		define( 'SSP_AC_1',			$ssp_settings['ac_1'] );
		define( 'SSP_AC_0',			$ssp_settings['ac_0'] );

		define( 'SSP_LOGO_RADIO',	$ssp_settings['logo_radio'] );

		define( 'SSP_T_LOGO',				stripslashes( $ssp_settings['t_logo'] ) );
		define( 'SSP_T_HEADLINE',			stripslashes( $ssp_settings['t_headline'] ) );
		define( 'SSP_T_MESSAGE',			stripslashes( $ssp_settings['t_message'] ) );
		define( 'SSP_T_EMAIL_NOTIFICATION',	stripslashes( $ssp_settings['t_email_notification'] ) );
		define( 'SSP_T_TY_HEADLINE',		stripslashes( $ssp_settings['t_ty_headline'] ) );
		define( 'SSP_T_TY_MESSAGE',			stripslashes( $ssp_settings['t_ty_message'] ) );
		define( 'SSP_T_IMPR_HEADLINE',		stripslashes( $ssp_settings['t_impr_headline'] ) );
		define( 'SSP_T_IMPR_MESSAGE',		stripslashes( $ssp_settings['t_impr_message'] ) );
		define( 'SSP_T_PRIV_HEADLINE',		stripslashes( $ssp_settings['t_priv_headline'] ) );
		define( 'SSP_T_PRIV_MESSAGE',		stripslashes( $ssp_settings['t_priv_message'] ) );
		define( 'SSP_T_SUBS_INPUT',			$ssp_settings['t_subs_input']) ;
		define( 'SSP_T_SUBS_BTN',			$ssp_settings['t_subs_btn'] );
		define( 'SSP_T_ERROR',				$ssp_settings['t_error'] );
		define( 'SSP_T_FOOTER',				stripslashes( $ssp_settings['t_footer'] ) );

		define( 'SSP_T_SHARING_MESSAGE',				$ssp_settings['t_sharing_message'] );

		define( 'SSP_T_CUSTOM_HTML',	stripslashes( $ssp_settings['t_custom_html'] ) );
		define( 'SSP_T_TY_CUSTOM_HTML',	stripslashes( $ssp_settings['t_ty_custom_html'] ) );
		define( 'SSP_T_CUSTOM_CSS',		stripslashes( $ssp_settings['t_custom_css'] ) );
		define( 'SSP_T_CUSTOM_JS',		stripslashes( $ssp_settings['t_custom_js'] ) );
		define( 'SSP_T_TY_CUSTOM_JS',	stripslashes( $ssp_settings['t_ty_custom_js'] ) );

		define( 'SSP_SM_FB',	$ssp_settings['sm_fb'] );
		define( 'SSP_SM_TW',	$ssp_settings['sm_tw'] );
		define( 'SSP_SM_GP',	$ssp_settings['sm_gp'] );
		define( 'SSP_SM_PI',	$ssp_settings['sm_pi'] );
		define( 'SSP_SM_T',		$ssp_settings['sm_t'] );
		define( 'SSP_SM_IN',	$ssp_settings['sm_in'] );
		define( 'SSP_SM_V',		$ssp_settings['sm_v'] );
		define( 'SSP_SM_YT',	$ssp_settings['sm_yt'] );
		define( 'SSP_SM_IG',	$ssp_settings['sm_ig'] );

		define( 'SSP_SHARE',	$ssp_settings['share'] );

		define( 'SSP_MC_API',	$ssp_settings['mc_api'] );
		define( 'SSP_MC_LID',	$ssp_settings['mc_lid'] );
		define( 'SSP_OWNER_MAIL',	$ssp_settings['owner_mail'] );

		define( 'SSP_CD_ACTIVE',	$ssp_settings['cd_active'] );
		define( 'SSP_CD_DATE',		$ssp_settings['cd_date'] );
		define( 'SSP_CD_FORMAT',	$ssp_settings['cd_format'] );

		define( 'SSP_FONT_RADIO',	$ssp_settings['font_radio'] );
		define( 'SSP_CUSTOM_FONTS',	$ssp_settings['custom_fonts'] );

		define( 'SSP_LOAD_FA',		$ssp_settings['load_fontawesome'] );
		define( 'SSP_TEMPLATE',		$ssp_settings['template'] );

		////// used font awesome icons
		define( 'SSP_NOTMARKED_ICON', 	'fa-circle-thin');
		define( 'SSP_UNSURE_ICON', 		'fa-question-circle');
		define( 'SSP_MARKED_ICON',		'fa-check-circle-o');

		////// check if userdefined background exists and replace it in case it was deleted
		$background = $ssp_settings['background_url'];

		define( 'SSP_TEST',		$ssp_settings['background_aid']);
		if ( $ssp_settings['background_aid'] != 0 ) {
			if ( get_post_status ( $ssp_settings['background_aid'] ) ) {
				$url = wp_get_attachment_url( $ssp_settings['background_aid'] );
				if ( strtolower( pathinfo( $url, PATHINFO_EXTENSION ) ) == 'jpg' ) {
					$background = $url;
				}
			}
		}
		define( 'SSP_BACKGROUND', $background );
		$logo = '';
		if ( $ssp_settings['logo_aid'] != 0 ) {
			if ( get_post_status ( $ssp_settings['logo_aid'] ) ) {
				$logo = wp_get_attachment_url( $ssp_settings['logo_aid'] );
			}
		}
		define( 'SSP_LOGO', $logo );
	}

	ssp_load_config();
?>
