jQuery(document).ready(function()
{
	if(!wpuef_is_logged)
		//jQuery('#wpuef-checkout-extra-fields').hide();
		{
			wpuef_move_extra_field_form_into_registration_form();
			wpuef_create_account_checkbox_check(null); 
		}
	
	jQuery(document).on('change', '#createaccount', wpuef_create_account_checkbox_check);
});

function wpuef_move_extra_field_form_into_registration_form()
{
	/* jQuery('.create-account').append(jQuery('#wpuef_required_fields_warning_message'));
	jQuery('.create-account').append(jQuery('#wpuef-checkout-extra-fields')); */
	jQuery('#wpuef_required_fields_warning_message').insertAfter(jQuery('#account_password_field'));
	jQuery('#wpuef-checkout-extra-fields').insertAfter(jQuery('#account_password_field'));
}
function wpuef_create_account_checkbox_check(event)
{
	var is_checked = jQuery('#createaccount').prop('checked');
	//console.log(jQuery(event.currentTarget).prop('checked'));
	//console.log(is_checked);
	
	if(is_checked)
	{
		jQuery('#wpuef-checkout-extra-fields').show(0);
		jQuery('.wpuef_element').each(function(index,elem)
		{
			if(jQuery(elem).data('required') == 'yes')
				jQuery(elem).prop('required','required');
		});
	}
	else
	{
		jQuery('#wpuef-checkout-extra-fields').hide(800);
		jQuery('.wpuef_element').each(function(index,elem)
		{
			if(jQuery(elem).data('required') == 'yes')
			{
				jQuery(elem).removeAttr('required');
			}
		});
	}
}