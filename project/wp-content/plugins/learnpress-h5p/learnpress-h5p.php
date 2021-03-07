<?php
/*
Plugin Name: LearnPress - H5P Content
Plugin URI: http://thimpress.com/learnpress
Description: H5P Content add-on for LearnPress.
Author: ThimPress
Version: 3.0.1
Author URI: http://thimpress.com
Tags: learnpress, lms, h5p
Text Domain: learnpress-h5p
Domain Path: /languages/
*/

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

define( 'LP_ADDON_H5P_FILE', __FILE__ );
define( 'LP_ADDON_H5P_PATH', dirname( __FILE__ ) );
define( 'LP_ADDON_H5P_INC_PATH', LP_ADDON_H5P_PATH . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR );
define( 'LP_ADDON_H5P_VER', '3.0.1' );
define( 'LP_ADDON_H5P_REQUIRE_VER', '3.2.6.4' );

/**
 * Class LP_Addon_H5p_Preload
 */
class LP_Addon_H5p_Preload {

	/**
	 * LP_Addon_H5p_Preload constructor.
	 */
	public function __construct() {
		add_action( 'learn-press/ready', array( $this, 'load' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
	}

	/**
	 * Load addon
	 */
	public function load() {
		LP_Addon::load( 'LP_Addon_H5p', 'inc/load.php', __FILE__ );
		remove_action( 'admin_notices', array( $this, 'admin_notices' ) );
	}

	/**
	 * Admin notice
	 */
	public function admin_notices() {
		?>
        <div class="error">
            <p><?php echo wp_kses(
					sprintf(
						__( '<strong>%s</strong> addon version %s requires %s version %s or higher is <strong>installed</strong> and <strong>activated</strong>.', 'learnpress-h5p' ),
						__( 'LearnPress H5P Content', 'learnpress-h5p' ),
						LP_ADDON_H5P_VER,
						sprintf( '<a href="%s" target="_blank"><strong>%s</strong></a>', admin_url( 'plugin-install.php?tab=search&type=term&s=learnpress' ), __( 'LearnPress', 'learnpress-h5p' ) ),
						LP_ADDON_H5P_REQUIRE_VER
					),
					array(
						'a'      => array(
							'href'  => array(),
							'blank' => array()
						),
						'strong' => array()
					)
				); ?>
            </p>
        </div>
		<?php
	}
}

new LP_Addon_H5p_Preload();