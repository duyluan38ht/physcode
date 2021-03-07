<?php

/**
 * Settings class.
 */
class SayWhatProSettings implements SayWhatProSettingsInterface {

	/**
	 * The path to the main plugin directory.
	 * @var string
	 */
	private $plugin_path;

	/**
	 * Replacements in a form optimised for checking and replacing.
	 * @var array
	 */
	private $optimised_replacements = [];

	/**
	 * @var array
	 */
	public $replacements      = [];

	/**
	 * @var array
	 */
	public $wildcards         = [];

	/**
	 * @var array
	 */
	private $available_strings = [];

	/**
	 * Constructor.
	 *
	 * Loads the settings from the database.
	 */
	public function __construct( $plugin_path ) {
		$this->plugin_path = $plugin_path;
	}


	/**
	 * Allow read-only access to selected private properties.
	 *
	 * @param  string  $key  The property name being requested.
	 * @return array         The property value.
	 */
	public function __get( $key ) {
		if ( 'optimised_replacements' === $key ) {
			return $this->optimised_replacements;
		} elseif ( 'plugin_path' === $key ) {
			return $this->plugin_path;
		} elseif ( 'available_strings' === $key ) {
			return $this->available_strings;
		}
		throw new \Exception( 'Invalid property access to ' . $key . ' on ' . __CLASS__ );
	}

	/**
	 * Run the class functionality.
	 */
	public function run() {
		global $wpdb, $table_prefix;
		// Do not do anything if we haven't created our custom tables yet.
		$current_db_version = get_option( 'say_what_pro_db_version' );
		if ( false === $current_db_version ) {
			return;
		}
		// Read the raw replacement data.
		$sql = "SELECT * FROM {$table_prefix}say_what_strings";
		$this->replacements = $wpdb->get_results( $sql, ARRAY_A );

		// Read the raw wildcard data.
		$sql = "SELECT original, replacement, lang FROM {$table_prefix}say_what_wildcards";
		$wildcards = $wpdb->get_results( $sql, ARRAY_A );
		foreach ( $wildcards as $wildcard ) {
			$lang = ! empty( $wildcard['lang'] ) ? $wildcard['lang'] : 'default';
			$this->wildcards[ $lang ][ $wildcard['original'] ] = $wildcard['replacement'];
		}

		// If string discovery is active, create a keyed list of available strings we already know about.
		if ( isset( $_COOKIE['say-what-pro-discovery-active'] ) && $_COOKIE['say-what-pro-discovery-active'] ) {
			$sql = "SELECT CONCAT_WS('|', orig_string, domain, context, translated_string) AS `unique_key` FROM {$table_prefix}say_what_available_strings";
			$this->available_strings = $wpdb->get_col( $sql, 0 );
		}

		// Generate the optimised lookup array for frontend use.
		$this->generate_optimised_replacements();
	}

	/**
	 * Return the configured replacements in a format optimised for looking up.
	 *
	 * The return array will be hierarchically keyed by domain, original string,
	 * context, and language, with the value (if any) the replacement string.
	 *
	 * @return array  The array of optimised replacements.
	 */
	private function generate_optimised_replacements() {
		if ( ! empty( $this->optimised_replacements ) ) {
			return;
		}
		$this->optimised_replacements = [];
		foreach ( $this->replacements as $value ) {
			if ( empty( $value['domain'] ) ) {
				$value['domain'] = 'default';
			}
			if ( empty( $value['context'] ) ) {
				$value['context'] = 'sw-default-context';
			}
			if ( empty( $value['lang'] ) ) {
				$value['lang'] = 'default';
			}
			$this->optimised_replacements[ $value['domain'] ][ $value['orig_string'] ][ $value['context'] ][ $value['lang'] ] = $value['replacement_string'];
		}
	}

	/**
	 * Whether to show multi-language options in the UI.
	 *
	 * @return bool True to show multi-lingual options.
	 */
	public function show_multi_lingual() {
		return
			class_exists( 'SitePress' ) ||
			class_exists( 'Babble_Plugin' ) ||
			class_exists( 'WPGlobus' ) ||
			defined( 'POLYLANG_VERSION' ) ||
			defined( 'WEGLOT_VERSION' );
	}

	/**
	 * Insert a new replacement into the database.
	 *
	 * @param $orig_string
	 * @param $domain
	 * @param $context
	 * @param $replacement_string
	 * @param string $language
	 */
	public function insert_replacement( $orig_string, $domain, $context, $replacement_string, $lang = '' ) {
		global $wpdb, $table_prefix;
		$sql = "INSERT INTO {$table_prefix}say_what_strings
	                 VALUES ( NULL, %s, %s, %s, %s, %s )";
		return $wpdb->query(
			$wpdb->prepare(
				$sql,
				$orig_string,
				$domain,
				$replacement_string,
				$context,
				$lang
			)
		);
	}


	public function update_replacement( $id, $orig_string, $domain, $context, $replacement_string, $lang = '' ) {
		global $wpdb, $table_prefix;
		$sql = "UPDATE {$table_prefix}say_what_strings
					   SET orig_string = %s,
						   domain = %s,
						   context = %s,
						   replacement_string = %s,
						   lang = %s
					 WHERE string_id = %d";
		return $wpdb->query(
			$wpdb->prepare(
				$sql,
				$orig_string,
				$domain,
				$context,
				$replacement_string,
				$lang,
				$id
			)
		);
	}

	/**
	 * Return if a given ID exists in the configured replacements.
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	public function has_id( $id ) {
		global $wpdb, $table_prefix;
		$sql = "SELECT string_id FROM {$table_prefix}say_what_strings WHERE string_id = %d";
		return $wpdb->get_var( $wpdb->prepare( $sql, $id ) ) == $id;
	}

	/**
	 * Check if an "available string" record exists in the database.
	 *
	 * Works by checking the extracted array to avoid excessive small DB queries during discovery.
	 *
	 * @param  string   $key  The composite key for the string.
	 *
	 * @return boolean        True if the string is in the table already.
	 */
	public function has_available_string( $key ) {
		return in_array( $key, $this->available_strings, true );
	}

	/**
	 * Add an "available string" to the extracted array.
	 *
	 * @param  string   $key  The composite key for the string.
	 */
	public function add_available_string( $key ) {
		$this->available_strings[] = $key;
	}
}
