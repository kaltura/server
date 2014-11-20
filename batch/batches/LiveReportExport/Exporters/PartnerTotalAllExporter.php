<?php

class PartnerTotalAllExporter extends LiveReportExporter {

	public function __construct(KalturaLiveReportExportJobData $data) {
		parent::__construct($data);
		$fromTime = date(LiveReportConstants::DATE_FORMAT, $data->timeReference - LiveReportConstants::SECONDS_36_HOURS);
		$toTime = date(LiveReportConstants::DATE_FORMAT, $data->timeReference);
		$this->fileName = "all-entries-%s-%s.csv";
	}
	
	protected function getEngines() {
		$subEngines = array(
				new LiveReportEntryQueryEngine("plays", LiveReportConstants::SECONDS_36_HOURS, "Plays", false), 
				new LiveReportEntryQueryEngine("peakAudience", LiveReportConstants::SECONDS_36_HOURS, "Peak Audience", false),
				new LiveReportEntryQueryEngine("secondsViewed", LiveReportConstants::SECONDS_36_HOURS, "Seconds Viewed", false),
				new LiveReportEntryQueryEngine("bufferTime", LiveReportConstants::SECONDS_36_HOURS, "Buffering Time", false),
				new LiveReportEntryQueryEngine("avgBitrate", LiveReportConstants::SECONDS_36_HOURS, "Average Bitrate", false),
		);

		$allEntriesReport = array(
				new LiveReportConstantStringEngine("Report Type: All entries"), 
				new LiveReportConstantStringEngine(LiveReportConstants::ROWS_SEPARATOR),
				new LiveReportConstantStringEngine("Time Range: %s - %s"),
				new LiveReportConstantStringEngine(LiveReportConstants::ROWS_SEPARATOR),
				
				new LiveReportPartnerEngine("plays", LiveReportConstants::SECONDS_36_HOURS, "Total Plays:"),
				new LiveReportPartnerEngine("secondsViewed", LiveReportConstants::SECONDS_36_HOURS, "Seconds Viewed:"),
				new LiveReportPartnerEngine("bufferTime", LiveReportConstants::SECONDS_36_HOURS, "Average Buffering Time per Minute (seconds):"),
				new LiveReportPartnerEngine("avgBitrate", LiveReportConstants::SECONDS_36_HOURS, "Average Bitrate (kbps):"),
				
				new LiveReportConstantStringEngine(LiveReportConstants::ROWS_SEPARATOR),
				new LiveReportEntryBasedChunkerEngine($subEngines));
		
		return $allEntriesReport;
	}
}
