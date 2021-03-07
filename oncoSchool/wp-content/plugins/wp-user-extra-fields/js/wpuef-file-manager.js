jQuery(document).ready(function()
{
	jQuery('.wpuef_delete_file_button').on("click",wpuef_delete_file);
	jQuery('.wpuef_view_download_file_button').on("click",wpuef_view_download_file);
	jQuery('.wpuef_file_upload_button').on("click",wpuef_upload_file);
	jQuery('.wpuef_file_tmp_delete_button').on("click",wpuef_delete_tmp_file);
	document.addEventListener('onWPUEFMultipleFileUploaderComplete', wpuef_on_file_uploaded);
	if (window.File && window.FileReader && window.FileList && window.Blob) 
	{
		jQuery('.wpuef_input_file').on('change' ,wpuef_encode_file);
	} 
	else {
		alert(file_check_popup_api);
	}
		
	// jQuery('#place_order').live('click', function(event) 
});

function wpuef_view_download_file(evt)
{
	evt.stopPropagation();
	evt.preventDefault();
	var href =  jQuery(evt.currentTarget).data('href');
	var win = window.open(href, '_blank');
	//win.focus();
	return false;
}
function wpuef_delete_file(evt)
{
	evt.stopPropagation();
	evt.preventDefault(); 
	var id =  jQuery(evt.currentTarget).data('id');
	if(confirm(delete_popup_warning_message))
	{
		jQuery('#wpuef-file-box-'+id).html(delete_pending_message);
		jQuery('#wpuef-file-box-'+id).append('<input type="hidden" name="wpuef_options[files_to_delete]['+id+']" value="'+id+'" id="wpuef-encoded-file_'+id+'" />');
	}
	return false;
}
function wpuef_check_file_size(files, max_size)
{
	if(max_size == "")
		return true;
	
	if (window.File && window.FileReader && window.FileList && window.Blob)
	{
		if(files != undefined)
		{
			var fsize =files[0].size;
			var ftype = files[0].type;
			var fname = files[0].name;
			if(fsize > max_size)
			{
				var size = fsize/1048576; // 1MB in bytes
				size = size.toFixed(2);
				alert(file_check_popup_size);
				return false;
			}
		}
	}
	else{
		alert(file_check_popup_browser);
		return false;
	}
	return true;
}
function wpuef_encode_file(evt) 
{
    var files = evt.target.files;
    var file = files[0];
	var id =  jQuery(evt.currentTarget).data('id');
	var max_size =  jQuery(evt.currentTarget).data('size');
	var sub_id = jQuery(evt.currentTarget).attr("data-extra-field-custom-form-id") ? "-"+jQuery(evt.currentTarget).data("extra-field-custom-form-id") : ""; //used in the extra field custom form in case of multiple forms
	var file_id = jQuery(evt.currentTarget).attr("data-extra-field-custom-form-id") ? jQuery(evt.currentTarget).data("file-id") : id;
	var upload_input_field = jQuery(evt.currentTarget);
	var upload_button = jQuery(evt.currentTarget).data('upload-button-id');
	
	//clear old one
	if(jQuery('#wpuef-encoded-file_'+id).length)
		jQuery('#wpuef-encoded-file_'+id).remove();
	
	// jQuery('#wpuef_file_tmp_delete_button_'+id).trigger('click');
	if(wpuef_check_file_size(files, max_size))
	{	
			
		wpuef_reset_upload_field_metadata(id);
		//jQuery('#wpuef_file_upload_button_'+id).show();
		jQuery('#wpuef_file_upload_button_'+id).trigger('click');
		
		/* var tempfile_name  = wpuef_replace_bad_char(file.name);
		jQuery('#wpuef-filename-'+id).val(tempfile_name); */
		if (files && file) 
		{
			/*
			Old method
			var reader = new FileReader();

			reader.onload = function(readerEvt) 
			{
				var binaryString = readerEvt.target.result;
				//document.getElementById("base64textarea").value = btoa(binaryString);
			   if(!jQuery('#wpuef-encoded-file_'+id).length)
			   {
					jQuery('#wpuef-file-container'+sub_id).append('<input type="hidden" name="wpuef_options[files]['+file_id+']" id="wpuef-encoded-file_'+id+'" />');
			   }
				
				jQuery('#wpuef-encoded-file_'+id).val(btoa(binaryString));			
		   };
		   reader.readAsBinaryString(file);*/
		   
		   //new
		  //jQuery(upload_button).show();
		}
		//ToDo: Show error message?
	}
};
function wpuef_reset_upload_field_metadata(id)
{
	jQuery('#wpuef_file_tmp_delete_button_'+id).hide();
	jQuery('#wpuef_file_tmp_delete_button_'+id).data('file-to-delete', "");
	jQuery('#wpuef-encoded-file_'+id).empty();
	jQuery('#wpuef-filename-'+id).val("");
    jQuery('#wpuef-filenameprefix-'+id).val("");
}
function wpuef_upload_file(evt)
{
	evt.preventDefault();
	evt.stopPropagation();
	
	var id =  jQuery(evt.currentTarget).data('id');
	var sub_id = jQuery(evt.currentTarget).attr("data-extra-field-custom-form-id") ? "-"+jQuery(evt.currentTarget).data("extra-field-custom-form-id") : ""; //used in the extra field custom form in case of multiple forms
	var file_id = jQuery(evt.currentTarget).attr("data-extra-field-custom-form-id") ? jQuery(evt.currentTarget).data("file-id") : id;
	var upload_input_field = jQuery(evt.currentTarget).data('upload-field-id');
	var files = jQuery(upload_input_field).prop('files');
    var file = files[0];
	
	//New One
   //UI
   jQuery(evt.currentTarget).hide();
   jQuery(upload_input_field).hide();
   jQuery('#wpuef_upload_progress_status_container_'+id).fadeIn();
   jQuery('#wpuef_file_tmp_delete_button_'+id).hide();
   jQuery('#wpuef_file_upload_button_'+id).hide();
   
   var current_upload_session_id = "chunk_"+Math.floor((Math.random() * 10000000) + 1);
   var tempfile_name  = wpuef_replace_bad_char(file.name);
 		
   var multiple_file_uploader = new WPUEFMultipleFileUploader({ field_id:id, 
																sub_id: sub_id, 
																file_id: file_id, 
																ajaxurl: wpuef.ajax_url, 
																files: files, 
																file: file, 
																file_name:tempfile_name,
																upload_input_field:upload_input_field,
																current_upload_session_id: current_upload_session_id});
	multiple_file_uploader.continueUploading();						
	return false;
			
}
function wpuef_show_tmp_file_preview_if_any(id, data)
{
	var formData = new FormData();
	formData.append('action', 'wpuef_get_tmp_uploaded_file_preview');
	formData.append('file_to_preview', data.current_upload_session_id+"_"+data.file_name);
	formData.append('preview_width', jQuery('#wpuef-file-preview-'+id).data('width'));
	
	jQuery.ajax({
		url: wpuef.ajax_url,
		type: 'POST',
		data: formData,
		async: true,
		success: function (data) 
		{
			jQuery('#wpuef-file-preview-'+id).html(data);
		},
		error: function (data) 
		{
			jQuery('#wpuef-file-preview-'+id).html("");
		},
		cache: false,
		contentType: false,
		processData: false
	}); 
	return false;
}
function wpuef_on_file_uploaded(event)
{
	//UI
	var id = event.field_id;
	jQuery(event.upload_input_field).fadeIn();
	jQuery('#wpuef_upload_progress_status_container_'+id).fadeOut();
	jQuery('#wpuef_file_tmp_delete_button_'+id).fadeIn();

	if(!jQuery('#wpuef-encoded-file_'+id).length)
	{
		jQuery('#wpuef-file-container'+event.sub_id).append('<input type="hidden" name="wpuef_options[files]['+event.file_id+']" id="wpuef-encoded-file_'+id+'" />');
	}
	
	jQuery('#wpuef-encoded-file_'+id).val(event.file_name); //to maintain compability with old managmnet via server
	jQuery('#wpuef-filename-'+id).val(event.file_name);
	jQuery('#wpuef-filenameprefix-'+id).val(event.current_upload_session_id);
	jQuery('#wpuef_file_tmp_delete_button_'+id).data('file-to-delete', event.current_upload_session_id+"_"+event.file_name);
	
	wpuef_show_tmp_file_preview_if_any(id, event);
}
function wpuef_delete_tmp_file(event)
{
	event.preventDefault();
	event.stopPropagation();

	var file_to_delete =  jQuery(event.currentTarget).data('file-to-delete');
	var id =  jQuery(event.currentTarget).data('id');
	
	jQuery(event.currentTarget).fadeOut();
	if(file_to_delete == "")
		return false;
	
	//UI
	jQuery('#wpuef_upload_field_'+id).show();
	jQuery("#wpuef_upload_field_"+id).val("");
	jQuery("#wpuef-filename-"+id).val("");
	jQuery("#wpuef-filenameprefix-"+id).val("");
	jQuery('#wpuef-file-preview-'+id).html("");
	
	var formData = new FormData();
	formData.append('action', 'wpuef_delete_tmp_uploaded_file');
	formData.append('file_to_delete', file_to_delete);
	
	jQuery.ajax({
		url: wpuef.ajax_url,
		type: 'POST',
		data: formData,
		async: true,
		success: function (data) 
		{
			
		},
		error: function (data) 
		{
			//console.log(data);
			//alert("Error: "+data);
		},
		cache: false,
		contentType: false,
		processData: false
	}); 
	return false;
}
function wpuef_replace_bad_char(text)
{
	text = text.replace(/'/g,"");
	text = text.replace(/"/g,"");
	text = text.replace(/ /g,"_");
	
	var remove_special_chars = wpuef.remove_special_chars_from_file_name == 'true' ? true : false;
	var translate_re = /[öäüÖÄÜ]/g;
	var translate = {
		"ä": "a", "ö": "o", "ü": "u",
		"Ä": "A", "Ö": "O", "Ü": "U",
		"ß": "ss" // probably more to come
	  };
	text = text.replace(translate_re, function(match) { 
      return translate[match]; 
    });
	
	if(remove_special_chars)
	{
		text = text.replace(/[^0-9a-zA-Z_.]/g, '');
	}
	
	text = text == "" ? 'file' : text;
	return text;
}