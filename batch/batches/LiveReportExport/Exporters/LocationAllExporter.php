<?php

class LocationAllExporter extends LiveReportEntryExporter {

	public function __construct(KalturaLiveReportExportJobData $data) {
		parent::__construct($data, "location-@ENTRY_ID@-%s-%s.csv", LiveReportConstants::SECONDS_36_HOURS);
	}
	
	protected function getEngines() {
		return array_merge(
			$this->getHeadersEngines("Location"),
			$this->allEntriesEngines,
			array(new LiveReportLocation1MinEngine($this->dateFormatter))
		);
	}
}