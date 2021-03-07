<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * The frontend class, responsible for performing the actual replacements
 */
class SayWhatProFrontend implements SayWhatProFrontendInterface{

	/**
	 * Instance of the plugin settings class.
	 * @var SayWhatProSettingsInterface
	 */
	private $settings;

	/**
	 * The current locale.
	 * @var string
	 */
	private $lang;

	/**
	 * @var SayWhatProStringDiscoveryInterface
	 */
	private $string_discovery;

	/**
	 * Constructor.
	 *
	 * Store our dependencies.
	 *
	 * @param SayWhatProSettingsInterface $settings                  The settings instance dependency.
	 * @param SayWhatProStringDiscoveryInterface $string_discovery   The string discovery instance.
	 */
	public function __construct( SayWhatProSettingsInterface $settings, SayWhatProStringDiscoveryInterface $string_discovery ) {
		$this->settings = $settings;
		$this->string_discovery = $string_discovery;
	}

	/**
	* Run the plugin functionality.
	*
	* Sets up all filters.
	*/
	public function run() {

		// Grab the locale as-set currently.
		$this->update_lang();

		// Most translation plugins filter the locale, so queue up a request to
		// update it when the alternative is available.
		add_action( 'plugins_loaded', array( $this, 'update_lang' ) );
		add_action( 'init', array( $this, 'update_lang' ) );
		add_action( 'template_redirect', array( $this, 'update_lang' ) );

		// Add filters to handle carrying out our replacements.
		add_filter( 'gettext', array( $this, 'gettext' ), 10, 3 );
		add_filter( 'ngettext', array( $this, 'ngettext' ), 10, 5 );
		add_filter( 'gettext_with_context', array( $this, 'gettext_with_context' ), 10, 4 );
		add_filter( 'ngettext_with_context', array( $this, 'ngettext_with_context' ), 10, 6 );
	}

	/**
	 * Update the internal locale selection from WordPress' current selection.
	 */
	public function update_lang() {
		$this->lang = get_locale();
	}

	/**
	 * Perform a string replacement without context.
	 */
	public function gettext( $translated, $original, $domain ) {
		return $this->ngettext_with_context( $translated, $original, null, null, 'sw-default-context', $domain );
	}

	/**
	 * Perform a string replacement with context.
	 */
	public function gettext_with_context( $translated, $original, $context, $domain ) {
		return $this->ngettext_with_context( $translated, $original, null, null, $context, $domain );
	}

	/**
	 * Perform a (possibly) pluralised translation without context.
	 */
	public function ngettext( $translated, $single, $plural, $number, $domain ) {
		return $this->ngettext_with_context( $translated, $single, $plural, $number, 'sw-default-context', $domain );
	}

	/**
	 * Perform a (possibly) pluralised translation with context.
	 *
	 * Note: This also handles the main logic for all other replacements.
	 *
	 * @param  string $translated The current string.
	 * @param  string $single     The original (singular) string.
	 * @param  string $plural     The original (pluralised) string.
	 *                            [May be NULL for non _n()-type calls]
	 * @param  int    $number     The number used to determine if singular or pluralised should be used.
	 *                            [May be NULL for non _n()-type calls]
	 * @param  [type] $context    The context, may be null for non _x()-type calls.
	 * @param  [type] $domain     The domain.
	 * @return [type]             The replaced string.
	 */
	public function ngettext_with_context( $translated, $single, $plural, $number, $context, $domain ) {
		/*
		 * Plugins can use the say_what_domain_aliases filter to return an alias for their domain
		 * if for any reason they change their text domain and want existing replacements to continue
		 * working. The filter should return an array keyed on the current text domain with the value
		 * set to an array of alternative domains to search for replacements. E.g
		 *   $aliases['easy-digital-downloads'][] = 'edd';
		 *   return $aliases;
		 */
		global $disable_say_what_replacements;
		static $domain_aliases = null;

		if ( $disable_say_what_replacements ) {
			return $translated;
		}

		if ( null === $domain_aliases ) {
			$domain_aliases = apply_filters( 'say_what_domain_aliases', array() );
		}
		$original = $single;
		if ( ! is_null( $number ) && 1 !== $number ) {
			$original = $plural;
		}
		$this->string_discovery->maybe_log_available_replacement(
			$original,
			$domain,
			$context,
			$translated
		);

		// Check the given domain.
		if ( isset( $this->settings->optimised_replacements[ $domain ][ $original ][ $context ][ $this->lang ] ) ) {
			// We have a replacement in the provided domain, for this language.
			return $this->settings->optimised_replacements[ $domain ][ $original ][ $context ][ $this->lang ];
		} elseif ( isset( $this->settings->optimised_replacements[ $domain ][ $original ][ $context ]['default'] ) ) {
			// We have a replacement in the provided domain, for the no-language variant.
			return $this->settings->optimised_replacements[ $domain ][ $original ][ $context ]['default'];
		}
		// Check any domain aliases.
		if ( isset( $domain_aliases[ $domain ] ) ) {
			foreach ( $domain_aliases[ $domain ] as $domain ) {
				if ( isset( $this->settings->optimised_replacements[ $domain ][ $original ][ $context ][ $this->lang ] ) ) {
					return $this->settings->optimised_replacements[ $domain ][ $original ][ $context ][ $this->lang ];
				} elseif ( isset( $this->settings->optimised_replacements[ $domain ][ $original ][ $context ]['default'] ) ) {
					return $this->settings->optimised_replacements[ $domain ][ $original ][ $context ]['default'];
				}
			}
		}
		// If we get here there was no replacement.

		// Apply any language-specific wildcards.
		if ( ! empty( $this->settings->wildcards[ $this->lang ] ) ) {
			foreach ( $this->settings->wildcards[ $this->lang ] as $original => $swap ) {
				$translated = str_replace( $original, $swap, $translated );
			}
		}
		// Apply any generic language replacements.
		if ( ! empty( $this->settings->wildcards['default'] ) ) {
			foreach ( $this->settings->wildcards['default'] as $original => $swap ) {
				$translated = str_replace( $original, $swap, $translated );
			}
		}
		return $translated;
	}

}
