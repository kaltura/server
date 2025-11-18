<?php

require_once(__DIR__ . '/playsViewsMonitorBase.php');

define('DEFAULT_PROM_FILE', '/etc/node_exporter/data/monitor.prom');

class monitorPlaysViewsMemcacheNew extends playsViewsMonitorBase
{
	protected function getPlays(array $entryIds)
	{
		global $memc;

		$keys = array();
		foreach ($entryIds as $entryId)
		{
			$keys[] = MEMC_KEY_PREFIX . $entryId;
		}

		$memcValues = $memc->multiGet($keys);
		if (!$memcValues)
		{
			Utils::writeLog('Error: failed to get plays/views from memcache');
			return null;
		}

		$memcPlays = array();
		foreach ($memcValues as $key => $value)
		{
			$entryId = substr($key, strlen(MEMC_KEY_PREFIX));
			$fields = json_decode($value, true);
			$memcPlays[$entryId] = isset($fields['plays_7days']) ? $fields['plays_7days'] : 0;
		}

		return $memcPlays;
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

$monitor = new monitorPlaysViewsMemcacheNew($fromTime, $toTime, $baseFolder);
$nonMatchingCount = $monitor->runMonitor();

$data = "playsviews_memcache_sync_diff_total $nonMatchingCount" . PHP_EOL;
file_put_contents(DEFAULT_PROM_FILE, $data, LOCK_EX);


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
$nonMatchingCount = 0;
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

	// in case we find non-matching plays, check if the difference is only for the last hour
	// (it could be that entry wasn't exceeding plays threshold in the last hour and memcache wasn't updated)
	$entryPlays = playsViewsQueries::getTopPlays($fromTime - 3600, $toTime - 3600, array($entryId));
	if ($entryPlays[$entryId] == $memcachePlay)
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

$data = "playsviews_memcache_sync_diff_total $nonMatchingCount" . PHP_EOL;
file_put_contents(DEFAULT_PROM_FILE, $data, LOCK_EX);
