<?php 
class WPUEF_Wpml
{
	function __construct()
	{
	}
	public function is_active()
	{
		return class_exists('SitePress');
	}
	public function get_current_language()
	{
		if(!class_exists('SitePress'))
			return get_locale();
		
		return ICL_LANGUAGE_CODE."_".strtoupper(ICL_LANGUAGE_CODE);
	}
	function unregister_fields($fields)
	{
		if (function_exists ( 'icl_unregister_string' ) && class_exists('SitePress'))
			foreach((array)$fields as $old_field)
				{
					icl_unregister_string ( 'wp-user-extra-fields', 'wpuef_'.$old_field->cid );
					if(isset($old_field->field_options->options))
						foreach($old_field->field_options->options as $index => $extra_option)
						{
							if(isset($extra_option->label))
								icl_unregister_string ( 'wp-user-extra-fields','wpuef_'.$old_field->cid."_sublabel_".$index);
						}
					if(isset($old_field->field_options->description))
							icl_unregister_string ( 'wp-user-extra-fields', 'wpuef_'.$old_field->cid.'_description');
				}
	}
	function translate_single_string($text, $id )
	{
		return apply_filters( 'wpml_translate_single_string', $text, 'wp-user-extra-fields', 'wpuef_'.$id, ICL_LANGUAGE_CODE  );									
	}
	function register_single_string($text, $id )
	{
		do_action( 'wpml_register_single_string', 'wp-user-extra-fields', 'wpuef_'.$id, $text);
	}
}
?>
