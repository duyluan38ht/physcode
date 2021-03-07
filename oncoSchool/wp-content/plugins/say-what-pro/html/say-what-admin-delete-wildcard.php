<p><?php printf( __( 'Are you sure you want to delete the wildcard swap for &quot;%s&quot;?', 'say_what' ), esc_html( $wildcard->original ) ); ?></p>
<p>
	<a href="tools.php?page=say_what_admin&amp;say_what_action=delete-wildcard-confirmed&amp;id=<?php echo urlencode( $_GET['id'] ); ?>&amp;nonce=<?php echo urlencode( $_GET['nonce'] ); ?>" class="button button-primary"><?php _e( 'Yes', 'say_what' ); ?></a> <a href="tools.php?page=say_what_admin&amp;say_what_action=wildcards" class="button"><?php _e( 'No', 'say_what' ); ?></a>
