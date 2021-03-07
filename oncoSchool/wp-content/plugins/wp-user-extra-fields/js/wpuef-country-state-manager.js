var wcuf_ajax_loader;
jQuery('document').ready(function()
{
	jQuery(document).on('change','.wpuef_country_selector' , wpuef_load_state);
});
function wpuef_load_state(event)
{
	/* if(wcuf_ajax_loader && wcuf_ajax_loader.readyState != 4)
            wcuf_ajax_loader.abort(); */
     
	var id = jQuery(event.currentTarget).data('id');
	var required = jQuery(event.currentTarget).data('required');
	var country_id = event.target.value;
	var random = Math.floor((Math.random() * 1000000) + 999);
	var formData = new FormData();
	formData.append('action', 'wpuef_load_state_by_country_id');
	formData.append('country_id', country_id);
	formData.append('field_id', id);
	formData.append('required', required); 
	
	//UI 
	jQuery('#wpuef_country_field_container_'+id).empty();
	if(event.target.value == 0)
		return;
	
	jQuery('#wpuef_preloader_image_'+id).show();
	
	wcuf_ajax_loader = jQuery.ajax({
		url: wpuef.ajax_url+"?nocache="+random,
		type: 'POST',
		data: formData,
		async: true,
		success: function (data) 
		{
			//UI	
			//var result = jQuery.parseJSON(data);
			jQuery('#wpuef_preloader_image_'+id).hide();
			jQuery('#wpuef_country_field_container_'+id).html(data);
						
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
}