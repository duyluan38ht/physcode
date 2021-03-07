<?php

/**
 * @wordpress-plugin
 * Plugin Name:       WPML Support Toolbox
 * Plugin URI:        test
 * Description:       This is a tool from WPML Support (please backup your website first)
 * Version:           1.0.7
 * Author:            The Support Team
 * Author URI:        test
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wpml-support-toolbox
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	exit;
}


if (class_exists('SitePress')) {   
	//Functions
	require('functions.php');

	//Create admin page and options
	require('admin-page.php');	

	//Adminer 
	$ari_adminer_switch = get_option('wpmld_ari_adminer');

	if ($ari_adminer_switch == true) {
		require('vendor/ari-adminer/ari-adminer.php');
	}

	//Move posts between languages
	if (get_option('wpmld_move_posts_switch') == true) {
		require('classes/class-move_posts.php');

		//Activate "Move Posts between languages" feature
		$move_posts = new WPMLD_Move_posts(); 
		$move_posts->wpmld_move_posts_hooks();
	}
	
	//Connect posts using Quik Edit Class
	if (get_option('wpmld_quick-edit-connect-switch') == true) {
		require('classes/class-connect_quick_edit.php');	

		//Activate "Connect using quick edit" feature	
		$connect_quick_edit = new WPMLD_Connect_Quick_Edit();
		$connect_quick_edit->wpmld_connect_quick_edit_hooks();
	}	

	//Folder Sizer
	require('classes/class-wp-folder-sizes.php');	

}


//TGM - Plugin Installer
require_once ('inc/tgm-plugin-activation/class-tgm-plugin-activation.php');
require('inc/support-plugins-installer.php');

//Include easy-theme-and-plugin-upgrades plugin
if (! class_exists('CAJ_ETPU_Admin')) {  
	require_once ('vendor/easy-theme-and-plugin-upgrades/init.php');
}

//Debug.log quick look (See https://github.com/norcross/debug-quick-look)
if (! function_exists('DebugQuickLook\activate')) {  
	require('vendor/debug-quick-look/debug-quick-look.php');
}

//WP Theme plugin download (See https://wordpress.org/plugins/wp-theme-plugin-download/)
if (get_option('wpmld_wp-theme-plugin-download', true) == true) {
	if (! function_exists('ab_downloads')) {  
		require('vendor/wp-theme-plugin-download/wp-theme-plugin-download.php');
	}
}