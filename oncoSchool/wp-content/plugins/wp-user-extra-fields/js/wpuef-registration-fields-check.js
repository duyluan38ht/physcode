jQuery(document).ready(function()
{
	jQuery(document).on('click', 'form#registerform input#wp-submit', wpuef_check_required_fields); //wp
	jQuery(document).on('click', 'form.register input.button[type="submit"]', wpuef_check_required_fields); //wc
});
function wpuef_check_required_fields(event)
{
	var required_error = false;
	var radio_array = new Array();
	jQuery('.wpuef_field').each( function(index,value)
	{
		/* if(jQuery(this).attr('type') ==  "radio")
			console.log(typeof jQuery('input[name="'+this.name+'"]:checked').val() === "undefined"); */
		if(jQuery(this).prop('required') && (( (jQuery(this).attr('type') == "checkbox" && !jQuery(this).prop('checked')) || (jQuery(this).attr('type') !=  "radio" &&
											  jQuery(this).val() == ""))  ))
		{
			//console.log(jQuery(this).val());
			required_error = true;
			jQuery(this).css('border-color', 'red');
		}
		else if( jQuery(this).prop('required') && jQuery(this).attr('type') ==  "radio" && typeof jQuery('input[name="'+this.name+'"]').val() === "undefined")
		{
			required_error = true;
			jQuery(this).css('border-color', 'red');
		}
		else
		{
			jQuery(this).css('border-color', '');
		}
		
	});
	
	if(required_error)
	{
		alert(wpuef_required_fields_error);
		event.preventDefault();
		event.stopImmediatePropagation();
		return false;
	}

}