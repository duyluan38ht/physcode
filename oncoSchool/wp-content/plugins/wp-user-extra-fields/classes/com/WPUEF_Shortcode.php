<?php 
class WPUEF_Shortcode
{
	public function __construct()
	{
		add_shortcode( 'wpuef_show_field_value', array(&$this, 'wpuef_show_field_value' ));
		add_shortcode( 'wpuef_display_image', array(&$this, 'wpuef_display_image' ));
		add_shortcode( 'wpuef_extra_fields_custom_form', array(&$this, 'wpuef_extra_fields_custom_form' ));
	}
	public function wpuef_extra_fields_custom_form($atts)
	{
		global $wpuef_htmlHelper;
		$a = shortcode_atts( array(
			/* 'user_id' => get_current_user_id(), */
			'field_ids' => null,
			), $atts );
			
		
		if(!isset($a['field_ids']) /* || !isset($a['user_id']) */ || !get_current_user_id())
			return "";
		
		$fields_ids =  explode(",",$a['field_ids']);
		$fields_ids = empty($fields_ids) ? $a['field_ids'] : $fields_ids;
		
		ob_start();
		$wpuef_htmlHelper->render_extra_fields_custom_form($fields_ids, get_current_user_id());
		return ob_get_clean();
	}
	public function wpuef_display_image($atts, $content = null)
	{
		 $a = shortcode_atts( array(
			'user_id' => get_current_user_id(),
			'field_id' => "",
			'height' => -1,
			'width' => -1
			), $atts );
	
		if(!isset($a['field_id']) || !isset($a['user_id']))
			return "";
		
		$result = wpuef_get_field($a['field_id'], $a['user_id']);
		$value = isset($result->value) ? $result->value : "";
		if(isset($result->field_type) && $result->field_type == "file" ) 
		{
			//wpuef_var_dump($result);
			if(isset($result->value['url']) && $result->value['url'] != "")
			{
				$height = $a['height'] > -1 ? 'height="'.$a['height'].'"' : "";
				$width = $a['width'] > -1 ? 'width="'.$a['width'].'"' : "";
				$value = '<img src="'.$result->value['url'].'" '.$height.' '.$width.' ></img>';
			}
		}
		
		ob_start();
		echo $value;
		return ob_get_clean();
	}
	public function wpuef_show_field_value($atts, $content = null)
	{
		
		 $a = shortcode_atts( array(
			'user_id' => get_current_user_id(),
			'field_id' => "",
			'raw_data' => false
			), $atts );
	
		if(!isset($a['field_id']) || !isset($a['user_id']))
			return "";
		
		$result = wpuef_get_field($a['field_id'], $a['user_id']);
		$value = isset($result->value) ? $result->value : "";
		$original_value = null;
		if(isset($result->field_type) && ($result->field_type == "dropdown" || $result->field_type == "checkboxes" || $result->field_type == "radio")) 
		{
			if(!is_array($result->value))
				$value = isset($result->field_options->options[$result->value]) ? $result->field_options->options[$result->value]->label : "";
			else
			{
				$value = array();
				foreach((array)$result->value as $index => $result_value)
				{
					$value[] = isset($result->field_options->options[$index]) ? $result->field_options->options[$index]->label : "";
				}
			}
			
		}
		else if(isset($result->field_type) && $result->field_type == "country_and_state" ) 
		{
			if(isset($result->value))
			{
				global $wpuef_country_model;
				
				if($a['raw_data'])
				{
					$result = serialize($result->value);
					$value = $result;
				}
				else
				{
					$country = $wpuef_country_model->country_code_to_name($result->value["country"]);
					$state = isset($result->value["state"]) && $result->value["state"] != "" ? $wpuef_country_model->state_code_to_name($result->value["country"], $result->value["state"]) : "";
					$state = $state == false && isset($result->value["state"]) && $result->value["state"] != "" ? $result->value["state"] : $state;
					$show_state = !isset($result->show_state) || $result->show_state != 'no' ? true : false;
			  
					$value = $country;
					if($state != false && $show_state)
						$value .= ", ".$state;
				}
			} 
		}
		else if(isset($result->field_type) && $result->field_type == "date" ) 
		{
			$date = "";
			if(isset($result->value))
			{
				$date = DateTime::createFromFormat("Y/m/d", $result->value );
				if(is_object($date))
					$date = $date->format(get_option( 'date_format' ));
			}
			$value = $date;
		}
		else if(isset($result->field_type) && $result->field_type == "file" ) 
		{
			//wpuef_var_dump($result);
			if(isset($result->value['url']) && $result->value['url'] != "")
			{
				$value = "";
				$width = isset($result->preview_width) && $result->preview_width != "" ? $result->preview_width : 120;
				if(isset($result->image_preview) && wpuef_is_image($result->value['url'])) 
						$value .= '<img class="wpuef_image_preview" src="'.$result->value['url'].'" width='. $width .'" />'; 
				
				$value .= '<a href="'.$result->value['url'].'">'.__('View / Download', 'wp-user-extra-fields').'</a>';
				$original_value = $result->value['url'];
			}
		}
		
		if(isset($content) && !empty($content))
		{
			$value = is_array($value) ? implode(",", $value):$value;
			$value = isset($original_value) ? $original_value : $value;
			$value = '<a href="'.$value.'">'.$content.'</a>';
		}
		
		ob_start();
		//wpuef_var_dump($result);
		if(!is_array($value))
			echo $value;
		else 
			//foreach($value as $string_value)
					echo implode(", ", $value);
		return ob_get_clean();
	}
}
?>