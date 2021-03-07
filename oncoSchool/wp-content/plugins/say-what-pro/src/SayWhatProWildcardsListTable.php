<?php

use leewillis77\WpListTableExportable\WpListTableExportable;

/**
 * List table class for the admin pages
 */
class SayWhatProWildcardsListTable extends WPListTableExportable implements SayWhatProListTableInterface {


	/**
	 * @var SayWhatProSettingsInterface
	 */
	private $settings;

	/**
	 * @var array
	 */
	private $translations;

	/**
	 * Constructor
	 *
	 * @param SayWhatProSettingsInterface $settings
	 */
	function __construct( SayWhatProSettingsInterface $settings ) {
		$this->settings     = $settings;
		$this->translations = wp_get_available_translations();
		parent::__construct();
		$this->export_button_text = __( 'Export wildcard swaps', 'say_what' );
	}

	/**
	 * Description shown when no replacements configured
	 */
	function no_items() {
		_e( 'No wildcard swaps configured yet.', 'say_what' );
	}

	/**
	 * Specify the list of columns in the table
	 * @return array The list of columns
	 */
	function get_columns() {
		$columns = array(
			'cb'          => '<input type="checkbox" />',
			'wildcard_id' => _x( 'ID', 'Unique ID of the wildcard swap', 'say_what' ),
			'original'    => __( 'Look for', 'say_what' ),
			'replacement' => __( 'Swap with', 'say_what' ),
		);
		if ( $this->settings->show_multi_lingual() ) {
			$columns['lang'] = __( 'Affected language', 'say_what' );
		}
		return $columns;
	}

	/**
	 * Set the primary column.
	 *
	 * @return string The name of the primary column.
	 */
	function get_default_primary_column_name() {
		return 'original';
	}

	/**
	 * Retrieve the items for display
	 */
	function prepare_items() {

		global $wpdb, $table_prefix;

		$this->process_bulk_actions();

		$columns  = $this->get_columns();
		$hidden   = array( 'wildcard_id' );
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		// We don't use the replacements from the settings object, we query them separately to make
		// ordering/searching/pagination easier. This may turn out bad if people have "lots"
		$sql = "SELECT * FROM {$table_prefix}say_what_wildcards";
		if ( isset( $_GET['orderby'] ) ) {
			$sql .= ' ORDER BY ' . $wpdb->escape( $_GET['orderby'] );
			if ( isset( $_GET['order'] ) ) {
				$sql .= ' ' . $wpdb->escape( $_GET['order'] );
			}
		} else {
			$sql .= ' ORDER BY original ASC';
		}
		$this->items = $wpdb->get_results( $sql, ARRAY_A );
	}

	/**
	 * Indicate which columns are sortable
	 * @return array A list of the columns that are sortable.
	 */
	function get_sortable_columns() {
		return array(
			'original'    => array( 'original', true ),
			'replacement' => array( 'replacement', false ),
		);
	}

	/**
	 * Specify the bulk actions available.
	 */
	function get_bulk_actions() {
		return array(
			'delete' => __( 'Delete', 'say_what' ),
		);
	}

	/**
	 * Checkboxes for the rows.
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="wildcard_id[]" value="%d" />',
			$item['wildcard_id']
		);
	}

	/**
	 * Output column data.
	 */
	function column_default( $item, $column_name ) {
		return esc_html( htmlspecialchars( $item[ $column_name ] ) );
	}

	/**
	 * Output column data.
	 */
	function column_lang( $item ) {
		if ( empty( $item['lang'] ) ) {
			return __( 'Any', 'say_what' );
		} else {
			if ( ! empty( $this->translations[ $item['lang'] ] ) ) {
				return esc_html( $this->translations[ $item['lang'] ]['english_name'] ) . ' (' . $item['lang'] . ')';
			}
			return esc_html( $item['lang'] );
		}
	}

	/**
	 * Output column data.
	 */
	function column_csv_lang( $item ) {
		if ( empty( $item['lang'] ) ) {
			return '';
		} else {
			return $item['lang'];
		}
	}

	/**
	 * Output the original column.
	 *
	 * Includes row actions.
	 *
	 * @param  array $item  The row item.
	 * @return string       The output for the column.
	 */
	function column_original( $item ) {
		$data = esc_html( htmlspecialchars( $item['original'] ) );
		return $data . ' ' . $this->generate_row_actions( $item );
	}

	/**
	 * Output the orig_string column for CSV output.
	 * @param  array $item  The row item.
	 * @return string       The output for the column.
	 */
	function column_csv_original( $item ) {
		return $item['original'];
	}

	/**
	 * Generate the row actions markup.
	 *
	 * @param  array $item  The row item.
	 * @return array        Array of row action links.
	 */
	private function generate_row_actions( $item ) {
		return $this->row_actions(
			array(
				'edit' => '<a href="tools.php?page=say_what_admin&amp;say_what_action=addeditwildcards&amp;id=' .
					urlencode( $item['wildcard_id'] ) .
					'&amp;nonce=' .
					urlencode( wp_create_nonce( 'swaddedit' ) ) .
					'">' .
					__( 'Edit', 'say_what' ) .
					'</a>',
				'delete' => '<a href="tools.php?page=say_what_admin&say_what_action=deletewildcard&id=' .
					urlencode( $item['wildcard_id'] ) .
					'&nonce=' .
					urlencode( wp_create_nonce( 'swdelete' ) ) .
					'">' .
					__( 'Delete', 'say_what' ) .
					'</a>',
			)
		);
	}

	/**
	 * Make sure that the ID column isn't hidden when exporting.
	 */
	public function hidden_columns_csv() {
		return array();
	}

	/**
	 * Bulk action controller.
	 */
	function process_bulk_actions() {
		if ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) {
			$nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
			$action = 'bulk-' . $this->_args['plural'];
			if ( ! wp_verify_nonce( $nonce, $action ) ) {
				wp_die( 'Nope! Security check failed!' );
			}
		}
		$action = $this->current_action();
		switch ( $action ) {
			case 'delete':
				if ( ! empty( $_POST['wildcard_id'] ) ) {
					$this->process_bulk_delete( $_POST['wildcard_id'] );
				}
				break;
			default;
				break;
		}
	}

	/**
	 * Process the delete bulk action for a list of IDs.
	 *
	 * @param  array $ids  Array of IDs to remove.
	 */
	private function process_bulk_delete( $ids ) {
		global $wpdb, $table_prefix;
		$id_list = implode( ',', array_map( 'intval', $ids ) );
		$wpdb->query(
			"DELETE FROM {$table_prefix}say_what_wildcards WHERE wildcard_id IN (" . $id_list . ")"
		);
		wp_redirect( 'tools.php?page=say_what_admin&say_what_action=wildcards', '303' );
		die();
	}


}
