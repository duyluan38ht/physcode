<?php 
class WPUEF_Order
{
	var $id_prefix = '[wpuef] ';
	public function __construct()
	{
		add_action('before_delete_post', array(&$this, 'before_deleting_order'));
		//add_action('delete_post_meta', array(&$this, 'on_meta_delete_check_if_file'),10, 4);
		add_action('delete_postmeta', array(&$this, 'on_meta_delete_check_if_file'), 10, 1);
	}
	static function get_customer_id($order)
	{
		if(version_compare( WC_VERSION, '2.7', '<' ))
			return $order->customer_user;
		
		return $order->get_customer_id();
	}
	//public function on_meta_delete_check_if_file($meta_id, $object_id, $meta_key, $meta_value)
	public function on_meta_delete_check_if_file($meta_id)
	{
		global $wpuef_file_model;
		/* $meta_id = $_POST['id'];*/
		$meta = get_metadata_by_mid( 'post', $meta_id ); 
		if(!is_object($meta))
			return;
		$order_metas = $this->get_order_wpuef_meta($meta->post_id);
		//wpuef_var_dump($meta);
		//wpuef_var_dump($meta_id);
		if(!empty($order_metas))
		{
			$files_to_delete = array();
			foreach($order_metas as $order_meta)
			{
				if($order_meta['is_file_path'] && $order_meta['is_file_path'] == $meta_id)
					$files_to_delete[] = array('absolute_path' => $order_meta['value']);
			}
			if(!empty($files_to_delete))
				$wpuef_file_model->delete_files($files_to_delete);
		}
	}
	public function before_deleting_order($order_id)
	{
		global $post_type, $wpuef_file_model;   
		if($post_type !== 'shop_order') 
			return;

		$order_metas = $this->get_order_wpuef_meta($order_id);
		if(!empty($order_metas))
		{
			$files_to_delete = array();
			foreach($order_metas as $order_meta)
			{
				if($order_meta['is_file_path'])
					$files_to_delete[] = array('absolute_path' => $order_meta['value']);
			}
			if(!empty($files_to_delete))
				$wpuef_file_model->delete_files($files_to_delete);
		}
	}
	public function get_order_wpuef_meta($order_id)
	{
		$metas = array();
		if(version_compare( WC_VERSION, '2.7', '<' ))
		{
			global $wpdb;
			$query = "SELECT * 
					  FROM {$wpdb->postmeta} 
					  WHERE meta_key LIKE '{$this->id_prefix}%' 
					  AND post_id = {$order_id}"; 
			$results = $wpdb->get_results($query);
			if($results)
				foreach($results as $result)
				{
					//wpuef_var_dump($result->meta_id);
					$metas[] = array('id'=> $result->meta_id, 'label' => $result->meta_key, 'value' => $result->meta_value, 'is_file_path' => file_exists($result->meta_value), 'is_url' => filter_var($result->meta_value, FILTER_VALIDATE_URL));
				}
		}
		else 
		{
			$error = false;
			try{
				$order = wc_get_order($order_id);
			}catch(Exception $e){$error = true;}
			
			if($error || !isset($order) || $order == false)
				return $metas;
			
			$results = $order->get_meta_data();
			if($results)
				foreach($results as $result)
				{
					//wpuef_var_dump($result->id);
					if(substr($result->key,0,strlen($this->id_prefix)) == $this->id_prefix)
						$metas[] = array('id' => $result->id, 'label' => $result->key, 'value' => $result->value, 'is_file_path' => file_exists($result->value), 'is_url' => filter_var($result->value, FILTER_VALIDATE_URL));
				}
		}
		return $metas;
	}
	public static function get_id($order)
	{
		if(version_compare( WC_VERSION, '2.7', '<' ))
			return $order->id;
		
		return $order->get_id();
	}
	public function save_fields($fields, $order_id)
	{
		global $wpuef_shortcodes,$wpuef_file_model ;
		$order = wc_get_order($order_id);//new WC_Order($order_id);
		
		foreach($fields as $field_id => $value)
		{
			//$cid_to_value[$field_id] = $value;
			if($field_id == 'files' || $field_id == 'files_to_copy') //wpuef_options[files][{cid}]
			{
				$file_info = $field_id == 'files' ? $wpuef_file_model->save_order_files($order_id, $value) : $wpuef_file_model->copy_to_order_files($order_id, $value);
				
				/*   Format:
					$file_info[$id]['absolute_path'] 
				    $file_info[$id]['url']  */
				
				foreach($file_info as $id => $file_infos)
				{
					
					//Key label 
					$label = wpuef_get_field_label($id);
					$file_info[$id]['is_file'] = true;
					
					$this->save_order_meta_field($order, $this->id_prefix.$label." ".__('(Absolute path)','wp-user-extra-fields'), $file_infos['absolute_path']);
					$this->save_order_meta_field($order, $this->id_prefix.$label." ".__('(Url)','wp-user-extra-fields'), $file_infos['url']);
				} 
			}
			else
			{
				//Key label 
				$label =  wpuef_get_field_label($field_id);
				
				$this->save_order_meta_field($order, $this->id_prefix.$label, $wpuef_shortcodes->wpuef_show_field_value(array('field_id'=>$field_id)));
			}
		}
	}
	public function save_order_meta_field($order, $key_name, $key_value)
	{
		if(version_compare( WC_VERSION, '2.7', '<' ))
			update_post_meta( $order->id, $key_name, $key_value );
		else
			$order->update_meta_data($key_name, $key_value);
				
		if(version_compare( WC_VERSION, '2.7', '>' ))	
			$order->save( );
	}
}
?>