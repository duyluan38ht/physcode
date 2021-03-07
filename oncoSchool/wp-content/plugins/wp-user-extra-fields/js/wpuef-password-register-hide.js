jQuery(document).ready(function()
{
	wpuef_hide_password();
});
function wpuef_hide_password()
{
	var selectors = Array('#reg_password', '#account_password_field', '#password_1');
	var found = false;
	for(var i = 0; i < selectors.length; i++)
	{
		if(!found && jQuery(selectors[i]).length)
		{
			found = true;
			//console.log(jQuery(selectors[i]));
			
			//repositioning
			if(jQuery(selectors[i]).attr('id') === 'account_password_field')
				jQuery('input.wpuef_password').parent().after(jQuery(selectors[i]).parent());
			else
				jQuery(selectors[i]).parent().after(jQuery('input.wpuef_password').parent());
			
			
			//remove old password field
			jQuery(selectors[i]).parent().hide().remove(); 
			//jQuery("#trap").remove(); --> moved to register-page.js
		} 
	}
	
}
wpuef_hide_password();