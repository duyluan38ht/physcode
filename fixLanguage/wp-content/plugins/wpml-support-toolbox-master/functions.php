<?php

//Disable non-essential plugins
function wpmld_disable_non_essentials_plugins()
{
	$plugins_switch = get_option('wpmld_plugin_switch');

	//White list of plugins
	$wpml_plugins[] = 'sitepress-multilingual-cms/sitepress.php';
	$wpml_plugins[] = 'woocommerce-multilingual/wpml-woocommerce.php';
	$wpml_plugins[] = 'woocommerce/woocommerce.php';
	$wpml_plugins[] = 'wpml-string-translation/plugin.php';
	$wpml_plugins[] = 'wpml-support-toolbox/wpml-support-toolbox.php';
	$wpml_plugins[] = 'wpml-translation-management/plugin.php';
	$wpml_plugins[] = 'wpml-media-translation/plugin.php';


	$function_message = 'All non-essential plugins disabled';

	//Disable plugins that are not listed in array
	if ($plugins_switch == false) {

		update_option('wpmld_plugin_switch', true);

		$plugins_list = get_option('active_plugins');

		//Store the active plugins on the wp_options table
		update_option( 'wpmld_active_plugins_list', $plugins_list);	

		foreach ($plugins_list as $plugin) {
			$array_check = in_array($plugin, $wpml_plugins);
			if ( $array_check == false) {
				deactivate_plugins( $plugin );
			} 
		}  		

		$function_message = 'All non-essential plugins disabled';

	} else {
		update_option('wpmld_plugin_switch', false);

		$plugins_list = get_option('wpmld_active_plugins_list');

		foreach ($plugins_list as $plugin) {

			$array_check = in_array($plugin, $wpml_plugins);

			if ( $array_check == false) {
				activate_plugins( $plugin );
			} 

		}  
		

		$function_message = 'All non-essential plugins enabled';
	}  
  
	echo '<div id="message" class="updated fade"><p>'.$function_message . '</p></div>';

}  


//Check if database exists
function wpmld_database_issue_fixer() {
	global $wpdb;

	$database_prefix = $wpdb->prefix;

	$table_names[] = $database_prefix.'icl_strings';
	$table_names[] = $database_prefix.'icl_string_translations';
	$table_names[] = $database_prefix.'icl_string_packages';
	$table_names[] = $database_prefix.'icl_string_pages';
	$table_names[] = $database_prefix.'icl_string_positions';
	$table_names[] = $database_prefix.'icl_string_status';

	echo '<ul class="log">';

	foreach ($table_names as $table_name) {
		$dabatase_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");		
		
		if ($dabatase_exists == null) {
			echo '<li style="color: #ff0000;"><strong>--- Table '.$table_name.' does not exist.</strong></li>'; 
			//runs the code from here https://wpml.org/errata/missing-_icl_strings-_icl_string_translations-data-tables/
			if( function_exists( 'icl_sitepress_activate' ) ) {  
			        icl_sitepress_activate();		
			        echo '<li><span><strong>icl_sitepress_activate() applied!</strong></span></li>'; 	 
			}
		} else {
			echo '<li>- Table '.$table_name.' already exist.</li>';
		}
		
	}

	echo '</ul>';

	echo '<p class="fade"><strong>Done! Please refresh the page.</strong></p>';
}


//Generate XML config for shortcode
function wpmld_convert_shortcodes($shortcode) {

	// 2019/09/24 - marcel.t: Added _ in preg_replace
	$shortcode = preg_replace('/[^A-Za-z0-9_=" \-]/', '', $shortcode);

    $shortcode_structure = shortcode_parse_atts( $shortcode ); 
 	
	$html = '<wpml-config>';
		$html .= '<shortcodes>';
			$html .= '<shortcode>';
				$html .= '<tag>'.$shortcode_structure[0].'</tag>';
				$html .= '<attributes>';
				foreach ($shortcode_structure as $shortcode_item_key => $shortcode_item_value ) {
					if ( $shortcode_item_key != '0') {
						$html .= '<attribute>'.$shortcode_item_key.'</attribute>';
					}
				}
				$html .= '</attributes>';
			$html .= '</shortcode>';
		$html .= '</shortcodes>';
	$html .= '</wpml-config>';

 	echo $html;
}


//Write Log function - See https://www.elegantthemes.com/blog/tips-tricks/using-the-wordpress-debug-log
if (!function_exists('write_log')) {

    function write_log($log) {
        if (true === WP_DEBUG) {
            if (is_array($log) || is_object($log)) {
                error_log(print_r($log, true));
            } else {
                error_log($log);
            }
        }
    }

}