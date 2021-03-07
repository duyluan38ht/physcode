<?php 
class WPUEF_Field
{
	var $all_fields_data = null;
	public function __construct()
	{
	}
	public function get_all_fields_data()
	{
		global $wpuef_option_model;
		
		if(!isset($this->all_fields_data))
			$this->all_fields_data = $wpuef_option_model->get_option('json_fields_string');		
		
		return $this->all_fields_data;
	}
	public function is_overwriting_wp_or_woocommerce_field($field_id, $value)
	{
		$fields_data = $this->get_all_fields_data();
		$result = array();
		foreach((array)$fields_data->fields as $extra_field)
		{
			/* if(isset($extra_field->override_wp_last_name_field) && $extra_field->override_wp_last_name_field && $extra_field->cid == $field_id)
			{
				$result[] = 'last_name';
			}
			if(isset($extra_field->override_wp_first_name_field) && $extra_field->override_wp_first_name_field && $extra_field->cid == $field_id)
			{
				$result[] = 'first_name';
			} */
			if(isset($extra_field->field_to_override) && $extra_field->field_to_override != 'none' && $extra_field->field_to_override != '' && $extra_field->cid == $field_id)
			{
				if($extra_field->field_to_override ==  'shipping_country_and_state')
				{
					$result['shipping_country'] = isset($value['country']) ? $value['country'] : "";
					$result['shipping_state'] = isset($value['state']) ? $value['state'] : "";
				}
				else if($extra_field->field_to_override ==  'billing_country_and_state')
				{
					$result['billing_country'] = isset($value['country']) ? $value['country'] : "";
					$result['billing_state'] = isset($value['state']) ? $value['state'] : "";
				}
				else if($extra_field->field_to_override ==  'billing_country_and_state_both')
				{
					$result['billing_country'] = isset($value['country']) ? $value['country'] : "";
					$result['billing_state'] = isset($value['state']) ? $value['state'] : "";
					$result['shipping_country'] = isset($value['country']) ? $value['country'] : "";
					$result['shipping_state'] = isset($value['state']) ? $value['state'] : "";
				}
				elseif(strpos($extra_field->field_to_override, '+') !== false)
				{
					$fields = explode('+',$extra_field->field_to_override);
					foreach($fields as $field)
						$result[$field] = $value;
				}
				else
				   $result[$extra_field->field_to_override] = $value;
			}
		}
		return $result;
	}
	public function exists_password_override_field()
	{
		$fields_data = $this->get_all_fields_data();
		foreach((array)$fields_data->fields as $extra_field)
			if(isset($extra_field->field_to_override) && $extra_field->field_to_override == 'password')
				return true;
		
		return false;
	}
}
?>