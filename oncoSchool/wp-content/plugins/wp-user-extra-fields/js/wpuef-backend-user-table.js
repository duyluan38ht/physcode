jQuery(document).ready(function()
{
	/* console.log("here"); */
	jQuery('#wpuef-search-submit').on('click', wpuef_on_search_by_extra_field);
});
function wpuef_on_search_by_extra_field(event)
{
	event.preventDefault();
	var oldURL = window.location.href;
	var index = 0;
	var newURL = oldURL;
	index = oldURL.indexOf('?');
	if(index == -1){
		index = oldURL.indexOf('#');
	}
	if(index != -1){
		newURL = oldURL.substring(0, index);
	}

	window.location.href = newURL + "?wpuef_extra_field_search="+jQuery('#wpuef-search-input').val();
	return false;
}