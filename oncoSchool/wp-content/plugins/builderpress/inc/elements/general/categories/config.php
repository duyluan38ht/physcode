<?php
/**
 * BuilderPress Posts config class
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

if ( ! class_exists( 'BuilderPress_Config_Categories' ) ) {
	/**
	 * Class BuilderPress_Config_Categories
	 */
	class BuilderPress_Config_Categories extends BuilderPress_Abstract_Config {

		/**
		 * BuilderPress_Config_Categories constructor.
		 */
		public function __construct() {
			// info
			self::$base = 'categories';
			self::$name = __( 'Categories', 'builderpress' );
			self::$desc = __( 'Display list categories', 'builderpress' );

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
                        'vblog-layout-sidebar'   => self::$assets_url . 'images/layouts/vblog-layout-sidebar.png',
                        'layout-list-1'   => self::$assets_url . 'images/layouts/vblog-layout-footer-1.png',
                    ),
                    'description' => __( 'Select type of style.', 'builderpress' )
                ),

				array(
					'type'        => 'textfield',
					'heading'     => esc_html__( 'Title', 'builderpress' ),
					'param_name'  => 'title',
					'admin_label' => true
				),

                array(
                    'type'             => 'number',
                    'heading'          => esc_html__( 'Number of categories', 'builderpress' ),
                    'param_name'       => 'number',
                    'std'              => '5',
                    'edit_field_class' => 'vc_col-xs-6'
                ),

                array(
                    'type'             => 'dropdown',
                    'heading'          => esc_html__( 'Category', 'builderpress' ),
                    'param_name'       => 'category',
                    'value'            => $this->_post_type_categories( 'category' ),
                    'edit_field_class' => 'vc_col-xs-6'
                ),
                array(
                    'type'             => 'checkbox',
                    'heading'          => esc_html__( 'Show post counts', 'builderpress' ),
                    'param_name'       => 'show_post_count',
                    'std'              => true,
                    'dependency' => array(
                        'element' => 'layout',
                        'value'   => array(
                            'layout-list-1'
                        ),
                    ),
                    'edit_field_class' => 'vc_col-xs-6'
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

                array(
                    'type' => 'css_editor',
                    'heading' => __( 'CSS Shortcode', 'js_composer' ),
                    'param_name' => 'bp_css',
                    'group' => __( 'Design Options', 'js_composer' ),
                )
			);
		}

		/**
		 * @return array|mixed
		 */
		public function get_styles() {
			return array(
				'categories' => array(
					'src' => 'categories.css'
				)
			);
		}

	}
}