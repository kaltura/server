<?php

require_once(__DIR__ . '/playsViewsMonitorBase.php');

define('DEFAULT_PROM_FILE', '/etc/node_exporter/data/monitor.prom');

class monitorPlaysViewsMemcache extends playsViewsMonitorBase
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

$monitor = new monitorPlaysViewsMemcache($fromTime, $toTime, $baseFolder);
$nonMatchingCount = $monitor->runMonitor();

$data = "playsviews_memcache_sync_diff_total $nonMatchingCount" . PHP_EOL;
createDirPath(DEFAULT_PROM_FILE);
file_put_contents(DEFAULT_PROM_FILE, $data, LOCK_EX);
