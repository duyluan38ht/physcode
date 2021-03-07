<?php class WPUEF_User
{
	var $id_prefix = 'wpuef_cid_';
	public function __construct()
	{
		add_action('wp_ajax_wpuef_save_extra_fields_custom_form', array(&$this, 'ajax_save_fields_custom_form'));
		add_action('wp_ajax_nopriv_wpuef_save_extra_fields_custom_form', array(&$this, 'ajax_save_fields_custom_form')); //???
	}
	public function ajax_save_fields_custom_form()
	{
		//wpuef_var_dump($_POST['wpuef_options']);
		if(get_current_user_id())
		{
			if(isset($_POST['wpuef_options']))
				$this->save_fields($_POST['wpuef_options'], get_current_user_id()); 
		}
		wp_die();
	}
	public function reset_field_contents_for_all_users()
	{
		global $wpdb;
		$query = "DELETE 
					FROM {$wpdb->usermeta} ".
					"WHERE meta_key LIKE 'wpuef_cid_%' ";
					/* WHERE meta_key IN('_was_period', '_was_start_time','_was_end_time','_was_shop_page_msg','_was_shop_page_during_msg','_was_shop_page_after_msg'
									   '_was_product_page_msg','_was_product_page_during_msg','_was_product_page_after_msg','_was_countdown_to_start',
									   '_was_countdown_to_end','_was_always_show_shop_message','_was_always_show_product_message','_was_where_countdown_to_start',
									   '_was_always_show_shop_message','_was_always_show_product_message', '_was_where_countdown_to_start', '_was_where_countdown_to_end',
									   '_was_where_sales_progressbar', '_was_expiring_date', '_was_total_sales_limit', '_was_total_sales_limit', '_was_customer_roles_restriction_type',
									   '_was_customer_roles_restriction_message') " */;
		$wpdb->get_results($query);
		//wpuef_var_dump("here");
	}
	public function save_fields( $fields, $user_id = null)
	{
		if(!isset($user_id))
			$user_id = get_current_user_id();
		
		global $wpuef_file_model;
		$cid_to_value = array();
		
		//wpuef_var_dump($fields);
		foreach($fields as $field_id => $value)
		{
			//$cid_to_value[$field_id] = $value;
			if($field_id == 'files') //wpuef_options[files][{cid}]
			{
				$file_info = $wpuef_file_model->save_files($user_id, $value);
				
				/*   Format:
					$file_info[$id]['absolute_path'] 
				    $file_info[$id]['url']  */
				
				$files_to_delete = array();
				foreach($file_info as $id => $file_infos)
				{
					//check file to delete
					$old_file = $this->get_field( $id, $user_id);
					if(isset($old_file))
						$files_to_delete[$id] = $old_file;
					
					//save file title and paths on user profile
					 update_user_meta( $user_id, $this->id_prefix.$id, $file_infos );
				} 
				//Delete old file (if any)
				$wpuef_file_model->delete_files($files_to_delete);
			}
			elseif($field_id == 'files_to_delete')
			{
				$files_to_delete = array();
				foreach($value as $id)
				{
					$files_to_delete[$id] = $this->get_field( $id, $user_id);
				}
				
				/* Format files_to_delete : 
				array(1) {
				  ["c32"]=>
				  array(3) {
					["absolute_path"]=>
					string(115) "/var/public_html/site/wp-content/uploads/wpuef/8/288888_test.pdf"
					["url"]=>
					string(82) "http://site.com/wp-content/uploads/wpuef/8/288888_test.pdf"
					["id"]=>
					string(3) "c32"
				  }
				}
				*/
				$wpuef_file_model->delete_files($files_to_delete);
				$this->delete_fields($files_to_delete, $user_id);
			}
			else
			{
				//checkbox
				//wpuef_var_dump($field_id);
				//wpuef_var_dump($value);
				if(is_array($value))
				{
					foreach($value as $key => $submitted_value)
						if($submitted_value == "null" || $submitted_value == "")
							unset($value[$key]);
					$value = empty($value) ? null : $value;
					
				}
				//wpuef_var_dump($value);
				//wpuef_var_dump(isset($value));
				if(isset($value))
				{
					//wpuef_var_dump(0);
					update_user_meta( $user_id, $this->id_prefix.$field_id, $value );
				}
				else
				{
					//wpuef_var_dump($this->id_prefix.$field_id);
					delete_user_meta( $user_id, $this->id_prefix.$field_id);
				}
				//Wp override
				$this->check_and_overwrite_wp_or_woocommerce_fields($user_id, $field_id, $value);
			}
		}	
		//wp_die();
	}
	public function save_field( $field_id, $value, $user_id = null)
	{
		if(!isset($user_id))
			$user_id = get_current_user_id();
	
		update_user_meta( $user_id, $this->id_prefix.$field_id, $value );
		//Wp override
		$this->check_and_overwrite_wp_or_woocommerce_fields($user_id, $field_id, $value);
	}
	private function check_and_overwrite_wp_or_woocommerce_fields($user_id, $field_id, $value)
	{
		global $wpuef_field_model;
		
		//Wp_override
		$override_data = $wpuef_field_model->is_overwriting_wp_or_woocommerce_field($field_id, $value);
		foreach((array)$override_data as $key_name => $field_value)
		{
			if($key_name != 'password' || !empty($field_value))
				update_user_meta( $user_id, $key_name, $field_value );	
			if($key_name == 'password') //In case of password, the wcuf field meta is deleted otherwise pass would be non encryted and saved on db
				delete_user_meta( $user_id, $this->id_prefix.$field_id, $field_value );
		}
	}
	public function get_field( $field_id, $user_id = null)
	{
		global $wpdb;
		if(!isset($user_id))
			$user_id = get_current_user_id();
		
		/* $query = "SELECT user_meta.meta_value 
				  FROM {$wpdb->usermeta} as user_meta
				  WHERE user_meta.user_id = {$user_id} 
				  AND  user_meta.meta_key = '".$this->id_prefix.$field_id."' "; 
		$result = $wpdb->get_results($query,ARRAY_N);*/
		
		$result = get_user_meta($user_id , $this->id_prefix.$field_id );
		$result = isset($result) && isset($result[0])  ? $result[0] : null;
		
		return $result;	
	}
	public function delete_fields($files_to_delete, $user_id = null)
	{
		global $wpdb;
		if(!isset($user_id))
			$user_id = get_current_user_id();
		
		$fields_to_remove  = array();
		foreach($files_to_delete as $field_id => $data)
			$fields_to_remove[] = $this->id_prefix.$field_id;
			
		if(!empty($fields_to_remove))
		{
			
			$fields_to_remove = implode("','", $fields_to_remove);
			$query_string = "DELETE  
							FROM {$wpdb->usermeta}
							WHERE meta_key IN ('{$fields_to_remove}') 
							AND user_id = {$user_id} ";
				
			
			$wpdb->get_results($query_string);
		}
	}
	public function delete_unused_fields($old_fields, $new_fields, $user_id = null)
	{
		if(!isset($user_id))
			$user_id = get_current_user_id();
		
		global $wpdb;
		$fields_to_remove = array();
		$fields_to_remove_object = array();
		$new_ids = array();
		
		foreach($new_fields as $new_field)
		{
			$new_ids[] = $new_field->cid;
		}
		
		if(isset($old_fields) && is_array($old_fields))
			foreach($old_fields as $old_field)
				if(!in_array($old_field->cid,$new_ids))
				{
					$fields_to_remove[] = $this->id_prefix.$old_field->cid;
					$fields_to_remove_object[] = $old_field;
				}
		
		if(!empty($fields_to_remove))
		{
			
			$fields_to_remove = implode("','", $fields_to_remove);
			$query_string = "DELETE  
							FROM {$wpdb->usermeta}  
							WHERE meta_key IN ('{$fields_to_remove}') ";
							
			//return 
			$wpdb->get_results($query_string);
		}
		
		return $fields_to_remove_object;
	}
	public function get_roles()
	{
		global $wp_roles;
		$result = array();
		foreach((array)$wp_roles->roles as $role_code => $role_name)
			$result[$role_code] = $role_name["name"];
			
		return $result;
	}
	public function field_can_be_displayed_for_current_user($field_data)
	{
		if(current_user_can('administrator'))
			return true;
		
		/* if(!isset($field_data->visible_per_roles) || empty($field_data->visible_per_roles))
			return true; */
		
		$roles_array = array();
		foreach($field_data as $name => $value)
		{
			if(strpos($name, 'visible_per_roles') !== false)
			{
				if($value)
				{
					$name = str_replace(array("visible_per_roles", "[", "]"), "", $name);
					$roles_array[$name] = $name;
				}
			}
		}
		
		//wpuef_var_dump($roles_array);
		if(empty($roles_array))
			return true;
		
		$customer_id = get_current_user_id();
		if($customer_id == 0)
			return false;
	
		$customer_info = get_userdata($customer_id);
		return !empty( $customer_info->roles ) && is_array( $customer_info->roles ) && count(array_intersect($roles_array, $customer_info->roles)) > 0;
	}
}
?>