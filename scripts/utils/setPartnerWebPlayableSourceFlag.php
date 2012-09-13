<?php
error_reporting ( E_ALL );
ini_set("memory_limit","128M");

define ( 'KALTURA_ROOT_PATH', realpath ( dirname ( __FILE__ ) . '/../../' ) );
define('SF_ROOT_DIR',    realpath(dirname(__FILE__).'/../../alpha/'));
define('SF_APP',         'kaltura');
define('SF_ENVIRONMENT', 'batch');
define('SF_DEBUG',       true);

require_once (KALTURA_ROOT_PATH.DIRECTORY_SEPARATOR.'server_infra'.DIRECTORY_SEPARATOR.'kConf.php');

// Autoloader
require_once(KALTURA_ROOT_PATH . DIRECTORY_SEPARATOR . "infra".DIRECTORY_SEPARATOR."KAutoloader.php");
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "alpha", "lib", "*")); // needed for testmeDoc
KAutoloader::setClassMapFilePath(kConf::get("cache_root_path") . '/scripts/' . basename(__FILE__) . '.cache');
KAutoloader::register();

$dbConf = kConf::getDB ();
DbManager::setConfig ( $dbConf );
DbManager::initialize ();

if ( $argc == 3)
{	
	$flavor_param_id = $argv[1];
	$conversion_profile_id = $argv[2];
}
else
{
	die ( 'usage: php ' . $_SERVER ['SCRIPT_NAME'] . " [flavor_param_id] [conversion profile id]" . PHP_EOL );
}

$conversion_flavor = flavorParamsConversionProfilePeer::retrieveByFlavorParamsAndConversionProfile($flavor_param_id, $conversion_profile_id);
if(!$conversion_flavor)
{
        die('no such flavor param id and conversion profile id.'.PHP_EOL);
}

$conversion = conversionProfile2Peer::retrieveByPK($conversion_profile_id);
$input_tags_maps = $conversion->getInputTagsMap();
$input_tags_maps .= ",mbr";

$conversion->setInputTagsMap($input_tags_maps);

$conversion_flavor->setReadyBehavior(flavorParamsConversionProfile::READY_BEHAVIOR_OPTIONAL);
$conversion_flavor->save();

echo "Done.";