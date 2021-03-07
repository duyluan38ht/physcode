<?php 
class WPUEF_Settings
{
	public function __construct()
	{
	}
	public function render_page()
	{
		 global $wpuef_option_model, $wpuef_woocommerce_is_active,  $wpuef_wpml_helper,$wpuef_user_model;
		
		if(isset($_POST['wpuef_general_options']))
			$wpuef_option_model->save_general_options($_POST['wpuef_general_options']); 
		
		if(isset($_FILES['wpuef_import_settings_file']))
			$wpuef_option_model->import_settings_file($_FILES['wpuef_import_settings_file']); 
		if(isset($_POST['wpuef_reset_all_fields']))
			$wpuef_user_model->reset_field_contents_for_all_users();
		
		//$options = $wpuef_option_model->get_general_options();
		$woocommerce_checkout_html_custom_snippets = $wpuef_option_model->get_woocommerce_checkout_html_custom_snippets();
		$woocommerce_checkout_page_positioning = $wpuef_option_model->get_woocommerce_checkout_page_positioning();
		$woocommerce_admin_order_page_show_extra_fields = $wpuef_option_model->get_woocommerce_admin_order_page_show_extra_fields();
		$woocommerce_register_page_positioning = $wpuef_option_model->get_woocommerce_register_page_positioning();
		$woocommerce_user_account_pages_positioning = $wpuef_option_model->get_woocommerce_user_account_pages_positioning();
		$file_upload_remove_special_characters = $wpuef_option_model->remove_special_chars_from_file_name();
		$date_format = $wpuef_option_model->get_date_format();//isset($options['date_format']) ? $options['date_format'] : "dd/mm/yyyy";
		
		/* wp_enqueue_style( 'wpuef-common', WPUEF_PLUGIN_PATH.'/css/wpuef_common.css'); */
		wp_enqueue_style( 'wpuef-admin', WPUEF_PLUGIN_PATH.'/css/wpuef-settings.css');		
		wp_register_script( 'wpuef-admin', WPUEF_PLUGIN_PATH.'/js/wpuef-backend-settings-page.js', array('jquery'));		
		$translation_array = array(
				'reset_confirmation_message' => __( 'Are you sure to want to reset extra fields content for all users?', 'wp-user-extra-fields' )
			);
		wp_localize_script( 'wpuef-admin', 'wpuef', $translation_array );
		wp_enqueue_script( 'wpuef-admin');
		//$order_statuses = wc_get_order_statuses();
		
		?>
		
		<div class="wrap white-box">
				
			<?php //screen_icon("options-general"); ?>
			<h2><?php _e('General options', 'wp-user-extra-fields');?></h2>
			<form action="" method="post" enctype="multipart/form-data" id="wpuef_settings_form"> <!--options.php -->
			<?php settings_fields('wpuef_general_options_group'); ?> 
				
				<h3><?php _e('Date format', 'wp-user-extra-fields');?></h3>
				<label><?php _e('Select the date format used to display dates in input fields', 'wp-user-extra-fields'); ?></label>
				<select name="wpuef_general_options[date_format]">
					<option value="dd/mm/yyyy" <?php if($date_format == "dd/mm/yyyy") echo 'selected="selected"';?>><?php _e('dd/mm/yyyy', 'wp-user-extra-fields');?></option>
					<option value="mm/dd/yyyy" <?php if($date_format == "mm/dd/yyyy") echo 'selected="selected"';?>><?php _e('mm/dd/yyyy', 'wp-user-extra-fields');?></option>
					<option value="yyyy/mm/dd" <?php if($date_format == "yyyy/mm/dd") echo 'selected="selected"';?>><?php _e('yyyy/mm/dd', 'wp-user-extra-fields');?></option>
					<option value="dd.mm.yyyy" <?php if($date_format == "dd.mm.yyyy") echo 'selected="selected"';?>><?php _e('dd.mm.yyyy', 'wp-user-extra-fields');?></option>
					<option value="mm.dd.yyyy" <?php if($date_format == "mm.dd.yyyy") echo 'selected="selected"';?>><?php _e('mm.dd.yyyy', 'wp-user-extra-fields');?></option>
					<option value="yyyy.mm.dd" <?php if($date_format == "yyyy.mm.dd") echo 'selected="selected"';?>><?php _e('yyyy.mm.dd', 'wp-user-extra-fields');?></option>
					<option value="dd-mm-yyyy" <?php if($date_format == "dd-mm-yyyy") echo 'selected="selected"';?>><?php _e('dd-mm-yyyy', 'wp-user-extra-fields');?></option>
					<option value="mm-dd-yyyy" <?php if($date_format == "mm-dd-yyyy") echo 'selected="selected"';?>><?php _e('mm-dd-yyyy', 'wp-user-extra-fields');?></option>
					<option value="yyyy-mm-dd" <?php if($date_format == "yyyy-mm-dd") echo 'selected="selected"';?>><?php _e('yyyy-mm-dd', 'wp-user-extra-fields');?></option>
				</select>
				
				<?php if($wpuef_woocommerce_is_active): ?>
					<h3><?php _e('WooCommerce registration page field positioning', 'wp-user-extra-fields');?></h3>
					<label><?php _e('Select the extra fields form positioning in WooCommerce registration page', 'wp-user-extra-fields'); ?></label>
					<select name="wpuef_general_options[woocommerce_register_page_positioning]">
						<option value="register_form" <?php if($woocommerce_register_page_positioning == "register_form") echo 'selected="selected"';?>><?php _e('After native fields', 'wp-user-extra-fields');?></option>
						<option value="woocommerce_register_form_start" <?php if($woocommerce_register_page_positioning == "woocommerce_register_form_start") echo 'selected="selected"';?>><?php _e('Before native field', 'wp-user-extra-fields');?></option>
					</select>
				
					<h3><?php _e('WooCommerce account details field positioning', 'wp-user-extra-fields');?></h3>
					<label><?php _e('Select the extra fields form positioning in WooCommerce account details page', 'wp-user-extra-fields'); ?></label>
					<select name="wpuef_general_options[woocommerce_account_details_page_positioning]">
						<option value="woocommerce_edit_account_form" <?php if($woocommerce_user_account_pages_positioning['woocommerce_account_details_page_positioning'] == "woocommerce_edit_account_form") echo 'selected="selected"';?>><?php _e('After native field', 'wp-user-extra-fields');?></option>
						<option value="woocommerce_before_edit_account_form" <?php if($woocommerce_user_account_pages_positioning['woocommerce_account_details_page_positioning'] == "woocommerce_before_edit_account_form") echo 'selected="selected"';?>><?php _e('Before native field', 'wp-user-extra-fields');?></option>
					</select>
				
					<h3><?php _e('WooCommerce billing details field positioning', 'wp-user-extra-fields');?></h3>
					<label><?php _e('Select the extra fields form positioning in WooCommerce billing details page', 'wp-user-extra-fields'); ?></label>
					<select name="wpuef_general_options[woocommerce_billing_details_page_positioning]">
						<option value="woocommerce_after_edit_address_form_billing" <?php if($woocommerce_user_account_pages_positioning['woocommerce_billing_details_page_positioning'] == "woocommerce_after_edit_address_form_billing") echo 'selected="selected"';?>><?php _e('After native field', 'wp-user-extra-fields');?></option>
						<option value="woocommerce_before_edit_address_form_billing" <?php if($woocommerce_user_account_pages_positioning['woocommerce_billing_details_page_positioning'] == "woocommerce_before_edit_address_form_billing") echo 'selected="selected"';?>><?php _e('Before native field', 'wp-user-extra-fields');?></option>
					</select>
				
					<h3><?php _e('WooCommerce shipping details field positioning', 'wp-user-extra-fields');?></h3>
					<label><?php _e('Select the extra fields form positioning in WooCommerce shipping details page', 'wp-user-extra-fields'); ?></label>
					<select name="wpuef_general_options[woocommerce_shipping_details_page_positioning]">
						<option value="woocommerce_after_edit_address_form_shipping" <?php if($woocommerce_user_account_pages_positioning['woocommerce_shipping_details_page_positioning'] == "woocommerce_after_edit_address_form_shipping") echo 'selected="selected"';?>><?php _e('After native field', 'wp-user-extra-fields');?></option>
						<option value="woocommerce_before_edit_address_form_shipping" <?php if($woocommerce_user_account_pages_positioning['woocommerce_shipping_details_page_positioning'] == "woocommerce_before_edit_address_form_shipping") echo 'selected="selected"';?>><?php _e('Before native field', 'wp-user-extra-fields');?></option>
					</select>
					
					<h3><?php _e('WooCommerce admin order details page', 'wp-user-extra-fields');?></h3>
					<label><?php _e('Show extra user fields', 'wp-user-extra-fields'); ?></label>
					<select name="wpuef_general_options[woocommerce_admin_order_page_show_extra_fields]">
						<option value="true" <?php if($woocommerce_admin_order_page_show_extra_fields) echo 'selected="selected"';?>><?php _e('Show', 'wp-user-extra-fields');?></option>
						<option value="false" <?php if(!$woocommerce_admin_order_page_show_extra_fields) echo 'selected="selected"';?>><?php _e('Hide', 'wp-user-extra-fields');?></option>
						
					</select>
					
					<h3><?php _e('WooCommerce checkout page - extra fields form positioning', 'wp-user-extra-fields');?></h3>
					<label><?php _e('Select the extra fields form in checkout page', 'wp-user-extra-fields'); ?></label>
					<select name="wpuef_general_options[woocommerce_checkout_page_positioning]">
						<option value="woocommerce_before_checkout_billing_form" <?php if($woocommerce_checkout_page_positioning == "woocommerce_before_checkout_billing_form") echo 'selected="selected"';?>><?php _e('Before billing form', 'wp-user-extra-fields');?></option>
						<option value="woocommerce_after_checkout_billing_form" <?php if($woocommerce_checkout_page_positioning == "woocommerce_after_checkout_billing_form") echo 'selected="selected"';?>><?php _e('After billing form', 'wp-user-extra-fields');?></option>
						<option value="woocommerce_before_checkout_shipping_form" <?php if($woocommerce_checkout_page_positioning == "woocommerce_before_checkout_shipping_form") echo 'selected="selected"';?>><?php _e('Before shipping form (Note: shipping form must be visible)', 'wp-user-extra-fields');?></option>
						<option value="woocommerce_after_checkout_shipping_form" <?php if($woocommerce_checkout_page_positioning == "woocommerce_after_checkout_shipping_form") echo 'selected="selected"';?>><?php _e('After shipping form (Note: shipping form must be visible)', 'wp-user-extra-fields');?></option>
						<option value="woocommerce_before_order_notes" <?php if($woocommerce_checkout_page_positioning == "woocommerce_before_order_notes") echo 'selected="selected"';?>><?php _e('Before order notes', 'wp-user-extra-fields');?></option>
						<option value="woocommerce_after_order_notes" <?php if($woocommerce_checkout_page_positioning == "woocommerce_after_order_notes") echo 'selected="selected"';?>><?php _e('After order notes', 'wp-user-extra-fields');?></option>
					</select>
					
					
					<h3><?php _e('WooCommerce checkout page - HTML custom snippet before extra fields form', 'wp-user-extra-fields');?></h3>
					<label><?php _e('Use this field to inject data before the extra fields checkout form. It can contain a specia title, description, etc.', 'wp-user-extra-fields'); ?></label>
					<?php if($wpuef_wpml_helper->is_active()):?>
						<p><?php _e('<strong>TRANSLATION NOTE:</strong> To translate the following content in for each installed language, use the WPML upper menu, switch language then save.', 'wp-user-extra-fields'); ?></p>
					<?php endif; ?>
					<textarea rows="6" cols="100" name="wpuef_general_options[woocommerce_checkout_page_before_extra_fields_html_snippet][<?php echo $wpuef_wpml_helper->get_current_language()?>]"><?php echo $woocommerce_checkout_html_custom_snippets['woocommerce_checkout_page_before_extra_fields_html_snippet'];?></textarea>
					
					
					<h3><?php _e('WooCommerce checkout page - HTML custom snippet after extra fields form', 'wp-user-extra-fields');?></h3>
					<label><?php _e('Use this field to inject data aftert he extra fields checkout form. It can contain a specia title, description, etc.', 'wp-user-extra-fields'); ?></label>
					<?php if($wpuef_wpml_helper->is_active()):?>
						<p><?php _e('<strong>TRANSLATION NOTE:</strong> To translate the following content in for each installed language, use the WPML upper menu, switch language then save.', 'wp-user-extra-fields'); ?></p>
					<?php endif; ?>
					<textarea rows="6" cols="100" name="wpuef_general_options[woocommerce_checkout_page_after_extra_fields_html_snippet][<?php echo $wpuef_wpml_helper->get_current_language()?>]"><?php echo $woocommerce_checkout_html_custom_snippets['woocommerce_checkout_page_after_extra_fields_html_snippet'];?></textarea>
				<?php endif; ?>
				
				<h3><?php _e('File upload settings', 'wp-user-extra-fields');?></h3>
				<label><?php _e('Remove all characters for exception of the alphanumeric and underscore characters from file name.', 'wp-user-extra-fields');?></label>
				<select name="wpuef_general_options[file_upload_remove_special_characters]" >
					<option value ="yes" <?php if($file_upload_remove_special_characters) echo 'selected="selected" '; ?>><?php _e('Remove special characters', 'wp-user-extra-fields'); ?></option>
					<option value = "no" <?php if(!$file_upload_remove_special_characters) echo 'selected="selected" '; ?>><?php _e('Do not remove special characters', 'wp-user-extra-fields'); ?></option>
				</select>
				
				<h3><?php _e('Export settings', 'wp-user-extra-fields');?></h3>
				<a class="button" href="<?php echo admin_url();?>?wpuef_create_export_file=true" target="_blank"><?php _e('Export', 'wp-user-extra-fields'); ?></a>
				
				<h3><?php _e('Import settings', 'wp-user-extra-fields');?></h3>
				<p><?php _e('Select a file then click on the <strong>Save Changes</strong> button to import data.', 'wp-user-extra-fields'); ?></p>
				<input type="file" name="wpuef_import_settings_file"></input>
				<p><?php _e('<strong>NOTE:</strong> To import and export user specific extra field values, you have to use the following plugin:', 'wp-user-extra-fields');?> <a target="_blank" href="https://codecanyon.net/item/woocommerce-customers-manager/10965432">WooCommerce Customers Manager</a></p>
				
				<h3 id="wpuef_reset_all_fields_title"><?php _e('Reset extra fields content for ALL users', 'wp-user-extra-fields');?></h3>
				<p><?php _e('Checking the following option and clicking on the <strong>Save Changes</strong> button will reset extra user fields content for ALL users.', 'wp-user-extra-fields'); ?></p>
				<input type="checkbox" name="wpuef_reset_all_fields" id="wpuef_reset_all_fields"><?php _e('Reset', 'wp-user-extra-fields'); ?></input>
				
				
				<p class="submit" id="wpuef_submit_button_container">
					<input  name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes', 'wp-user-extra-fields'); ?>" />
				</p>
			</fom>
		</div>
		<?php
	}
}
?>