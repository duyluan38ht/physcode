<p><?php printf( __( 'Are you sure you want to delete the replacement string for &quot;%s&quot;?', 'say_what' ), esc_html( $replacement->orig_string ) ); ?></p>
<p>
	<a href="tools.php?page=say_what_admin&amp;say_what_action=delete-confirmed&amp;id=<?php echo urlencode( $_GET['id'] ); ?>&amp;nonce=<?php echo urlencode( $_GET['nonce'] ); ?>" class="button button-primary"><?php _e( 'Yes', 'say_what' ); ?></a> <a href="tools.php?page=say_what_admin" class="button"><?php _e( 'No', 'say_what' ); ?></a>
