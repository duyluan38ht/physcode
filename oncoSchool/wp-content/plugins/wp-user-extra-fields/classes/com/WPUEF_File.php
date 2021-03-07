<?php 
class WPUEF_File
{
	var $to_remove_from_file_name = array(".php", "../");
	var $already_saved_files = array();
	public function __construct()
	{
		add_action( 'wp_ajax_wpuef_file_chunk_upload', array( &$this, 'ajax_manage_file_chunk_upload' ));
		add_action( 'wp_ajax_nopriv_wpuef_file_chunk_upload', array( &$this, 'ajax_manage_file_chunk_upload' ));
		
		add_action( 'wp_ajax_wpuef_delete_tmp_uploaded_file', array( &$this, 'delete_tmp_uploaded_file' ));
		add_action( 'wp_ajax_nopriv_wpuef_delete_tmp_uploaded_file', array( &$this, 'delete_tmp_uploaded_file' ));
		
		add_action( 'wp_ajax_wpuef_get_tmp_uploaded_file_preview', array( &$this, 'get_tmp_uploaded_file_preview' ));
		add_action( 'wp_ajax_nopriv_wpuef_get_tmp_uploaded_file_preview', array( &$this, 'get_tmp_uploaded_file_preview' ));
		
		add_action('init', array( &$this, 'delete_unused_tmp_files' ));
	}
	public function generate_unique_file_name($dir, $name, $ext = "")
	{
		return rand(0,1000000)."_".$name.$ext;
	}
	public function delete_all_user_files($user_id)
	{
		$upload_dir = wp_upload_dir();
		try{
				//@unlink($upload_dir['basedir'].'/wpuef/'.$user_id);
				$this->recursiveRemoveDirectory($upload_dir['basedir'].'/wpuef/'.$user_id);

			}catch(Exception $e){};
	}
	private function recursiveRemoveDirectory($directory)
	{
		foreach(glob("{$directory}/*") as $file)
		{
			if(is_dir($file))
				$this->recursiveRemoveDirectory($file);
			else 
				@unlink($file);
		}
		@rmdir($directory);
	}
	function ajax_manage_file_chunk_upload()
	{
		global $wpuef_session_model ;
		
		$buffer = 5242880; //1048576; //1mb
		$target_path = $this->get_temp_dir_path();
		$tmp_name = $_FILES['wpuef_file_chunk']['tmp_name'];
		$size = $_FILES['wpuef_file_chunk']['size'];
		//$chunk_name = $_FILES['wpuef_file_chunk']['name'];
		//$chunk_name = $this->get_random_chunk_name();
		//$file_tmp_name = $_POST['wpuef_current_file_tmp_name'];
		//$chunk_name = $file_tmp_name ."_".$_POST['wpuef_current_chunk_num'];
		$current_chunk_num = $_POST['wpuef_current_chunk_num'];
		$file_name = str_replace($this->to_remove_from_file_name, "",$_POST['wpuef_file_name']);
		$tmp_file_name = $_POST['wpuef_current_upload_session_id']."_".$file_name;
		$upload_field_name = str_replace($this->to_remove_from_file_name, "", $_POST['wpuef_upload_field_name']);
		$wpuef_is_last_chunk = $_POST['wpuef_is_last_chunk'] == 'true' ? true : false;
	

		$com = fopen($target_path.$tmp_file_name, "ab");
		$in = fopen($tmp_name, "rb");
			if ( $in ) 
				while ( $buff = fread( $in, $buffer ) ) 
				   fwrite($com, $buff);
				 
			fclose($in);
		fclose($com);
		
		wp_die();
	}
	function get_tmp_uploaded_file_preview()
	{
		$file_to_preview = isset($_POST['file_to_preview']) ? $_POST['file_to_preview'] : null;
		$preview_width = isset($_POST['preview_width']) ? $_POST['preview_width'] : 80;
		$target_path = $this->get_temp_dir_path().$file_to_preview;
		$wp_path = wp_upload_dir();
		$image_url = $wp_path['baseurl'].'/wpuef/tmp/'.$file_to_preview;
		
		if($this->is_image($target_path))
		{
			echo '<img src="'.$image_url.'" width="'.$preview_width.'" />';
		}
		wp_die();
	}
	function delete_tmp_uploaded_file()
	{
		$file_to_delete = isset($_POST['file_to_delete']) ? $_POST['file_to_delete'] : null;
		$target_path = $this->get_temp_dir_path();
		
		if(isset($file_to_delete))
		{
			try{
				@unlink($target_path.$file_to_delete);
			}catch(Exception $e){};
		}
		wp_die();
	}
	function delete_unused_tmp_files()
	{
		$files = glob($this->get_temp_dir_path()."*");
		$now   = time();

		if(is_array($files) && !empty($files))
			foreach ($files as $file) 
				if (is_file($file)) 
				  if (basename ($file) != "index.html" && $now - filemtime($file) >= 60 * 60 /* * 24 * 2 */) //1 hpur
				  {
					  try{
							@unlink($file);
						}catch(Exception $e){};
				  }
			
		  
	}
	public function get_temp_dir_path($order_id = null, $baseurl = false)
	{
		$upload_dir = wp_upload_dir();
		
		$temp_dir = !$baseurl ? $upload_dir['basedir']. '/wpuef/' : $upload_dir['baseurl']. '/wpuef/';
		$temp_dir .= isset($order_id) && $order_id !=0 ? $order_id.'/': 'tmp/';
		
		if(!$baseurl)
		{
			if (!file_exists($temp_dir)) 
					mkdir($temp_dir, 0775, true);
			
			if( !file_exists ($temp_dir.'index.html'))
				//touch ($temp_dir.'index.html');
				$this->create_empty_file  ($temp_dir.'index.html');
		}
		return $temp_dir;
	}
	private function create_empty_file($path)
	{
		$file = fopen($path, 'w'); 
		fclose($file); 
	}
	public function save_order_files($order_id, $files_data)
	{
		global $wpuef_user_model;
		 $links_to_notify_via_mail = array();
		 $links_to_attach_to_mail = array();
		 $file_info = array();
		 foreach($files_data as $id => $file_data) //$files_data[{cid}]
		 {
			//decode data
			$upload_dir = wp_upload_dir();
			$upload_complete_dir = $upload_dir['basedir']. '/wpuef/orders/'.$order_id.'/';
			if (!file_exists($upload_complete_dir)) 
					mkdir($upload_complete_dir, 0775, true);
			
			
			//ToDo: new method implementation
			/* $unique_file_name = $this->generate_unique_file_name(null, $_POST['wpuef_files'][$id]['file_name']);
			$ifp = fopen($upload_complete_dir.$unique_file_name, "w"); 
			fwrite($ifp, base64_decode($file_data)); 
			fclose($ifp);  */
			
			//new method 
			$tmp_file_folder = $this->get_temp_dir_path();
			$unique_file_name = $this->generate_unique_file_name(null, $_POST['wpuef_files'][$id]['file_name']);
			$prefix = $_POST['wpuef_files'][$id]['file_name_tmp_prefix']."_";
			$original_file_name = $_POST['wpuef_files'][$id]['file_name'];
			if(file_exists($tmp_file_folder.$prefix.$original_file_name) ) //In case the file has just been uploaded
				rename($tmp_file_folder.$prefix.$original_file_name, $upload_complete_dir.$unique_file_name);
			else //in case the file already exists: like for the one with "just one time upload" option enabled
			{
				$field_value = $wpuef_user_model->get_field( $_POST['wpuef_files'][$id]);
				copy ($field_value['absolute_path'], $upload_complete_dir.$unique_file_name);
			}
		
			if( !file_exists ($upload_dir['basedir'].'/wpuef/index.html'))
				//touch ($upload_dir['basedir'].'/wpuef/orders/index.html');
				$this->create_empty_file  ($upload_dir['basedir'].'/wpuef/orders/index.html');
				
			if( !file_exists ($upload_dir['basedir'].'/wpuef/orders/'.$order_id.'/index.html'))
				//touch ($upload_dir['basedir'].'/wpuef/orders/'.$order_id.'/index.html');
				$this->create_empty_file ($upload_dir['basedir'].'/wpuef/orders/'.$order_id.'/index.html');
			
			$file_info[$id]['absolute_path'] = $upload_complete_dir.$unique_file_name;
			$file_info[$id]['url'] = $upload_dir['baseurl'].'/wpuef/orders/'.$order_id.'/'.$unique_file_name;
			$file_info[$id]['id'] = $id;
		 }
		return $file_info;
	}
	public function copy_to_order_files($order_id, $files_data)
	{
		$file_info = array();
		foreach($files_data as $id => $file_data) //$files_data[{cid}]
		 {
			//decode data
			$upload_dir = wp_upload_dir();
			$upload_complete_dir = $upload_dir['basedir']. '/wpuef/orders/'.$order_id.'/';
			$upload_complete_url = $upload_dir['baseurl'].'/wpuef/orders/'.$order_id.'/';
			if (!file_exists($upload_complete_dir)) 
					mkdir($upload_complete_dir, 0775, true);
			
			$file_name = pathinfo($file_data['absolute_path']);
			$complete_file_name = $file_name['extension'] != "" ? $file_name['filename'].".".$file_name['extension'] : $file_name['filename'];
			copy($file_data['absolute_path'], $upload_complete_dir.$complete_file_name);
			
			if( !file_exists ($upload_dir['basedir'].'/wpuef/index.html'))
				//touch ($upload_dir['basedir'].'/wpuef/orders/index.html');
				$this->create_empty_file ($upload_dir['basedir'].'/wpuef/orders/index.html');
				
			if( !file_exists ($upload_complete_dir.'index.html'))
				//touch ($upload_complete_dir.'index.html');
				$this->create_empty_file ($upload_complete_dir.'index.html');
			
			$file_info[$id]['absolute_path'] = $upload_complete_dir.$complete_file_name;
			$file_info[$id]['url'] = $upload_complete_url.$complete_file_name;
			$file_info[$id]['id'] = $id;
		 }
		return $file_info;
	}
	public function save_files($user_id, $files_data)
	{
		 $links_to_notify_via_mail = array();
		 $links_to_attach_to_mail = array();
		 $file_info = array();
		 foreach($files_data as $id => $file_data) //$files_data[{cid}]
		 {
			 if(!isset($_POST['wpuef_files'][$id]['file_name']) || $_POST['wpuef_files'][$id]['file_name'] == "")
				 continue;
			 
			//decode data
			$upload_dir = wp_upload_dir();
			$upload_complete_dir = $upload_dir['basedir']. '/wpuef/'.$user_id.'/';
			if (!file_exists($upload_complete_dir)) 
					mkdir($upload_complete_dir, 0775, true);
				
			//old method with string encoding	
			/*$unique_file_name = $this->generate_unique_file_name(null, $_POST['wpuef_files'][$id]['file_name']);
			$ifp = fopen($upload_complete_dir.$unique_file_name, "w"); 
			fwrite($ifp, base64_decode($file_data)); 
			fclose($ifp); */
			
			//new method 
			$tmp_file_folder = $this->get_temp_dir_path();
			$unique_file_name = $this->generate_unique_file_name(null, $_POST['wpuef_files'][$id]['file_name']);
			$prefix = $_POST['wpuef_files'][$id]['file_name_tmp_prefix']."_";
			$original_file_name = $_POST['wpuef_files'][$id]['file_name'];
			$from_path = $tmp_file_folder.$prefix.$original_file_name;
			$to_path = $upload_complete_dir.$unique_file_name;
			
			if(isset($this->already_saved_files[$from_path]))
			{
				//wpuef_var_dump("already existing");
				return  $this->already_saved_files[$from_path];
			}
			
			
			if(file_exists ($from_path)) //in case of user registration on checkout, it has been already copyed
				rename($from_path, $to_path);
			
			if( !file_exists ($upload_dir['basedir'].'/wpuef/index.html'))
				//touch ($upload_dir['basedir'].'/wpuef/index.html');
				$this->create_empty_file ($upload_dir['basedir'].'/wpuef/index.html');
				
			
			if( !file_exists ($upload_dir['basedir'].'/wpuef/'.$user_id.'/index.html'))
				//touch ($upload_dir['basedir'].'/wpuef/'.$user_id.'/index.html');
				$this->create_empty_file ($upload_dir['basedir'].'/wpuef/'.$user_id.'/index.html');
			
			$file_info[$id]['absolute_path'] = $to_path;
			$file_info[$id]['url'] = $upload_dir['baseurl'].'/wpuef/'.$user_id.'/'.$unique_file_name;
			$file_info[$id]['id'] = $id;
			$this->already_saved_files[$from_path] = $file_info[$id];
			
			//$options passed via parameter
			/* foreach($options as $option)
			{
				if($option['id'] == $id && $option['notify_admin'] )
					array_push($links_to_notify_via_mail, array('title' => $file_info[$id]['title'], 'url'=> $file_info[$id]['url']));
				if($option['id'] == $id && $option['notify_admin'] && $option['notify_attach_to_admin_email'])
					array_push($links_to_attach_to_mail, $file_info[$id]['absolute_path'] );
			} */
				 
			
		 }
		 //Notification via mail
		/* if(count($links_to_notify_via_mail) > 0)
		{
			$this->email_sender = new wpuef_Email();
			$this->email_sender->trigger($links_to_notify_via_mail, $order, $links_to_attach_to_mail );	
		} */
		return $file_info;
	}
	function delete_files($files_to_delete)
	{
		if(!isset($files_to_delete) || empty($files_to_delete))
			return;
		
		foreach($files_to_delete as $file_data)
		{
			try{
				@unlink($file_data['absolute_path']);
			}catch(Exception $e){};
		}
	}
	function is_image($filepath)
	{
		if(@is_array($mime = getimagesize($filepath)))
		{
			switch($mime['mime'])
			{
				case 'image/jpeg': return true;
				case 'image/png': return true;
				case 'image/bmp': return true;
			}
		}
		
		return false;
	}
}
?>