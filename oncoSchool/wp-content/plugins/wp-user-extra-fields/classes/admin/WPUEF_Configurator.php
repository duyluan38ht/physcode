<?php class WPUEF_Configurator
{
	public function __construct()
	{
	}
	public function render_page()
	{
		global $wpuef_option_model, $wpuef_user_model, $wpuef_woocommerce_is_active, $wpuef_buddypress_is_active;
		$fields_json_string = $wpuef_option_model->get_option('json_fields_string', false);
		
		//wpuef_var_dump(json_decode(stripcslashes($fields_json_string))); 
		//wpuef_var_dump(wpuef_get_field('c5',3)); 
		// $fields_json->fields->array{}->woocommerce_visible_on_checkout
		
		wp_enqueue_style('wpuef-vendor',  WPUEF_PLUGIN_PATH.'/css/vendor.css');
		wp_enqueue_style('wpuef-formbuilder',  WPUEF_PLUGIN_PATH.'/css/formbuilder.css');
		wp_enqueue_style('wpuef-configurator',  WPUEF_PLUGIN_PATH.'/css/wpuef-configurator.css');
		
		wp_enqueue_script( 'wpuef-vendor', WPUEF_PLUGIN_PATH.'/js/vendor.js', array( 'jquery' ));
		wp_enqueue_script( 'wpuef-configurator', WPUEF_PLUGIN_PATH.'/js/wpuef-configurator.js', array( 'jquery' ));
		
		wp_register_script( 'wpuef-formbuilder', WPUEF_PLUGIN_PATH.'/js/formbuilder.js', array( 'jquery' ));
		$js_options = array(
			'roles' => $wpuef_user_model->get_roles()
		);
		wp_localize_script( 'wpuef-formbuilder', 'wpuef_values', $js_options );
		wp_enqueue_script( 'wpuef-formbuilder' );
		
		?>
		<div class="wrap">
			<h2><?php _e('Extra fields configuration', 'wp-user-extra-fields');?></h2>
			<?php //settings_fields('wppas_general_options_group'); ?> 
			<div id="white-box">
		
				
				<div id='formbuilder'></div>
			</div>
		</div>
		<script>
		var woocommerce_is_active = <?php echo $wpuef_woocommerce_is_active ? 'true':'false'; ?>;
		var buddypress_is_active = <?php echo $wpuef_buddypress_is_active ? 'true':'false'; ?>;
		var fields_json_string = "<?php echo $fields_json_string; ?>"; 
		</script>
		<?php 
	}
}
?>