<?php
/**
 * BuilderPress Gallery-images config class
 *
 * @version     1.0.0
 * @author      ThimPress
 * @package     BuilderPress/Classes
 * @category    Classes
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'BuilderPress_Config_Gallery_Images' ) ) {
	/**
	 * Class BuilderPress_Config_Gallery_Images
	 */
	class BuilderPress_Config_Gallery_Images extends BuilderPress_Abstract_Config {

		/**
		 * BuilderPress_Config_Gallery_Images constructor.
		 */
		public function __construct() {
			// info
			self::$base = 'gallery-images';
			self::$name = __( 'Gallery Images', 'builderpress' );
			self::$desc = __( 'Display gallery images', 'builderpress' );

			parent::__construct();
		}

		/**
		 * @return array
		 */
		public function get_options() {

			// options
			return array(
                array(
                    'type'        => 'radio_image',
                    'heading'     => __( 'Layout', 'builderpress' ),
                    'param_name'  => 'layout',
                    'options'     => array(
                        'layout-1'   => self::$assets_url . 'images/layout-1.jpg',
                        'layout-2'   => self::$assets_url . 'images/layout-2.jpg',
                        'layout-3'   => self::$assets_url . 'images/layout-3.jpg',
                        'layout-4'   => self::$assets_url . 'images/layout-4.jpg',
                        'layout-5'   => self::$assets_url . 'images/layout-3.jpg',
                        'vblog-layout-sidebar'   => self::$assets_url . 'images/vblog-layout-sidebar.png',
                        'kindergarten-layout-1'   => self::$assets_url . 'images/kindergarten-layout-1.jpg',
                        'kindergarten-layout-2'   => self::$assets_url . 'images/marketing-layout-grid-1.jpg',
                    ),
                    'std'         => 'layout-1',
                    'description' => __( 'Select type of style.', 'builderpress' )
                ),
                array(
                    'type'        => 'textfield',
                    'heading'     => __( 'Title', 'builderpress' ),
                    'param_name'  => 'title',
                    'std'         => __( 'Work with Passion', 'builderpress' ),
                    'admin_label' => true,
                    'dependency'       => array(
                        'element' => 'layout',
                        'value'   => array(
                            'vblog-layout-sidebar',
                        )
                    ),
                ),
                array(
                    'type'       => 'attach_images',
                    'heading'    => esc_html__( 'Photos', 'builderpress' ),
                    'param_name' => 'photos',
                ),
                array(
                    'type'        => 'textfield',
                    'heading'     => __( 'Image Size', 'builderpress' ),
                    'param_name'  => 'img_size',
                    'description' => esc_html__('Enter image sizes defined by theme). Alternatively enter size in pixels (Example: "607x295,294x295" (Width x Height)).','pizza-hut'),
                    'admin_label' => true,
                    'std'         => '607x295,294x295',
                ),
                array(
                    'type'             => 'dropdown',
                    'heading'          => __( 'Style Layout', 'builderpress' ),
                    'param_name'       => 'style_layout',
                    'value'            => array(
                        __( 'Style Default', 'builderpress' )   => '',
                    ),
                    'std'              => '',
                    'edit_field_class' => 'vc_col-sm-6'
                ),
            );
		}

		/**
		 * @return array|mixed
		 */
		public function get_styles() {
			return array(
				'gallery-images' => array(
					'src' => 'gallery-images.css'
				)
			);
		}

		/**
		 * @return array|mixed
		 */
		public function get_scripts() {
			return array(
				'gallery-images' => array(
					'src'  => 'gallery-images.js',
                    'deps' => array(
                        'jquery',
                        'builder-press-isotope',
                        'builder-press-packery-mode',
                        'builder-press-magnific-popup'
                    )
				)
			);
		}
	}
}