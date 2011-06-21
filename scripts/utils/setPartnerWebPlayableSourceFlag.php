<?php
error_reporting ( E_ALL );
ini_set("memory_limit","128M");

define('SF_ROOT_DIR',    realpath(dirname(__FILE__).'/../../alpha/'));
define('SF_APP',         'kaltura');
define('SF_ENVIRONMENT', 'batch');
define('SF_DEBUG',       true);

require_once(SF_ROOT_DIR.'/../infra/bootstrap_base.php');

require_once(KALTURA_ROOT_PATH.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."infra".DIRECTORY_SEPARATOR."bootstrap_base.php");
require_once (KALTURA_ROOT_PATH.DIRECTORY_SEPARATOR.'alpha'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'kConf.php');

// Autoloader
require_once(KALTURA_INFRA_PATH.DIRECTORY_SEPARATOR."KAutoloader.php");
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "alpha", "lib", "*")); // needed for testmeDoc
KAutoloader::setClassMapFilePath(kConf::get("cache_root_path") . '/general/classMap.cache');
KAutoloader::register();

$dbConf = kConf::getDB ();
DbManager::setConfig ( $dbConf );
DbManager::initialize ();
//0 1571571
//0 650722
if ( $argc == 3)
{	
	$flavor_param_id = $argv[1];
	$conversion_profile_id = $argv[2];
}
else
{
	die ( 'usage: php ' . $_SERVER ['SCRIPT_NAME'] . " [flavor_param_id] [conversion profile id]" . PHP_EOL );
}

$conversion = flavorParamsConversionProfilePeer::retrieveByFlavorParamsAndConversionProfile($flavor_param_id, $conversion_profile_id);
if(!$conversion)
{
        die('no such flavor param id and conversion profile id.'.PHP_EOL);
}

$conversion->setReadyBehavior(flavorParamsConversionProfile::READY_BEHAVIOR_OPTIONAL);
$conversion->save();

echo "Done.";