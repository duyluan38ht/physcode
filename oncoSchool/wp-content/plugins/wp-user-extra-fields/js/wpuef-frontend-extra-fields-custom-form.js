jQuery(document).ready(function()
{
	jQuery(document).on('click','.wpuef_extra_fields_custom_form_save_button',wpuef_save_custom_form_fields);
});
function wpuef_save_custom_form_fields(event)
{
	event.preventDefault();
	event.stopImmediatePropagation();
	
	var random = Math.floor((Math.random() * 1000000) + 999);
	var id =  jQuery(event.currentTarget).data('id');
	var formData = new FormData();
	var required_field_error = false;
	var numeric_field_min_max_error = false;
	formData.append('action', 'wpuef_save_extra_fields_custom_form');
	jQuery('#wpuef-extra-fields-custom-form-'+id+' input, #wpuef-extra-fields-custom-form-'+id+' select ,#wpuef-extra-fields-custom-form-'+id+' textarea').each(function(index, elem)
	{
		
		var numeric_field_warning_to_print = wpuef_num_field_min_max_error;
		if(jQuery(this).prop('required') && ( (jQuery(this).attr('type') == "checkbox" && !jQuery(this).prop('checked')) || (jQuery(this).attr('type') !=  "radio" &&
											  jQuery(this).val() == "") || (jQuery(this).attr('type') == "text" && jQuery(this).val() == "") || (jQuery(this).attr('type') == "number" && jQuery(this).val() == "")
											  || (jQuery(this).attr('type') == "email" && jQuery(this).val() == "")
											 )  
											)
											  
											  {
												 required_field_error = true;
											  }
		if(typeof jQuery(elem).attr('name') !== 'undefined')
		{
			/* console.log(jQuery(elem).attr('name')+" --> "+jQuery(elem).attr('type'));
			console.log(jQuery(elem).val()); */
			
			if(jQuery(elem).attr('type') == 'radio')
			{
				if(jQuery(this).prop('checked'))
					formData.append(jQuery(elem).attr('name'), jQuery(elem).val());
			}
			else if( jQuery(elem).attr('type') == 'checkbox' && !jQuery(this).prop('checked'))
				formData.append(jQuery(elem).attr('name'), "null");
			else 
			{
				formData.append(jQuery(elem).attr('name'), jQuery(elem).val());
			}
		}
		
		//Number min max 
		if(typeof jQuery(this).attr('min') !== typeof undefined && jQuery(this).attr('min') !== false && jQuery(this).attr('min') != "")
		{
			if(jQuery(this).val() < jQuery(this).attr('min'))
				numeric_field_min_max_error = true;
			numeric_field_warning_to_print += " "+wpuef_num_field_min_text+" "+jQuery(this).attr('min') ;
		}
		if(typeof jQuery(this).attr('max') !== typeof undefined && jQuery(this).attr('max') !== false && jQuery(this).attr('max') != "")
		{
			if(jQuery(this).val() > jQuery(this).attr('max'))
				numeric_field_min_max_error = true;
			numeric_field_warning_to_print += " "+wpuef_num_field_max_text+" "+jQuery(this).attr('max') 
		}
		
		if(numeric_field_min_max_error)
		{
			alert(numeric_field_warning_to_print);	
			return false;
		}
	});
	if(required_field_error)
	{
		alert(wpuef_required_fields_error);												  
		return false;
	}
	else if(numeric_field_min_max_error)
	{
		return false;
	}
	
	//UI
	wpuef_start_saving_data(id);
	
	jQuery.ajax({
		url: wpuef_ajax_url+"?nocache="+random,
		type: 'POST',
		data:formData,
		async: true,
		success: function (data) 
		{
			//UI	
			wpuef_end_saving_data(id,false);			
		},
		error: function (data) 
		{
			//UI
			wpuef_end_saving_data(id, true);	
			//console.log(data);
			//alert("Error: "+data);
		},
		cache: false,
		contentType: false,
		processData: false
	});
	
	return false;
}