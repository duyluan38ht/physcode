var wpuef_exists_a_required_checkbox_empty = false;
jQuery(document).ready(function()
{
	jQuery(document).on('change', '.wpuef_checkbox_perform_check', wpuef_checkbox_required_check);
	jQuery(document).on('click', '.wpuef_checkbox', wpuef_checkbox_on_click);
	jQuery('.wpuef_checkbox_perform_check').trigger('change');
	
	//Custom validation
	jQuery(document).on('submit', ".wpuef_custom_form, form.woocommerce-EditAccountForm.edit-account", wpuef_additional_validation); 
	
	jQuery(document).on('click', "#place_order", wpuef_additional_validation);
});
function wpuef_checkbox_on_click(event)
{
	var elem = jQuery(event.currentTarget).data("id");
	jQuery("#"+elem).val( jQuery(event.currentTarget).attr('checked') === 'checked' ? "true" : "null");
}
function wpuef_checkbox_required_check(event)
{
	var id = jQuery(event.currentTarget).data('id'); //c14-0
	id = id.split("-");
	id = id[0];
	
	var at_least_one_checked = false;
	jQuery('.wpuef_checkobox_group_'+id).each(function(index,elem)
	{
		if(elem.checked || (jQuery('#createaccount').length  && !jQuery('#createaccount').prop('checked')) )
			at_least_one_checked = true;
	});
	
	if(at_least_one_checked)
	{
		jQuery('.wpuef_checkobox_group_'+id).removeAttr('required');
		wpuef_exists_a_required_checkbox_empty = false;
	}
	else
	{
		jQuery('.wpuef_checkobox_group_'+id).attr('required', 'required');
		wpuef_exists_a_required_checkbox_empty = true;
	}
}
function wpuef_additional_validation(event)
{
	var validation_error = false;
	
	//Custom work
	/*jQuery(".wpuef_input_text").each(function(index,obj)
	{
		//console.log(jQuery(this).attr('name'));
		if( jQuery(this).attr('name') == 'wpuef_options[c10]')
		{
			validation_error = !wpuef_checkVATNumber(jQuery(this).val());
			//console.log(validation_error);
		}
		
	});
	
	if(validation_error)
	{
		alert("Invalid VAT Number");
		event.preventDefault();
		event.stopImmediatePropagation();
		return false;
	} */
	
	jQuery(".wpuef_element").each(function(index,obj)
	{
		var has_reqired = jQuery(this).attr('required');
	
		if(typeof has_reqired !== typeof undefined && has_reqired !== false && 
			(jQuery(this).val() === 'undefined' || jQuery(this).val() == ""))
			{
				validation_error = true;
			}
		
	});
	
	jQuery(".wpuef_input_file").each(function(index,obj)
	{
		var has_reqired = jQuery(this).attr('required');
		var id = jQuery(this).data('id');
		validation_error = has_reqired && jQuery('#wpuef-filename-'+id).val() == "";
		
	});
	
	//console.log(wpuef_exists_a_required_checkbox_empty);
	
	if(validation_error || wpuef_exists_a_required_checkbox_empty)
	{
		jQuery('#wpuef_required_fields_warning_message').show();
		/* document.querySelector('#wpuef_required_fields_warning_message').scrollIntoView({ 
		  behavior: 'smooth' 
		}); */
		
		/* window.scroll({
		  top: jQuery('#wpuef_required_fields_warning_message').offset().top - 90, 
		  left: 0, 
		  behavior: 'smooth' 
		}); */
		
		jQuery('html, body').animate({
          scrollTop: jQuery('#wpuef_required_fields_warning_message').offset().top - 90, 
        }, 1000);
		
		event.preventDefault();
		event.stopImmediatePropagation();
		return false;
	}		
}
function wpuef_checkVATNumber(toCheck) 
{
    var vatNumber = toCheck.toUpperCase();
    var defCCode = "FR";
    var countryRE = {
       /*  ATU: /^\d{8}$/,
        BE : /^0?\d{9}$/,
        BG : /^\d{9,10}$/,
        CHE: /^\d{9}(?=MWST$|$)/,
        CY : /^[0-59]\d{7}[A-Z]$/,
        CZ : /^\d{8,10}(?=\d{3}$|$)/,
        DE : /^[1-9]\d{8}$/,
        DK : /^\d{8}$/,
        EE : /^10\d{7}$/,
        EL : /^\d{9}$/,
        ES : /^(?:[A-Z]\d{8}|[A-HN-SW]\d{7}[A-J]|[0-9YZKLMX]\d{7}[A-Z])$/,
        EU : /^\d{9}$/,
        FI : /^\d{8}$/, */
        FR : /^(?:\d|[A-HJ-NP-Z]){2}\d{9}$/,
        /* GB : /^(?:\d{9}(?:\d{3})?|(?:GD[0-4]|HA[5-9])\d{2})$/,
        GR : /^\d{8,9}$/,
        HR : /^\d{11}$/,
        HU : /^\d{8}$/,
        IE : /^(?:\d{7}[A-W][AH]?|[789][A-Z*+]\d{5}[A-W])$/,
        IT : /^\d{11}$/,
        LV : /^\d{11}$/,
        LT : /^\d{9}(?:\d{3})?$/,
        LU : /^\d{8}$/,
        MT : /^[1-9]\d{7}$/,
        NL : /^\d{9}B\d{2}$/,
        NO : /^\d{9}$/,
        PL : /^\d{10}$/,
        PT : /^\d{9}$/,
        RO : /^[1-9]\d{1,9}$/,
        RU : /^\d{10}(?:\d{2}?)$/,
        RS : /^\d{9}$/,
        SI : /^[1-9]\d{7}$/,
        SK : /^[1-9]\d[2346-9]\d{7}$/,
        SE : /^\d{10}01$/ */
    };
    var cCode, cNumber;

    // expensive! consider the alternatives: /^(?:ATU|BE|...)$/
    // or, just match /^(?:ATU|CHE|[A-Z]{2})$/. If you do this, you'll need to check
    // that countryRE[cCode] exists before exec'ing later.
    if (RegExp("^(" + Object.keys(countryRE).join("|") + ")(.+)$").test(vatNumber)) {
        cCode = RegExp.$1;
        cNumber = RegExp.$2;
    } else {
        // couldn't match a country; fallback to default
        cCode = defCCode;
        cNumber = vatNumber;
    }
    cNumber = countryRE[cCode].exec(cNumber);

    // if we match the pattern for this country, AND the check function is good 
    // (e.g. checksum is valid), then return the reformatted VAT number.
    if (cNumber /* && window[cCode + "VATCheckDigit"](cNumber[0]) */)
        //return [cCode, cNumber[0]];
        return true;

    // either the pattern is invalid for the country, or the check function failed.
    return false;
}