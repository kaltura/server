<?php

require_once(__DIR__ . '/../bootstrap.php');
require_once(__DIR__ . '/../../../../../kava-utils/lib/StreamQueue.php');
require_once(__DIR__ . '/playsViewsCommon.php');

define('QUERY_CACHE_KEY_PREFIX', 'QCI-entry:id=');
define('QUERY_CACHE_KEY_EXPIRY', 90000);
define('MEMC_KEY_PREFIX', 'plays_views_');
define('MEMC_KEY_LAST_PLAYED_AT', 'plays_views_last_played_at');


class playsViewsMemcacheConsumer extends BaseConsumer
{
	protected function processMessage($message)
	{
		global $memc, $queryCacheMemc, $maxLastPlayedAt;
		$data = json_decode($message, true);
		$entryId = $data['entry_id'];
		unset($data['entry_id']);
		if (isset($data['partner_id']))
		{
			unset($data['partner_id']);
		}
		$key = MEMC_KEY_PREFIX . $entryId;

		if (isset($data['last_played_at']))
		{
			$lastPlayedAt = $data['last_played_at'];
			if ($lastPlayedAt > $maxLastPlayedAt)
			{
				$memc->set(MEMC_KEY_LAST_PLAYED_AT, $lastPlayedAt);
				$maxLastPlayedAt = $lastPlayedAt;
			}
		}

		if (!$memc->set($key, json_encode($data)))
		{
			Utils::writeLog("Error: Failed to set key [$key] in memcache");
			return;
		}

		if ($queryCacheMemc)
		{
			$key = QUERY_CACHE_KEY_PREFIX . $entryId;
			foreach ($queryCacheMemc as $qc)
			{
				if (!$qc->set($key, strval(time()), QUERY_CACHE_KEY_EXPIRY))
				{
					Utils::writeLog("Error: Failed to set key [$key] in memcache");
					return;
				}
			}
		}
	}
}

// parse the command line
if ($argc < 2)
{
	echo "Usage:\n\t" . basename(__file__) . " <memcache host>:<memcache port> [<qc1 memcache host>:<qc1 memcache port>,<qc2 memcache host>:<qc2 memcache port>]\n";
	echo "For example, php " . basename(__file__) . " localhost:11211\n";
	exit(1);
}

$memcache = $argv[1];
$queryCacheMemcaches = isset($argv[2]) ? $argv[2] : null;

try
{
	$topicsPath = kConf::get(CONF_TOPICS_PATH);
}
catch (Exception $ex)
{
	Utils::errorLog('Missing topics path config');
	exit(1);
}

Utils::writeLog('Info: started, pid=' . getmypid());

// connect to memcache
list($memcacheHost, $memcachePort) = explode(':', $memcache);
$memc = new kInfraMemcacheCacheWrapper();
$ret = $memc->init(array('host'=>$memcacheHost, 'port'=>$memcachePort));
if (!$ret)
{
	Utils::writeLog("Failed to connect to cache host {$memcacheHost} port {$memcachePort}");
	exit(1);
}

// connect to query cache memcaches
$queryCacheMemc = array();
if ($queryCacheMemcaches)
{
	$queryCacheMemcachesArr = explode(',', $queryCacheMemcaches);
	foreach ($queryCacheMemcachesArr as $queryCacheMemcache)
	{
		list($currMemcacheHost, $currMemcachePort) = explode(':', $queryCacheMemcache);
		$currQueryCacheMemc = new kInfraMemcacheCacheWrapper();
		$ret = $currQueryCacheMemc->init(array('host'=>$currMemcacheHost, 'port'=>$currMemcachePort));
		if (!$ret)
		{
			Utils::writeLog("Failed to connect to cache host {$currMemcacheHost} port {$currMemcachePort}");
			exit(1);
		}
		$queryCacheMemc[] = $currQueryCacheMemc;
	}
}

//When loading the server bootstrap it disables the stream wrappers, we need to enable it back for the s3Wrapper to be able to fetch files form s3
if (!array_intersect(array('https', 'http'), stream_get_wrappers()))
{
	stream_wrapper_restore('http');
	stream_wrapper_restore('https');
}

$consumer = new playsViewsMemcacheConsumer($topicsPath, PLAYSVIEWS_TOPIC, str_replace(':', '_', $memcache));
$maxLastPlayedAt = 0;
$consumer->consumeQueue();

Utils::writeLog('Info: done');