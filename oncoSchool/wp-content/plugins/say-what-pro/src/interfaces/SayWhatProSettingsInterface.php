<?php

interface SayWhatProSettingsInterface {
	public function __construct( $plugin_path );
	public function run();
	public function insert_replacement( $orig_string, $domain, $context, $replacement_string, $lang = '');
	public function update_replacement( $id, $orig_string, $domain, $context, $replacement_string, $lang = '');
}
