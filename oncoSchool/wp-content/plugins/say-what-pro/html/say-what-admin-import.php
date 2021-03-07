<div>
<p><?php _e( 'Use this form to import replacements from a CSV file. The file should have a header row, and the following four columns:', 'say_what' ); ?></p>
<ol>
	<li><?php _e( 'Original string', 'say_what' ); ?></li>
	<li><?php _e( 'Text domain', 'say_what' ); ?></li>
	<li><?php _e( 'Text context', 'say_what' ); ?></li>
	<li><?php _e( 'Replacement string', 'say_what' ); ?></li>
	<?php if ( $this->settings->show_multi_lingual() ) : ?>
		<li><?php _e( 'Language code', 'say_what' ); ?></li>
	<?php endif; ?>
</ol>
<p><?php _e( 'You can generate a suitable file by exporting your current replacements on the &quot;Manage replacements&quot; tab.', 'say_what' ); ?></p>
<hr>
<form enctype="multipart/form-data" id="import-upload-form" method="post">
	<?php wp_nonce_field( 'say-what-import' ); ?>
	<p><label for="upload"><?php _e( 'Choose a file from your computer:', 'say_what' ); ?></label></p>
	<p><input type="file" id="say-what-import-file" name="say_what_import_file" size="25"></p>
	<p><input type="submit" class="button button-primary" value="Upload file and import"></p>
</form>
