<?php

require_once(__DIR__ . '/../../apps/kaltura/lib/reports//kDruidBase.php');
require_once(__DIR__ . '/playsViewsCommon.php');
require_once(__DIR__ . '/../../../../../kava-utils/lib/Utils.php');

require_once (dirname(__FILE__).'/../bootstrap.php');

define('MAX_PLAYS_VIEWS_LAG', 129600);		// 36 hours

class playsViewsQueries extends kKavaBase
{
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
// parse the command line
if ($argc < 2)
{
	echo "Usage:\n\t" . basename(__file__) . " <id> [<plays views base>]\n";
	exit(1);
}

$id = $argv[1];
$memcache = getenv(MEMCACHE_VAR . "_$id");
list($memcacheHost, $memcachePort) = explode(':', $memcache);

$baseFolder = isset($argv[2]) ? $argv[2] : null;

// connect to memcache
$memc = new kInfraMemcacheCacheWrapper();
$ret = $memc->init(array('host'=>$memcacheHost, 'port'=>$memcachePort));
if (!$ret)
{
	Utils::writeLog("Failed to connect to cache host {$memcacheHost} port {$memcachePort}");
	exit(1);
}

// get last played at
$lastPlayedAt = $memc->get(MEMC_KEY_LAST_PLAYED_AT);
if (!$lastPlayedAt)
{
	Utils::writeLog('Error: failed to get last played at from memcache');
	exit(1);
}

$lag = time() - $lastPlayedAt;
if ($lag > MAX_PLAYS_VIEWS_LAG)
{
	Utils::writeLog("Error: last played at is lagging $lag seconds");
}

$lastPlayedAt += 3600;

$fromTime = $lastPlayedAt - 7 * 86400;
$toTime = $lastPlayedAt;

// get top plays from druid (using 2 topn queries to get accurate results)
$druidPlays = playsViewsQueries::getTopPlays($fromTime, $toTime);
if (!$druidPlays)
{
	Utils::writeLog('Error: failed to get top plays from druid (1)');
	exit(1);
}

$druidPlays = playsViewsQueries::getTopPlays($fromTime, $toTime, array_keys($druidPlays));
if (!$druidPlays)
{
	Utils::writeLog('Error: failed to get top plays from druid (2)');
	exit(1);
}

// get plays from memcache
$keys = array();
foreach (array_keys($druidPlays) as $entryId)
{
	$keys[] = MEMC_KEY_PREFIX . $entryId;
}

$memcValues = $memc->multiGet($keys);
if (!$memcValues)
{
	Utils::writeLog('Error: failed to get plays/views from memcache');
	exit(1);
}

$memcPlays = array();
foreach ($memcValues as $key => $value)
{
	$entryId = substr($key, strlen(MEMC_KEY_PREFIX));
	$fields = json_decode($value, true);
	$memcPlays[$entryId] = isset($fields['plays_7days']) ? $fields['plays_7days'] : 0;
}

// compare
$firstDiff = true;
foreach ($druidPlays as $entryId => $druidPlay)
{
	$entryId = normalizeEntryId($entryId);
	if (!$entryId)
	{
		continue;
	}

	$memcachePlay = $memcPlays[$entryId];
	if ($memcachePlay == $druidPlay)
	{
		continue;
	}

	Utils::writeLog("Error: non-matching plays for entry $entryId, druid=$druidPlay memcache=$memcachePlay");

	if (!$baseFolder)
	{
		continue;
	}

	if (!$firstDiff)
	{
		continue;
	}

	$firstDiff = false;

	// try to find the source of the discrepancy by comparing hour-by-hour
	$graph = playsViewsQueries::runPlaysViewsGraphQuery($fromTime, $toTime, kKavaBase::EVENT_TYPE_PLAY, array($entryId));
	foreach ($graph as $timestamp => $druidCount)
	{
		$timestamp = strtr(substr($timestamp, 0, 13), 'T', '-');
		list($year, $month, $day, $hour) = explode('-', $timestamp);
		$filePath = "$baseFolder/$year/$month/$day/playsviews-$year-$month-$day-$hour.gz";

		if (substr($baseFolder, 0, strlen('s3://')) == "s3://")
		{
			$savedResult = shell_exec("aws s3 cp $filePath - | zgrep $entryId");
		}
		else
		{
			$savedResult = shell_exec("zgrep $entryId $filePath");
		}

		if ($savedResult)
		{
			$savedResult = explode(',', trim($savedResult));
			$savedCount = $savedResult[1];
		}
		else
		{
			$savedCount = 0;
		}

		if ($savedCount != $druidCount)
		{
			Utils::writeLog("Error: found mismatch for entry $entryId in file $filePath, savedCount=$savedCount druidCount=$druidCount");
		}
	}
}
