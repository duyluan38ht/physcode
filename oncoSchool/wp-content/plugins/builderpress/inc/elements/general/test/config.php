<?php
/**
 * BuilderPress Accordion config class
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

if ( ! class_exists( 'BuilderPress_Config_Test' ) ) {
	/**
	 * Class BuilderPress_Config_Test
	 */
	class BuilderPress_Config_Test extends BuilderPress_Abstract_Config {

		/**
		 * BuilderPress_Config_Test constructor.
		 */
		public function __construct() {
			// info
			self::$base = 'test';
			self::$name = __( 'Test', 'builderpress' );
			self::$desc = __( 'Display accordion', 'builderpress' );

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

                        'layout-1'   => self::$assets_url . 'images/layouts/layout-1.png',
                        'layout-3'   => self::$assets_url . 'images/layouts/layout-3.png',
                        'layout-tab' => self::$assets_url . 'images/layouts/layout-tab.png',
                        'kindergarten-layout-1' => self::$assets_url . 'images/layouts/kindergarten-layout-1.jpg',
                    ),
                    'std'         => 'layout-1',
                    'description' => __( 'Select type of style.', 'builderpress' )
                ),

				array(
					'type'       => 'param_group',
					'heading'    => __( 'Accordion', 'builderpress' ),
					'param_name' => 'accordion',
					'params'     => array_merge(
                        $this->_icon_options(),
						array(
                            array(
                                'type'       => 'textfield',
                                'heading'    => esc_html__( 'Title', 'builderpress' ),
                                'param_name' => 'title'
                            ),
                            array(
                                'type'       => 'textarea',
                                'heading'    => esc_html__( 'Content', 'builderpress' ),
                                'param_name' => 'content'
                            ),
                            array(
                                'type'             => 'checkbox',
                                'heading'          => esc_html__( 'Hidden icon', 'builderpress' ),
                                'description'      => __( 'Hidden icon', 'builderpress' ),
                                'param_name'       => 'hidden_icon',
                                'std'              => false,
                                'admin_label'      => true,
                                'edit_field_class' => 'vc_col-xs-6'
                            ),
                            array(
                                'type'       => 'colorpicker',
                                'heading'    => esc_html__('Color Border and Icon', 'builderpress'),
                                'param_name' => 'color',
                                'description'=> __('Display color of border and color of icon'),
                                'edit_field_class' => 'vc_col-xs-6'
                            )
                        )
					),
                    'dependency' => array(
                        'element' => 'layout',
                        'value'   => array('layout-1', 'layout-3', 'kindergarten-layout-1'),
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
                    'type'       => 'param_group',
                    'heading'    => __( 'Tabs', 'builderpress' ),
                    'param_name' => 'accordion_tab',
                    'params'     => array(
                        array(
                            'type'       => 'textfield',
                            'heading'    => esc_html__( 'Tab Title', 'builderpress' ),
                            'param_name' => 'tab_title'
                        ),
                        array(
                            'type'       => 'param_group',
                            'heading'    => __( 'Accordion', 'builderpress' ),
                            'param_name' => 'accordion_item',
                            'params'     => array(
                                array(
                                    'type'       => 'textfield',
                                    'heading'    => esc_html__( 'Tab Title', 'builderpress' ),
                                    'param_name' => 'title'
                                ),
                                array(
                                    'type'       => 'textarea',
                                    'heading'    => esc_html__( 'Content', 'builderpress' ),
                                    'param_name' => 'content'
                                )
                            )
                        ),

                    ),
                    'dependency' => array(
                        'element' => 'layout',
                        'value'   => 'layout-tab',
                    ),
                ),

                array(
                    'type' => 'css_editor',
                    'heading' => __( 'CSS Shortcode', 'js_composer' ),
                    'param_name' => 'bp_css',
                    'group' => __( 'Design Options', 'js_composer' ),
                ),
			);
		}

		/**
		 * @return array|mixed
		 */
		public function get_styles() {
			return array(
				'accordion' => array(
					'src' => 'accordion.css'
				)
			);
		}

		/**
		 * @return array|mixed
		 */
		public function get_scripts() {
			return array(
				'accordion' => array(
					'src'  => 'accordion.js',
					'deps' => array(
						'jquery'
					)
				)
			);
		}
	}
}