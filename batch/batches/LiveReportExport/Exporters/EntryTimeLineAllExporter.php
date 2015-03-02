<?php

class EntryTimeLineAllExporter extends LiveReportEntryExporter {

	public function __construct(KalturaLiveReportExportJobData $data) {
		parent::__construct($data, "audience-@ENTRY_ID@-%s-%s.csv", LiveReportConstants::SECONDS_36_HOURS);
	}
	
	protected function getEngines() {
		$audienceAllReport = array_merge(
			$this->getHeadersEngines("Audience"),
			$this->allEntriesEngines,
			array(new LiveReportAudienceEngine($this->dateFormatter))
		);
		return $audienceAllReport;
	}

}
