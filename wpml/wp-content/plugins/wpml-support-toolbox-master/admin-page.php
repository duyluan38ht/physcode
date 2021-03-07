<?php

class WPMLD_Admin_Page {

	// Overview / Intro / System Info
	public static function admin_overview () {
		 echo "<h1>WPML Support Toolbox</h1>";    

		    echo '<p><strong>Important:</strong> Please backup the database before using this plugin.</p>';

			echo '<div style="border: 1px solid #ccc; padding: 20px; margin: 5px; border-radius: 6px;">';

				echo '<h3>System Info</h3>';

				//WP_DEBUG
				if( WP_DEBUG == 1 ) {
					$debug_status = '<span style="color: #4abd1c; font-weight: bold;">Enabled</span>';
				} else {
					$debug_status = '<span style="color: #ed2b2b; font-weight: bold;">Disabled</span>';
				}

				//WP_DEBUG_LOG
				if( WP_DEBUG_LOG == 1 ) {
					$debug_log_status = '<span style="color: #4abd1c; font-weight: bold;">Enabled</span>';
				} else {
					$debug_log_status = '<span style="color: #ed2b2b; font-weight: bold;">Disabled</span>';
				}		

				//WP_DEBUG_DISPLAY
				if( WP_DEBUG_DISPLAY == 1 ) {
					$debug_display_status = '<span style="color: #4abd1c; font-weight: bold;">Enabled</span>';
				} else {
					$debug_display_status = '<span style="color: #ed2b2b; font-weight: bold;">Disabled</span>';
				}

				echo '<strong>PHP VERSION: </strong>'.phpversion().' &nbsp;|&nbsp; ';		
				echo '<strong>MEMORY_LIMIT: </strong>'.ini_get('memory_limit').' &nbsp;|&nbsp; ';
				echo '<strong>WP_MEMORY_LIMIT: </strong>'.WP_MEMORY_LIMIT.' &nbsp;|&nbsp; ';
				if (function_exists( 'memory_get_usage' ) ){
					$memory_usage =  round( memory_get_usage(), 2 );
					echo '<strong>Memory usage </strong>:' .size_format($memory_usage, 2 ).' &nbsp;|&nbsp; ';
				}

				echo '<strong>MAX_INPUT_VARS: </strong>'.ini_get('max_input_vars').' <hr/> ';		
     			
				

				echo '<h4>Disk Usage</h4>';
				$folderSizes = new WPMLD_Wp_Folder_Sizes();
				$folderSizes->display_folder_sizes();


				echo '<hr/>';

				echo '<h4>WP-CONFIG Settings</h4>';				
				echo '<strong>WP_DEBUG: </strong>'.$debug_status.' &nbsp;|&nbsp; ';
				echo '<strong>WP_DEBUG_LOG: </strong>'.$debug_log_status.' &nbsp;|&nbsp; ';
				echo '<strong>WP_DEBUG_DISPLAY: </strong>'.$debug_display_status.' &nbsp;|&nbsp; ';

				// Current site prefix
				global $wpdb;
				echo '<strong>WP DATABASE PREFIX: </strong>'.$wpdb->prefix;

				

			echo '</div>';
	}

	//Plugins Troubleshoot
	public static function plugins_troubleshoot() {
		echo '<div style="border: 1px solid #ccc; padding: 20px; margin: 5px; border-radius: 6px;">';

			if (isset($_POST['test_button']) && check_admin_referer('disable_plugins_button_clicked')) {
				// the button has been pressed AND we've passed the security check
				wpmld_disable_non_essentials_plugins();
			}
			
			echo '<form action="options-general.php?page=wpml-support-toolbox" method="post">';
				wp_nonce_field('disable_plugins_button_clicked');
				echo '<input type="hidden" value="true" name="test_button" />';

				$plugins_switch = get_option('wpmld_plugin_switch');

				if ($plugins_switch == false) {
					$disable_plugins_button_label = __('Disable non-essential plugins','wpml-support-toolbox');
					$class = 'wpmld-btn-disabled';	
				} 
				else {
					$disable_plugins_button_label = __('Enable non-essential plugins','wpml-support-toolbox');
					$class = 'wpmld-btn-enabled';		
				}			

				echo '<h3>Disable/Enable plugins (troubleshooting)</h3>';
				echo '<p>Use this option to quickly disable and enable the no-essential plugins (minimal install). It will save the list of active plugins on the database, so you will be able to re-active all the plugins later.</p>';

				echo '<div class="wpmld-btn '.$class.'">';
					submit_button($disable_plugins_button_label);
				echo '</div>';
				
			echo '</form>';

		echo '</div>';
	}


	public static function plugins_manager() {
		//Support Plugins Manager
		echo '<div style="border: 1px solid #ccc; padding: 20px; margin: 5px; border-radius: 6px;">';

			echo '<h3>Support Plugins Installer</h3>';

			echo '<p>Select and install the plugins that we frequently use with a few clicks.</p>';

			$plugin_manager_link = admin_url().'themes.php?page=tgmpa-install-plugins';

			echo '<a href="'.$plugin_manager_link.'" class="button button-primary">Go to the support plugins manager ⮕</a>';


		echo '</div>';		
	}

	//Adminer
	public static function adminer() {
		//Action
		if (isset($_POST['wpmld_adminer_input']) && check_admin_referer('wpmld_ari_adminer')) {
			$ari_adminer_switch = get_option('wpmld_ari_adminer');

			if ($ari_adminer_switch == false) {
				update_option('wpmld_ari_adminer', true);
				echo '<div id="message" class="updated fade"><p>ADMINER enabled! Please refresh the page.</p></div>';
			} else {
				update_option('wpmld_ari_adminer', false);
				echo '<div id="message" class="updated fade"><p>ADMINER disabled. Please refresh the page.</p></div>';
			}	
		}
		//Form
		echo '<div style="border: 1px solid #ccc; padding: 20px; margin: 5px; border-radius: 6px;">';

			echo '<h3>Database (Caution)</h3>';
			echo '<strong>DB HOST</strong>: '.DB_HOST.' | ';
			echo '<strong>DB name</strong>: '.DB_NAME.' | ';
			echo '<strong>DB USER</strong>: '.DB_USER.' | ';
			echo '<strong>DB PASSWORD</strong>: '.DB_PASSWORD.' | ';

			echo '<h4>Adminer PHP file</h4>';
			echo '<p style="magin-bottom: 5px;">The original PHP file from https://www.adminer.org. You will need database credentials to use it (see above).</p>';

			echo '<a class="button button-primary" href="'.esc_url( plugins_url( 'adminer.php', __FILE__ ) ).'" target="_blank">Open Adminer.php file</a>';

			echo '<h4>ARI Adminer (plugin)</h4>';

			echo '<p style="magin-bottom: 5px;">It will enable/disable the ARI Adminer plugin functionality on this website.<br/><strong>Important:</strong> Use with caution and disable after use.</p>';

			$ari_adminer_switch = get_option('wpmld_ari_adminer');

			if ($ari_adminer_switch == false) {
				$ari_adminer_button_label = __('Enable ARI adminer (plugin)','wpml-support-toolbox');	
				$class = 'wpmld-btn-disabled';			
			} 
			else {
				$ari_adminer_button_label = __('Disable ARI adminer','wpml-support-toolbox');
				echo '<div style="font-size: 16px; background: #ed2b2b; color: #fff; padding: 5px; display: inline-block; border-radius: 5px;">Adminer is currently enabled. Please disable it after use.</div>';
				$class = 'wpmld-btn-enabled';	

			}	

			echo '<form action="options-general.php?page=wpml-support-toolbox" method="post">';
				wp_nonce_field('wpmld_ari_adminer');
				echo '<input type="hidden" value="true" name="wpmld_adminer_input" />';
				
				echo '<div class="wpmld-btn '.$class.'">';
					submit_button($ari_adminer_button_label);
				echo '</div>';
				
			echo '</form>';
			

		echo '</div>';
	}

	//Check WPML tables then run icl_sitepress_activate();
	public static function check_tables() {
		echo '<div style="border: 1px solid #ccc; padding: 20px; margin: 5px; border-radius: 6px;">';

		echo '<form action="options-general.php?page=wpml-support-toolbox" method="post">';
			wp_nonce_field('wpml_table_fixer_clicked');
			echo '<input type="hidden" value="true" name="test_button2" />';

			echo '<h3>Create missing database tables and run icl_sitepress_activate()</h3>';
			echo '<p>Check missing WPML tables and runs the icl_sitepress_activate function to create them <em>(check this errata for more infos: https://wpml.org/errata/missing-_icl_strings-_icl_string_translations-data-tables/)</em>.</p>';

			submit_button('Check missing WPML tables');
			
		echo '</form>';

		if (isset($_POST['test_button2']) && check_admin_referer('wpml_table_fixer_clicked')) {
			// the button has been pressed AND we've passed the security check
			wpmld_database_issue_fixer();
		}

		echo '</div>';
	}


	//More Features Block
	public static function more_features() {
		echo '<div style="border: 1px solid #ccc; padding: 20px; margin: 5px; border-radius: 6px;">';

			echo '<h3>More Features</h3>';

			//Table
			echo '<table class="wpmld-options-table">';				

				//view debug log
				echo '<tr>';
					echo '<td>';
						echo '<strong>Quick Update plugins</strong><br/> We already included the easy-theme-and-plugin-upgrades plugin to speed-up a manual update process of plugins and themes. You just need to upload the .zip file of the plugin on "Plugins > Add New" and it will be automatically updated.';
					echo '</td>';
					echo '<td>';
						echo 'Enabled by default';
					echo '</td>';
				echo '</tr>';

				//view debug log
				echo '<tr>';
					echo '<td>';
						echo '<strong>View Debug Log</strong><br/> You can view the content of the debug.log file using the links at the top admin bar. WP_DEBUG_LOG should be set as TRUE.';
					echo '</td>';
					echo '<td>';
						echo 'Enabled by default';
					echo '</td>';
				echo '</tr>';

				
				//WP Theme Plugin Download
				echo '<tr>';
					echo '<td>';
						echo '<strong>Wp Theme plugin Download</strong><br/> Download plugins and themes on your site as a .zip file, directly from the admin panel.';
					echo '</td>';
					echo '<td>';
						WPMLD_Admin_Page::create_option_item('WP THeme and Plugin download', 'wpmld_wp-theme-plugin-download');
					echo '</td>';
				echo '</tr>';

				//Move posts between languages
				echo '<tr>';
					echo '<td>';
						echo '<strong>(EXPERIMENTAL) Bulk move posts and pages from one language to another</strong><br/> Go to Posts > All Posts (or Pages > All Pages), select the posts you want then choose a language to move on the bulk options dropdown.';
					echo '</td>';
					echo '<td>';
						WPMLD_Admin_Page::create_option_item('Move posts between languages', 'wpmld_move_posts_switch');
					echo '</td>';
				echo '</tr>';

				//Connect with translations
				echo '<tr>';
					echo '<td>';
						echo '<strong>(EXPERIMENTAL) Connect with Translations using "Quick Edit"</strong><br/> Go to Posts > All Posts (or Pages > All Pages) then click on "quick edit" at the post you want to edit. A option to connect it to a original post will be available.';
					echo '</td>';
					echo '<td>';
						WPMLD_Admin_Page::create_option_item('Move posts between languages', 'wpmld_quick-edit-connect-switch');
					echo '</td>';
				echo '</tr>';



			echo '</table>';

		echo '</div>';
	}

	//XML for Shortcode generator
	public static function xml_shortcode_generator() {
		echo '<div style="border: 1px solid #ccc; padding: 20px; margin: 5px; border-radius: 6px;">';

			echo '<h3>Generate WPML XML code for a shortcode (beta)</h3>';

			echo '<p style="magin-bottom: 5px;">It will generate the XML code to translate a custom shortcode and and its attributes.</p>';

			echo '<form action="options-general.php?page=wpml-support-toolbox" method="post">';
				wp_nonce_field('wpml_generate_xml_clicked');
				echo '<input type="hidden" value="true" name="test_button3" />';
				echo '<input type="text" value="" name="the_shortcode" style="width: 90%; height: 40px; line-height: 40px;" placeholder="Paste the shortcode here" />';

				submit_button('Generate XML');
				
			echo '</form>';

		if (isset($_POST['test_button3']) && check_admin_referer('wpml_generate_xml_clicked')) {
			
			$the_shortcode = $_POST['the_shortcode'];			

			echo '<h3 style="margin-bottom: 5px;">XML Code:</h3>';
			echo '<p>Copy and paste to WPML > Settings > Custom XML Configuration. You may need to edit it (see https://wpml.org/documentation/support/language-configuration-files/#page-builder-content).';
			
			echo '<textarea style="width: 90%;">';
				wpmld_convert_shortcodes($the_shortcode);
			echo'</textarea>';
		}

		echo '</div>';
	}	



	//WPML Support Toolbox Screen
	public static function wpmld_admin_init(){
		WPMLD_Admin_Page::admin_overview();
		WPMLD_Admin_Page::plugins_manager();
		WPMLD_Admin_Page::plugins_troubleshoot();		
		WPMLD_Admin_Page::adminer();		
		WPMLD_Admin_Page::xml_shortcode_generator();
		WPMLD_Admin_Page::more_features();
		WPMLD_Admin_Page::check_tables();
	}

	//Other options
	public static function create_option_item($title, $optionkey) {
		//Action
		if (isset($_POST[$optionkey.'_input_hidden']) && check_admin_referer($optionkey.'_clicked')) {
			$option = get_option($optionkey);

			if ($option== false) {
				update_option($optionkey, true);
			} else {
				update_option($optionkey, false);
			}
			
		}

		$option = get_option($optionkey);

		if ($option == false) {
			$option_button_label = __('Enable','wpml-support-toolbox');	
			$class = 'wpmld-btn-disabled';		
		} 
		else {
			$option_button_label = __('Disable','wpml-support-toolbox');
			$class = 'wpmld-btn-enabled';		
		} 			

		echo '<form action="options-general.php?page=wpml-support-toolbox" method="post">';

			wp_nonce_field($optionkey.'_clicked');

			echo '<input type="hidden" value="true" name="'.$optionkey.'_input_hidden" />';

			echo '<div class="wpmld-btn '.$class.'">';

				submit_button($option_button_label);

			echo '</div>';

		echo  '</form>';
		
	}
	

	//Content of PHP Info page
	public static function wpmld_submenu_phpinfo(){
		echo '<h1>PHP Info</h1><hr style="margin-bottom: 40px;"/>';  
		
		ob_start();
		phpinfo();
		$pinfo = ob_get_contents();
		ob_end_clean();
		 
		$pinfo = preg_replace( '%^.*<body>(.*)</body>.*$%ms','$1',$pinfo);
		echo $pinfo;
	}

	//Add pages to the admin menu
	public static function wpmld_plugin_setup_menu(){
        add_menu_page( 'WPML Support Toolbox', 'WPML Support Toolbox', 'manage_options', 'wpml-support-toolbox', array( __CLASS__, 'wpmld_admin_init')  );
        add_submenu_page( 'wpml-support-toolbox', 'PHP Info', 'PHP Info',  'manage_options', 'wpml-support-toolbox-phpinfo', array( __CLASS__, 'wpmld_submenu_phpinfo') );        
	}

	// custom css and js 
	public static function wpmld_css_and_js($hook) { 
	    wp_enqueue_style('wpmld_css', plugins_url('inc/admin-styles.css',__FILE__ ));
	    //wp_enqueue_script('wpmld_js', plugins_url('inc/scripts.js',__FILE__ ));
	}

	//Hook everything
	public function wpmld_admin_page_hooks() {
	    add_action('admin_menu', array( __CLASS__, 'wpmld_plugin_setup_menu'));
	    add_action('admin_enqueue_scripts', array( __CLASS__, 'wpmld_css_and_js') );
	 }
	
}


//Run functions
$admin_pages = new WPMLD_Admin_Page(); 
$admin_pages->wpmld_admin_page_hooks();


