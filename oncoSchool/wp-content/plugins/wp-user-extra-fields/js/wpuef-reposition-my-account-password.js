jQuery(document).ready(function()
{
	wpuef_reposition_password_field();
});
function wpuef_reposition_password_field()
{
	jQuery('.wpuef_password').parent().insertBefore(jQuery('#password_1').parent());
}
wpuef_reposition_password_field();