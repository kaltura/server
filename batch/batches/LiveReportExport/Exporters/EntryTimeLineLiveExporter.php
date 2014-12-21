<?php

class EntryTimeLineLiveExporter extends LiveReportEntryExporter {

	public function __construct(KalturaLiveReportExportJobData $data) {
		parent::__construct($data, "audience-@ENTRY_ID@-%s-%s.csv", LiveReportConstants::SECONDS_36_HOURS);
		$this->params[LiveReportConstants::IS_LIVE] = true;
	}
	
	protected function getEngines() {
		$audienceLiveReport = array_merge(
			$this->getHeadersEngines("Audience"),
			$this->liveEntriesEngines,
			array(new LiveReportAudienceEngine($this->dateFormatter))
		);
		return $audienceLiveReport;
	}

}
