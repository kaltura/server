<?php

if($argc != 2)
{
	echo "Arguments missing.\n\n";
	echo "Usage: php updatePartnerEntries2Sphinx.php {partner id}\n";
	exit;
} 
$partnerId = $argv[1];



ini_set("memory_limit","1024M");

define('ROOT_DIR', realpath(dirname(__FILE__) . '/../../'));
require_once(ROOT_DIR . '/infra/bootstrap_base.php');
require_once(ROOT_DIR . '/infra/KAutoloader.php');

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "metadata", "*"));
KAutoloader::setClassMapFilePath('../cache/classMap.cache');
KAutoloader::register();

error_reporting(E_ALL);
KalturaLog::setLogger(new KalturaStdoutLogger());

$dbConf = kConf::getDB();
DbManager::setConfig($dbConf);
DbManager::initialize();


$c = new Criteria();
$c->add(entryPeer::PARTNER_ID, $partnerId);
entryPeer::setUseCriteriaFilter(false);
$entries = entryPeer::doSelect($c);
foreach($entries as $entry)
{
	usleep(100);
	$entry->saveToSphinx(false, true);
	echo $entry->getId() . "Saved\n";
}
echo "Done\n";
