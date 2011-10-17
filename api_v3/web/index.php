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

KalturaLog::analytics(array(
	'session_start',
	'pid' => getmypid(),
	'time' => $start,
	'agent' => '"' . (isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : null) . '"',
	'host' => (isset($_SERVER["HOSTNAME"]) ? $_SERVER["HOSTNAME"] : null),
	'clientTag' => '"' . (isset($_REQUEST['clientTag']) ? $_REQUEST['clientTag'] : null) . '"',
));

KalturaLog::debug(">------------------------------------- api_v3 -------------------------------------");
KalturaLog::info("API-start ");

$controller = KalturaFrontController::getInstance();
$controller->run();

$end = microtime(true);
KalturaLog::info("API-end [".($end - $start)."]");
KalturaLog::debug("<------------------------------------- api_v3 -------------------------------------");

KalturaLog::analytics(array(
	'session_end',
	'duration' => ($end - $start),
	'partnerId' => kCurrentContext::$partner_id,
	'masterPartnerId' => kCurrentContext::$master_partner_id,
	'ks' => kCurrentContext::$ks,
	'isAdmin' => kCurrentContext::$is_admin_session,
	'kuserId' => '"' . str_replace('"', '\\"', (kCurrentContext::$uid ? kCurrentContext::$uid : kCurrentContext::$ks_uid)) . '"',
));

$cache->end();
