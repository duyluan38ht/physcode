<?php
/**
 * BuilderPress Tabs config class
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

if ( ! class_exists( 'BuilderPress_Config_Tabs' ) ) {
	/**
	 * Class BuilderPress_Config_Tabs
	 */
	class BuilderPress_Config_Tabs extends BuilderPress_Abstract_Config {

		/**
		 * BuilderPress_Config_Tabs constructor.
		 */
		public function __construct() {
			// info
			self::$base = 'tabs';
			self::$name = __( 'Tabs', 'builderpress' );
			self::$desc = __( 'Display tabs', 'builderpress' );

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
						'layout-default' => self::$assets_url . 'images/layouts/layout-default.jpg',
						'layout-step'    => self::$assets_url . 'images/layouts/layout-step.jpg',
                        'marketing-layout-slider'    => self::$assets_url . 'images/layouts/marketing-layout-slider.jpg'
					),
					'std'         => 'layout-default',
					'description' => __( 'Select type of style.', 'builderpress' )
				),
				array(
					'type'       => 'param_group',
					'heading'    => __( 'Tabs', 'builderpress' ),
					'param_name' => 'tabs',
					'params'     => array(
						array(
							'type'       => 'textfield',
							'heading'    => esc_html__( 'Title', 'builderpress' ),
							'param_name' => 'title'
						),
						array(
							'type'       => 'textarea',
							'heading'    => esc_html__( 'Content', 'builderpress' ),
							'param_name' => 'content'
						)
					)
				),
				array(
					'type'       => 'dropdown',
					'heading'    => esc_html__( 'Title position', 'builderpress' ),
					'param_name' => 'title_position',
					'value'      => array(
						esc_html__( 'Left', 'builderpress' )   => 'title-left',
						esc_html__( 'Right', 'builderpress' )  => 'title-right',
						esc_html__( 'Top', 'builderpress' )    => 'title-top',
						esc_html__( 'Bottom', 'builderpress' ) => 'title-bottom'
					),
					'std'        => 'title-left',
					'dependency' => array(
						'element' => 'layout',
						'value'   => array( 'layout-default' )
					),
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
				'tabs' => array(
					'src' => 'tabs.css'
				)
			);
		}

		/**
		 * @return array|mixed
		 */
		public function get_scripts() {
			return array(
				'tabs' => array(
					'src' => 'tabs.js'
				)
			);
		}
	}
}