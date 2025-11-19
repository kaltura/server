<?php

require_once(__DIR__ . '/playsViewsMonitorBase.php');

define('ROOT_DIR', realpath(dirname(__FILE__) . '/../../../'));
define('DEFAULT_PROM_FILE', '/etc/node_exporter/data/monitor.prom');

class monitorPlaysViewsElastic extends playsViewsMonitorBase
{
	protected function getPlays(array $entryIds)
	{
		global $host, $port;
		$url = $host .'/kaltura_entry*/_search';

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_PORT, $port);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));

		$query = array(
				'query' => array(
					'terms' => array(
						'_id' => $entryIds
					)
				)
		);
		$post = json_encode($query);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

		$result = null;
		for ($retry = 0; $retry < 3; $retry++)
		{
			$response = curl_exec($ch);

			$curlError = curl_errno($ch);
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

			if ($curlError)
			{
				$errorMsg = 'Error while trying to connect to:' . $url . ' error=' . curl_error($ch);
			}
			else if ($httpCode != KCurlHeaderResponse::HTTP_STATUS_OK)
			{
				$errorMsg = 'Got invalid status code from elastic: ' . $httpCode;
			}
			else
			{
				$result = json_decode($response, true);
			}
		}

		if (!$result)
		{
			KalturaLog::log('Error: failed to get plays/views from elasticsearch, ' . $errorMsg);
			return null;
		}

		$elasticPlays = array();
		$indexes = $result['hits']['hits'];
		foreach ($indexes as $index)
		{
			$entryId = $index['_id'];
			$elasticPlays[$entryId] = isset($index['_source']['plays_7days']) ? $index['_source']['plays_7days'] : 0;
		}

		return $elasticPlays;
	}
}

// load cluster configurations
$config = kConf::get('elasticPopulateSettings', 'elastic_populate', array());
if (empty($config))
{
	$hostname = $_SERVER["HOSTNAME"] ?? gethostname();
	$configFile = ROOT_DIR . "/configurations/elastic/populate/$hostname.ini";

	if (!file_exists($configFile))
	{
		$message = "Configuration file [$configFile] not found";
		KalturaLog::err($message);
		writeFailure($message);
		exit(1);
	}

	$config = parse_ini_file($configFile);
	KalturaLog::debug("Configuration file [$configFile] loaded successfully - values: " . print_r($config, true));
}
$consumerId = $config['elasticCluster'] ?? null;	// the consumer id to use
$host = $config['elasticServer'] ?? null;			// the host endpoint
$port = $config['elasticPort'] ?? null;				// the port to connect

$baseFolder = isset($argv[1]) ? $argv[1] : null;

// connect to memcache
$memcache = getenv(MEMCACHE_VAR);
$memcInstances = array();
$memcacheArr = explode(',', $memcache);
$lastPlayedAt = 0;
foreach ($memcacheArr as $memcacheConfig)
{
	list($currMemcacheHost, $currMemcachePort) = explode(':', $memcacheConfig);
	$currMemc = new kInfraMemcacheCacheWrapper();
	$ret = $currMemc->init(array('host' => $currMemcacheHost, 'port' => $currMemcachePort));
	if (!$ret)
	{
		$message = "Failed to connect to cache host {$currMemcacheHost} port {$currMemcachePort}";
		KalturaLog::err($message);
		exit(1);
	}
	$currLastPlayedAt = $currMemc->get(MEMC_KEY_LAST_PLAYED_AT . "_$consumerId");
	if ($currLastPlayedAt && $currLastPlayedAt > $lastPlayedAt)
	{
		$lastPlayedAt = $currLastPlayedAt;
	}
}

if (!$lastPlayedAt)
{
	KalturaLog::err('Error: failed to get last played at from memcache');
	exit(1);
}

$lag = time() - $lastPlayedAt;
if ($lag > MAX_PLAYS_VIEWS_LAG)
{
	KalturaLog::err("Error: last played at is lagging $lag seconds");
}

$lastPlayedAt += 3600;

$fromTime = $lastPlayedAt - 7 * 86400;
$toTime = $lastPlayedAt;

$monitor = new monitorPlaysViewsElastic($fromTime, $toTime, $baseFolder);
$nonMatchingCount = $monitor->runMonitor();

$data = "playsviews_elastic_sync_diff_total $nonMatchingCount" . PHP_EOL;
createDirPath(DEFAULT_PROM_FILE);
file_put_contents(DEFAULT_PROM_FILE, $data, LOCK_EX);
