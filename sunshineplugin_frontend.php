<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) & !(IE 8)]><!-->
<html <?php language_attributes() ?> style="margin-top: 0px !important; <?php if ( SSP_BACKGROUND != '' ) echo 'background: url('.SSP_BACKGROUND.') no-repeat center center fixed;' ?>">
<!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<title><?php wp_title( '|', true, 'right' ) ?></title>
<?php echo ssp_get_custom_fonts( 'css_short' ) ?>
<?php ssp_project_css().wp_head() ?>
</head>

<body id="ssp_body">
	<div id="ssp_background_image" ></div>
	<?php if ( SSP_TILE_ACTIVE ) { ?>
	<div id="ssp_wrapper_tile">
	<?php } else { ?>
	<div id="ssp_wrapper">
	<?php }?>
		<header>
			<?php echo ssp_get_logo() ?>
		</header>
		<div id="main">

		<?php
		if ( $_GET['page'] == 'impr') {
			?>	<div id="ssp_headline">
					<?php echo SSP_T_IMPR_HEADLINE ?>
				</div>

				<div id="ssp_message" class="ssp_hide_on_ty">
					<?php echo SSP_T_IMPR_MESSAGE ?>
				</div>

				<div>
					<a href="<?php echo home_url('/') ?>">zurück</a>
				</div>

			<?php
		} else if ( $_GET['page'] == 'priv') {
			?>	<div id="ssp_headline">
					<?php echo SSP_T_PRIV_HEADLINE ?>
				</div>

				<div id="ssp_message" class="ssp_hide_on_ty">
					<?php echo SSP_T_PRIV_MESSAGE ?>
				</div>

				<div>
					<a href="<?php echo home_url('/') ?>">zurück</a>
				</div>

			<?php
		} else {

			if ( SSP_T_HEADLINE != '' ) {
			?>
			<div id="ssp_headline">
				<?php echo SSP_T_HEADLINE ?>
			</div>
			<?php
			}

			if ( SSP_T_MESSAGE != '' ) {
			?>
			<div id="ssp_message" class="ssp_hide_on_ty">
				<?php echo SSP_T_MESSAGE ?>
			</div>
			<?php
			}

			if ( SSP_T_CUSTOM_HTML != '' ) {
			?>
			<div id="ssp_custom_html_wrapper" class="ssp_hide_on_ty">
				<?php echo SSP_T_CUSTOM_HTML ?>
				</div>
			<?php
			}

			if ( SSP_EMAIL_ACTIVE && ( ( SSP_MC_API != '' ) || SSP_FREE ) ) {
			?>
			<div id="ssp_email_form" class="ssp_hide_on_ty">
				<input type="text" id="ssp_email_input" name="ssp_email_input" placeholder="<?php echo SSP_T_SUBS_INPUT ?>"/> <span id="ssp_email_btn" onclick="ssp_send_email();"><?php echo SSP_T_SUBS_BTN ?></span>
			</div>
			<div id="ssp_error" style="display: none" class="ssp_hide_on_ty"><?php echo SSP_T_ERROR ?></div>
			<div id="ssp_thx" class="ssp_show_on_ty" style="display: none"><?php echo SSP_T_TY_HEADLINE ?></div>
			<?php
			}
			?>
			<div id="ssp_share_txt" class="ssp_show_on_ty" style="display: none"><?php echo SSP_T_TY_MESSAGE ?></div>
			
			<div id="ssp_social_media">
				<?php
				if ( SSP_SM_FB != '' ) {
					echo '<a href="'.SSP_SM_FB.'" target="_blank"><i class="fa fa-facebook"></i></a>';
				}
				if ( SSP_SM_TW != '' ) {
					echo '<a href="'.SSP_SM_TW.'" target="_blank"><i class="fa fa-twitter"></i></a>';
				}
				if ( SSP_SM_GP != '' ) {
					echo '<a href="'.SSP_SM_GP.'" target="_blank"><i class="fa fa-google-plus"></i></a>';
				}
				if ( SSP_SM_PI != '' ) {
					echo '<a href="'.SSP_SM_PI.'" target="_blank"><i class="fa fa-pinterest-p"></i></a>';
				}
				if ( SSP_SM_T != '' ) {
					echo '<a href="'.SSP_SM_T.'" target="_blank"><i class="fa fa-tumblr"></i></a>';
				}
				if ( SSP_SM_IN != '' ) {
					echo '<a href="'.SSP_SM_IN.'" target="_blank"><i class="fa fa-linkedin"></i></a>';
				}
				if ( SSP_SM_V != '' ) {
					echo '<a href="'.SSP_SM_V.'" target="_blank"><i class="fa fa-vimeo-square"></i></a>';
				}
				if ( SSP_SM_YT != '' ) {
					echo '<a href="'.SSP_SM_YT.'" target="_blank"><i class="fa fa-youtube"></i></a>';
				}
				if ( SSP_SM_IG != '' ) {
					echo '<a href="'.SSP_SM_IG.'" target="_blank"><i class="fa fa-instagram"></i></a>';
				}
				?>
			</div>

		<?php
		}
		?>
		</div><!-- #main .wrapper -->
		<footer>
			<?php echo SSP_T_FOOTER ?>
		</footer>
		<?php if ( is_user_logged_in() ) { ?>
		<div id="ssp_show_wp_admin">
			<span class="dashicons dashicons-admin-settings"></span>
		</div>
		<?php } ?>
	</div>

	<?php wp_footer(); ?>
</body>
</html>