<div class="wrap <?php echo $swp_additional_wrap_classes; ?>">
	<h2><?php _e( 'Text changes', 'say_what' ); ?>&nbsp;<a href="tools.php?page=say_what_admin&amp;say_what_action=addedit" class="add-new-h2"><?php _e( 'Add Replacement', 'say_what' ); ?></a></h2>
	<h2 class="nav-tab-wrapper">
		<a href="tools.php?page=say_what_admin" class="nav-tab <?php echo esc_attr( $default_active ); ?>"><?php _e( 'String replacements', 'say_what' ); ?></a>
		<a href="tools.php?page=say_what_admin&amp;say_what_action=discovery" class="nav-tab <?php echo esc_attr( $discovery_active ); ?>"><?php _e( 'String discovery', 'say_what' ); ?></a>
		<a href="tools.php?page=say_what_admin&amp;say_what_action=import" class="nav-tab <?php echo esc_attr( $import_active ); ?>"><?php _e( 'Import replacements', 'say_what' ); ?></a>
		<a href="tools.php?page=say_what_admin&amp;say_what_action=wildcards" class="nav-tab <?php echo esc_attr( $wildcards_active ); ?>"><?php _e( 'Wildcard swaps', 'say_what' ); ?></a>
	</h2>
