<?php
ini_set("memory_limit","512M");

define('SF_ROOT_DIR',    realpath(dirname(__FILE__).'/../../alpha/'));
define('SF_APP',         'kaltura');
define('SF_ENVIRONMENT', 'batch');
define('SF_DEBUG',       true);

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');
require_once(SF_ROOT_DIR.'/../infra/bootstrap_base.php');
require_once(KALTURA_INFRA_PATH.DIRECTORY_SEPARATOR."KAutoloader.php");

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "batch", "mediaInfoParser", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*"));
KAutoloader::setClassMapFilePath('./logs/classMap.cache');
KAutoloader::register();

error_reporting ( E_ALL );

$dbConf = kConf::getDB ();
DbManager::setConfig ( $dbConf );
DbManager::initialize ();

if ($argc !== 2)
{
	die('pleas provide entry id as input' . PHP_EOL . 
		'to run script: ' . basename(__FILE__) . ' X' . PHP_EOL . 
		'whereas X is entry id' . PHP_EOL);
}
$entryId = @$argv[1];

$dbConf = kConf::getDB();
DbManager::setConfig($dbConf);
DbManager::initialize();

$sphinx = new kSphinxSearchManager();

entryPeer::setUseCriteriaFilter(false);
$entry = entryPeer::retrieveByPK($entryId);
if ($entry)
{
	$sphinx->saveToSphinx($entry, false, true);
	echo $entry->getId() . " Saved\n";
}
echo "Done\n";
