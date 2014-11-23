<?php

class SyndicationLiveExporter extends LiveReportEntryExporter {

	public function __construct($partnerId, KalturaLiveReportExportJobData $data) {
		parent::__construct($partnerId, $data);
		$this->params[LiveReportConstants::IS_LIVE] = true;
		
		$fromTime = date(LiveReportConstants::DATE_FORMAT, $data->timeReference - LiveReportConstants::SECONDS_36_HOURS);
		$toTime = date(LiveReportConstants::DATE_FORMAT, $data->timeReference);
		$this->fileName = $data->outputPath . DIRECTORY_SEPARATOR . "referrers-live-now-%s-%s.csv";
		$data->outputPath =  $this->fileName;
	}

	protected function getEngines() {
		return array_merge(
			array(
					new LiveReportConstantStringEngine("Report Type: Referrers of pure live (%s)"),
					new LiveReportConstantStringEngine(LiveReportConstants::ROWS_SEPARATOR),
					new LiveReportConstantStringEngine("Time Range: %s - %s")),
			$this->liveEntriesEngines,
			array(new LiveReportReferrerEngine())
		);

	}
}
