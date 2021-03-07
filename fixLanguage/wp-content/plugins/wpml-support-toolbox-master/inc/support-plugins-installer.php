<?php

add_action( 'tgmpa_register', 'wpmld_register_support_plugins' );
/**
 * Register the required plugins for this plugin.
 *
 * The variable passed to tgmpa_register_plugins() should be an array of plugin
 * arrays.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */
function wpmld_register_support_plugins() {
	/**
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins = array(		
		/** This is an example of how to include a plugin from the WordPress Plugin Repository */
		array(
			'name' => 'Classic Editor',
			'slug' => 'classic-editor',
		),
		array(
			'name' => 'WP File Manager',
			'slug' => 'wp-file-manager',
		),
		array(
			'name' => 'Duplicator',
			'slug' => 'duplicator',
		),
		array(
			'name' => 'Cloudways Migrator',
			'slug' => 'bv-cloudways-automated-migration',
		),
		array(
			'name' => 'All-in-one WP Migration',
			'slug' => 'all-in-one-wp-migration',
		),
		array(
			'name' => 'Updraft',
			'slug' => 'updraftplus',
		),
		array(
			'name' => 'Query Monitor',
			'slug' => 'query-monitor',
		),
		array(
			'name' => 'Debug This',
			'slug' => 'debug-this',
		),
		array(
			'name' => 'Debug Bar',
			'slug' => 'debug-bar',
		),
		array(
			'name' => 'Debug Bar: Actions and filters addon',
			'slug' => 'debug-bar-actions-and-filters-addon',
		),
		array(
			'name' => 'Debug Bar: Rewrite Rules addon',
			'slug' => 'debug-bar-rewrite-rules',
		),
		array(
			'name' => 'Debug Bar: MO files, Languages and Localization',
			'slug' => 'debug-bar-localization',
		),
		array(
			'name' => 'Query Monitor',
			'slug' => 'query-monitor',
		),
		array(
			'name' => 'What the file',
			'slug' => 'what-the-file ',
		),
		array(
			'name' => 'WWP DB Manager',
			'slug' => 'wp-dbmanager ',
		),
		array(
			'name' => 'Duplicate Posts',
			'slug' => 'duplicate-post',
		),
		array(
			'name' => 'Display Post Meta',
			'slug' => 'jsm-show-post-meta',
		),
		array(
			'name' => 'Template Debugger',
			'slug' => 'quick-edit-template-link',
		),
		array(
			'name' => 'WP Rollback',
			'slug' => 'wp-rollback',
		),
		array(
			'name' => 'WP Mail Log',
			'slug' => 'wp-mail-logging',
		),


	);
	/** Change this to your theme text domain, used for internationalising strings */
	$theme_text_domain = 'wpml-support-toolbox';
	/**
	 * Array of configuration settings. Uncomment and amend each line as needed.
	 * If you want the default strings to be available under your own theme domain,
	 * uncomment the strings and domain.
	 * Some of the strings are added into a sprintf, so see the comments at the
	 * end of each line for what each argument will be.
	 */
	$config = array(
		'domain'       => $theme_text_domain,         // Text domain - likely want to be the same as your theme. 
		'default_path' => '',                         // Default absolute path to pre-packaged plugins */
		//'parent_slug'  => 'wpml-support-toolbox',   
		//'menu'         => 'install-wpmld-plugins', // Menu slug */
		'has_notices' => false,
		'strings'      	 => array(
			'page_title'             => __( 'Support Plugins Installer', $theme_text_domain ), // */
			'menu_title'             => __( 'Support Plugins Installer', $theme_text_domain ), // */
			/*'instructions_install'   => __( 'The %1$s plugin is required for this theme. Click on the big blue button below to install and activate %1$s.', $theme_text_domain ), // %1$s = plugin name */
			/*'instructions_activate'  => __( 'The %1$s is installed but currently inactive. Please go to the <a href="%2$s">plugin administration page</a> page to activate it.', $theme_text_domain ), // %1$s = plugin name, %2$s = plugins page URL */
			/*'button'                 => __( 'Install %s Now', $theme_text_domain ), // %1$s = plugin name */
			/*'installing'             => __( 'Installing Plugin: %s', $theme_text_domain ), // %1$s = plugin name */
			/*'oops'                   => __( 'Something went wrong with the plugin API.', $theme_text_domain ), // */
			/*'notice_can_install'     => __( 'This theme requires the %1$s plugin. <a href="%2$s"><strong>Click here to begin the installation process</strong></a>. You may be asked for FTP credentials based on your server setup.', $theme_text_domain ), // %1$s = plugin name, %2$s = TGMPA page URL */
			/*'notice_cannot_install'  => __( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', $theme_text_domain ), // %1$s = plugin name */
			/*'notice_can_activate'    => __( 'This theme requires the %1$s plugin. That plugin is currently inactive, so please go to the <a href="%2$s">plugin administration page</a> to activate it.', $theme_text_domain ), // %1$s = plugin name, %2$s = plugins page URL */
			/*'notice_cannot_activate' => __( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', $theme_text_domain ), // %1$s = plugin name */
			/*'return'                 => __( 'Return to Required Plugins Installer', $theme_text_domain ), // */
		),
	);
	tgmpa( $plugins, $config );
}
