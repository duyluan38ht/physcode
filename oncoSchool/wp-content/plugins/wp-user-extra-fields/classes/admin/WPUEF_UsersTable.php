<?php 
class WPUEF_UsersTable
{
	public function __construct()
	{
		add_filter( 'manage_users_columns', array(&$this,'add_column') );
		add_filter( 'manage_customers_columns', array(&$this,'add_column') );
		
		add_filter( 'manage_users_custom_column', array(&$this,'insert_colum_data'), 10, 3 );
		add_filter( 'manage_customers_custom_column', array(&$this,'insert_colum_data'), 10, 3 );
		
		add_filter( 'manage_users_sortable_columns',  array(&$this,'make_column_sortable'));
		
		add_action( 'pre_user_query', array(&$this,'sort_by_extra_fields'), 9 );
		//add_filter('parse_query',array( &$this,'search_by_extra_fields')); 
		//add_action('restrict_manage_users', array( &$this,'add_extra_search_box'));
		//add_filter( 'user_search_columns', array( &$this, 'woocommerce_shop_order_search_extra_field'), 10, 3 );

	}
	/* function woocommerce_shop_order_search_extra_field( $search_columns, $search, $wp_user_query  ) 
	{
		global $wpuef_field_model,$wpuef_user_model;
		$field_data = $wpuef_field_model->get_all_fields_data();
		
		wpuef_var_dump(isset($field_data));
		if(isset($field_data))
			foreach($field_data->fields as $field)
			{
				wpuef_var_dump($wpuef_user_model->id_prefix.$field->cid);
				$search_columns[] = $wpuef_user_model->id_prefix.$field->cid;
			}
		 return $search_columns;
	}*/
	function add_column( $columns ) 
	{
		global $wpuef_option_model;
		$extra_fields = $wpuef_option_model->get_option('json_fields_string');
		if($extra_fields)
			foreach($extra_fields->fields as $extra_field)
				if( isset($extra_field->add_field_to_user_table_colum) && ($extra_field->add_field_to_user_table_colum))
				{
					$columns['wpuef_'.$extra_field->cid] = $extra_field->label;
				}
	
		return $columns;
	}
	function insert_colum_data( $val, $column_name, $user_id ) {
		//$user = get_userdata( $user_id );
		global $wpuef_option_model,$wpuef_shortcodes;
		$extra_fields = $wpuef_option_model->get_option('json_fields_string');
		
		if($extra_fields)
			foreach($extra_fields->fields as $extra_field)
				if($column_name == 'wpuef_'.$extra_field->cid)
					return $wpuef_shortcodes->wpuef_show_field_value(array('user_id' => $user_id, 'field_id' =>$extra_field->cid));
						
		return $val;
	}
	function make_column_sortable( $columns ) 
	{
		global $wpuef_option_model;
		$extra_fields = $wpuef_option_model->get_option('json_fields_string');
		if($extra_fields)
			foreach($extra_fields->fields as $extra_field)
				if( isset($extra_field->add_field_to_user_table_colum) && ($extra_field->add_field_to_user_table_colum))
				{
					if($extra_field->field_type != "country_and_state" && 
						$extra_field->field_type != 'title_no_input' && 
						$extra_field->field_type != 'checkboxes' && 
						$extra_field->field_type != 'paragraph' && 
						$extra_field->field_type != 'file' && 
						$extra_field->field_type != 'radio'  )
					$columns['wpuef_'.$extra_field->cid] = 'wpuef_cid_'.$extra_field->cid;
				}
	 
		return $columns;
	}
	public function add_extra_search_box()
	{
		wp_enqueue_style( 'wpuef-admin-user-table',  WPUEF_PLUGIN_PATH.'/css/wpuef-admin-user-table.css' );
		wp_enqueue_script( 'wpuef-backend-user-table',  WPUEF_PLUGIN_PATH.'/js/wpuef-backend-user-table.js' );
		?>
		<div class="wpuef-search-box">
			<input id="wpuef-search-input" name="wpuef_extra_field_search" value="" type="text">
			<input id="wpuef-search-submit" class="button" value="<?php _e('Search user by extra field', 'wp-user-extra-fields') ?>" type="submit">
		</div>
		<?php 
	}
	function sort_by_extra_fields( $query )  
	{
		global $wpuef_option_model, $wpdb;
		/*wpuef_var_dump($query);
		 wp_die();
		 */
		if( ! is_admin() || !isset($wpuef_option_model) || !isset($wpdb))
			return $query;
	
		$orderby = $query->get( 'orderby');
		$extra_fields = $wpuef_option_model->get_option('json_fields_string');
		//wpuef_var_dump($orderby) ;
		//Order user list according exra field value
		 if($extra_fields)
			foreach($extra_fields->fields as $extra_field)
				if( isset($extra_field->add_field_to_user_table_colum) && ($extra_field->add_field_to_user_table_colum))
				{
					if( 'wpuef_cid_'.$extra_field->cid == $orderby )  
					{
						//wpuef_var_dump("ok");
						//wpuef_var_dump($orderby) ;
		
						//$query->set('meta_key', 'wpuef_cid_'.$extra_field->cid );
						//$query->set('orderby','meta_value'); //'meta_value_num' to sort numerically
						// $query->query_orderby = 'ORDER BY wpuef_cid_'.$extra_field->cid.' '.$query->query_vars["order"];
						$query->query_from .= " LEFT JOIN $wpdb->usermeta AS wpuef_metavalue ON ($wpdb->users.ID = wpuef_metavalue.user_id) ";
						$query->query_where .= " AND (wpuef_metavalue.meta_key = 'wpuef_cid_".$extra_field->cid."' OR wpuef_metavalue.meta_value is NULL) ";
						$query->query_orderby = " ORDER BY ISNULL(wpuef_metavalue.meta_value), wpuef_metavalue.meta_value ".$query->query_vars["order"]."";
					
						//wp_die();
					}
				} 
		
		return $query;
		
		// Moved into the search_by_extra_fields() method
		//if ( $query->query_vars['search'] )
		if ( isset($_REQUEST['wpuef_extra_field_search']) && $_REQUEST['wpuef_extra_field_search'] != "")
		{
			/* wpuef_var_dump($_GET['wpuef_extra_field_search']);
			wp_die() ;  */
			
			$search_query = trim( $_REQUEST['wpuef_extra_field_search'], '*' );
			//if ( $_REQUEST['s'] == $search_query )
			if ( $_REQUEST['wpuef_extra_field_search'] == $search_query )
			{
				global $wpuef_field_model,$wpuef_user_model, $wpdb;
				$field_data = $wpuef_field_model->get_all_fields_data();
				
				//$query->query_fields = " * "; 
				if(isset($field_data))
				{
					//$wpdb->query('SET SQL_BIG_SELECTS=1');
					
					$search_by = array();
					$already_and = false;
					$first_run = true;
					$end_where = "";
					$custom_query = array('relation' => 'OR');
					//$qv = &$query->query_vars;
					foreach($field_data->fields as $field)
					{
						//User left join to include user with no values
						/* $query->query_from .= " JOIN {$wpdb->usermeta} AS wpuef_meta_".$field->cid." ON wpuef_meta_".$field->cid.".user_id = {$wpdb->users}.ID AND  wpuef_meta_".$field->cid.".meta_key = '".$wpuef_user_model->id_prefix.$field->cid."' "; //OR wpuef_meta_".$field->cid.".meta_value is NULL
						//$search_by[] = "wpuef_meta_".$field->cid;
						//$end_where .= " AND (wpuef_meta_".$field->cid.".meta_key = 'wpuef_cid_".$field->cid."' OR wpuef_meta_".$field->cid.".meta_value is NULL ) ";
						
						if(!$already_and )
						{
							//$query->query_where .= " OR (";
							$query->query_where .= " AND (";
							$already_and = true;
						}
						if(!$first_run)
							$query->query_where .= " OR ";
						$query->query_where .= " wpuef_meta_".$field->cid.".meta_value LIKE '%".$search_query."%' ";
						
						$first_run = false;  */
						
						 $custom_query[] = 
						  /*  array
						   ( */
							 //'relation' => 'OR',
							  array(
								'key' => "wpuef_cid_".$field->cid,
								'compare' => 'LIKE',
								 'value' => $search_query
							 /*  ), */
							 /*  array(
								'key' => "wpuef_cid_".$field->cid,
								'compare' => 'NOT EXISTS',
								'value' => ''
							  ),
							  array(
								'key' => "wpuef_cid_".$field->cid,
								'compare' => '=',
								'value' => null
							  ) */
						  ); 
					}

					//$query->meta_query = new WP_Meta_Query( $custom_query);
					$query->query_vars['meta_query'][] =  $custom_query;
					/* $query->query_where .= ")";
					$query->query_where .= $end_where; */
					//$query->query_where .= $query->get_search_sql( $search_query, $search_by, false );
				}
			}
		}
		/*wpuef_var_dump($query); 
		wpuef_var_dump($query->query_from);
		wpuef_var_dump($query->query_where);
		wp_die() ;  */
		return $query;
	}
	function search_by_extra_fields($query)
	{
		global $wpuef_field_model,$wpuef_user_model, $wpdb, $pagenow;
		$field_data = $wpuef_field_model->get_all_fields_data();
		if($pagenow !='users.php' || !isset($_REQUEST['s']) || $_REQUEST['s'] == "")
			return;
		
		$search_query = trim($_REQUEST['s'], '*' );
		$custom_query = array('relation' => 'OR');
		$qv = &$query->query_vars;
		if(isset($field_data))
		{
			foreach($field_data->fields as $field)
			{
				$custom_query[] = 
							  /*  array
							   ( */
								 //'relation' => 'OR',
								  array(
									'key' => "wpuef_cid_".$field->cid,
									'compare' => 'LIKE',
									 'value' => $search_query
								 /*  ), */
								 /*  array(
									'key' => "wpuef_cid_".$field->cid,
									'compare' => 'NOT EXISTS',
									'value' => ''
								  ),
								  array(
									'key' => "wpuef_cid_".$field->cid,
									'compare' => '=',
									'value' => null
								  ) */
							  ); 
			}
			
			$qv['meta_query'][] = $custom_query;
			/* wpuef_var_dump($pagenow);
			 wpuef_var_dump( $qv['post_type']);
			 wpuef_var_dump($qv['meta_query']);
			wp_die();  */
		}
	
	}
}
?>