<?php

set_time_limit(0);

ini_set("memory_limit","700M");

chdir(dirname(__FILE__));

define('ROOT_DIR', realpath(dirname(__FILE__) . '/../../../'));
require_once(ROOT_DIR . '/infra/bootstrap_base.php');
require_once(ROOT_DIR . '/infra/KAutoloader.php');
require_once(ROOT_DIR . '/infra/kConf.php');

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*"));
KAutoloader::setClassMapFilePath(kConf::get("cache_root_path") . '/deploy/' . basename(__FILE__) . '.cache');
KAutoloader::register();

date_default_timezone_set(kConf::get("date_default_timezone"));

error_reporting(E_ALL);

KalturaLog::setLogger(new KalturaStdoutLogger());

$dbConf = kConf::getDB();
DbManager::setConfig($dbConf);
DbManager::initialize();

$c = new Criteria();

if($argc > 1 && is_numeric($argv[1]))
	$c->add(CuePointPeer::UPDATED_AT, $argv[1], Criteria::GREATER_EQUAL);
if($argc > 2 && is_numeric($argv[2]))
	$c->add(CuePointPeer::PARTNER_ID, $argv[2], Criteria::EQUAL);
if($argc > 3 && is_numeric($argv[3]))
	$c->add(CuePointPeer::ID, $argv[3], Criteria::GREATER_EQUAL);

$c->addAscendingOrderByColumn(CuePointPeer::UPDATED_AT);
$c->addAscendingOrderByColumn(CuePointPeer::ID);
$c->setLimit(10000);

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
//$sphinxCon = DbManager::getSphinxConnection();

$entries = CuePointPeer::doSelect($c, $con);
$sphinx = new kSphinxSearchManager();
while(count($entries))
{
	foreach($entries as $entry)
	{
	    /* @var $entry CuePoint */
		KalturaLog::log('cue point id ' . $entry->getId() . ' updated at '. $entry->getUpdatedAt(null));
		
		try {
			$ret = $sphinx->saveToSphinx($entry, true);
		}
		catch(Exception $e){
			KalturaLog::err($e->getMessage());
			exit -1;
		}
	}
	
	$c->setOffset($c->getOffset() + count($entries));
	kMemoryManager::clearMemory();
	$entries = CuePointPeer::doSelect($c, $con);
}

KalturaLog::log('Done. Cureent time: ' . time());
