<?php 
global $wpuef_option_model, $wpuef_user_model, $wpuef_wpml_helper, $wpuef_woocommerce_is_active;

//$extra_fields = json_decode(stripcslashes($fields_json_string));
$rendered_elements = 0;
?>
<?php if($render_form): ?>
	<!-- <header class="title">
		<h3><?php _e('Extra Info','wp-user-extra-fields');?></h3>
	</header> -->
	<form action="" method="post" class="wpuef_custom_form">
	<style>
	.wpuef_label
	{
		display:block;
		margin-top: 20px;
	}
	</style>
<?php endif; ?>
<div id="wpuef_required_fields_warning_message"><?php _e('Please fill all the required fields.', 'wp-user-extra-fields'); ?> </div>
<div id="wpuef-file-container" style="display:none"></div> <!--file upload -->
<?php foreach($extra_fields->fields as $extra_field): 
		if( (!isset($extra_field->invisible) || !$extra_field->invisible) &&
			/* (!isset($extra_field->woocommerce_checkout_only_editable) || !$extra_field->woocommerce_checkout_only_editable) && */
			(!isset($extra_field->visible_only_at_register_page) || !$extra_field->visible_only_at_register_page ) && 
			( //($edit_page_type == 0 && (!isset($extra_field->woocommerce_edit_on_billing_address_page) || !$extra_field->woocommerce_edit_on_billing_address_page) && (!isset($extra_field->woocommerce_edit_on_shipping_address_page) || !$extra_field->woocommerce_edit_on_shipping_address_page)) ||
			  ($edit_page_type == 0 && isset($extra_field->woocommerce_edit_on_my_account_page) && $extra_field->woocommerce_edit_on_my_account_page) ||
			  ($edit_page_type == 1 && isset($extra_field->woocommerce_edit_on_billing_address_page) && $extra_field->woocommerce_edit_on_billing_address_page) ||
			  ($edit_page_type == 2 && isset($extra_field->woocommerce_edit_on_shipping_address_page) && $extra_field->woocommerce_edit_on_shipping_address_page)
			) &&
			$wpuef_user_model->field_can_be_displayed_for_current_user($extra_field)
		  ):
		 
	$is_password_override = isset($extra_field->field_to_override) && $extra_field->field_to_override == 'password' ? true : false;
	if($is_password_override && $edit_page_type != 0)
	continue;

	$password_field_name = 'password_1';
	$password_class = '';
	$rendered_elements++;
	$required = isset($extra_field->required) && $extra_field->required ? true:false; 
	$read_only = !current_user_can( 'manage_options' ) && isset($extra_field->editable_only_by_admin) && $extra_field->editable_only_by_admin ? true : false;
	$placeholder = !isset($extra_field->field_options->placeholder) ? "": str_replace('"', "", $extra_field->field_options->placeholder);
	$one_time_upload = isset($extra_field->one_time_upload) && $extra_field->one_time_upload;
	$row_size = isset($extra_field->checkout_row_size) && $extra_field->checkout_row_size != "" ? $extra_field->checkout_row_size : 'wide';
	if($is_password_override)
		{
			$required = true;
			$password_class = 'wpuef_password';			
			//wp_enqueue_script('wpuef-reposition-my-account-password', WPUEF_PLUGIN_PATH.'/js/wpuef-reposition-my-account-password.js', array( 'jquery' ));
			wp_enqueue_script('wpuef-hide-password', WPUEF_PLUGIN_PATH.'/js/wpuef-password-register-hide.js', array( 'jquery' ));
		}
		
	if(!$read_only)
	{
		$read_only = isset($extra_field->uneditable) && $extra_field->uneditable ? true : false;
	}
	$extra_field->re_upload = !$read_only && !$one_time_upload;
	$extra_field->can_delete_file = !$read_only && !$one_time_upload;
	
	?>
	<?php if($extra_field->field_type != "country_and_state" && $extra_field->field_type != 'title_no_input'): ?>
	<p class="form-row form-row-<?php echo $row_size ;?>" id="<?php echo "wpuef-row-".$extra_field->cid; ?>">
	<?php endif; ?>
	
	<!-- label -->
	<?php if($extra_field->field_type == 'title_no_input'): 
			$tag = !isset($extra_field->title_tag) ? 'label' : $extra_field->title_tag; 
			$classes = !isset($extra_field->title_classes) ? '' : $extra_field->title_classes; 
			$margin = !isset($extra_field->title_margin) ? '' : 'margin: '.$extra_field->title_margin.";"; 
		?>
		<p class="form-row form-row-full" id="<?php echo "wpuef-row-".$extra_field->cid; ?>"><!--need to make space between elements -->
		<<?php echo $tag ?> class="wpuef_label <?php echo $classes;?>" style="<?php echo $margin; ?>"><?php echo $extra_field->label;?></<?php echo $tag ?>>
	<?php else: ?>
		<label class="wpuef_label <?php if($required && !$is_password_override) echo "wpuef_required";?>"><?php echo $extra_field->label; if($is_password_override) _e(' (leave blank to leave unchanged)','wp-user-extra-fields');?></label>
	<?php endif; 
	
		$field_value = $wpuef_user_model->get_field( $extra_field->cid, $user_id );
		//wpuef_var_dump($field_value);
		//Types
		if($extra_field->field_type == "dropdown"): 
		//wpuef_var_dump($field_value);
		//wpuef_var_dump($extra_field);?>
		<select class="wpuef_input_select" id="<?php echo "wpuef_field_".$extra_field->cid; ?>" name="wpuef_options[<?php echo $extra_field->cid; ?>]" <?php if($read_only) echo ' disabled="disabled"'; if($extra_field->required) echo 'required="required"';?>>
			<?php if($extra_field->field_options->include_blank_option): ?>
			   <option value=""><?php echo $placeholder; ?> </option>
			<?php endif; 
				foreach($extra_field->field_options->options as $index => $extra_option): ?>
			  <option value="<?php echo $index; ?>" <?php if((isset($field_value) && $field_value != "" && $field_value == $index) || (isset($field_value) && $extra_option->checked && !$extra_field->field_options->include_blank_option)) echo 'selected';?>><?php echo $extra_option->label; ?></option>
			<?php endforeach; ?>
		</select>
	
	<?php elseif($extra_field->field_type == "country_and_state"): 
			  global $wpuef_country_model;
			  $countries = array("" => __('Select one','wp-user-extra-fields'));
			  $country_list = $wpuef_country_model->get_countries(isset($extra_field->coutries_to_show) ? $extra_field->coutries_to_show : null);
			  reset($country_list);
			  $first_country_code = key($country_list);
			  foreach((array)$country_list as $country_code => $country_name)
						$countries[$country_code] = $country_name;
			  $show_state = !isset($extra_field->show_state) || $extra_field->show_state != 'no' ? true : false;
			  
			   //defaults
			  if(isset($extra_field->default_country))
				$field_value["country"] = isset($field_value["country"]) && $field_value["country"] != "" ? $field_value["country"] : strtoupper($extra_field->default_country);
			  if(isset($extra_field->default_state))
				$field_value["state"] = isset($field_value["state"]) && $field_value["state"] != "" ? $field_value["state"] : strtoupper($extra_field->default_state);
			
			  //js
			  if($show_state)
			  {
				  wp_enqueue_script('wpuef-country-state-manager', WPUEF_PLUGIN_PATH."/js/wpuef-country-state-manager.js", array('jquery'));
				  $js_vars = array(
						'ajax_url' => admin_url('admin-ajax.php'),
					);
					wp_localize_script( 'wpuef-country-state-manager', 'wpuef', $js_vars );
			  }
			  $custom_attributes = array('data-id'=>$extra_field->cid, 'data-required'=> $required ? 'true' : 'false');
			  if($required)
				   $custom_attributes['required'] = 'required'; 
			  if($read_only)
				  $custom_attributes['disabled'] = 'disabled';
			  if(count($country_list) > 1)
				  woocommerce_form_field('wpuef_options['. $extra_field->cid.'][country]', array(
									'type'       => 'select',
									'class'      =>  $show_state ? array( 'form-row-first', 'select2-container' ) :  array( 'form-row-wide', 'select2-container' ),
									'input_class' => array('select2-choice', 'wpuef_country_selector'),
									//'label'      => __('Select a country','wp-user-extra-fields'),
									'label_class' => array( 'wcmca_form_label' ),
									'custom_attributes' =>  array('data-id'=>$extra_field->cid),
									//placeholder'    => __('Select a country','wp-user-extra-fields'),
									'required' => $required,
									'custom_attributes' =>  $custom_attributes,
									'options'    => $countries,
									'default' => isset($field_value["country"]) ? $field_value["country"] : ""
										)
									);
									
			  else 
			  {
				  $field_value["country"] = $first_country_code;
				  echo '<input type="hidden" name="wpuef_options['. $extra_field->cid.'][country]" value="'.$first_country_code.'"></input>';
			  }
		?>
		<div id="wpuef_country_field_container_<?php echo $extra_field->cid; ?>">
		<?php 
		if($show_state && isset($field_value["country"]) && $field_value["country"] != "")
		{
			$wpuef_country_model->render_state_select_html_by_country($field_value["country"], $extra_field->cid, isset($field_value["state"]) ? $field_value["state"] : null, $required, $read_only, count($country_list) == 1);
		}
		?>
		</div>
		<img class="wpuef_preloader_image"id="wpuef_preloader_image_<?php echo $extra_field->cid; ?>" src="<?php echo WPUEF_PLUGIN_PATH.'/img/loader.gif' ?>" ></img>
			
	<?php elseif($extra_field->field_type == "file"): ?>
		<span id="wpuef-file-box-<?php echo $extra_field->cid; ?>"> <!--  //file upload edit -->
		<?php  //wpuef_var_dump($extra_field);
			if(isset($field_value) && isset($field_value["url"])): 
				$required = false; ?> 
				<?php if(isset($extra_field->image_preview) && wpuef_is_image($field_value["url"])): ?>
						<img class="wpuef_image_preview" src="<?php echo $field_value["url"]; ?>" width="<?php if(isset($extra_field->preview_width) && $extra_field->preview_width != "") echo $extra_field->preview_width; else echo 120;?>" /> 
				<?php endif; ?>
				<button class="button button-primary wpuef_view_download_file_button" target="_blank" data-href="<?php echo $field_value["url"]; ?>"><?php _e('Download / View', 'wp-user-extra-fields') ?></button> 
			<?php endif; 
			if(isset($field_value) && isset($field_value["url"]) &&  (isset($extra_field->can_delete_file) && $extra_field->can_delete_file ) ): ?> 	
				<button class="button wpuef_delete_file_button" target="_blank" data-id="<?php echo $extra_field->cid; ?>" ><?php _e('Delete', 'wp-user-extra-fields') ?></button><br/>
			<?php endif; 
			if (!$read_only && (!isset($field_value) || (isset($extra_field->re_upload) && $extra_field->re_upload && isset($field_value)))): ?>
				<span class="wpuef_file_uploader_container">
					<input class="wpuef_input_file input-text" 
							type="file" 
							value=""  
							id="wpuef_upload_field_<?php echo $extra_field->cid; ?>"
						   data-id="<?php echo $extra_field->cid; ?>"  
							data-upload-button-id="#wpuef_file_upload_button_<?php echo $extra_field->cid; ?>"
						   <?php if($required) echo 'required="required"';?> 
						   <?php if(isset($extra_field->file_types) && $extra_field->file_types) echo 'accept="'.$extra_field->file_types.'"';?> 
						   data-size="<?php if(isset($extra_field->file_size) && $extra_field->file_size) echo $extra_field->file_size*1048576; ?>" 
						   value="<?php if(isset($extra_field->file_size) && $extra_field->file_size) echo $extra_field->file_size*1048576; ?>">
						   </input>
						   <strong><?php if(isset($extra_field->file_size) && $extra_field->file_size) echo __('( Max size: ', 'wp-user-extra-fields').$extra_field->file_size." MB )"; ?></strong>
					<input type="hidden" id="wpuef-filename-<?php echo $extra_field->cid; ?>" name="wpuef_files[<?php echo $extra_field->cid; ?>][file_name]" value=""></input>
					<input type="hidden" id="wpuef-filenameprefix-<?php echo $extra_field->cid; ?>" name="wpuef_files[<?php echo $extra_field->cid; ?>][file_name_tmp_prefix]" value=""></input>
					<?php if(isset($extra_field->image_preview)): ?>
						<span id="wpuef-file-preview-<?php echo $extra_field->cid; ?>" class="wpuef_tmp_image_preview" data-width="<?php if(isset($extra_field->preview_width) && $extra_field->preview_width != "") echo $extra_field->preview_width; else echo 120;?>"></span>
					<?php endif; ?>
					<!-- Upload button -->
					<button class="button wpuef_file_upload_button"  
							id="wpuef_file_upload_button_<?php echo $extra_field->cid; ?>"
						   data-id="<?php echo $extra_field->cid; ?>"  
						   data-upload-field-id="#wpuef_upload_field_<?php echo $extra_field->cid; ?>"><?php _e('Upload', 'wp-user-extra-fields') ?></button>
					<button class="button wpuef_file_tmp_delete_button"  
							id="wpuef_file_tmp_delete_button_<?php echo $extra_field->cid; ?>"
						   data-id="<?php echo $extra_field->cid; ?>"  
						   data-file-to-delete=""><?php _e('Delete', 'wp-user-extra-fields') ?> </button>
					<!-- Upload progress managment -->
					<span id="wpuef_upload_progress_status_container_<?php echo $extra_field->cid; ?>" class="wpuef_upload_progress_status_container">
						<span class="wpuef_upload_progressbar" id="wpuef_upload_progressbar_<?php echo $extra_field->cid; ?>"></span >
						<span class="wpuef_upload_progressbar_percent" id="wpuef_upload_progressbar_percent_<?php echo $extra_field->cid; ?>">0%</span>
					</span>
				</span>
			<?php endif ?>
		</span>
	<?php elseif($extra_field->field_type == "checkboxes"): ?>
		<?php foreach($extra_field->field_options->options as $index => $extra_option): 
			$field_value = !isset($field_value) && $extra_option->checked ?  $index : $field_value; ?> 
			<input type="hidden" value="<?php if(isset($field_value[$index]) || $default ) echo 'true'; else 'null';?>" id="<?php echo $extra_field->cid."-".$index; ?>" name="wpuef_options[<?php echo $extra_field->cid; ?>][<?php echo $index ?>]" />
			<input type="checkbox" <?php if(isset($field_value[$index]) || $default ) echo 'checked';?>  value="<?php echo $index ?>" id="<?php echo "wpuef_field_".$extra_field->cid; ?>" data-id="<?php echo $extra_field->cid."-".$index; ?>" <?php if($required) echo 'required="required"  class="wpuef_checkbox wpuef_checkbox_perform_check wpuef_checkobox_group_'.$extra_field->cid.'" '; else echo 'class = "wpuef_checkbox"';?> <?php if($read_only) echo 'readonly="readonly"   disabled="disabled"';?>><span class="wpuef_checkbox_label"><?php echo $extra_option->label; ?></span></input><br/>
		<?php endforeach; ?>
		
		
	<?php elseif($extra_field->field_type == "radio"): ?>
		<?php foreach($extra_field->field_options->options as $index => $extra_option): 
			$field_value = !isset($field_value) && $extra_option->checked?  $index : $field_value;  ?>
			<input type="radio" name="wpuef_options[<?php echo $extra_field->cid; ?>]" id="<?php echo "wpuef_field_".$extra_field->cid; ?>" value="<?php echo $index; ?>" <?php if($field_value == $index) echo 'checked';?> <?php if($required) echo 'required="required"';?> <?php if($read_only) echo ' disabled="disabled" readonly="readonly" ';?>><span class="wpuef_checkbox_label"><?php echo $extra_option->label; ?></span></input><br/>
		<?php endforeach; ?>

		
	<?php elseif($extra_field->field_type == "date"): 
			  if(isset($field_value))
				{
					$date = DateTime::createFromFormat("Y/m/d", $field_value );
					if(is_object($date))
						$field_value = $date->format($wpuef_option_model->get_date_format(true));
				}
				?>
		 <input class="<?php if(!$read_only) echo 'wpuef_input_date'?> " type="text" id="<?php echo "wpuef_field_".$extra_field->cid; ?>" placeholder="<?php echo $placeholder; ?>" value="<?php echo $field_value; ?>" name="wpuef_options[<?php echo $extra_field->cid; ?>]" <?php if($required) echo 'required="required"';?> <?php if($read_only) echo 'readonly="readonly" ';?>></input>
	<?php elseif($extra_field->field_type == "time"): ?>
		 <input class="<?php if(!$read_only) echo 'wpuef_input_time'?> " type="text" id="<?php echo "wpuef_field_".$extra_field->cid; ?>" placeholder="<?php echo $placeholder; ?>" value="<?php echo $field_value; ?>" name="wpuef_options[<?php echo $extra_field->cid; ?>]" <?php if($required) echo 'required="required"';?> <?php if($read_only) echo 'readonly="readonly" ';?>></input>
	
	<?php elseif($extra_field->field_type == "website"): ?>
		<input class="input-text wpuef_input_url" type="url" value="<?php echo $field_value; ?>" id="<?php echo "wpuef_field_".$extra_field->cid; ?>" placeholder="<?php echo $placeholder; ?>" name="wpuef_options[<?php echo $extra_field->cid; ?>]" <?php if($required) echo 'required="required"';?> <?php if($read_only) echo 'readonly="readonly" ';?>></input>

	<?php elseif($extra_field->field_type == "paragraph" || ($extra_field->field_type == "html" && !$read_only)): ?>
		<textarea  class="wpuef_input_textarea" name="wpuef_options[<?php echo $extra_field->cid; ?>]" id="<?php echo "wpuef_field_".$extra_field->cid; ?>" placeholder="<?php echo $placeholder; ?>" <?php if($required) echo 'required="required"';?> <?php if($read_only) echo 'readonly="readonly" ';?>><?php echo $field_value; ?></textarea>
	
	<?php elseif($extra_field->field_type == "html" && $read_only): 
		echo $field_value;
	?>
		
	<?php elseif($extra_field->field_type == "number"): ?>
		<input class="wpuef_input_number" type="<?php echo $extra_field->field_type; ?>" id="<?php echo "wpuef_field_".$extra_field->cid; ?>" placeholder="<?php echo $placeholder; ?>" value="<?php echo $field_value; ?>" name="wpuef_options[<?php echo $extra_field->cid; ?>]" <?php if(isset($extra_field->field_options->min)) echo 'min="'.$extra_field->field_options->min.'"'?>  <?php if(isset($extra_field->field_options->max)) echo 'max="'.$extra_field->field_options->max.'"'?>  <?php if($required) echo 'required="required"';?> <?php if($read_only) echo 'readonly="readonly" ';?> />
	<?php elseif($extra_field->field_type != "title_no_input"): //Text ?>
		<input class="input-text wpuef_input_text <?php echo $password_class; ?>" id="<?php echo "wpuef_field_".$extra_field->cid; ?>" type="<?php if($is_password_override) echo 'password'; else echo $extra_field->field_type; ?>" placeholder="<?php echo $placeholder; ?>" value="<?php if(!$is_password_override) echo htmlspecialchars($field_value); ?>" name="<?php if($is_password_override) echo $password_field_name; else echo 'wpuef_options['.$extra_field->cid.']'; ?>" <?php if($required && !$is_password_override) echo 'required="required"';?> <?php if($read_only) echo 'disabled="disabled"';?>/>
	<?php endif; 
	//End types
	?>
	
	<?php //Description
		if( isset($extra_field->field_options->description)): ?>
			<span class="description wpuef_description"> <?php echo $extra_field->field_options->description; ?></span>
		<?php endif; ?>
	
<?php if($extra_field->field_type != "country_and_state" && $extra_field->field_type != 'title_no_input'): ?>	
		</p>	
	<?php endif; ?>
	
<?php endif; endforeach; ?>
<?php if($render_form && $rendered_elements > 0): ?>
	<p>
		<input type="submit" value="<?php _e('Save changes', 'wp-user-extra-fields'); ?>" name="save_account_details" class="button wpuef_save_button_my_account">
	</p>
	</form>
<?php endif; ?>
<div style="display:block; clear:both; width: 100%; height:1px;"></div>
<script>
var delete_pending_message = "<?php _e('Click on the update user profile button to delete the file.', 'wp-user-extra-fields'); ?>";  //file upload
var delete_popup_warning_message = "<?php _e('Are you sure to delete the file?', 'wp-user-extra-fields'); ?>";  //file upload
var file_check_popup_browser = "<?php _e("Please upgrade your browser, because your current browser lacks some new features we need!", 'wp-user-extra-fields'); ?>";  
var file_check_popup_size = "<?php _e("Choosen file is too big and will not be uploaded!", 'wp-user-extra-fields'); ?>";  
var file_check_popup_api = "<?php _e("The File APIs are not fully supported in this browser.", 'wp-user-extra-fields'); ?>"; 
jQuery(document).ready(function()
{
	jQuery( ".wpuef_input_date" ).pickadate({formatSubmit: 'yyyy/mm/dd', format: '<?php echo $wpuef_option_model->get_date_format(); ?>',selectMonths: true,  selectYears: true, selectYears: 100, max: [<?php echo date('Y', strtotime('+10 years'))  ?>,11,31] });
	jQuery( ".wpuef_input_time" ).pickatime({formatSubmit: 'HH:i', format: 'HH:i'});
});
</script>
			