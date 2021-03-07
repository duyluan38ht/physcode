jQuery(document).ready(function()
{
	jQuery(document).on('submit', '#wpuef_settings_form', wpuef_check_if_reset_has_been_checked);
});
function wpuef_check_if_reset_has_been_checked(event)
{
	if(!jQuery('#wpuef_reset_all_fields').prop('checked')|| confirm(wpuef.reset_confirmation_message))
		return;
	
	event.preventDefault();
	event.stopImmediatePropagation();
	return false;
}