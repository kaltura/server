<?php
 
/**
 * This class is the basic implemenation for quering for an entry's audience report. 
 */
class LiveReportAudienceEngine extends LiveReportEngine {
	
	const TIME_CHUNK = 3600;
	
	public function run($fp, array $args = array()) {
		
		$this->checkParams($args, array(LiveReportConstants::TIME_REFERENCE_PARAM));
		
		fwrite($fp, "DateTime" . LiveReportConstants::CELLS_SEPARATOR . "Audience\n");
		
		$endTime =  $args[LiveReportConstants::TIME_REFERENCE_PARAM];
		$timeRange = LiveReportConstants::SECONDS_36_HOURS;
		
		for($curTime = $endTime - $timeRange; $curTime < $endTime ; $curTime = $curTime + self::TIME_CHUNK) {
			$this->executeAudienceQuery($fp, $curTime, $curTime + self::TIME_CHUNK, $args);
		}
		
	}
	
	protected function executeAudienceQuery($fp, $fromTime, $toTime, $args) {
		$this->checkParams($args, array(LiveReportConstants::ENTRY_IDS));

		$reportType = KalturaLiveReportType::ENTRY_TIME_LINE;
		$filter = new KalturaLiveReportInputFilter();
		$filter->toTime = $toTime;
		$filter->fromTime = $fromTime;
		$filter->entryIds = $args[LiveReportConstants::ENTRY_IDS];

		$resultsStr = LiveReportQueryHelper::getEvents($reportType, $filter, null, "audience");
		$couples = explode(";", $resultsStr);
		
		foreach($couples as $couple) {
			$parts = explode(",", $couple);
			if(count($parts) == 2) {
				$msg = implode(LiveReportConstants::CELLS_SEPARATOR, $parts) . "\n";
				fwrite($fp, $msg);
			}
		}
	}

}
