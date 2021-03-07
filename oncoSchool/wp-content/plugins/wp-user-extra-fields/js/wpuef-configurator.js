var wpuef_formbuilder;
jQuery(document).ready(function()
{
	//bootstrapData: array di oggetti JSON
	var args = { selector: '#formbuilder' };
	if(fields_json_string != "")
		args.bootstrapData = JSON.parse(fields_json_string).fields;
	wpuef_formbuilder = new Formbuilder(args);
	wpuef_formbuilder.on('save', wpuef_onSaveFormData);
	//jQuery("form").submit(wpuef_onFormSubmit);
});		
function wpuef_onFormSubmit(event)
{
	event.preventDefault();
	/* event.stopImmediatePropagation(); */
	return false;
}
function wpuef_onSaveFormData(payload)
{
	//console.log(payload);
	
	var formData = new FormData();
	formData.append('action', 'wpuef_save_fields'); 
	formData.append('json_fields_string', payload); 
	
	jQuery.ajax({
		url: ajaxurl, 
		type: 'POST',
		data: formData,
		async: true,
		success: function (data) {
			//alert(data);
			wpuef_process_response_data(data);
		},
		error: function (data,error) 
		{
			alert("Could not contact the server, Error message: "+error);
		},
		cache: false,
		contentType: false,
		processData: false
	});
}
function wpuef_process_response_data(data)
{
}