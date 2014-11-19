<?php
 
/**
 * This class is a complex implementation for 1min aggregation of location report
 */
class LiveReportLocation1MinEngine extends LiveReportEngine {
	
	const TIME_CHUNK = 3600;
	const AGGREGATION_CHUNK = LiveReportConstants::SECONDS_60;
	
	public function run($fp, array $args = array()) {
		$this->checkParams($args, array(LiveReportConstants::TIME_REFERENCE_PARAM, LiveReportConstants::ENTRY_IDS));
		$endTime = $args[LiveReportConstants::TIME_REFERENCE_PARAM];
		$timeRange = LiveReportConstants::SECONDS_36_HOURS;
		
		$this->printHeaders($fp);
		
		$objs = array();
		$lastTimeGroup = null;
		for($curTime = $endTime; $curTime >= $endTime - $timeRange; $curTime = $curTime - self::TIME_CHUNK) {
			$results = $this->getRecords($curTime - self::TIME_CHUNK, $curTime, $args[LiveReportConstants::ENTRY_IDS]);
			
			foreach($results->objects as $result) {
				$groupTime = $this->roundTime($result->timestamp);
				
				if(is_null($lastTimeGroup))
					$lastTimeGroup = $groupTime;
					
				if($lastTimeGroup > $groupTime + self::AGGREGATION_CHUNK) {
					$this->printRows($objs, $lastTimeGroup);
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
		
		$this->printRows($objs, $lastTimeGroup);
	}
	
	// ASUMPTION - we have a single entry ID (that's a constraint of the cassandra)
	// and the results are ordered from the newest to the oldest
	protected function getRecords($fromTime, $toTime, $entryId) {
		
		$reportType = KalturaLiveReportType::ENTRY_GEO_TIME_LINE;
		$filter = new KalturaLiveReportInputFilter();
		$filter->toTime = $toTime;
		$filter->fromTime = $fromTime;
		$filter->entryIds = $entryId;
		
		return EngineUtils::getReport($reportType, $filter, null);
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
		
		fwrite($fp, implode(LiveReportConstants::CELLS_SEPARATOR, $values));
	}
	
	protected function printRows($fp, &$objects, $lastTimeGroup) {
		foreach ($objects as $records) {

			$firstRecord = $records[0];
			
			$values = array();
			$values[] = date(LiveReportConstants::DATE_FORMAT, $lastTimeGroup);
			$values[] = $firstRecord->country->name;
			$values[] = $firstRecord->city->name;
			$values[] = $firstRecord->city->latitude;
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
			$values[] = $plays / $nObj;
			$values[] = $avgBitrate / $nObj;
			$values[] = $bufferTime / $nObj;
			$values[] = $secondsViewed / $nObj;
			
			fwrite($fp, implode(LiveReportConstants::CELLS_SEPARATOR, $values));
		}
		
		$objects = array();
	}
	
	protected function roundTime($time) {
		return floor($time / self::AGGREGATION_CHUNK) * self::AGGREGATION_CHUNK;
	}

}

