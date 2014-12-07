<?php

class LocationAllExporter extends LiveReportEntryExporter {

	public function __construct(KalturaLiveReportExportJobData $data) {
		parent::__construct($data, "location-@ENTRY_ID@-%s-%s.csv", LiveReportConstants::SECONDS_36_HOURS);
		$this->fileName = str_replace("@ENTRY_ID@", $data->entryIds, $this->fileName);
		$data->outputPath = $this->fileName;
	}
	
	protected function getEngines() {
		return array_merge(
			array(
					new LiveReportConstantStringEngine("Report Type:". LiveReportConstants::CELLS_SEPARATOR ."Locations of pure live (%s)",
							 array(LiveReportConstants::ENTRY_IDS)),
					new LiveReportConstantStringEngine(LiveReportConstants::ROWS_SEPARATOR),
					new LiveReportConstantStringEngine("Time Range:". LiveReportConstants::CELLS_SEPARATOR ."%s", array(self::TIME_RANGE))),
			$this->allEntriesEngines,
			array(new LiveReportLocation1MinEngine($this->dateFormatter))
		);
	}
}