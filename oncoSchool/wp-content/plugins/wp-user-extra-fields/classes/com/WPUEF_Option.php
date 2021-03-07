<?php class WPUEF_Option
{
	public function __construct()
	{
		if(is_admin())
		{
			add_action( 'wp_ajax_wpuef_save_fields', array(&$this, 'ajax_save_fields') );
			add_action( 'init', array(&$this, 'create_export_file') );
		}
	}
	function create_export_file()
	{
		if(!isset($_GET['wpuef_create_export_file']))
			return;
		
		$wpuef_options = get_option( 'wpuef_options'); 
		if(isset($wpuef_options)  && isset($wpuef_options['json_fields_string']))
			$wpuef_options['json_fields_string'] = json_decode(stripcslashes($wpuef_options['json_fields_string']));
		$wpuef_general_options = get_option('wpuef_general_options');
		$to_export = array();
		$to_export['wpuef_options'] = isset($wpuef_options) ? $wpuef_options : "";
		$to_export['wpuef_general_options'] = isset($wpuef_general_options) ? $wpuef_general_options : "";
		$to_export = json_encode($to_export);
		
		$fh = @fopen( 'php://output', 'w' );
		header('Content-disposition: attachment; filename=wpuef_settings.json');
		header('Content-type: application/json'); 
		header('Content-Description: File Transfer');
		header("Expires: 0");
		header("Pragma: public");
		fwrite($fh,$to_export);
		fclose($fh);
		//wp_die();
		exit;
	}
	function import_settings_file($settings_file)
	{
		if(!isset($settings_file['tmp_name']) || $settings_file['tmp_name'] == "")
			return;
		$content = file_get_contents($settings_file['tmp_name']);
		$settings = json_decode($content ,true);
		if(isset($settings['wpuef_options']) && $settings['wpuef_options'] != "")
		{
			$this->delete_option('wpuef_options');
			//$this->save_option('json_fields_string'); //For WPML purpose
			foreach($settings['wpuef_options'] as $option_name => $option_value) //For WPML purpose
			{
				//wpuef_var_dump($option_name);
				//wpuef_var_dump($option_value);
				$this->save_option($option_name, addslashes(json_encode($option_value)));
			}
		}
			
		if(isset($settings['wpuef_general_options']) && $settings['wpuef_general_options'] != "")
		{
			$this->delete_option('wpuef_general_options');
			$this->save_general_options($settings['wpuef_general_options']);
		}
	}
	function delete_option($option_name)
	{
		global $wpuef_wpml_helper;
		if($option_name == 'wpuef_options')
		{
			$old_fields = $this->get_option('json_fields_string');
			if(isset($old_fields->fields))
				$wpuef_wpml_helper->unregister_fields($old_fields->fields);
		}
		delete_option($option_name);
	}
	static function get_option($option_name = null, $unserialize = true)
	{
		global $wpuef_wpml_helper;
		$options = get_option( 'wpuef_options'); 
		$options = isset($options) ? $options: null;
		
		$result = null;
		if($option_name)
		{
			if(isset($options[$option_name]))
				$result = $options[$option_name];
			
			if($unserialize && $option_name == 'json_fields_string' && isset($result))
			{
				$result = json_decode(stripcslashes($result));
				//WPML
				if (class_exists('SitePress'))
					foreach($result->fields as $extra_field)
					{
						//$extra_field->label = apply_filters( 'wpml_translate_single_string', $extra_field->label, 'wp-user-extra-fields', 'wpuef_'.$extra_field->cid, ICL_LANGUAGE_CODE  );
						$extra_field->label = $wpuef_wpml_helper->translate_single_string($extra_field->label, $extra_field->cid);
						if(isset($extra_field->field_options->options))
								foreach($extra_field->field_options->options as $index => $extra_option)
								{
									if(isset($extra_option->label))
										//apply_filters( 'wpml_translate_single_string', $extra_option->label, 'wp-user-extra-fields', 'wpuef_'.$extra_field->cid."_sublabel_".$index, ICL_LANGUAGE_CODE  );
										$extra_option->label = $wpuef_wpml_helper->translate_single_string($extra_option->label, $extra_field->cid."_sublabel_".$index);
										
								}
						if( isset($extra_field->field_options->description))
							//$extra_field->field_options->description = apply_filters( 'wpml_translate_single_string', $extra_field->field_options->description, 'wp-user-extra-fields', 'wpuef_'.$extra_field->cid.'_description', ICL_LANGUAGE_CODE  );
							$extra_field->field_options->description = $wpuef_wpml_helper->translate_single_string($extra_field->field_options->description, $extra_field->cid.'_description');
					}
			}
		}
		else
			$result = $options;
		
		
		return $result;
	}
	public function save_general_options($options)
	{
		$options_old = get_option('wpuef_general_options');
		if(isset($options_old['woocommerce_checkout_page_before_extra_fields_html_snippet']))
			$options['woocommerce_checkout_page_before_extra_fields_html_snippet'] = array_merge($options_old['woocommerce_checkout_page_before_extra_fields_html_snippet'],$options['woocommerce_checkout_page_before_extra_fields_html_snippet']);
		if(isset($options_old['woocommerce_checkout_page_after_extra_fields_html_snippet']))
			$options['woocommerce_checkout_page_after_extra_fields_html_snippet'] = array_merge($options_old['woocommerce_checkout_page_after_extra_fields_html_snippet'],$options['woocommerce_checkout_page_after_extra_fields_html_snippet']);
		update_option('wpuef_general_options', $options);
	}
	public function get_general_options()
	{
		$options = get_option('wpuef_general_options');
		return $options;
	}
	public function get_woocommerce_checkout_html_custom_snippets()
	{
		global $wpuef_wpml_helper;
		$options = get_option('wpuef_general_options');
		$result['woocommerce_checkout_page_before_extra_fields_html_snippet'] = isset($options['woocommerce_checkout_page_before_extra_fields_html_snippet']) && isset($options['woocommerce_checkout_page_before_extra_fields_html_snippet'][$wpuef_wpml_helper->get_current_language()]) ? $options['woocommerce_checkout_page_before_extra_fields_html_snippet'][$wpuef_wpml_helper->get_current_language()]: '';
		$result['woocommerce_checkout_page_after_extra_fields_html_snippet'] = isset($options['woocommerce_checkout_page_after_extra_fields_html_snippet']) && isset($options['woocommerce_checkout_page_after_extra_fields_html_snippet'][$wpuef_wpml_helper->get_current_language()]) ? $options['woocommerce_checkout_page_after_extra_fields_html_snippet'][$wpuef_wpml_helper->get_current_language()] : '';
		return $result;
	}
	public function get_woocommerce_checkout_page_positioning()
	{
		$options = get_option('wpuef_general_options');
		$position = isset($options['woocommerce_checkout_page_positioning']) ? $options['woocommerce_checkout_page_positioning'] : 'woocommerce_after_checkout_billing_form';
		return $position;
	}
	public function get_woocommerce_admin_order_page_show_extra_fields()
	{
		$options = get_option('wpuef_general_options');
		$show = isset($options['woocommerce_admin_order_page_show_extra_fields']) ? $options['woocommerce_admin_order_page_show_extra_fields'] : "true";
		$show = $show == 'true' ? true : false;
		return $show;
	}
	public function get_woocommerce_register_page_positioning()
	{
		$options = get_option('wpuef_general_options');
		$position = isset($options['woocommerce_register_page_positioning']) ? $options['woocommerce_register_page_positioning'] : 'register_form';
		if(version_compare( WC_VERSION, '2.7', '>' ))
			$position = $position == 'register_form' ? 'woocommerce_register_form' : $position;
		return $position;
	}
	public function get_woocommerce_user_account_pages_positioning()
	{
		$options = get_option('wpuef_general_options');
		$result = array();
		$result['woocommerce_account_details_page_positioning'] = isset($options['woocommerce_account_details_page_positioning']) ? $options['woocommerce_account_details_page_positioning'] : 'woocommerce_edit_account_form';
		$result['woocommerce_billing_details_page_positioning'] = isset($options['woocommerce_billing_details_page_positioning']) ? $options['woocommerce_billing_details_page_positioning'] : 'woocommerce_after_edit_address_form_billing';
		$result['woocommerce_shipping_details_page_positioning'] = isset($options['woocommerce_shipping_details_page_positioning']) ? $options['woocommerce_shipping_details_page_positioning'] : 'woocommerce_after_edit_address_form_shipping';
		
		return $result;
	}
	public function get_date_format($php_format = false)
	{
		$options = get_option('wpuef_general_options');
		if(!$php_format)
			return isset($options['date_format']) ? $options['date_format'] : "dd/mm/yyyy";
		$format = "d/m/Y";
		if(isset($options['date_format']) )
		{
			switch($options['date_format'])
			{
				case 'dd/mm/yyyy': $format = "d/m/Y"; break;
				case 'mm/dd/yyyy': $format = "m/d/Y"; break;
				case 'dd.mm.yyyy': $format = "d.m.Y"; break;
				case 'mm.dd.yyyy': $format = "m.d.Y"; break;
				case 'dd-mm-yyyy': $format = "d-m-Y"; break;
				case 'mm-dd-yyyy': $format = "m-d-Y"; break;
			}
		}
		return $format;
	}
	public function remove_special_chars_from_file_name()
	{
		$options = get_option('wpuef_general_options');
		return isset($options['file_upload_remove_special_characters']) && $options['file_upload_remove_special_characters'] == 'no' ? false : true;
	}
	public function save_option($option_name, $value = null)
	{
		global $wpuef_user_model, $wpuef_wpml_helper;
		$options = get_option( 'wpuef_options');
		if(isset($value))
		{
			if($option_name == 'json_fields_string')
			{
				$old_fields = json_decode(stripcslashes($options[$option_name]));
				$new_fields = json_decode(stripcslashes($value));
				$deleted_fields = is_object($old_fields) ? $wpuef_user_model->delete_unused_fields( $old_fields->fields, $new_fields->fields) : array();
				
				//WPML managment
				foreach($new_fields->fields as $new_field)
				{
					//Register new string
					if(class_exists('SitePress'))
					{
						//do_action( 'wpml_register_single_string', 'wp-user-extra-fields', 'wpuef_'.$new_field->cid, $new_field->label );
						$wpuef_wpml_helper->register_single_string($new_field->label, $new_field->cid);
						if(isset($new_field->field_options->options))
							foreach($new_field->field_options->options as $index => $extra_option)
							{
								if(isset($extra_option->label))
									//do_action( 'wpml_register_single_string', 'wp-user-extra-fields', 'wpuef_'.$new_field->cid."_sublabel_".$index, $extra_option->label );
									$wpuef_wpml_helper->register_single_string($extra_option->label, $new_field->cid."_sublabel_".$index);
								
							}
						if(isset($new_field->field_options->description))
							//do_action( 'wpml_register_single_string', 'wp-user-extra-fields', 'wpuef_'.$new_field->cid.'_description', $new_field->field_options->description );
							$wpuef_wpml_helper->register_single_string($new_field->field_options->description, $new_field->cid.'_description');
					}
				}
				//Unregister deleted string 
				$wpuef_wpml_helper->unregister_fields($deleted_fields);
				//End WPML managment
			}
			$options[$option_name] = $value;
		}
		if(is_admin())
			update_option( 'wpuef_options', $options);
	}
	public function ajax_save_fields()
	{
		if(isset($_POST['json_fields_string']))
			$this->save_option('json_fields_string', $_POST['json_fields_string']);
		wp_die();
	}
	
}
?>