<?php 
class WPUEF_CheckoutPage
{
	public function __construct()
	{
		add_action('wp_head', array( &$this, 'add_js_scripts'));
	}
	public function add_js_scripts()
	{
		if(!is_admin() && function_exists('is_checkout') &&  @is_checkout())
		{
			$woocommerce_enable_guest_checkout = get_option( 'woocommerce_enable_guest_checkout' );
			$woocommerce_enable_guest_checkout = isset($woocommerce_enable_guest_checkout) ? $woocommerce_enable_guest_checkout : 'no';
			wp_enqueue_script('wctbp-checkout-page', WPUEF_PLUGIN_PATH.'/js/wpuef-frontend-checkout-page.js', array('jquery'));
			?>
			<script type="text/javascript">
				//var wpuef_ajax_url = '<?php echo admin_url('admin-ajax.php'); ?>';
				var wpuef_is_logged = <?php echo is_user_logged_in() || $woocommerce_enable_guest_checkout == 'no' ? 'true' : 'false'; ?>;
			</script>
			<?php
		}
	}
}
?>