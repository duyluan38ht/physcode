<form action="tools.php?page=say_what_admin&amp;say_what_action=addeditwildcards" method="post">
	<input type="hidden" name="say_what_save_wildcard" value="1">
	<?php wp_nonce_field( 'swaddedit', 'nonce' ); ?>
	<?php if ( ! empty( $wildcard->wildcard_id ) ) : ?>
		<input type="hidden" name="say_what_wildcard_id" value="<?php echo esc_attr( $wildcard->wildcard_id ); ?>">
	<?php endif; ?>
	<p>
		<label for="say_what_original"><?php _e( 'Look for', 'say_what' ); ?></label><br/>
		<textarea class="say_what_original" name="say_what_original" rows="1" cols="120"><?php echo esc_textarea( $wildcard->original ); ?></textarea><br>
		<em><?php _e( 'Note: This is case-sensitive', 'say_what' ); ?></em>
	</p>
	<p>
		<label for="say_what_replacement"><?php _e( 'Replace with', 'say_what' ); ?></label><br/>
		<textarea class="say_what_replacement" name="say_what_replacement" cols="120" rows="1"><?php echo esc_textarea( $wildcard->replacement ); ?></textarea>
	</p>
	<?php if ( $this->settings->show_multi_lingual() ) : ?>
	<p>
		<label for="say_what_lang"><?php _e( 'Affected language', 'say_what' ); ?></label><br/>
		<select name="say_what_lang">
			<?php foreach ( $languages as $lang ) : ?>
				<option value="<?php echo esc_attr( $lang['language'] ); ?>" <?php selected( $wildcard->lang, $lang['language'] ); ?>><?php echo esc_html( $lang['english_name'] ); ?>
					<?php if ( ! empty( $lang['language'] ) ) : ?>
						(<?php echo esc_html( $lang['language'] ); ?>)
					<?php endif; ?>
				</option>
			<?php endforeach; ?>
		</select>
	</p>
	<?php else : ?>
		<input type="hidden" name="say_what_lang" value="<?php echo esc_attr( $wildcard->lang ); ?>">
	<?php endif; ?>
	<p>
		<input type="submit" class="button-primary" value="<?php  ! empty( $wildcard->wildcard_id ) ? _e( 'Update', 'say_what' ) : _e( 'Add', 'say_what' ); ?>">
	</p>
</form>
