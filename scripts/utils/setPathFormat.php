<?php

ini_set("memory_limit","256M");

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

$dbConf = kConf::getDB ();
DbManager::setConfig ( $dbConf );
DbManager::initialize ();

if ( $argc == 3)
{	
	$partner_id = $argv[1];
	$path_format = $argv[2];
	
}
else
{
	die ( 'usage: php ' . $_SERVER ['SCRIPT_NAME'] . " [partner id] [date format]" . PHP_EOL );
}

$storages = StorageProfilePeer::retrieveExternalByPartnerId($partner_id);

if(!$storages)
{
        die('no such partner.'.PHP_EOL);
}

foreach ($storages as $storage)
{
	$storage->setPathFormat($path_format);
	$storage->save();
}
echo "Done.";
