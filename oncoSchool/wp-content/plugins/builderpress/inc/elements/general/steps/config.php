<?php
/**
 * BuilderPress Steps config class
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

if ( ! class_exists( 'BuilderPress_Config_Steps' ) ) {
	/**
	 * Class BuilderPress_Config_Steps
	 */
	class BuilderPress_Config_Steps extends BuilderPress_Abstract_Config {

		/**
		 * BuilderPress_Config_Steps constructor.
		 */
		public function __construct() {
			// info
			self::$base = 'steps';
			self::$name = __( 'Steps', 'builderpress' );
			self::$desc = __( 'Display steps', 'builderpress' );

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
                    'std'         => 'layout-1',
                ),

                array(
                    "type"        => "textfield",
                    "heading"     => esc_html__( "Step circle text", 'course-builder' ),
                    "param_name"  => "circle-text",
                    "value"       => '',
                    "description" => "Enter text in step circle. However, icon is preferred.",
                ),

                array(
                    'type'       => 'param_group',
                    'heading'    => esc_html__( 'Step', 'course-builder' ),
                    'value'      => '',
                    'param_name' => 'steps',
                    'dependency' => array(
                        'element' => 'layout',
                        'value'   => 'layout-1',
                    ),
                    'params'     => array(

                        array(
                            'type'             => 'dropdown',
                            'heading'          => esc_attr__( 'Icon type', 'builderpress' ),
                            'param_name'       => 'icon_type',
                            'admin_label'      => true,
                            'value'            => array(
                                esc_attr__( 'None', 'builderpress' )         => 'none',
                                esc_attr__( 'Font Awesome', 'builderpress' ) => 'icon_fontawesome',
                                esc_attr__( 'Ionicon', 'builderpress' )      => 'icon_ionicon',
                                esc_attr__( 'Upload Image', 'builderpress' ) => 'icon_upload',
                            ),
                            'std'              => 'icon_fontawesome',
                            'edit_field_class' => 'vc_col-sm-6',
                        ),

                        // Fontawesome Picker
                        array(
                            'type'             => 'iconpicker',
                            'heading'          => esc_attr__( 'Font Awesome', 'builderpress' ),
                            'param_name'       => 'icon_fontawesome',
                            'value'            => 'fa fa-heart',
                            'settings'         => array(
                                'emptyIcon'    => false,
                                'iconsPerPage' => 50,
                                'type'         => 'fontawesome',
                            ),
                            'dependency'       => array(
                                'element' => 'icon_type',
                                'value'   => array( 'icon_fontawesome' ),
                            ),
                            'edit_field_class' => 'vc_col-sm-6',
                            'description'      => esc_html__( 'Font awesome library.', 'builderpress' ),
                        ),

                        // Ionicon Picker
                        array(
                            'type'             => 'iconpicker',
                            'heading'          => esc_attr__( 'Ionicon', 'builderpress' ),
                            'param_name'       => 'icon_ionicon',
                            'value'            => 'ion-android-add-circle',
                            'settings'         => array(
                                'emptyIcon'    => false,
                                'iconsPerPage' => 50,
                                'type'         => 'ionicon',
                            ),
                            'dependency'       => array(
                                'element' => 'icon_type',
                                'value'   => array( 'icon_ionicon' ),
                            ),
                            'edit_field_class' => 'vc_col-sm-6',
                            'description'      => esc_html__( 'Font awesome library.', 'builderpress' ),
                        ),

                        // Upload icon image
                        array(
                            'type'             => 'attach_image',
                            'heading'          => esc_attr__( 'Upload icon', 'builderpress' ),
                            'param_name'       => 'icon_upload',
                            'admin_label'      => true,
                            'description'      => esc_attr__( 'Select an image to upload', 'builderpress' ),
                            'dependency'       => array(
                                'element' => 'icon_type',
                                'value'   => array( 'icon_upload' )
                            ),
                            'edit_field_class' => 'vc_col-sm-6',
                        ),

                        array(
                            "type"       => "textarea",
                            "heading"    => esc_html__( "Step Description", 'course-builder' ),
                            "param_name" => "description",
                        ),

                        array(
                            "type"       => "vc_link",
                            "heading"    => esc_html__( "Read more URL", 'course-builder' ),
                            "param_name" => "readmore",
                        ),

                    )
                ),

                array(
                    'type'       => 'param_group',
                    'heading'    => esc_html__( 'Step', 'course-builder' ),
                    'value'      => '',
                    'param_name' => 'steps-2',
                    'dependency' => array(
                        'element' => 'layout',
                        'value'   => 'layout-2',
                    ),
                    'params'     => array(

                        array(
                            'type'             => 'dropdown',
                            'heading'          => esc_attr__( 'Icon type', 'builderpress' ),
                            'param_name'       => 'icon_type',
                            'admin_label'      => true,
                            'value'            => array(
                                esc_attr__( 'None', 'builderpress' )         => 'none',
                                esc_attr__( 'Font Awesome', 'builderpress' ) => 'icon_fontawesome',
                                esc_attr__( 'Ionicon', 'builderpress' )      => 'icon_ionicon',
                                esc_attr__( 'Upload Image', 'builderpress' ) => 'icon_upload',
                            ),
                            'std'              => 'icon_fontawesome',
                            'edit_field_class' => 'vc_col-sm-6',
                        ),

                        // Fontawesome Picker
                        array(
                            'type'             => 'iconpicker',
                            'heading'          => esc_attr__( 'Font Awesome', 'builderpress' ),
                            'param_name'       => 'icon_fontawesome',
                            'value'            => 'fa fa-heart',
                            'settings'         => array(
                                'emptyIcon'    => false,
                                'iconsPerPage' => 50,
                                'type'         => 'fontawesome',
                            ),
                            'dependency'       => array(
                                'element' => 'icon_type',
                                'value'   => array( 'icon_fontawesome' ),
                            ),
                            'edit_field_class' => 'vc_col-sm-6',
                            'description'      => esc_html__( 'Font awesome library.', 'builderpress' ),
                        ),

                        // Ionicon Picker
                        array(
                            'type'             => 'iconpicker',
                            'heading'          => esc_attr__( 'Ionicon', 'builderpress' ),
                            'param_name'       => 'icon_ionicon',
                            'value'            => 'ion-android-add-circle',
                            'settings'         => array(
                                'emptyIcon'    => false,
                                'iconsPerPage' => 50,
                                'type'         => 'ionicon',
                            ),
                            'dependency'       => array(
                                'element' => 'icon_type',
                                'value'   => array( 'icon_ionicon' ),
                            ),
                            'edit_field_class' => 'vc_col-sm-6',
                            'description'      => esc_html__( 'Font awesome library.', 'builderpress' ),
                        ),

                        // Upload icon image
                        array(
                            'type'             => 'attach_image',
                            'heading'          => esc_attr__( 'Upload icon', 'builderpress' ),
                            'param_name'       => 'icon_upload',
                            'admin_label'      => true,
                            'description'      => esc_attr__( 'Select an image to upload', 'builderpress' ),
                            'dependency'       => array(
                                'element' => 'icon_type',
                                'value'   => array( 'icon_upload' )
                            ),
                            'edit_field_class' => 'vc_col-sm-6',
                        ),

                        array(
                            "type"       => "textfield",
                            "heading"    => esc_html__( "Step Title", 'course-builder' ),
                            "param_name" => "title",
                        ),

                        array(
                            "type"       => "textfield",
                            "heading"    => esc_html__( "Step Sub Title", 'course-builder' ),
                            "param_name" => "sub-title",
                        ),

                        array(
                            "type"       => "textarea",
                            "heading"    => esc_html__( "Step Description", 'course-builder' ),
                            "param_name" => "description",
                        ),

                    )
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
				'steps' => array(
					'src' => 'steps.css'
				)
			);
		}

		/**
		 * @return array|mixed
		 */
		public function get_scripts() {
			return array(
				'steps' => array(
					'src'  => 'steps.js',
					'deps' => array(
						'jquery'
					)
				)
			);
		}
	}
}