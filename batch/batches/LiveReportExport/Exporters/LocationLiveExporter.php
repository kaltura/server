<?php

class LocationLiveExporter extends LiveReportEntryExporter {

	public function __construct(KalturaLiveReportExportJobData $data) {
		parent::__construct($data, "location-live-now-@ENTRY_ID@-%s-%s.csv", LiveReportConstants::SECONDS_36_HOURS);
		$this->params[LiveReportConstants::IS_LIVE] = true;
	}

	protected function getEngines() {
		return array_merge(
			$this->getHeadersEngines("Location"),
			$this->liveEntriesEngines,
			array(new LiveReportLocation1MinEngine($this->dateFormatter))
		);
	}
}
