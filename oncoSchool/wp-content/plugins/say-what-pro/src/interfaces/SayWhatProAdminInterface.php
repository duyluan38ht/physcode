<?php

interface SayWhatProAdminInterface {
	public function __construct(
		SayWhatProSettingsInterface $settings,
		SayWhatProStringDiscoveryInterface $string_discovery,
		SayWhatProAutocompleteMatcherInterface $autocomplete_matcher,
		SayWhatProListTableFactoryInterface $list_table_factory,
		SayWhatProImporterInterface $importer
	);
	public function run();
	public function autocomplete();
}
