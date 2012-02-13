<?php

define('SF_ROOT_DIR',    realpath(dirname(__FILE__).'/../../alpha/'));
define('SF_APP',         'kaltura');
define('SF_ENVIRONMENT', 'batch');
define('SF_DEBUG',       true);

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');
require_once(SF_ROOT_DIR.'/../infra/bootstrap_base.php');
require_once(KALTURA_INFRA_PATH.DIRECTORY_SEPARATOR."KAutoloader.php");

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "api_v3", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "batch", "mediaInfoParser", "*"));
KAutoloader::setClassMapFilePath('./logs/classMap.cache');
KAutoloader::register();

error_reporting ( E_ALL );

KalturaLog::setLogger(new KalturaStdoutLogger());

$dbConf = kConf::getDB ();
DbManager::setConfig ( $dbConf );
DbManager::initialize ();

$partnerId = null;
if ( $argc == 3)
{	
	$partnerId = (int) $argv[1];
	$ignore = (int) $argv[2];
}

if(!$partnerId)
{
	die ( 'usage: php ' . $_SERVER ['SCRIPT_NAME'] . " [partner id] [ignore 0/1]" . PHP_EOL );
}

$partner = PartnerPeer::retrieveByPK($partnerId);
$partner->setIgnoreSeoLinks($ignore);
$partner->save();

echo "Done.";
