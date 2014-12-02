<?php
 
/**
 * This class is the basic implemenation for quering Syndication report.
 * If you need more complex behavior - please inherit this class
 */
 class LiveReportReferrerEngine extends LiveReportEngine {
	
	const CHUNK_SIZE = 100;
	
	public function run($fp, array $args = array()) {
		
		fwrite($fp, "Referrer" . LiveReportConstants::CELLS_SEPARATOR . "Visits\n");
		for($i = 0 ; ; $i += 1) {
			$res = $this->querySyndicationReport(LiveReportConstants::SECONDS_36_HOURS, $i, $args);
			foreach ($res as $referer => $plays) {
				fwrite($fp, $referer . LiveReportConstants::CELLS_SEPARATOR . $plays . "\n");
			}
			if(count($res) < self::CHUNK_SIZE)
				break; 
		}
	}
	
	protected function querySyndicationReport($timeFrame, $pageIdx, $args) {
			$this->checkParams($args, array(LiveReportConstants::TIME_REFERENCE_PARAM, LiveReportConstants::ENTRY_IDS));
		
		$reportType = KalturaLiveReportType::ENTRY_SYNDICATION_TOTAL;
		$filter = new KalturaLiveReportInputFilter();
		$filter->toTime = $args[LiveReportConstants::TIME_REFERENCE_PARAM];
		$filter->fromTime = $args[LiveReportConstants::TIME_REFERENCE_PARAM] - $timeFrame;
		$filter->entryIds =  $args[LiveReportConstants::ENTRY_IDS];
		
		$pager = new KalturaFilterPager();
		$pager->pageIndex = $pageIdx;
		$pager->pageSize = self::CHUNK_SIZE;
		
		return LiveReportQueryHelper::retrieveFromReport($reportType, $filter, null, "referrer", "plays");
	}
}
