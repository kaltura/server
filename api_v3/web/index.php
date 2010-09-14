<?php
$start = microtime(true);
require_once(dirname(__FILE__).'/../../alpha/config/sfrootdir.php');
ActKeyUtils::checkCurrent();

// check cache before loading anything
require_once("../lib/KalturaResponseCacher.php");
$cache = new KalturaResponseCacher();
$cache->checkOrStart();

require_once("../bootstrap.php");

KalturaLog::setContext("API");

KalturaLog::debug(">------------------------------------- api_v3 -------------------------------------");
KalturaLog::info("API-start ");

$controller = KalturaFrontController::getInstance();
$controller->run();

$end = microtime(true);
KalturaLog::info("API-end [".($end - $start)."]");
KalturaLog::debug("<------------------------------------- api_v3 -------------------------------------");

$cache->end();
