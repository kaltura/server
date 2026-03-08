<?php

require_once(__DIR__ . '/../../apps/kaltura/lib/reports/kDruidBase.php');
require_once(__DIR__ . '/playsViewsCommon.php');
require_once(__DIR__ . '/../../../../../kava-utils/lib/Utils.php');

require_once (dirname(__FILE__).'/../bootstrap.php');

define('MAX_PLAYS_VIEWS_LAG', 129600);		// 36 hours

abstract class PlaysViewsMonitorBase extends kKavaBase
{
	protected $fromTime;
	protected $toTime;
	protected $baseFolder;

	public function __construct($fromTime, $toTime, $baseFolder = null)
	{
		$this->fromTime = $fromTime;
		$this->toTime = $toTime;
		$this->baseFolder = $baseFolder;
	}

	abstract protected function getPlays(array $entryIds);

	public function runMonitor()
	{
		$druidPlays = self::getTopPlays($this->fromTime, $this->toTime);
		if (!$druidPlays) {
			Utils::writeLog('Error: failed to get top plays from druid');
			return -1;
		}

		$druidPlays = self::getTopPlays($this->fromTime, $this->toTime, array_keys($druidPlays));
		if (!$druidPlays) {
			Utils::writeLog('Error: failed to get top plays from druid (2)');
			return -1;
		}

		$otherPlays = $this->getPlays(array_keys($druidPlays));
		if (!$otherPlays) {
			Utils::writeLog('Error: failed to get plays from source');
			return -1;
		}

		$nonMatchingCount = 0;
		$firstDiff = true;
		foreach ($druidPlays as $entryId => $druidPlay) {
			$entryId = normalizeEntryId($entryId);
			if (!$entryId) {
				continue;
			}

			$otherPlay = $otherPlays[$entryId] ?? 0;
			if ($otherPlay == $druidPlay) {
				continue;
			}

			// in case we find non-matching plays, check if the difference is only for the last hour
			// (it could be that entry wasn't exceeding plays threshold in the last hour and memcache wasn't updated)
			$entryPlays = self::getTopPlays($this->fromTime - 3600, $this->toTime - 3600, array($entryId));
			if ($entryPlays[$entryId] == $otherPlay) {
				continue;
			}
			Utils::writeLog("Error: non-matching plays for entry $entryId, druid=$druidPlay other=$otherPlay");
			$nonMatchingCount++;

			if (!$this->baseFolder) {
				continue;
			}

			if (!$firstDiff) {
				continue;
			}

			$firstDiff = false;

			// try to find the source of the discrepancy by comparing hour-by-hour
			$graph = self::runPlaysViewsGraphQuery($this->fromTime, $this->toTime, kKavaBase::EVENT_TYPE_PLAY, array($entryId));
			foreach ($graph as $timestamp => $druidCount) {
				$timestamp = strtr(substr($timestamp, 0, 13), 'T', '-');
				list($year, $month, $day, $hour) = explode('-', $timestamp);
				$filePath = $this->baseFolder . "/$year/$month/$day/playsviews-$year-$month-$day-$hour.gz";

				if (substr($this->baseFolder, 0, strlen('s3://')) == "s3://") {
					$savedResult = shell_exec("aws s3 cp $filePath - | zgrep $entryId");
				} else {
					$savedResult = shell_exec("zgrep $entryId $filePath");
				}

				if ($savedResult) {
					$savedResult = explode(',', trim($savedResult));
					$savedCount = $savedResult[1];
				} else {
					$savedCount = 0;
				}

				if ($savedCount != $druidCount) {
					Utils::writeLog("Error: found mismatch for entry $entryId in file $filePath, savedCount=$savedCount druidCount=$druidCount");
				}
			}
		}

		return $nonMatchingCount;
	}

	public static function getTopPlays($fromTime, $toTime, $entryIds = null, $threshold = 10)
	{
		$response = self::runPlaysViewsQuery($fromTime, $toTime, self::EVENT_TYPE_PLAY, $threshold, $entryIds);
		if (!isset($response[0][self::DRUID_RESULT]))
		{
			return false;
		}

		$result = array();
		foreach ($response[0][self::DRUID_RESULT] as $cur)
		{
			$entryId = $cur[self::DIMENSION_ENTRY_ID];
			$count = $cur[self::METRIC_COUNT];

			$result[$entryId] = $count;
		}

		return $result;
	}

	protected static function runPlaysViewsQuery($fromTime, $toTime, $eventType, $threshold = 1000000, $entryIds = null)
	{

		$query = array(
			self::DRUID_QUERY_TYPE => self::DRUID_TOPN,
			self::DRUID_DATASOURCE => self::DATASOURCE_HISTORICAL,
			self::DRUID_INTERVALS => self::getIntervals($fromTime, $toTime),
			self::DRUID_GRANULARITY => self::DRUID_GRANULARITY_ALL,
			self::DRUID_CONTEXT => self::getDruidContext(),
			self::DRUID_FILTER => self::getDruidFilter($eventType, $entryIds),
			self::DRUID_DIMENSION => self::DIMENSION_ENTRY_ID,
			self::DRUID_AGGR => array(self::getLongSumAggregator(self::METRIC_COUNT, self::METRIC_COUNT)),
			self::DRUID_METRIC => self::METRIC_COUNT,
			self::DRUID_THRESHOLD => $threshold
		);

		try
		{
			return self::runQuery($query);
		}
		catch (Exception $ex)
		{
			return false;
		}
	}

	public static function runPlaysViewsGraphQuery($fromTime, $toTime, $eventType, $entryIds = null)
	{
		$query = array(
			self::DRUID_QUERY_TYPE => self::DRUID_TIMESERIES,
			self::DRUID_DATASOURCE => self::DATASOURCE_HISTORICAL,
			self::DRUID_INTERVALS => self::getIntervals($fromTime, $toTime),
			self::DRUID_GRANULARITY => self::getGranularityPeriod('PT1H'),
			self::DRUID_CONTEXT => self::getDruidContext(),
			self::DRUID_FILTER => self::getDruidFilter($eventType, $entryIds),
			self::DRUID_AGGR => array(self::getLongSumAggregator(self::METRIC_COUNT, self::METRIC_COUNT)),
		);

		$graph = self::runQuery($query);
		$result = array();
		if ($graph)
		{
			foreach ($graph as $curItem) {
				$timestamp = $curItem[self::DRUID_TIMESTAMP];
				$count = $curItem[self::DRUID_RESULT][self::METRIC_COUNT];
				$result[$timestamp] = $count;
			}
		}
		return $result;
	}

	protected static function getDruidContext()
	{
		return array(

			self::DRUID_COMMENT => gethostname() . '[playsview]'
		);
	}

	protected static function getDruidFilter($eventType, $entryIds)
	{
		$filter = self::getSelectorFilter(self::DIMENSION_EVENT_TYPE, $eventType);
		if ($entryIds)
		{
			$filter = self::getAndFilter(array($filter,
				self::getInFilter(self::DIMENSION_ENTRY_ID, $entryIds)));
		}

		return $filter;
	}
}

