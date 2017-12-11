<?php
 
/**
 * This class is a complex implementation for 1min aggregation of location report
 */
class LiveReportLocation1MinEngine extends LiveReportEngine {
	
	const TIME_CHUNK = 300;
	const MAX_RECORDS_PER_CHUNK = 10000;
	const AGGREGATION_CHUNK = LiveReportConstants::SECONDS_60;
	
	protected $dateFormatter;
	protected $nameFormatter;
	
	public function __construct(LiveReportDateFormatter $dateFormatter) {
		$this->dateFormatter = $dateFormatter;
		$this->nameFormatter = new LiveReportStringFormatter();
	}
	
	public function run($fp, array $args = array()) {
		$this->checkParams($args, array(LiveReportConstants::TIME_REFERENCE_PARAM, LiveReportConstants::ENTRY_IDS));
		$toTime = $args[LiveReportConstants::TIME_REFERENCE_PARAM];
		$fromTime = $args[LiveReportConstants::TIME_REFERENCE_PARAM] - LiveReportConstants::SECONDS_36_HOURS;
		$showDvr = $this->shouldShowDvrColumns($args[LiveReportConstants::ENTRY_IDS]);

		$this->printHeaders($fp, $showDvr);
		
		$objs = array();
		$lastTimeGroup = null;
		
		for($curTime = $fromTime; $curTime < $toTime; $curTime = $curTime + self::TIME_CHUNK) {
			$curTo = min($toTime, $curTime + self::TIME_CHUNK - LiveReportConstants::SECONDS_10);
			
			$results = $this->getRecords($curTime, $curTo, $args[LiveReportConstants::ENTRY_IDS]);
			
			foreach($results->objects as $result) {
				
				$groupTime = $this->roundTime($result->timestamp);
				
				if(is_null($lastTimeGroup))
					$lastTimeGroup = $groupTime;
				
				if($lastTimeGroup < $groupTime) {
					$this->printRows($fp, $objs, $lastTimeGroup, $showDvr);
					$lastTimeGroup = $groupTime;
				}
				
				$country = $result->country->name;
				$city = $result->city->name;
				$key = ($result->entryId . "#" . $country . "#" . $city);
		
				if(!array_key_exists($key, $objs)) {
					$objs[$key] = array();
				}
				$objs[$key][] = $result;
			}
		}
		
		$this->printRows($fp, $objs, $lastTimeGroup, $showDvr);
	}
	
	// ASUMPTION - we have a single entry ID (that's a constraint of the cassandra)
	// and the results are ordered from the oldest to the newest
	protected function getRecords($fromTime, $toTime, $entryId) {
		
		$reportType = KalturaLiveReportType::ENTRY_GEO_TIME_LINE;
		$filter = new KalturaLiveReportInputFilter();
		$filter->toTime = $toTime;
		$filter->fromTime = $fromTime;
		$filter->entryIds = $entryId;
		
		$pager = new KalturaFilterPager();
		$pager->pageIndex = 1;
		$pager->pageSize = self::MAX_RECORDS_PER_CHUNK;
		
		return KBatchBase::$kClient->liveReports->getReport($reportType, $filter, $pager);
	}


	
	protected function printHeaders($fp, $showDvr) {
		$values = array();
		$values[] = "Date";
		$values[] = "Country";
		$values[] = "City";
		$values[] = "Latitude";
		$values[] = "Longitude";
		$values[] = "Plays";
		$values[] = "Average Audience";
		$values[] = "Min Audience";
		$values[] = "Max Audience";
		if ($showDvr) {
			$values[] = "Average DVR Audience";
			$values[] = "Min DVR Audience";
			$values[] = "Max DVR Audience";
		}
		$values[] = "Average bitrate";
		$values[] = "Buffer time";
		$values[] = "Seconds viewed";
		
		fwrite($fp, implode(LiveReportConstants::CELLS_SEPARATOR, $values) . "\n");
	}
	
	protected function printRows($fp, &$objects, $lastTimeGroup, $showDvr) {
		
		foreach ($objects as $records) {

			$firstRecord = $records[0];
			
			$values = array();
			$values[] = $this->dateFormatter->format($lastTimeGroup);
			$values[] = $this->nameFormatter->format($firstRecord->country->name);
			$values[] = $this->nameFormatter->format($firstRecord->city->name);
			$values[] = $firstRecord->city->latitude;
			$values[] = $firstRecord->city->longitude;
			
			$plays = $audience = $avgBitrate = $bufferTime = $secondsViewed = $maxAudience = $dvrAudience = $maxDvrAudience = 0;
			$minAudience = PHP_INT_MAX;
			$minDvrAudience = PHP_INT_MAX;
			
			foreach ($records as $record) {
				$plays += $record->plays;
				$audience += $record->audience;
				$maxAudience = max($maxAudience, $record->audience);
				if ($showDvr) {
					$dvrAudience += $record->dvrAudience;
					$maxDvrAudience = max($maxDvrAudience, $record->dvrAudience);
					$minDvrAudience = min($minDvrAudience, $record->dvrAudience);
				}
				$minAudience = min($minAudience, $record->audience);
				$avgBitrate += $record->avgBitrate;
				$bufferTime += $record->bufferTime;
				$secondsViewed += $record->secondsViewed;
			}
			
			$nObj = count($records);
			$values[] = $plays;
			$values[] = round($audience / $nObj, 2);
			$values[] = $minAudience;
			$values[] = $maxAudience;
			if ($showDvr) {
				$values[] = round($dvrAudience / $nObj, 2);
				$values[] = $minDvrAudience;
				$values[] = $maxDvrAudience;
			}
			$values[] = round($avgBitrate / $nObj, 2);
			$values[] = round($bufferTime / $nObj, 2);
			$values[] = $secondsViewed;
			
			fwrite($fp, implode(LiveReportConstants::CELLS_SEPARATOR, $values) . "\n");
		}
		
		$objects = array();
	}
	
	protected function roundTime($time) {
		return floor($time / self::AGGREGATION_CHUNK) * self::AGGREGATION_CHUNK;
	}

}

