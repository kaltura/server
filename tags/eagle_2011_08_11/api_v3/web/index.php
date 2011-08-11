<?php
$start = microtime(true);
require_once(dirname(__FILE__).'/../../alpha/config/sfrootdir.php');

// check cache before loading anything
require_once(dirname(__FILE__)."/../lib/KalturaResponseCacher.php");
$cache = new KalturaResponseCacher();
$cache->checkOrStart();

require_once(dirname(__FILE__)."/../bootstrap.php");

ActKeyUtils::checkCurrent();
KalturaLog::setContext("API");

KalturaLog::debug(">------------------------------------- api_v3 -------------------------------------");
KalturaLog::info("API-start ");

$controller = KalturaFrontController::getInstance();
$controller->run();

$end = microtime(true);
KalturaLog::info("API-end [".($end - $start)."]");
KalturaLog::debug("<------------------------------------- api_v3 -------------------------------------");

$cache->end();
