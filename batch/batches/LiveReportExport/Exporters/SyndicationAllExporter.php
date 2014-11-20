<?php

class SyndicationAllExporter extends LiveReportEntryExporter {

	public function __construct(KalturaLiveReportExportJobData $data) {
		parent::__construct($data);
		
		$fromTime = date(LiveReportConstants::DATE_FORMAT, $data->timeReference - LiveReportConstants::SECONDS_36_HOURS);
		$toTime = date(LiveReportConstants::DATE_FORMAT, $data->timeReference);
		$this->fileName = "referrers-%s-%s.csv";
	}

	protected function getEngines() {
		return array_merge(
			array(
					new LiveReportConstantStringEngine("Report Type: Referrers of pure live (%s)"),
					new LiveReportConstantStringEngine(LiveReportConstants::ROWS_SEPARATOR),
					new LiveReportConstantStringEngine("Time Range: %s - %s")),
			$this->allEntriesEngines,
			array(new LiveReportReferrerEngine())
		);
	}
}

