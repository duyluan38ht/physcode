<?php 
class WPUEF_OrderPage
{
	function __construct()
	{
		//Order detail page (admin)
		//add_action( 'woocommerce_admin_order_data_after_shipping_address', array( &$this,'woocommerce_after_shipping_address_meta_box') );
		add_action( 'woocommerce_admin_order_data_after_billing_address', array( &$this,'woocommerce_custom_checkout_field_display_admin_order_meta'), 10, 1 );		
		add_action( 'woocommerce_admin_order_data_after_order_details', array( &$this,'woocommerce_after_shipping_address_meta_box') ); //Order details
		
		add_action( 'woocommerce_process_shop_order_meta', array( &$this, 'woocommerce_on_save_order_details_admin_page' ), 5, 2 );//save order
	}
	
	public function woocommerce_after_shipping_address_meta_box( $order )
	{
		global $wpuef_htmlHelper, $wpuef_option_model;
		$user_id = WPUEF_Order::get_customer_id($order);
		
		if(isset($user_id) && $user_id > 0 && $wpuef_option_model->get_woocommerce_admin_order_page_show_extra_fields())
			$wpuef_htmlHelper->woocommerce_render_order_edit_form_extra_fields($user_id);
	}
	public function woocommerce_on_save_order_details_admin_page( $post_id, $post )
	{
		$order = wc_get_order($post_id);
		$user_id = WPUEF_Order::get_customer_id($order);
		if(isset($user_id) && $user_id > 0 && isset($order) && $order != false)
		{
			global $wpuef_user_model;
			if(isset($_POST['wpuef_options']))
				$wpuef_user_model->save_fields($_POST['wpuef_options'], $user_id );
		}
	}
	function woocommerce_custom_checkout_field_display_admin_order_meta($order)
	{
		global $wpuef_order_model;
		//echo '<p><strong>'.__('My Field').':</strong> ' . get_post_meta( WPUEF_Order::get_id($order), 'My Field', true ) . '</p>';
		$order_metas = $wpuef_order_model->get_order_wpuef_meta(WPUEF_Order::get_id($order));	
		if(empty($order_metas))
			return;
		echo '<h3>'.__('Order extra fields','wp-user-extra-fields').'</h3>';
		foreach($order_metas as $order_meta)
		{
			if(!$order_meta['is_file_path'])
			{
				if($order_meta['is_url'])
					echo '<p><strong>'.str_replace($wpuef_order_model->id_prefix, "",$order_meta['label']).':</strong><br/><a href="' .$order_meta['value'] .'" target="_blank" download>'.__('Download','wp-user-extra-fields').'</a></p>';
				else
					echo '<p><strong>'.str_replace($wpuef_order_model->id_prefix, "",$order_meta['label']).':</strong><br/>' . $order_meta['value'] . '</p>';
			}
		}
	}
}
?>