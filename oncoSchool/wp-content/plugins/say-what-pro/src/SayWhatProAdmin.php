<?php

require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );

/**
 * Say What admin class - controller for all of the admin pages
 */
class SayWhatProAdmin implements SayWhatProAdminInterface {

	/**
	 * Instance of the settings class.
	 * @var SayWhatProSettingsInterface
	 */
	private $settings;

	/**
	 * @var SayWhatProStringDiscoveryInterface
	 */
	private $string_discovery;

	/**
	 * @var SayWhatProAutocompleteMatcherInterface
	 */
	private $autocomplete_matcher;

	/**
	 * @var SayWhatProListTableFactoryInterface
	 */
	private $list_table_factory;

	/**
	 * @var SayWhatProImporterInterface
	 */
	private $importer;

	/**
	 * Constructor.
	 *
	 * Store the settings instance and other dependencies for later use.
	 *
	 * @param SayWhatProSettingsInterface $settings
	 * @param SayWhatProStringDiscoveryInterface $string_discovery
	 * @param SayWhatProAutocompleteMatcherInterface $autocomplete_matcher
	 * @param SayWhatProListTableFactoryInterface $list_table_factory
	 * @param SayWhatProImporterInterface $importer
	 */
	public function __construct(
		SayWhatProSettingsInterface $settings,
		SayWhatProStringDiscoveryInterface $string_discovery,
		SayWhatProAutocompleteMatcherInterface $autocomplete_matcher,
		SayWhatProListTableFactoryInterface $list_table_factory,
		SayWhatProImporterInterface $importer
	) {
		$this->settings = $settings;
		$this->string_discovery = $string_discovery;
		$this->autocomplete_matcher = $autocomplete_matcher;
		$this->list_table_factory = $list_table_factory;
		$this->importer = $importer;
	}

	/**
	 * Run the admin features.
	 */
	public function run() {
		// Take care of database upgrades, and saving stuff before redirects.
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		// Add our admin page.
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		// Add a Settings link to the plugin page.
		$plugin_file = basename( dirname( dirname( __FILE__ ) ) ) . '/say-what-pro.php';
		add_filter( 'plugin_action_links_' . $plugin_file, array( $this, 'add_settings_link' ), 11 );
		// Bootstrap the WPListTableExportable class for output buffering if we
		// are on a Say What admin page. e.g. ?page=say_what_admin
		if ( ! empty( $_GET['page'] ) && 'say_what_admin' === $_GET['page'] ) {
			require_once( $this->settings->plugin_path . '/vendor/leewillis77/WpListTableExportable/bootstrap.php' );
		}
	}

	/**
	 * Add a "Settings" link next to the plugin on the Plugins page.
	 *
	 * @param   array  $links  The existing plugin links.
	 * @return  array          The revised list of plugin links.
	 */
	public function add_settings_link( $links ) {
		$settings_url = add_query_arg(
			array( 'page' => 'say_what_admin' ),
			admin_url( 'tools.php' )
		);
		$settings_link = sprintf( '<a href="%s">%s</a>', $settings_url, __( 'Settings', 'say_what' ) );
		$links[] = $settings_link;
		return $links;
	}

	/**
	 * Admin init actions.
	 *
	 * Takes care of database upgrades, and saving stuff before redirects.
	 */
	public function admin_init() {

		$this->check_db_version();

		if ( isset( $_POST['say_what_save'] ) ) {
			$this->save();
		}
		if ( isset( $_POST['say_what_save_wildcard'] ) ) {
			$this->save_wildcard();
		}
		if ( isset( $_GET['say_what_action'] ) && ( 'delete-confirmed' === $_GET['say_what_action'] ) ) {
			$this->admin_delete_confirmed();
		}
		if ( isset( $_GET['say_what_action'] ) && ( 'delete-wildcard-confirmed' === $_GET['say_what_action'] ) ) {
			$this->admin_delete_wildcard_confirmed();
		}
		// Discovery enabling.
		if ( isset( $_GET['say_what_action'] ) && 'discovery' === $_GET['say_what_action'] && ! empty( $_POST['enable'] ) ) {
			if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'say_what_pro_discovery_enable' ) ) {
				wp_die( 'Could not validate request.' );
			} else {
				setcookie( 'say-what-pro-discovery-active', true, 0, '/' );
				$_COOKIE['say-what-pro-discovery-active'] = true;
			}
		}
		// Discovery disabling.
		if ( isset( $_GET['say_what_action'] ) && 'discovery' === $_GET['say_what_action'] && ! empty( $_POST['disable'] ) ) {
			if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'say_what_pro_discovery_disable' ) ) {
				wp_die( 'Could not validate request.' );
			} else {
				setcookie( 'say-what-pro-discovery-active', false, 0, '/' );
				$_COOKIE['say-what-pro-discovery-active'] = false;
			}
		}
		// Import
		if ( isset( $_GET['say_what_action'] ) && 'import' === $_GET['say_what_action'] && ! empty( $_FILES['say_what_import_file'] ) ) {
			$file = $_FILES['say_what_import_file'];
			if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'say-what-import' ) || ! current_user_can( 'manage_options' ) ) {
				echo '<div class="error"><p>' . __( 'Error, you do not have permissions to import replacements.', 'say_what' ) . '</p></div>';
			} elseif ( 'text/csv' !== $file['type'] && 'application/vnd.ms-excel' !== $file['type'] ) {
				echo '<div class="error"><p>' . __( 'Incorrect file type (' . esc_html( $file['type'] ) . '), import request ignored.', 'say_what' ) . '</p></div>';
			}else {
				$response = $this->importer->import_file( $file['tmp_name'] );
				if ( ! $response['success'] ) {
					foreach ( $response['errors'] as $error ) {
						echo '<div class="error"><p>' . esc_html( $error ) . '</p></div>';
					}
				} else {
					echo '<div class="updated"><p>' . esc_html( $response['success_message'] ) . '</p></div>';
				}
			}
		}
		add_action( 'wp_ajax_say_what_autocomplete', array( $this, 'autocomplete' ) );
	}

	/**
	 * Register the menu item for the admin pages
	 */
	public function admin_menu() {
		if ( current_user_can( 'manage_options' ) ) {
			$page = add_management_page(
				__( 'Text changes', 'say_what' ),
				__( 'Text changes', 'say_what' ),
				'manage_options',
				'say_what_admin',
				array( $this, 'admin' )
			);
			if ( isset( $_GET['page'] ) && 'say_what_admin' === $_GET['page'] ) {
				add_action( 'admin_print_styles-' . $page, array( $this, 'enqueue_scripts' ) );
			}
		}
	}

	/**
	 * Add CSS / javascript to admin pages
	 */
	public function enqueue_scripts() {
		wp_register_style( 'say_what_admin_css', plugins_url() . '/say-what-pro/css/admin.css', array() );
		wp_enqueue_style( 'say_what_admin_css' );

		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		wp_register_script( 'say_what_admin_js', plugins_url() . '/say-what-pro/js/admin' . $suffix . '.js', array() );
		$args = array(
			'autocomplete_url' => wp_nonce_url(
				add_query_arg(
					array(
						'action' => 'say_what_autocomplete',
					),
					admin_url( 'admin-ajax.php' )
				),
				'say_what_autocomplete'
			),
			'string_discovery_url' => add_query_arg(
				[
					'page' => 'say_what_admin',
					'say_what_action' => 'discovery',
				],
				admin_url( 'tools.php' )
			),
		);
		wp_localize_script( 'say_what_admin_js', 'say_what', $args );
		wp_enqueue_script( 'say_what_admin_js' );

		wp_enqueue_script( 'jquery-ui-autocomplete' );
		wp_register_style( 'jquery-ui-styles','//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/black-tie/jquery-ui.css' );
		wp_enqueue_style( 'jquery-ui-styles' );
	}

	/**
	 * The main admin page controller
	 */
	public function admin() {
		$action = isset( $_GET['say_what_action'] ) ? $_GET['say_what_action'] : 'list';
		$default_active = $wildcards_active = $discovery_active = $import_active = false;
		if ( 'list' === $action ) {
			$default_active = ' nav-tab-active';
		} elseif ( 'discovery' === $action ) {
			$discovery_active = ' nav-tab-active';
		} elseif ( 'import' === $action ) {
			$import_active = ' nav-tab-active';
		} elseif ( 'wildcards' === $action ) {
			$wildcards_active = ' nav-tab-active';
		}
		if ( $this->settings->show_multi_lingual() ) {
			$swp_additional_wrap_classes = 'swp-has-multi-lingual';
		} else {
			$swp_additional_wrap_classes = '';
		}
		require_once( $this->settings->plugin_path . '/html/say-what-admin-header.php' );
		switch ( $action ) {
			case 'addedit':
				$this->admin_addedit();
				break;
			case 'delete':
				$this->admin_delete();
				break;
			case 'deletewildcard':
				$this->admin_delete_wildcard();
				break;
			case 'discovery':
				$this->admin_discovery();
				break;
			case 'import':
				$this->admin_import();
				break;
			case 'wildcards':
				$this->wildcards_list();
				break;
			case 'addeditwildcards':
				$this->admin_addedit_wildcards();
				break;
			case 'list':
			default:
				$this->admin_list();
				break;
		}
		require_once( $this->settings->plugin_path . '/html/say-what-admin-footer.php' );
	}

	/**
	 * Render the list of currently configured wildcard strings
	 */
	public function wildcards_list() {
		require_once( $this->settings->plugin_path . '/html/say-what-admin-wildcard-explanation.php' );
		require_once( $this->settings->plugin_path . '/html/say-what-admin-wildcard-warning.php' );
		require_once( $this->settings->plugin_path . '/html/say-what-admin-add-wildcard-button.php' );

		echo '<form id="say-what-list-table-form" method="post">';
		$this->list_table_factory->get_wildcard_list_table()->prepare_items();
		$this->list_table_factory->get_wildcard_list_table()->display();
		echo '</form>';
	}

	/**
	 * Render the list of currently configured replacement strings
	 */
	public function admin_list() {
		echo '<form id="say-what-list-table-form" method="post">';
		$this->list_table_factory->get_replacement_list_table()->prepare_items();
		$this->list_table_factory->get_replacement_list_table()->display();
		echo '</form>';
	}

	/**
	 * Render the discovery page.
	 */
	public function admin_discovery() {
		// Do stuff
		require_once( $this->settings->plugin_path . '/html/say-what-admin-discovery.php' );
		if ( $this->string_discovery->is_active() ) {
			require_once( $this->settings->plugin_path . '/html/say-what-admin-discovery-disable.php' );
		} else {
			require_once( $this->settings->plugin_path . '/html/say-what-admin-discovery-enable.php' );
		}
	}

	/**
	 * Render the import page.
	 */
	public function admin_import() {
		require_once( $this->settings->plugin_path . '/html/say-what-admin-import.php' );
	}

	/**
	 * Show the page asking the user to confirm deletion
	 */
	public function admin_delete() {
		global $wpdb, $table_prefix;
		if ( ! wp_verify_nonce( $_GET['nonce'], 'swdelete' ) ) {
			wp_die( __( 'Did you really mean to do that? Please go back and try again.', 'say_what' ) );
		}
		if ( isset( $_GET['id'] ) ) {
			$sql = "SELECT * FROM {$table_prefix}say_what_strings WHERE string_id = %d";
			$replacement = $wpdb->get_row( $wpdb->prepare( $sql, $_GET['id'] ) );
		}
		if ( ! $replacement ) {
			wp_die( __( 'Did you really mean to do that? Please go back and try again.', 'say_what' ) );
		}
		require_once( $this->settings->plugin_path . '/html/say-what-admin-delete.php' );
	}

	/**
	 * Show the page asking the user to confirm deletion
	 */
	public function admin_delete_wildcard() {
		global $wpdb, $table_prefix;
		if ( ! wp_verify_nonce( $_GET['nonce'], 'swdelete' ) ) {
			wp_die( __( 'Did you really mean to do that? Please go back and try again.', 'say_what' ) );
		}
		if ( isset( $_GET['id'] ) ) {
			$sql = "SELECT * FROM {$table_prefix}say_what_wildcards WHERE wildcard_id = %d";
			$wildcard = $wpdb->get_row( $wpdb->prepare( $sql, $_GET['id'] ) );
		}
		if ( ! $wildcard ) {
			wp_die( __( 'Did you really mean to do that? Please go back and try again.', 'say_what' ) );
		}
		require_once( $this->settings->plugin_path . '/html/say-what-admin-delete-wildcard.php' );
	}

	/**
	 * Delete the replacement.
	 */
	public function admin_delete_confirmed() {
		global $wpdb, $table_prefix;
		if ( ! wp_verify_nonce( $_GET['nonce'], 'swdelete' ) ||
			 empty( $_GET['id'] ) ) {
			wp_die( __( 'Did you really mean to do that? Please go back and try again.', 'say_what' ) );
		}
		$sql = "DELETE FROM {$table_prefix}say_what_strings WHERE string_id = %d";
		$wpdb->query( $wpdb->prepare( $sql, $_GET['id'] ) );
		wp_redirect( 'tools.php?page=say_what_admin', '303' );
		die();
	}

	/**
	 * Delete the wildcard.
	 */
	public function admin_delete_wildcard_confirmed() {
		global $wpdb, $table_prefix;
		if ( ! wp_verify_nonce( $_GET['nonce'], 'swdelete' ) ||
			 empty( $_GET['id'] ) ) {
			wp_die( __( 'Did you really mean to do that? Please go back and try again.', 'say_what' ) );
		}
		$sql = "DELETE FROM {$table_prefix}say_what_wildcards WHERE wildcard_id = %d";
		$wpdb->query( $wpdb->prepare( $sql, $_GET['id'] ) );
		wp_redirect( 'tools.php?page=say_what_admin&say_what_action=wildcards', '303' );
		die();
	}

	/**
	 * Render the add/edit page for a replacement
	 */
	public function admin_addedit() {
		global $wpdb, $table_prefix;
		$replacement = false;
		if ( isset( $_GET['id'] ) ) {
			$sql         = "SELECT *
			                  FROM {$table_prefix}say_what_strings
							 WHERE string_id = %d";
			$replacement = $wpdb->get_row( $wpdb->prepare( $sql, $_GET['id'] ) );
		}
		if ( ! $replacement ) {
			$replacement = new stdClass();
			$replacement->string_id          = '';
			$replacement->orig_string        = '';
			$replacement->replacement_string = '';
			$replacement->domain             = '';
			$replacement->context            = '';
			$replacement->lang               = '';
		}

		$languages = $this->generate_language_dropdown_list();
		require_once( $this->settings->plugin_path . '/html/say-what-admin-addedit.php' );
	}

	/**
	 * Generate a language list to generate the dropdown from.
	 *
	 * @return array    Array of language options.
	 */
	private function generate_language_dropdown_list() {
		$languages = wp_get_available_translations();
		// en_US isn't returned by get_available_translations().
		$languages['en_US'] = array(
			'language'     => 'en_US',
			'english_name' => __( 'English (United States)' ),
		);

		$languages['separator'] = array(
			'english_name' => __( '------------', 'say_what' ),
			'language'    => ' separator',
		);

		// Sort the list
		$languages = apply_filters( 'say_what_pro_language_list', $languages );
		array_unshift(
			$languages,
			array(
				'english_name' => __( 'Any', 'say_what' ),
				'language'    => '',
			)
		);
		return $languages;
	}

	/**
	 * Render the add/edit page for a wildcard.
	 */
	public function admin_addedit_wildcards() {
		global $wpdb, $table_prefix;
		$wildcard = false;
		if ( isset( $_GET['id'] ) ) {
			$sql         = "SELECT *
			                  FROM {$table_prefix}say_what_wildcards
							 WHERE wildcard_id = %d";
			$wildcard = $wpdb->get_row( $wpdb->prepare( $sql, $_GET['id'] ) );
		}
		if ( ! $wildcard ) {
			$wildcard = new stdClass();
			$wildcard->wildcard_id = '';
			$wildcard->original    = '';
			$wildcard->replacement = '';
			$wildcard->lang        = '';
		}

		$languages = $this->generate_language_dropdown_list();
		require_once( $this->settings->plugin_path . '/html/say-what-admin-wildcard-warning.php' );
		require_once( $this->settings->plugin_path . '/html/say-what-admin-addeditwildcards.php' );
	}

	/**
	 * Strip CRs out of strings. array_walk() callback.
	 */
	private function strip_cr_callback( &$val, $key ) {
		$val = str_replace( "\r\n", "\n", $val );
	}

	/**
	 * Something on the admin pages needs saved. Handle it here
	 * Output error/warning messages as required
	 */
	private function save() {
		global $wpdb, $table_prefix;
		if ( ! wp_verify_nonce( $_POST['nonce'], 'swaddedit' ) ) {
			wp_die( __( 'Did you really mean to do that? Please go back and try again.', 'say_what' ) );
		}
		$_POST = stripslashes_deep( $_POST );
		array_walk( $_POST, array( $this, 'strip_cr_callback' ) );
		if ( isset( $_POST['say_what_string_id'] ) ) {
			$this->settings->update_replacement(
				$_POST['say_what_string_id'],
				$_POST['say_what_orig_string'],
				$_POST['say_what_domain'],
				$_POST['say_what_context'],
				$_POST['say_what_replacement_string'],
				$_POST['say_what_lang']
			);
		} else {
			$this->settings->insert_replacement(
				$_POST['say_what_orig_string'],
				$_POST['say_what_domain'],
				$_POST['say_what_context'],
				$_POST['say_what_replacement_string'],
				$_POST['say_what_lang']
			);
		}
		wp_redirect( 'tools.php?page=say_what_admin', '303' );
		die();
	}

	/**
	 * A wildcard on the admin pages needs saved. Handle it here
	 * Output error/warning messages as required
	 */
	private function save_wildcard() {
		global $wpdb, $table_prefix;
		if ( ! wp_verify_nonce( $_POST['nonce'], 'swaddedit' ) ) {
			wp_die( __( 'Did you really mean to do that? Please go back and try again.', 'say_what' ) );
		}
		$_POST = stripslashes_deep( $_POST );
		array_walk( $_POST, array( $this, 'strip_cr_callback' ) );
		if ( isset( $_POST['say_what_wildcard_id'] ) ) {
			$sql = "UPDATE {$table_prefix}say_what_wildcards
					   SET original = %s,
						   replacement = %s,
						   lang = %s
					 WHERE wildcard_id = %d";
			$wpdb->query(
				$wpdb->prepare(
					$sql,
					$_POST['say_what_original'],
					$_POST['say_what_replacement'],
					$_POST['say_what_lang'],
					$_POST['say_what_wildcard_id']
				)
			);
		} else {
			$sql = "INSERT INTO {$table_prefix}say_what_wildcards
		                 VALUES ( NULL, %s, %s, %s )";
			$wpdb->query(
				$wpdb->prepare(
					$sql,
					$_POST['say_what_original'],
					$_POST['say_what_replacement'],
					$_POST['say_what_lang']
				)
			);
		}
		wp_redirect( 'tools.php?page=say_what_admin&say_what_action=wildcards', '303' );
		die();
	}

	/**
	 * AJAX callback that provides autocomplete suggestions.
	 *
	 * @return array Array of suggestions.
	 */
	public function autocomplete() {
		if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'say_what_autocomplete' ) ) {
			echo json_encode( array() );
			exit;
		}
		$term = isset( $_GET['term'] ) ? $_GET['term'] : '';
		if ( $term === '' ) {
			echo json_encode( array() );
			exit;
		}
		echo json_encode( $this->autocomplete_matcher->match( $term ) );
		exit();
	}

	/**
	 * Check for pending upgrades, and run them if required.
	 */
	private function check_db_version() {
		$current_db_version = (int) get_option( 'say_what_pro_db_version', 1 );
		// Bail if we're already up to date.
		if ( $current_db_version >= SAY_WHAT_PRO_DB_VERSION ) {
			return;
		}
		// Otherwise, check for, and run updates.
		foreach ( range( $current_db_version + 1, SAY_WHAT_PRO_DB_VERSION ) as $version ) {
			if ( is_callable( array( $this, 'upgrade_db_to_' . $version ) ) ) {
				$this->{'upgrade_db_to_' . $version}();
				update_option( 'say_what_pro_db_version', $version );
			} else {
				update_option( 'say_what_pro_db_version', $version );
			}
		}
	}

	/**
	 * Create available_strings table if missing, or remove (broken) unique indexes if the
	 * table does exist.
	 */
	private function upgrade_db_to_3() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'say_what_available_strings';
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) === $table_name ) {
			// If the available_strings table exists, remove the (broken) index.
			$sql = "ALTER TABLE $table_name DROP INDEX replacement";
			$wpdb->query( $sql );
		} else {
			// If the table is missing, create it.
			$sql = "CREATE TABLE $table_name (
								 orig_string text NOT NULL,
								 domain varchar(255),
								 context text
								 ) DEFAULT CHARACTER SET utf8";
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );
		}

	}

	/**
	 * Add language field.
	 */
	private function upgrade_db_to_4() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'say_what_strings';
		$sql = "CREATE TABLE $table_name (
					string_id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
					orig_string text NOT NULL,
					domain varchar(255),
					replacement_string text,
					context text,
					lang varchar(10)
				) DEFAULT CHARACTER SET utf8";
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Add wildcads table
	 */
	private function upgrade_db_to_5() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'say_what_wildcards';
		$sql = "CREATE TABLE $table_name (
					wildcard_id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
					original text NOT NULL,
					replacement text,
					lang varchar(10)
				) DEFAULT CHARACTER SET utf8";
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Add translated_string column to available strings
	 */
	private function upgrade_db_to_6() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'say_what_available_strings';
		$sql = "CREATE TABLE $table_name (
					orig_string text NOT NULL,
					domain varchar(255),
					context text,
					translated_string text
				) DEFAULT CHARACTER SET utf8";
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

}
