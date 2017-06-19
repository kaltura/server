<?php
if($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
{
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Range, Cache-Control');
	header('Access-Control-Allow-Methods: POST, GET, HEAD, OPTIONS');
	header('Access-Control-Expose-Headers: Server, Content-Length, Content-Range, Date, X-Kaltura, X-Kaltura-Session, X-Me');
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

$end = microtime(true);
KalturaLog::info("API-end [".($end - $start)."]");
KalturaLog::debug("<------------------------------------- api_v3 -------------------------------------");

$cache->end($result);
