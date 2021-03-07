<?php

class SayWhatProImporter implements SayWhatProImporterInterface {
	/**
	 * @var SayWhatProSettingsInterface
	 */
	private $settings;

	/**
	 * SayWhatProImporter constructor.
	 *
	 * @param SayWhatProSettingsInterface $settings
	 */
	public function __construct( SayWhatProSettingsInterface $settings) {
		$this->settings = $settings;
	}

	/**
	 * Process the file for import.
	 *
	 * @param $filename
	 *
	 * @return array Response indicated success, errors, and/or success message.
	 */
	public function import_file( $filename ) {
		$response = [
			'success' => false,
			'errors' => [],
			'success_message' => '',
		];
		try {
			$fh = fopen( $filename, 'r' );
			$headers = $this->get_headers( $fh );
			$inserted = $updated = 0;
			while ( $row = fgetcsv( $fh ) ) {
				$result = $this->process_row( $row, $headers );
				if ( 'inserted' === $result ) {
					$inserted++;
				} elseif ( 'updated' === $result) {
					$updated++;
				}
			}
			fclose( $fh );
		} catch ( \Exception $e ) {
			$response['errors'][] = $e->getMessage();
			return $response;
		}
		$response['success'] = true;

		$status_summary = sprintf(
			__( 'new records: %d, updates: %d', 'say_what' ),
			$inserted,
			$updated
		);
		$response['success_message'] = sprintf( __( 'Number of items imported: %d (%s).', 'say_what' ), $inserted + $updated, $status_summary );
		return $response;
	}

	/**
	 * Process a row of the CSV file.
	 *
	 * If there is an ID column, and it matches an existing ID then update an existing translation.
	 * Otherwise insert a new one.
	 *
	 * @param $row       The row of data.
	 * @param $headers   The array indicating the header positions.
	 */
	private function process_row( $row, $headers ) {

		// Pull out the values based on the CSV headings.

		//ID and language are optional.
		if ( isset( $headers['ID'] ) ) {
			$id = isset( $row[ $headers['ID'] ] ) ? $row[ $headers['ID'] ] : null;
		} else {
			$id = null;
		}
		if ( isset( $headers['Affected language'] ) ) {
			$language = isset( $row[ $headers['Affected language'] ] ) ? $row[ $headers['Affected language'] ] : '';
		} else {
			$language = '';
		}

		// Everything else is mandatory and has been checked as present.
		$original = isset( $row[ $headers['Original string'] ] ) ? $row[ $headers['Original string'] ] : '';
		$domain = isset( $row[ $headers['Text domain'] ] ) ? $row[ $headers['Text domain'] ] : '';
		$context = isset( $row[ $headers['Text context'] ] ) ? $row[ $headers['Text context'] ] : '';
		$replacement = isset( $row[ $headers['Replacement string'] ] ) ? $row[ $headers['Replacement string'] ] : '';

		if ( empty( $id ) ) {
			$this->settings->insert_replacement(
				$original,
				$domain,
				$context,
				$replacement,
				$language
			);
			return 'inserted';
		} else {
			if ( ! is_numeric( $id ) || (int) $id != $id ) {
				throw new \Exception( sprintf( __( 'Invalid ID received ("%s"), processing stopped.', 'say_what' ), $id ) );
			}
			if ( $this->settings->has_id( $id ) ) {
				$this->settings->update_replacement(
					$id,
					$original,
					$domain,
					$context,
					$replacement,
					$language
				);
				return 'updated';
			} else {
				$this->settings->insert_replacement(
					$original,
					$domain,
					$context,
					$replacement,
					$language
				);
				return 'inserted';
			}
		}
	}

	/**
	 * Read a row from the file handle, and extract required headers, or throw exception.
	 *
	 * @param $fh
	 *
	 * @return array
	 * @throws Exception
	 */
	private function get_headers( $fh ) {
		// Skip the first row as it should be headers only.
		$headers = fgetcsv( $fh );
		// Check we have the relevant fields.
		$missing = [];
		foreach ( [ 'Original string', 'Text domain', 'Text context', 'Replacement string' ] as $required_header ) {
			if ( ! in_array( $required_header, $headers, true ) ) {
				$missing[] = $required_header;
			}
		}
		if ( ! empty( $missing ) ) {
			throw new \Exception( sprintf( __( 'File missing required headers: %s.', 'say_what' ), implode( ', ', $missing ) ) );
		}
		return array_flip( $headers );
	}
}