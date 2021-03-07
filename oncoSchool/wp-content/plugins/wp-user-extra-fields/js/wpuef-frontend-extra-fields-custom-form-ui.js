function wpuef_start_saving_data(id)
{
	jQuery('#wpuef_extra_fields_custom_form_save_button_'+id).hide();
	jQuery('#wpuef_save_result_box_'+id).html(wpuef_please_wait_message);
	jQuery('#wpuef_save_result_box_'+id).fadeIn();
}
function wpuef_end_saving_data(id, is_error)
{
	jQuery('#wpuef_extra_fields_custom_form_save_button_'+id).delay(2000).fadeIn();
	if(!is_error)
		jQuery('#wpuef_save_result_box_'+id).html(wpuef_done_saving_message);
	else	
		jQuery('#wpuef_save_result_box_'+id).html(wpuef_error_saving_message);
	setTimeout(function(){jQuery('#wpuef_save_result_box_'+id).fadeOut()},1500);
}