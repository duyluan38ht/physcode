<form method="post" action="tools.php?page=say_what_admin&say_what_action=discovery">
	<?php wp_nonce_field( 'say_what_pro_discovery_enable' ); ?>
	<input type="submit" name="enable" value="<?php esc_attr_e( 'Enable', 'say_what' ); ?>" class="button button-primary">
</form>
