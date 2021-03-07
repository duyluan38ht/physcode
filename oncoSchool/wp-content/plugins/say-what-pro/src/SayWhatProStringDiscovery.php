<?php

/**
 * Class SayWhatProStringDiscovery
 */
class SayWhatProStringDiscovery implements SayWhatProStringDiscoveryInterface {

	/**
	 * @var SayWhatProSettingsInterface
	 */
	private $settings;

	/**
	 * Temporary storage during String Discovery to buffer writes to the DB.
	 *
	 * @var array
	 */
	private $available_replacements = array();


	/**
	 * SayWhatProStringDiscovery constructor.
	 *
	 * @param SayWhatProSettingsInterface $settings
	 */
	public function __construct( SayWhatProSettingsInterface $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Turn on the features.
	 */
	public function run() {
		if ( $this->is_active() ) {
			// Write available replacements to the DB during string discovery.
			add_action( 'shutdown', array( $this, 'write_available_replacements' ) );
		}
	}

	/**
	 * Enables string discovery.
	 */
	public function enable() {
		setcookie( 'say-what-pro-discovery-active', true, 0, '/' );
		$_COOKIE['say-what-pro-discovery-active'] = true;
	}

	/**
	 * Disables string discovery.
	 */
	public function disable() {
		setcookie( 'say-what-pro-discovery-active', false, 0, '/' );
		$_COOKIE['say-what-pro-discovery-active'] = false;
	}

	/**
	 * Whether string discovery is active or not.
	 * @return bool
	 */
	public function is_active() {
		return isset( $_COOKIE['say-what-pro-discovery-active'] ) && $_COOKIE['say-what-pro-discovery-active'];
	}

	/**
	 * Decide whether this possible replacement should be logged, and if it should - log it.
	 */
	public function maybe_log_available_replacement( $original, $domain, $context, $translated_string ) {
		if ( $this->is_active() ) {
			$this->log_available_replacement( $original, $domain, $context, $translated_string );
		}
	}

	/**
	 * Write out a block of available replacements into the database.
	 */
	public function write_available_replacements() {
		global $wpdb, $table_prefix;
		if ( ! count( $this->available_replacements ) ) {
			return;
		}
		$cnt = count( $this->available_replacements ) / 4;
		// Store items in the DB
		$sql = "INSERT LOW_PRIORITY IGNORE INTO ${table_prefix}say_what_available_strings ( orig_string, domain, context, translated_string ) VALUES ";
		$sql .= str_repeat( '(%s,%s,%s,%s),', $cnt - 1 ) . '(%s,%s,%s,%s)';
		$wpdb->query( $wpdb->prepare( $sql, $this->available_replacements ) );
		// Empty the array
		$this->available_replacements = array();
	}

	/**
	 * Log an available replacement.
	 */
	private function log_available_replacement( $original, $domain, $context, $translated_string ) {
		$key = implode( '|', array( $original, $domain, $context, $translated_string ) );
		if ( $this->settings->has_available_string( $key ) ) {
			return;
		}
		$this->available_replacements[] = $original;
		$this->available_replacements[] = $domain;
		$this->available_replacements[] = $context;
		$this->available_replacements[] = $translated_string;
		$this->settings->add_available_string( $key );
		if ( count( $this->available_replacements ) > 20 ) {
			$this->write_available_replacements();
		}
	}
}
