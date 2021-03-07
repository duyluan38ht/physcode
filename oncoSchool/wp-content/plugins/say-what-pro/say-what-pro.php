<?php

/*
Plugin Name: Say What? Pro
Plugin URI: https://plugins.leewillis.co.uk/downloads/say-what-pro
Description: An easy-to-use plugin that allows you to alter strings on your site without editing WordPress core, or plugin code.
Version: 2.6.0
Author: Ademti Software Ltd.
Author URI: https://www.ademti-software.co.uk/
*/

/**
 * Copyright (c) 2015-2019 Ademti Software Ltd. // www.ademti-software.co.uk
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * **********************************************************************
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

define( 'SAY_WHAT_PRO_DB_VERSION', 6 );

/**
 * Deactivate the plugin cleanly.
 */
function swp_plugin_deactivate() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	deactivate_plugins( plugin_basename( __FILE__ ) );
}

/**
 * Show an admin notice about PHP requirements.
 */
function swp_plugin_admin_notice_php() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	echo '<div class="error"><p><strong>Say What? Pro</strong> requires PHP version 5.6 or above.</p></div>';
	if ( isset( $_GET['activate'] ) ) {
		unset( $_GET['activate'] );
	}
}

/**
 * Install function. Create the table to store the replacements
 */
function say_what_pro_install() {
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$table_name = $wpdb->prefix . 'say_what_strings';
	$sql = "CREATE TABLE $table_name (
						 string_id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
						 orig_string text NOT NULL,
						 domain varchar(255),
						 replacement_string text,
						 context text,
						 lang varchar(10)
						 ) DEFAULT CHARACTER SET utf8";
	dbDelta( $sql );
	$table_name = $wpdb->prefix . 'say_what_available_strings';
	$sql = "CREATE TABLE $table_name (
						 orig_string text NOT NULL,
						 domain varchar(255),
						 context text,
						 translated_string text
						 ) DEFAULT CHARACTER SET utf8";
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );
	$table_name = $wpdb->prefix . 'say_what_wildcards';
	$sql = "CREATE TABLE $table_name (
						 wildcard_id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
						 original text NOT NULL,
						 replacement text,
						 lang varchar(10)
						 ) DEFAULT CHARACTER SET utf8";
	dbDelta( $sql );
	update_option( 'say_what_pro_db_version', SAY_WHAT_PRO_DB_VERSION );
}

global $disable_say_what_replacements;
$disable_say_what_replacements = false;

if ( version_compare( phpversion(), '5.6', '<' ) ) {
	add_action( 'admin_init', 'swp_plugin_deactivate' );
	add_action( 'admin_notices', 'swp_plugin_admin_notice_php' );
} else {
	require_once( plugin_dir_path( __FILE__ ) . 'vendor/autoload.php' );
	register_activation_hook( __FILE__, 'say_what_pro_install' );
	require_once( plugin_dir_path( __FILE__ ) . 'say-what-pro-bootstrap.php' );
}

// Plugin updates
if ( ! class_exists( 'PBLW_Plugin_Updater' ) ) {
	include( dirname( __FILE__ ) . '/PBLW-Plugin-Updater.php' );
}
new PBLW_Plugin_Updater(
	__FILE__,
	'say_what_pro'
);
