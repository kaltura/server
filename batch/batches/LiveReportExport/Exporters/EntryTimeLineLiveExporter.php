<?php

class EntryTimeLineLiveExporter extends LiveReportEntryExporter {

	public function __construct(KalturaLiveReportExportJobData $data) {
		parent::__construct($data, "audience-%s-%s.csv", LiveReportConstants::SECONDS_36_HOURS);
		$this->params[LiveReportConstants::IS_LIVE] = true;
	}
	
	protected function getEngines() {
		$audienceLiveReport = array_merge(
			array(
					new LiveReportConstantStringEngine("Report Type:". LiveReportConstants::CELLS_SEPARATOR ."Audience of pure live (%s)", array(LiveReportConstants::ENTRY_IDS)),
					new LiveReportConstantStringEngine(LiveReportConstants::ROWS_SEPARATOR),
					new LiveReportConstantStringEngine("Time Range:". LiveReportConstants::CELLS_SEPARATOR ."%s", array(self::TIME_RANGE))),
			$this->liveEntriesEngines,
			array(new LiveReportAudienceEngine())
		);
		return $audienceLiveReport;
	}

}
