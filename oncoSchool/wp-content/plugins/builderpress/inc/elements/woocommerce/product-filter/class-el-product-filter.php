<?php
/**
 * BuilderPress Elementor Product-filter widget
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

if ( ! class_exists( 'BuilderPress_El_Product_Filter' ) ) {
	/**
	 * Class BuilderPress_El_Product-filter
	 */
	class BuilderPress_El_Product_Filter extends BuilderPress_El_Widget {

		/**
		 * @var string
		 */
		protected $config_class = 'BuilderPress_Config_Product_Filter';

		/**
		 * Register controls.
		 */
		protected function _register_controls() {
			$this->start_controls_section(
				'product-filter', [ 'label' => esc_html__( 'Product Filter', 'builderpress' ) ]
			);

			$controls = \BuilderPress_El_Mapping::mapping( $this->options() );

			foreach ( $controls as $key => $control ) {
				$this->add_control( $key, $control );
			}

			$this->end_controls_section();
		}

		/**
		 * Render
		 */
		protected function render() {

			$settings = $this->get_settings_for_display();

			parent::render();

			builder_press_get_template( $this->get_name(), array( 'params' => $settings ), $this->get_group() . '/' . $this->get_name() . '/tpl/' );
		}
	}
}