<?php

/*
Plugin Name: WA Protect Site
Plugin URI: http://www.webatual.pt
Description:
Version: 1.0.1
Author: WebAtual
Author URI: http://www.webatual.pt
License: GPL2
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
*
*/
if(!class_exists('WA_Protect_Site')) {
	class WA_Protect_Site {

		public static function init() {
			$class = __CLASS__;
			new $class;
		}

		function __construct() {

			$this->id = 'wa_protect_site';
			$this->name = __( 'Protect Site', 'wa-protect-site' );

			global $wpdb;
			$this->table_name = $wpdb->prefix . $this->id;

			$this->options = get_option( $this->id . '_options' );

			$this->hooks();

		}

		public static function activation() {
			global $wpdb;

			$default_options = array(
				'popup_title' => 'Area Reservada',
				'popup_text' => 'Digite a sua senha',
				'popup_textbox_placeholder' => 'Senha',
				'popup_button_label' => 'Continuar',
				'user_popup_title' => 'Area Reservada',
				'user_popup_text' => 'Por razões de segurança, pedimos-lhe que crie uma senha para o seu acesso à área reservada.',
				'user_popup_textbox_placeholder' => 'Nova senha',
				'user_popup_button_label' => 'Continuar',
				'wrong_password_text' => 'Password errada',
				'popup_overlay_color' => '#999999',
				'popup_overlay_opacity' => '1',
				'popup_background_color' => '#000000',
				'the_logo_image' => '',
				'popup_background_opacity' => '1',
				'cookie_duration' => '365',
				'change_required' => 'yes',
				'pages_to_protect' => 'all',
			);
			if ( ! get_option( 'wa_protect_site_options' ) ) {
				add_option( 'wa_protect_site_options', $default_options );
			}

			$table_passwords = $wpdb->prefix . 'wa_protect_site';
			$charset_collate = $wpdb->get_charset_collate();
			$thesql = "CREATE TABLE IF NOT EXISTS $table_passwords (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				password varchar(255) DEFAULT '' NOT NULL,
				upassword varchar(255) DEFAULT '' NOT NULL,
				is_pass_change int(1) DEFAULT 0,
				change_ip varchar(100) DEFAULT '' NOT NULL,
				change_time datetime,
				UNIQUE KEY id (id)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $thesql );
		}

		public function deactivation() {
			// delete_option( 'wa_protect_site_options' );
			// global $wpdb;
			// $table_passwords = $wpdb->prefix . 'wa_protect_site';
			// $wpdb->query( "DROP TABLE IF EXISTS $table_password" );
		}

		function hooks() {
			add_action( 'admin_menu', array( $this, 'wa_add_menu_pages' ) );

			add_action('admin_print_scripts', array( $this, 'admin_scripts' ) );
			add_action('admin_print_styles', array( $this, 'admin_styles' ) );

			/* create session */
			add_action('init', array( $this, 'startSession' ), 1);
			add_action('wp_logout', array( $this, 'endSession' ) );

			add_action( 'wp_head', array( $this, 'popup_footer' ) );
		}

		function wa_add_menu_pages() {
			add_menu_page(
				$this->name,
				$this->name,
				'manage_options',
				$this->id,
				array( $this, 'admin_page' ),
				'dashicons-admin-network'
			);

			add_submenu_page(
				$this->id,
				__( 'Options', 'wa-protect-site' ),
				__( 'Options', 'wa-protect-site' ),
				'manage_options',
				$this->id,
				array( $this, 'admin_page' )
			);

			add_submenu_page(
				$this->id,
				__( 'Passwords', 'wa-protect-site' ),
				__( 'Passwords', 'wa-protect-site' ),
				'manage_options',
				$this->id . '_passwords',
				array( $this, 'passwords_page' )
			);

		}

		function admin_page() {
			include_once( plugin_dir_path( __FILE__ ) . 'includes/admin-options.php' );
		}

		function passwords_page() {
			include_once( plugin_dir_path( __FILE__ ) . 'includes/admin-passwords.php' );
		}

		function admin_scripts() {
			wp_enqueue_script( 'media-upload' );
			wp_enqueue_script( 'thickbox' );
			wp_enqueue_script( 'farbtastic' );
		}

		function admin_styles() {
			wp_enqueue_style( 'thickbox' );
			wp_enqueue_style( 'farbtastic' );
		}

		function startSession() {
			if ( ! session_id() ) {
				session_start();
			}
		}

		function endSession() {
			session_destroy();
		}

		function popup_footer() {

			switch ( $this->options['pages_to_protect'] ) {
				case 'none':
				break;

				case 'postmeta':
				global $post;
				if ( get_post_meta( $post->ID, 'protect_page', true ) == 'yes' ) {
					include_once( plugin_dir_path( __FILE__ ) . 'includes/popup-footer.php' );
				}
				break;

				default:
				include_once( plugin_dir_path( __FILE__ ) . 'includes/popup-footer.php' );
				break;
			}

		}

		function access_website_popup_container() {
			include_once( plugin_dir_path( __FILE__ ) . 'includes/popup-access-block.php' );
		}

		function set_user_password_popup() {
			include_once( plugin_dir_path( __FILE__ ) . 'includes/popup-define-password.php' );
		}

		function encode_password( $password ) {
			$password = esc_sql( $password );
			return base64_encode( $password );
		}

		function decode_password( $password ) {
			return base64_decode( $password );
		}

		function check_password( $password, $object = false ) {
			global $wpdb;

			$validate_password = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->table_name WHERE password = %s", $this->encode_password( $password ) ) );

			if ( $validate_password ) {
				if ( $object ) {
					return $validate_password;
				} else {
					return true;
				}
			}

			return false;
		}

		function validate_cookie( $cookie ) {
			if ( $cookie == 'YESACCESS' ) return true;
			
			global $wpdb;
			$password = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->table_name WHERE CONCAT(id, password) = %s", $cookie ) );

			if ( $password ) {
				return true;
			} else {
				return false;
			}

		}

		function add_new_password( $password ) {
			global $wpdb;

			$create = $wpdb->insert( $this->table_name, array( 'password' => $this->encode_password( trim( $password ) ) ) );

			if ( $create ) return $password;

			return false;
		}

		function generate_random_password( $count = 1 ) {
			global $wpdb;
			$generated = array();

			for ($i=0; $i < $count; $i++) {
				$this->add_new_password( $this->generateRandomString( 8 ) );
				$generated[] = $encryptedpass;
			}

			return $generated;

		}

		function generateRandomString( $length = 8 ) {
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$charactersLength = strlen($characters);
			$randomString = '';
			for ($i = 0; $i < $length; $i++) {
				$randomString .= $characters[rand(0, $charactersLength - 1)];
			}
			return $randomString;
		}


	}

	add_action( 'plugins_loaded', array( 'WA_Protect_Site', 'init' ));
	register_activation_hook(__FILE__, array('WA_Protect_Site', 'activation'));
	register_deactivation_hook(__FILE__, array('WA_Protect_Site', 'deactivation'));

} // if class_exists
?>
