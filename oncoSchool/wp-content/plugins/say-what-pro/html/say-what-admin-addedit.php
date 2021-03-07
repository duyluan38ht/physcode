<p><?php _e( "Fill in the details of the original translatable string, the string's text domain, and the string you would like to use instead. For more information check out the <a href='http://plugins.leewillis.co.uk/doc_post/adding-string-replacement/'>getting started guide</a>." ); ?></p>
<form action="tools.php?page=say_what_admin&amp;say_what_action=addedit" method="post">
	<input type="hidden" name="say_what_save" value="1">
	<?php wp_nonce_field( 'swaddedit', 'nonce' ); ?>
	<?php if ( ! empty( $replacement->string_id ) ) : ?>
		<input type="hidden" name="say_what_string_id" value="<?php echo esc_attr( $replacement->string_id ); ?>">
	<?php endif; ?>
	<p>
		<label for="say_what_orig_string"><?php _e( 'Original string', 'say_what' ); ?></label><br>
		<span class="text-muted">Enter part of the string you're trying to replace to see suggestions</span><br>
		<textarea class="say_what_orig_string" name="say_what_orig_string" rows="1" cols="120"><?php echo esc_textarea( $replacement->orig_string ); ?></textarea>
		<div class="say_what_translated_string"></div>
	</p>
	<p>
		<label for="say_what_domain"><?php _e( 'Text domain', 'say_what' ); ?></label> <a href="http://plugins.leewillis.co.uk/doc_post/adding-string-replacement/" target="_blank" rel="noopener noreferrer"><i class="dashicons dashicons-info">&nbsp;</i></a><br>
		<span class="text-muted">Enter the plugin / theme text domain. If you selected a suggestion above this will have been filled in if required.</span><br>
		<input type="text" class="say_what_domain" name="say_what_domain" size="30" value="<?php echo esc_attr( htmlspecialchars( $replacement->domain ) ); ?>"><br>
	</p>
	<p>
		<label for="say_what_context"><?php _e( 'Text context', 'say_what' ); ?></label> <a href="http://plugins.leewillis.co.uk/doc_post/replacing-wordpress-strings-context/"  target="_blank" rel="noopener noreferrer"><i class="dashicons dashicons-info">&nbsp;</i></a><br>
		<span class="text-muted">Enter the string context. If you selected a suggestion above this will have been filled in if required.</span><br>
		<input type="text" class="say_what_context" name="say_what_context" size="30" value="<?php echo esc_attr( htmlspecialchars( $replacement->context ) ); ?>"><br>
	</p>
	<p>
		<label for="say_what_replacement_string"><?php _e( 'Replacement string', 'say_what' ); ?></label><br>
		<span class="text-muted">Enter your replacement string.</span><br>
		<textarea class="say_what_replacement_string" name="say_what_replacement_string" cols="120" rows="1"><?php echo esc_textarea( $replacement->replacement_string ); ?></textarea>
	</p>
	<?php if ( $this->settings->show_multi_lingual() ) : ?>
	<p>
		<label for="say_what_lang"><?php _e( 'Affected language', 'say_what' ); ?></label><br>
		<select name="say_what_lang">
			<?php foreach ( $languages as $lang ) : ?>
				<option value="<?php echo esc_attr( $lang['language'] ); ?>" <?php selected( $replacement->lang, $lang['language'] ); ?> <?php if ( $lang['language'] == ' separator' ) echo ' disabled'; ?>><?php echo esc_html( $lang['english_name'] ); ?>
					<?php if ( ! empty( $lang['language'] ) && $lang['language'] !== ' separator' ) : ?>
						(<?php echo esc_html( $lang['language'] ); ?>)
					<?php endif; ?>
				</option>
			<?php endforeach; ?>
		</select>
	</p>
	<?php else : ?>
		<input type="hidden" name="say_what_lang" value="<?php echo esc_attr( $replacement->lang ); ?>">
	<?php endif; ?>
	<p>
		<input type="submit" class="button-primary" value="<?php  ! empty( $replacement->string_id ) ? _e( 'Update', 'say_what' ) : _e( 'Add', 'say_what' ); ?>">
	</p>
</form>
