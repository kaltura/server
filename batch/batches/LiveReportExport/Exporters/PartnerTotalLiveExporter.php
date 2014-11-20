<?php

class PartnerTotalLiveExporter extends LiveReportExporter {

	public function __construct(KalturaLiveReportExportJobData $data) {
		parent::__construct($data);
		$this->params[LiveReportConstants::IS_LIVE] = true;
		
		$fromTime = date(LiveReportConstants::DATE_FORMAT, $data->timeReference - LiveReportConstants::SECONDS_36_HOURS);
		$toTime = date(LiveReportConstants::DATE_FORMAT, $data->timeReference);
		$this->fileName = "live-noe-entries-%s-%s.csv";
	}
	
	protected function getEngines() {
		$subEngines = array(
				new LiveReportEntryQueryEngine("audience", LiveReportConstants::SECONDS_10, "Total Plays:", false),
				new LiveReportEntryQueryEngine("peakAudience", LiveReportConstants::SECONDS_36_HOURS, "Peak Audience", false),
				new LiveReportEntryQueryEngine("secondsViewed", LiveReportConstants::SECONDS_36_HOURS, "Seconds Viewed", false),
				new LiveReportEntryQueryEngine("bufferTime", LiveReportConstants::SECONDS_60, "Buffering Time", false),
				new LiveReportEntryQueryEngine("avgBitrate", LiveReportConstants::SECONDS_10, "Average Bitrate", false),
		);
		
		$liveEntriesReport = array(
				new LiveReportConstantStringEngine("Report Type: Live Now Only"),
				new LiveReportConstantStringEngine(LiveReportConstants::ROWS_SEPARATOR),
				new LiveReportConstantStringEngine("Time Range: %s - %s"),
				new LiveReportConstantStringEngine(LiveReportConstants::ROWS_SEPARATOR),
				
				new LiveReportLivePartnerEngine("audience", LiveReportConstants::SECONDS_10, "Total Audience:"),
				new LiveReportLivePartnerEngine("secondsViewed", LiveReportConstants::SECONDS_36_HOURS, "Seconds Viewed:"),
				new LiveReportLivePartnerEngine("bufferTime", LiveReportConstants::SECONDS_60, "Average Buffering Time per Minute (seconds):"),
				new LiveReportLivePartnerEngine("avgBitrate", LiveReportConstants::SECONDS_10, "Average Bitrate (kbps):"),
				
				new LiveReportEntryBasedChunkerEngine($subEngines));
		
		return $liveEntriesReport;
	}
}
