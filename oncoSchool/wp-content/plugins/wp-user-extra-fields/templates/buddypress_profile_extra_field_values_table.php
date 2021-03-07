<?php 
global $wpuef_option_model, $wpuef_shortcodes, $wpuef_user_model;

foreach($extra_fields->fields as $extra_field): 
	$is_password_override = isset($extra_field->field_to_override) && $extra_field->field_to_override == 'password' ? true : false;
	if($is_password_override)
		continue;
	
	if( (!isset($extra_field->invisible) || !$extra_field->invisible) &&
	  (!isset($extra_field->visible_only_at_register_page) || !$extra_field->visible_only_at_register_page )  &&
	  (isset($extra_field->buddypress_profile_page_show) && $extra_field->buddypress_profile_page_show) &&
	   $wpuef_user_model->field_can_be_displayed_for_current_user($extra_field)
		) :
	  ?>
	<tr>
		<td class="label"><?php echo $extra_field->label; ?></td>
		<td class="data"><?php echo $wpuef_shortcodes->wpuef_show_field_value(array("user_id"=>$user_id, "field_id" =>$extra_field->cid)) ?></td>
	</tr>
 <?php
	endif;
endforeach;
?>