<?php

class PartnerTotalAllExporter extends LiveReportExporter {

	public function __construct(KalturaLiveReportExportJobData $data) {
		parent::__construct($data, "all-entries-%s-%s.csv", LiveReportConstants::SECONDS_36_HOURS);
	}
	
	public function init(KalturaLiveReportExportJobData $jobData) {
		
		$filter = new KalturaLiveReportInputFilter();
		$filter->live =  false;
		$filter->toTime = $jobData->timeReference;
		$filter->fromTime = $jobData->timeReference - LiveReportConstants::SECONDS_36_HOURS;
		
		$pager = new KalturaFilterPager();
		$pager->pageIndex = 0;
		$pager->pageSize = LiveReportConstants::MAX_ENTRIES;
		
		$entryIds = LiveReportQueryHelper::retrieveFromReport(KalturaLiveReportType::ENTRY_TOTAL, $filter, $pager, null, "entryId");
		$this->params[LiveReportConstants::ENTRY_IDS] = implode(",", $entryIds);
	}
	
	protected function getEngines() {
		$subEngines = array(
				new LiveReportEntryEngine("name", "Entry name"),
				new LiveReportEntryEngine("firstBroadcast", "First broadcast"),
				new LiveReportEntryQueryEngine("plays", LiveReportConstants::SECONDS_36_HOURS, "Plays", false), 
				new LiveReportEntryQueryEngine("peakAudience", LiveReportConstants::SECONDS_36_HOURS, "Peak Audience", false),
				new LiveReportEntryQueryEngine("secondsViewed", LiveReportConstants::SECONDS_36_HOURS, "Seconds Viewed", false),
				new LiveReportEntryQueryEngine("bufferTime", LiveReportConstants::SECONDS_36_HOURS, "Buffering Time", false),
				new LiveReportEntryQueryEngine("avgBitrate", LiveReportConstants::SECONDS_36_HOURS, "Average Bitrate", false),
		);

		$allEntriesReport = array(
				new LiveReportConstantStringEngine("Report Type:". LiveReportConstants::CELLS_SEPARATOR ."All entries", array(LiveReportConstants::ENTRY_IDS)), 
				new LiveReportConstantStringEngine(LiveReportConstants::ROWS_SEPARATOR),
				new LiveReportConstantStringEngine("Time Range:". LiveReportConstants::CELLS_SEPARATOR ."%s", array(self::TIME_RANGE)),
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
