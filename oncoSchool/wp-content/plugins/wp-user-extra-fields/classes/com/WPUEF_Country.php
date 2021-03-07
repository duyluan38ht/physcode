<?php 
class WPUEF_Country
{
	public function __construct()
	{
		add_action('wp_ajax_nopriv_wpuef_load_state_by_country_id', array(&$this, 'ajax_load_state_by_country_id'));
		add_action('wp_ajax_wpuef_load_state_by_country_id', array(&$this, 'ajax_load_state_by_country_id'));
	}
	function ajax_load_state_by_country_id()
	{
		$country_id = isset($_POST['country_id']) ? $_POST['country_id'] : "";
		$field_id = isset($_POST['field_id']) ? $_POST['field_id'] : null;
		$required = isset($_POST['required']) && $_POST['required'] == 'true' ? true : false;
		if($country_id != "" && isset($field_id))
			$this->render_state_select_html_by_country($country_id, $field_id, $required);
		wp_die();
	}
	function get_countries($type = null) //null || all
	{
		$countries_obj   = new WC_Countries();
		$countries   = isset($type) && $type == 'all_countries' ?  $countries_obj->get_countries() : $countries_obj->get_allowed_countries() ;//$countries_obj->__get('countries');
		
		//$default_country = $countries_obj->get_base_country();
		//$default_county_states = $countries_obj->get_states( $default_country ); //paramenter -> GB, IT ... is the "value" selected in the $countries select box
		return $countries;
	}
	function render_state_select_html_by_country($country_id, $field_id, $default_val = null, $required = true, $read_only = false, $use_wide_row = false)
	{
		$countries_obj   = new WC_Countries();
		$states = $countries_obj->get_states( $country_id ); //paramenter -> GB, IT ... is the "value" selected in the $countries select box
		$label_data =  $countries_obj->get_country_locale();
		//wpuef_var_dump($states);
		//wpuef_var_dump($label_data[$country_id]);
		
		$custom_attributes = array();
		if($required)
		   $custom_attributes['required'] = 'required'; 
		if($read_only)
			$custom_attributes['disabled'] = 'disabled';
		if ( is_array( $states ) && empty( $states ) ) //Like Germany, it doesn't have a states/provinces
		{
			woocommerce_form_field('wpuef_options['. $field_id.'][state]', array(
							'type'       => 'hidden',
							'class'      => $use_wide_row ? array( 'form-row-wide' ) : array( 'form-row-last' ),
							'label_class' => array( 'wcmca_form_label' ),
							//'placeholder'    => __('Select a state','wp-user-extra-fields'),
							'custom_attributes' =>  $custom_attributes,
							'value'    => $states
							));
		}
		elseif(is_array($states)) //Ex.: Italy
		{
			$reordered_states = array();
			$reordered_states[""] = __('Select one','wp-user-extra-fields');
			foreach($states as $state_code => $state_name)
				$reordered_states[$state_code] = $state_name;
			
			$required = isset($label_data[$country_id]['state']['required']) ? $label_data[$country_id]['state']['required'] : false;
			woocommerce_form_field('wpuef_options['. $field_id.'][state]', array(
							'type'       => 'select',
							'required'          => $required,
							'class'      => $use_wide_row ? array( 'form-row-wide', 'select2-container' ) : array( 'form-row-last', 'select2-container' ),
							//'label'      => $label_data[$country_id]['state']['label'],//__('State / Province','wp-user-extra-fields'),
							'label_class' => array( 'wcmca_form_label' ),
							'input_class' => array('select2-choice', 'wpuef_state_selector', "wpuef_element"),
							//'placeholder'    => __('Select a state','wp-user-extra-fields'),
							'custom_attributes' =>  $custom_attributes,
							'options'    => $reordered_states,
							'default' => isset($default_val) ? $default_val : ""
							)
			);
		}
		else //$states is false. Ex.: UK
		{
			$required = isset($label_data[$country_id]['state']['required']) ? $label_data[$country_id]['state']['required'] : false;
			woocommerce_form_field('wpuef_options['. $field_id.'][state]', array(
						'type'       => 'text',
						'class'      => $use_wide_row ? array( 'form-row-wide' ) : array( 'form-row-last' ),
						'required'          => $required,
						'input_class' => array('wcmca_input_field'),
						//'label'      => $label_data[$country_id]['state']['label'],//__('State / Province','wp-user-extra-fields'),
						'label_class' => array( 'wcmca_form_label' ),
						//'placeholder'    => __('State / Country','wp-user-extra-fields'),
						'custom_attributes' =>  $custom_attributes,
						'default' => isset($default_val) ? $default_val : ""
						)
						);
		}
	}
	function country_code_to_name($code)
	{
		$countries_obj   = new WC_Countries();
		return  isset($countries_obj->countries[ $code ])  ? $countries_obj->countries[ $code ]  : $code;
	}
	function state_code_to_name($country_code, $state_code)
	{
		$countries_obj   = new WC_Countries();
		$result = $countries_obj->get_states($country_code);
		if($result)
			return isset($result[$state_code]) ? $result[$state_code] : false;
		
		return false;
	}
}
?>