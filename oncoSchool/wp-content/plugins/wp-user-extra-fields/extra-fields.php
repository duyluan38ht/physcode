<?php 
/*
Plugin Name: WordPress User Extra Fields
Description: Add user extra fields on registration page. Compatible with WooCommerce and BuddyPress.
Author: Lagudi Domenico
Version: 14.8
*/

//define('WPUEF_PLUGIN_PATH', WP_PLUGIN_URL."/".dirname( plugin_basename( __FILE__ ) ) );
define('WPUEF_PLUGIN_PATH', rtrim(plugin_dir_url(__FILE__), "/") ) ;
define('WPUEF_PLUGIN_ABS_PATH', dirname( __FILE__ ) ); ///ex.: "woocommerce/wp-content/plugins/wp-user-extra-fields"

$wpuef_woocommerce_is_active = false;
$wpuef_buddypress_is_active = false;
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ||
     (is_multisite() && array_key_exists( 'woocommerce/woocommerce.php', get_site_option('active_sitewide_plugins') ))	
	) 
{
	$wpuef_woocommerce_is_active = true;
}
if ( in_array( 'buddypress/bp-loader.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ||
     (is_multisite() && array_key_exists( 'buddypress/bp-loader.php', get_site_option('active_sitewide_plugins') ))	
	) 
{
	$wpuef_buddypress_is_active = true;
}

$wpuef_id = 12949844;
$wpuef_name = "User Extra Fields";
$wpuef_activator_slug = "wpuef-activator";
include 'classes/com/WPUEF_Globals.php';
require_once('classes/admin/WPUEF_ActivationPage.php');

add_action('admin_notices', 'wpuef_admin_notices' );
add_action('admin_menu', 'wpuef_init_act');
if(defined('DOING_AJAX') && DOING_AJAX)
		wpuef_init_act();
add_action('init', 'wpuef_init');

function wpuef_init()
{
	load_plugin_textdomain('wp-user-extra-fields', false, basename( dirname( __FILE__ ) ) . '/languages' );
	/* if(is_admin())
		wpuef_init_act(); */
}
function wpuef_init_act()
{
	global $wpuef_activator_slug, $wpuef_name, $wpuef_id;
	new WPUEF_ActivationPage($wpuef_activator_slug, $wpuef_name, 'wp-user-extra-fields', $wpuef_id, WPUEF_PLUGIN_PATH);
}
function wpuef_admin_notices()
{
	global $wpuef_notice, $wpuef_name, $wpuef_activator_slug;
	if($wpuef_notice && (!isset($_GET['page']) || $_GET['page'] != $wpuef_activator_slug))
	{
		 ?>
		<div class="notice notice-success">
			<p><?php echo sprintf(__( 'To complete the <span style="color:#96588a; font-weight:bold;">%s</span> plugin activation, you must verify your purchase license. Click <a href="%s">here</a> to verify it.', 'wp-user-extra-fields' ), $wpuef_name, get_admin_url()."admin.php?page=".$wpuef_activator_slug); ?></p>
		</div>
		<?php
	}
}
function wpuef_setup()
{
	global $wpuef_shortcodes, $wpuef_order_model, $wpuef_user_model, $wpuef_file_model, $wpuef_field_model, $wpuef_option_model,
	$wpuef_htmlHelper, $wpuef_wpml_helper, $wpuef_country_model, $wpuef_userProfile_addon, $wpuef_checkout_page, $wpuef_userTable_addon,
	$wpuef_admin_order_page_addon;
	
	//com
	if(!class_exists('WPUEF_Shortcode'))
		require_once('classes/com/WPUEF_Shortcode.php');
	$wpuef_shortcodes = new WPUEF_Shortcode();

	if(!class_exists('WPUEF_Order'))
		require_once('classes/com/WPUEF_Order.php');
	$wpuef_order_model = new WPUEF_Order();

	if(!class_exists('WPUEF_File'))
		if(!class_exists('WPUEF_User'))
		require_once('classes/com/WPUEF_User.php');
	$wpuef_user_model = new WPUEF_User();

	if(!class_exists('WPUEF_File'))
		require_once('classes/com/WPUEF_File.php');
	$wpuef_file_model = new WPUEF_File();

	if(!class_exists('WPUEF_Field'))
		require_once('classes/com/WPUEF_Field.php');
	$wpuef_field_model = new WPUEF_Field();

	if(!class_exists('WPUEF_Option'))
		require_once('classes/com/WPUEF_Option.php');
	$wpuef_option_model = new WPUEF_Option();

	if(!class_exists('WPUEF_HtmlHelper'))
		require_once('classes/com/WPUEF_HtmlHelper.php');
	$wpuef_htmlHelper = new WPUEF_HtmlHelper();

	if(!class_exists('WPUEF_Wpml'))
		require_once('classes/com/WPUEF_Wpml.php');
	$wpuef_wpml_helper = new WPUEF_Wpml();

	if(!class_exists('WPUEF_Country'))
		require_once('classes/com/WPUEF_Country.php');
	$wpuef_country_model = new WPUEF_Country();

	//frontend
	if(!class_exists('WPUEF_UserProfileFormsAddon'))
		require_once('classes/frontend/WPUEF_UserProfileFormsAddon.php');
	$wpuef_userProfile_addon = new WPUEF_UserProfileFormsAddon();

	if(!class_exists('WPUEF_CheckoutPage'))
		require_once('classes/frontend/WPUEF_CheckoutPage.php');
	$wpuef_checkout_page = new WPUEF_CheckoutPage();

	//admin
	if(!class_exists('WPUEF_UsersTable'))
		require_once('classes/admin/WPUEF_UsersTable.php');
	$wpuef_userTable_addon = new WPUEF_UsersTable();

	if(!class_exists('WPUEF_OrderPage'))
		require_once('classes/admin/WPUEF_OrderPage.php');
	$wpuef_admin_order_page_addon = new WPUEF_OrderPage();

	add_action('admin_menu', 'wpuef_init_admin_panel');
	add_action( 'admin_init', 'wpuef_register_settings');
	add_action( 'init', 'wpuef_init');
	add_action( 'login_enqueue_scripts', 'wpuef_custom_login_enqueue_scripts' );
}
function wpuef_custom_login_enqueue_scripts()
{
	wp_enqueue_script('jquery');
}

function wpuef_register_settings()
{
	register_setting('wpuef_user_extra_fields_group','wpuef_user_extra_fields');
}
function wpuef_init_admin_panel()
{ 
	global $wpuef_woocommerce_is_active;
	$place = wpuef_get_free_menu_position(69 , .1);
	$cap = 'edit_users';
	/* if($wpuef_woocommerce_is_active)
		$cap = 'manage_woocommerce'; */
	add_submenu_page( 'users.php', __('Extra fields', 'wp-user-extra-fields'), __('Extra fields', 'wp-user-extra-fields'), $cap, 'wp-user-extra-fields-configurator', 'wpuef_load_configurator_view');
	add_submenu_page( 'options-general.php', __('User Extra Fields', 'wp-user-extra-fields'), __('User Extra Fields', 'wp-user-extra-fields'), $cap, 'wp-user-extra-fields-settings', 'wpuef_load_settings_view');
}
function wpuef_get_free_menu_position($start, $increment = 0.3)
{
	foreach ($GLOBALS['menu'] as $key => $menu) {
		$menus_positions[] = $key;
	}

	if (!in_array($start, $menus_positions)) return $start;

	/* the position is already reserved find the closet one */
	while (in_array($start, $menus_positions)) {
		$start += $increment;
	}
	return $start;
}
function wpuef_load_settings_view()
{
	if(!class_exists('WPUEF_Settings'))
	require_once('classes/admin/WPUEF_Settings.php');
	$wpuef_setting_page = new WPUEF_Settings();
	$wpuef_setting_page->render_page();
}
function wpuef_load_configurator_view()
{
	if(!class_exists('WPUEF_Configurator'))
		require_once('classes/admin/WPUEF_Configurator.php');
	$wpuef_configurator = new WPUEF_Configurator();
	$wpuef_configurator->render_page();
}
function wpuef_var_dump_log($log)
{
	 if ( is_array( $log ) || is_object( $log ) ) {
	 error_log( print_r( $log, true ) );
	  } else {
		 error_log( $log );
	  }
}
function wpuef_var_dump($var)
{
	echo "<pre>";
	var_dump($var);
	//error_log(var_dump($var));
	echo "</pre>";
}
?>