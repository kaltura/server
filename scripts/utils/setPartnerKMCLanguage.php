<?php
error_reporting ( E_ALL );
ini_set("memory_limit","512M");

define ( 'KALTURA_ROOT_PATH', realpath ( dirname ( __FILE__ ) . '/../../' ) );
define('SF_ROOT_DIR',    realpath(dirname(__FILE__).'/../../alpha/'));
define('SF_APP',         'kaltura');
define('SF_ENVIRONMENT', 'batch');
define('SF_DEBUG',       true);

require_once (KALTURA_ROOT_PATH.DIRECTORY_SEPARATOR.'server_infra'.DIRECTORY_SEPARATOR.'kConf.php');

// Autoloader
require_once(KALTURA_ROOT_PATH . DIRECTORY_SEPARATOR . "infra".DIRECTORY_SEPARATOR."KAutoloader.php");
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "alpha", "plugins", "*")); // needed for testmeDoc
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*"));
KAutoloader::setClassMapFilePath(kConf::get("cache_root_path") . '/scripts/' . basename(__FILE__) . '.cache');
KAutoloader::register();

$dbConf = kConf::getDB ();
DbManager::setConfig ( $dbConf );
DbManager::initialize ();

if ( $argc == 3)
{	
	$partner_id = $argv[1];
	$language = $argv[2];
}
else
{
	die ( 'usage: php ' . $_SERVER ['SCRIPT_NAME'] . " [partner id] [language]" . PHP_EOL );
}

$partner = PartnerPeer::retrieveByPK($partner_id);
if(!$partner)
{
        die('no such partner.'.PHP_EOL);
}

$partner->setKMCLanguage($language);
$partner->save();

echo "Done.";
