<?php global $wpuef_option_model, $wpuef_user_model, $wpuef_woocommerce_is_active; ?>
<div id="wpuef-file-container" style="display:none"></div> <!--file upload -->
<?php foreach($extra_fields->fields as $extra_field): 

	$is_password_override = isset($extra_field->field_to_override) && $extra_field->field_to_override == 'password' ? true : false;
	if($is_password_override)
		continue;
	?> 
	<p class="form-row form-row-wide" id="<?php echo "wpuef-row-".$extra_field->cid; ?>">
	<label class="wpuef_label "><?php echo $extra_field->label; ?></label>
	<?php
	$field_value = $wpuef_user_model->get_field( $extra_field->cid, $user_id );
			
		//Types
		if($extra_field->field_type == "dropdown"): 
			foreach($extra_field->field_options->options as $index => $extra_option): 
				if(isset($field_value) && $field_value != "" && $field_value == $index) echo $extra_option->label;
			endforeach; 
		
		elseif($extra_field->field_type == "country_and_state"): 
				if(isset($field_value)):
					global $wpuef_country_model;
					$show_state = !isset($extra_field->show_state) || $extra_field->show_state != 'no' ? true : false;
					$country = $wpuef_country_model->country_code_to_name($field_value["country"]);
					$state = isset($field_value["state"]) && $field_value["state"] != "" ? $wpuef_country_model->state_code_to_name($field_value["country"], $field_value["state"]) : "";
					$state = $state == false && isset($field_value["state"]) && $field_value["state"] != "" ? $field_value["state"] : $state;
			  
					echo $country;
					if($state != false && $show_state)
						echo ", ".$state;
				endif; 
				
		elseif($extra_field->field_type == "file"):
			if(isset($field_value) && isset($field_value["url"])): ?> 
				<?php if(isset($extra_field->image_preview) && wpuef_is_image($field_value["url"])): ?>
						<img class="wpuef_image_preview" src="<?php echo $field_value["url"]; ?>" width="<?php if(isset($extra_field->preview_width) && $extra_field->preview_width != "") echo $extra_field->preview_width; else echo 120;?>" /> 
				<?php endif; ?>
				<a  target="_blank" href="<?php echo $field_value["url"]; ?>"><?php _e('Download / View', 'wp-user-extra-fields') ?></a> 
		<?php endif; 
		
		elseif($extra_field->field_type == "checkboxes"):
			  foreach($extra_field->field_options->options as $index => $extra_option): 
					if(isset($field_value[$index])) echo $extra_option->label." "; 
				endforeach; 

		elseif($extra_field->field_type == "radio"):
			 foreach($extra_field->field_options->options as $index => $extra_option):
				if(isset($field_value) && $field_value == $index) echo $extra_option->label." ";
			 endforeach;
			
		elseif($extra_field->field_type == "date"):
			$date = "";
			if(isset($field_value))
			{
				$date = DateTime::createFromFormat("Y/m/d", $field_value );
				if(is_object($date))
					$date = $date->format(get_option( 'date_format' ));
			}
			echo $date;
		elseif($extra_field->field_type == "time"): 
			 echo $field_value;
		
		elseif($extra_field->field_type == "website"): 
			echo $field_value;

		elseif($extra_field->field_type == "paragraph"): 
			 echo $field_value;
		elseif($extra_field->field_type == "html"): 
				 echo $field_value;
		elseif($extra_field->field_type == "number"): 
			echo $field_value;
		 elseif($extra_field->field_type != "title_no_input"): 
			echo $field_value;
		endif; ?>

</p>			
<?php endforeach; ?>