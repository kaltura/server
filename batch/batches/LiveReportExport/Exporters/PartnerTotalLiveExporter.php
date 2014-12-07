<?php

class PartnerTotalLiveExporter extends LiveReportExporter {

	public function __construct(KalturaLiveReportExportJobData $data) {
		parent::__construct($data, "live-now-entries-%s-%s.csv", LiveReportConstants::SECONDS_36_HOURS);
		$this->params[LiveReportConstants::IS_LIVE] = true;
	}
	
	public function init(KalturaLiveReportExportJobData $jobData) {
		$filter = new KalturaLiveStreamEntryFilter();
		$filter->orderBy = KalturaLiveStreamEntryOrderBy::CREATED_AT_DESC;
		$filter->isLive = true;
	
		$pager = new KalturaFilterPager();
		$pager->pageIndex = 0;
		$pager->pageSize = LiveReportConstants::MAX_ENTRIES;
	
		/** @var KalturaLiveStreamListResponse */
		$response = KBatchBase::$kClient->liveStream->listAction($filter, $pager);
		$entryIds = array();
		foreach($response->objects as $object) {
			$entryIds[] = $object->id;
		}
	
		$this->params[LiveReportConstants::ENTRY_IDS] = implode(",", $entryIds);
	}
	
	protected function getEngines() {
		$subEngines = array(
				new LiveReportEntryEngine("name", "Entry name", new LiveReportStringFormatter()),
				new LiveReportEntryEngine("firstBroadcast", "First broadcast", $this->dateFormatter),
				new LiveReportEntryExactTimeEngine("audience", LiveReportConstants::SECONDS_60, "Total Audience", false), 
				new LiveReportEntryQueryEngine("peakAudience", LiveReportConstants::SECONDS_36_HOURS, "Peak Audience", false),
				new LiveReportEntryQueryEngine("secondsViewed", LiveReportConstants::SECONDS_36_HOURS, "Seconds Viewed", false),
				new LiveReportEntryQueryEngine("bufferTime", LiveReportConstants::SECONDS_60, "Buffering Time", false),
				new LiveReportEntryQueryEngine("avgBitrate", LiveReportConstants::SECONDS_60, "Average Bitrate", false),
		);
		
		$liveEntriesReport = array(
				new LiveReportConstantStringEngine("Report Type:". LiveReportConstants::CELLS_SEPARATOR ."Live Now Only"),
				new LiveReportConstantStringEngine(LiveReportConstants::ROWS_SEPARATOR),
				new LiveReportConstantStringEngine("Time Range:". LiveReportConstants::CELLS_SEPARATOR ."%s", array(self::TIME_RANGE)),
				new LiveReportConstantStringEngine(LiveReportConstants::ROWS_SEPARATOR),
				
				new LiveReportEntryExactTimeEngine("audience", LiveReportConstants::SECONDS_60, "Total Audience:"), 
				new LiveReportLivePartnerEngine("secondsViewed", LiveReportConstants::SECONDS_36_HOURS, "Seconds Viewed:"),
				new LiveReportLivePartnerEngine("bufferTime", LiveReportConstants::SECONDS_60, "Average Buffering Time per Minute (seconds):"),
				new LiveReportLivePartnerEngine("avgBitrate", LiveReportConstants::SECONDS_60, "Average Bitrate (kbps):"),
				
				new LiveReportEntryBasedChunkerEngine($subEngines));
		
		return $liveEntriesReport;
	}
}
