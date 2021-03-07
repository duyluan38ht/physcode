<?php
/**
 * BuilderPress Product-isotope config class
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

if ( ! class_exists( 'BuilderPress_Config_Product_Isotope' ) ) {
	/**
	 * Class BuilderPress_Config_Product-isotope
	 */
	class BuilderPress_Config_Product_Isotope extends BuilderPress_Abstract_Config {

		/**
		 * BuilderPress_Config_Product-isotope constructor.
		 */
		public function __construct() {
			// info
			self::$base = 'product-isotope';
			self::$name = __( 'Product Isotope', 'builderpress' );
			self::$desc = __( 'Display product isotope', 'builderpress' );

			parent::__construct();

		}

		/**
		 * @return array
		 */
		public function get_options() {

			// options
			return array(
                array(
                    'type'        => 'dropdown',
                    'heading'     => esc_html__( 'Layout', 'builderpress' ),
                    'param_name'  => 'layout',
                    'save_always' => true,
                    'value'       => array(
                        esc_html__( 'Layout 1', 'builderpress' )    => 'layout-1',
                        esc_html__( 'Layout 2', 'builderpress' )    => 'layout-2',
                    ),
                    'std'         => 'layout-1'
                ),

                array(
                    'type'        => 'dropdown',
                    'heading'     => esc_attr__( 'Category', 'builderpress' ),
                    'param_name'  => 'category',
                    'admin_label' => true,
                    'value'       => $this->_post_type_categories( 'product_cat' ),
                    'description' => esc_attr__( 'Select product category.', 'builderpress' )
                ),
                array(
                    'type'        => 'number',
                    'heading'     => esc_html__( 'Number of product', 'builderpress' ),
                    'param_name'  => 'number',
                    'std'         => '6',
                    'admin_label' => true,
                    'edit_field_class' => 'vc_col-xs-6'
                ),

                array(
                    'type'        => 'number',
                    'heading'     => esc_html__( 'Number item of load more', 'builderpress' ),
                    'param_name'  => 'number_loadmore',
                    'std'         => '3',
                    'admin_label' => true,
                    'edit_field_class' => 'vc_col-xs-6'
                ),

                array(
                    'type'       => 'dropdown',
                    'heading'    => esc_html__( 'Number Columns', 'builderpress' ),
                    'param_name' => 'columns',
                    'value'      => array(
                        esc_attr__( '2', 'builderpress' ) => '2',
                        esc_attr__( '3', 'builderpress' ) => '3',
                        esc_attr__( '4', 'builderpress' ) => '4',
                    ),
                    'std'        => '3',
                    'edit_field_class' => 'vc_col-xs-6'
                ),
                array(
                    'type'             => 'checkbox',
                    'heading'          => esc_html__( 'Show rating', 'builderpress' ),
                    'param_name'       => 'rating',
                    'std'              => true,
                    'admin_label'      => true,
                    'edit_field_class' => 'vc_col-xs-4'
                ),
                array(
                    'type'             => 'checkbox',
                    'heading'          => esc_html__( 'Show description', 'builderpress' ),
                    'param_name'       => 'description',
                    'std'              => true,
                    'admin_label'      => true,
                    'edit_field_class' => 'vc_col-xs-4'
                ),
                array(
                    'type'             => 'checkbox',
                    'heading'          => esc_html__( 'Show price', 'builderpress' ),
                    'param_name'       => 'price',
                    'std'              => true,
                    'admin_label'      => true,
                    'edit_field_class' => 'vc_col-xs-4'
                ),
                array(
                    'type'             => 'checkbox',
                    'heading'          => esc_html__( 'Show label', 'builderpress' ),
                    'description'      => __( 'Show label likes Hot, Sale', 'builderpress' ),
                    'param_name'       => 'label',
                    'std'              => true,
                    'admin_label'      => true,
                    'edit_field_class' => 'vc_col-xs-4'
                ),
                array(
                    'type'        => 'textfield',
                    'heading'     => __( 'Image Size', 'builderpress' ),
                    'param_name'  => 'img_size',
                    'description' => esc_html__('Enter image size (Example: "thumbnail", "medium", "large", "full" or other sizes defined by theme). Alternatively enter size in pixels (Example: 200x100 (Width x Height)).','pizza-hut'),
                    'admin_label' => true,
                    'std'         => 'full',
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
                'product-isotope' => array(
                    'src'  => 'product-isotope.css',
                )
            );
        }

        /**
         * @return array|mixed
         */
        public function get_scripts() {
            return array(
                'product-isotope' => array(
                    'src'  => 'product-isotope.js',
                    'deps' => array(
                        'jquery',
                        'builder-press-isotope',
                    )
                )
            );
        }

	}
}