<?php
 
/**
 * This class is the basic implemenation for quering for an entry's audience report. 
 */
class LiveReportAudienceEngine extends LiveReportEngine {
	
	const TIME_CHUNK = 3600;
	
	protected $formatter;
	
	public function __construct(LiveReportDateFormatter $formatter) {
		$this->formatter = $formatter;
	}
	
	public function run($fp, array $args = array()) {
		
		$this->checkParams($args, array(LiveReportConstants::TIME_REFERENCE_PARAM));
		$showDvr = $this->shouldShowDvrColumns($args[LiveReportConstants::ENTRY_IDS]);
		$dvrHeader =  $showDvr ? LiveReportConstants::CELLS_SEPARATOR . "DVR" : "";
		fwrite($fp, "DateTime" . LiveReportConstants::CELLS_SEPARATOR . "Audience" . "$dvrHeader\n");
		
		$endTime =  $args[LiveReportConstants::TIME_REFERENCE_PARAM];
		$timeRange = LiveReportConstants::SECONDS_36_HOURS;
		
		$fix = 0; // The report is inclussive, therefore starting from the the second request we shouldn't query twice
		for($curTime = $endTime - $timeRange; $curTime < $endTime ; $curTime = $curTime + self::TIME_CHUNK) {
			$this->executeAudienceQuery($fp, $curTime + $fix, $curTime + self::TIME_CHUNK, $args, $showDvr);
			$fix = LiveReportConstants::SECONDS_10;
		}
		
	}
	
	protected function executeAudienceQuery($fp, $fromTime, $toTime, $args, $showDvr) {
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
			if(count($parts) >= 2) {
				$parts[0] = $this->formatter->format($parts[0]);
				if ($showDvr) {
					$msg = implode(LiveReportConstants::CELLS_SEPARATOR, $parts) . "\n";
				}
				else {
					$msg = implode(LiveReportConstants::CELLS_SEPARATOR, array_slice($parts,0,2)) . "\n";
				}
				fwrite($fp, $msg);
			}
		}
	}

}
