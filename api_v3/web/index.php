<?php

header('Access-Control-Expose-Headers: Server, Content-Length, Content-Range, Date, X-Kaltura, X-Kaltura-Session, X-Me');

if($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
{
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Range, Cache-Control');
	header('Access-Control-Allow-Methods: POST, GET, HEAD, OPTIONS');
	header('Access-Control-Max-Age: 86400');
	exit;
}

$start = microtime(true);
// check cache before loading anything
require_once(dirname(__FILE__)."/../lib/KalturaResponseCacher.php");
$cache = new KalturaResponseCacher();
$cache->checkOrStart();

require_once(dirname(__FILE__)."/../bootstrap.php");

// Database
DbManager::setConfig(kConf::getDB());
DbManager::initialize();

ActKeyUtils::checkCurrent();

KalturaLog::debug(">------------------------------------- api_v3 -------------------------------------");
KalturaLog::info("API-start pid:".getmypid());

$controller = KalturaFrontController::getInstance();
$result = $controller->run();

$dataSourceAccessCounters = KalturaMonitorClient::prettyPrintCounters();
KalturaLog::info('Session data source counters ' . $dataSourceAccessCounters);

const SESSION_COUNTERS_SECRET_HEADER = 'HTTP_SESSION_COUNTERS_SECRET';
if(isset ($_SERVER[SESSION_COUNTERS_SECRET_HEADER]))
{
	addSessionCounters($_SERVER[SESSION_COUNTERS_SECRET_HEADER],$dataSourceAccessCounters);
}

$end = microtime(true);
KalturaLog::info("API-end [".($end - $start)."]");
KalturaLog::debug("<------------------------------------- api_v3 -------------------------------------");

$cache->end($result);

function addSessionCounters($sessionCountersSecretHeader, $dataSourceAccessCounters)
{
	$sessionCountersShardSecret = kConf::get('SESSION_COUNTERS_SECRET','local',null);
	list ($clientRequestTime,$hash) = explode(',', $sessionCountersSecretHeader);
	if($sessionCountersShardSecret && $clientRequestTime && $hash)
	{
		if(validateSessionCountersSharedSecret($sessionCountersShardSecret,$clientRequestTime,$hash))
		{
			header('X-Kaltura-session-counters: ' . base64_encode(json_encode($dataSourceAccessCounters)) );
		}
	}
}

function validateSessionCountersSharedSecret($sessionCountersShardSecret,$clientRequestTime,$hash)
{
	if (abs(time() - $clientRequestTime) > 300 )
	{
		return false;
	}

	return $hash === md5("$clientRequestTime,$sessionCountersShardSecret");
}