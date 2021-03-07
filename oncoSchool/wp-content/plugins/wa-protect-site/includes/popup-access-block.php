<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<style type="text/css">
#cover {
	position: fixed;
	top: 0;
	left: 0;
	background: <?php echo $this->options['popup_overlay_color']; ?>;
	opacity: <?php echo $this->options['popup_overlay_opacity']; ?>;
	z-index: 999998;
	width: 100%;
	height: 100%;
	display: none;
}

#loginScreen {
	height: 330px;
	width: 320px;
	margin: 0 auto;
	position: fixed;
	left:0%;
	top:25%;
	right:0;
	bottom:0;
	z-index: 999999;
	display: none;
	background: <?php echo $this->options['popup_background_color']; ?>;
	opacity: <?php echo $this->options['popup_background_opacity']; ?>;
}

@media screen and (max-width: 640px) {
	#loginScreen {
		width: 90%;
		left:5%;
		top:5%;
		height: auto;
		position: absolute;
	}
}

.content-popup {
	position: relative;
	margin-top: 0px;
	left: 0;
	color: black;
	width: auto;
	text-decoration: none;
	text-align: center;
	padding: 0 20px;
}

h2#googleAdsContainer {
	font-size: 20px;
	font-family: arial;
	text-transform: uppercase;
	margin:20px 0;
	padding:0;
}

p#googleAdsParagraph {
	font-size: 16px;
	margin-top: 10px;
}

.input-box{
	text-align: center!important ;
}

.logo-img {
	text-align: center;
	width:100%;
	margin:auto;
}

.logo-img img {
	text-align: center;
	margin:auto;
	max-width:60%;
}

.input-box {
	margin: 10px auto !important;
	min-height: 40px !important;
	padding: 0 5px !important;
	width: 96% !important;
	text-align:center;
}

.submit-button {
	font-size: 16px !important;
	font-weight: bold !important;
	padding: 5px !important;
	/* width: 100px !important;
	float: right !important; */
	width: 97% !important;
	color: #ffffff !important;
	background-color:#2e5688;
	font-family:"Lucida Sans Unicode" !important;
}

.error-password {
	color: #ff0000;
	font-size: 16px;
	font-weight: normal;
}

.form-outer {
	margin: 10px auto;
	width: 100%;
}
</style>

<div id="loginScreen">

	<div class="content-popup">
		<h2 title="<?php echo $this->options['popup_title']; ?>" id="googleAdsContainer"><?php echo $this->options['popup_title']; ?></h2>
		<?php if ( isset( $this->options['the_logo_image'] ) && !empty( $this->options['the_logo_image'] ) ): ?>
			<div class="logo-img">
				<img src="<?php echo $this->options['the_logo_image']; ?>" />
			</div>
		<?php endif; ?>
		<p title="<?php echo $this->options['popup_text']; ?>" id="googleAdsParagraph"><?php echo $this->options['popup_text']; ?></p>

		<div class="form-outer">

			<?php if ( isset( $_SESSION['error'] ) ): ?>
				<div class="error-password">
					<?php echo $_SESSION['error']; ?>
					<?php unset( $_SESSION['error'] ); ?>
				</div>
			<?php endif; ?>

			<form name="login-form" id="login-form" method="post">
				<?php wp_nonce_field( 'security_admin_action_nonce', 'security_admin_nonce' ); ?>
				<input type="text" class="input-box" name="password" placeholder="<?php echo $this->options['popup_textbox_placeholder']; ?>" id="password" required>
				<button type="submit" class="submit-button" name="submitform" id="submitform" value="login"><?php echo $this->options['popup_button_label']; ?></button>
			</form>

		</div>

	</div>

</div>
<div id="cover"></div>

<script>
jQuery('#loginScreen').show();
jQuery('#cover').show();
</script>
