<?php
 
/**
 * This class is a complex implementation for 1min aggregation of location report
 */
class LiveReportLocation1MinEngine extends LiveReportEngine {
	
	const TIME_CHUNK = 600;
	const AGGREGATION_CHUNK = LiveReportConstants::SECONDS_60;
	
	protected $formatter;
	
	public function LiveReportLocation1MinEngine(LiveReportDateFormatter $formatter) {
		$this->formatter = $formatter;
	}
	
	public function run($fp, array $args = array()) {
		$this->checkParams($args, array(LiveReportConstants::TIME_REFERENCE_PARAM, LiveReportConstants::ENTRY_IDS));
		$toTime = $args[LiveReportConstants::TIME_REFERENCE_PARAM];
		$fromTime = $args[LiveReportConstants::TIME_REFERENCE_PARAM] - LiveReportConstants::SECONDS_36_HOURS;
		
		$this->printHeaders($fp);
		
		$objs = array();
		$lastTimeGroup = null;
		
		
		for($curTime = $fromTime; $curTime < $toTime; $curTime = $curTime + self::TIME_CHUNK) {
			$curTo = min($toTime, $curTime + self::TIME_CHUNK);
			$results = $this->getRecords($curTime, $curTo, $args[LiveReportConstants::ENTRY_IDS]);
			if($results->totalCount == 0)
				continue;
			
			foreach($results->objects as $result) {
				
				$groupTime = $this->roundTime($result->timestamp);
				
				if(is_null($lastTimeGroup))
					$lastTimeGroup = $groupTime;
				
				if($lastTimeGroup < $groupTime) {
					$this->printRows($fp, $objs, $lastTimeGroup);
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
		
		$this->printRows($fp, $objs, $lastTimeGroup);
	}
	
	// ASUMPTION - we have a single entry ID (that's a constraint of the cassandra)
	// and the results are ordered from the oldest to the newest
	protected function getRecords($fromTime, $toTime, $entryId) {
		
		$reportType = KalturaLiveReportType::ENTRY_GEO_TIME_LINE;
		$filter = new KalturaLiveReportInputFilter();
		$filter->toTime = $toTime;
		$filter->fromTime = $fromTime;
		$filter->entryIds = $entryId;
		
		return KBatchBase::$kClient->liveReports->getReport($reportType, $filter, null);
	}
	
	protected function printHeaders($fp) {
		$values = array();
		$values[] = "Date";
		$values[] = "Country";
		$values[] = "City";
		$values[] = "latitude";
		$values[] = "longitude";
		$values[] = "Plays";
		$values[] = "Average bitrate";
		$values[] = "Buffer time";
		$values[] = "Seconds viewed";
		
		fwrite($fp, implode(LiveReportConstants::CELLS_SEPARATOR, $values) . "\n");
	}
	
	protected function printRows($fp, &$objects, $lastTimeGroup) {
		
		foreach ($objects as $records) {

			$firstRecord = $records[0];
			
			$values = array();
			$values[] = $this->formatter->format($lastTimeGroup);
			$values[] = $firstRecord->country->name;
			$values[] = $firstRecord->city->name;
			$values[] = $firstRecord->city->latitude;
			$values[] = $firstRecord->city->longitude;
			
			$plays = $avgBitrate = $bufferTime = $secondsViewed = 0;
			foreach ($records as $record) {
				$plays += $record->plays;
				$avgBitrate += $record->avgBitrate;
				$bufferTime += $record->bufferTime;
				$secondsViewed += $record->secondsViewed;
			}
			
			$nObj = count($records);
			$values[] = round($plays / $nObj, 2);
			$values[] = round($avgBitrate / $nObj, 2);
			$values[] = round($bufferTime / $nObj, 2);
			$values[] = round($secondsViewed / $nObj, 2);
			
			fwrite($fp, implode(LiveReportConstants::CELLS_SEPARATOR, $values) . "\n");
		}
		
		$objects = array();
	}
	
	protected function roundTime($time) {
		return floor($time / self::AGGREGATION_CHUNK) * self::AGGREGATION_CHUNK;
	}

}

