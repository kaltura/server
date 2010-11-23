<?php

set_time_limit(0);

ini_set("memory_limit","700M");

chdir(dirname(__FILE__));

define('ROOT_DIR', realpath(dirname(__FILE__) . '/../../../'));
require_once(ROOT_DIR . '/infra/bootstrap_base.php');
require_once(ROOT_DIR . '/infra/KAutoloader.php');

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*"));
KAutoloader::setClassMapFilePath('../../cache/classMap.cache');
KAutoloader::register();

error_reporting(E_ALL);

KalturaLog::setLogger(new KalturaStdoutLogger());

$dbConf = kConf::getDB();
DbManager::setConfig($dbConf);
DbManager::initialize();

$c = new Criteria();

if($argc > 1 && is_numeric($argv[1]))
	$c->add(entryPeer::INT_ID, $argv[1], Criteria::GREATER_EQUAL);

$c->addAscendingOrderByColumn(entryPeer::INT_ID);
$c->setLimit(10000);

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
//$sphinxCon = DbManager::getSphinxConnection();

$entries = entryPeer::doSelect($c, $con);
$sphinx = new kSphinxSearchManager();
while(count($entries))
{
	foreach($entries as $entry)
	{
		KalturaLog::log('entry id ' . $entry->getId() . ' int id[' . $entry->getIntId() . '] crc id[' . $sphinx->getSphinxId($entry) . ']');
		
		try {
			$ret = $sphinx->saveToSphinx($entry, true);
		}
		catch(Exception $e){
			KalturaLog::err($e->getMessage());
			exit -1;
		}
	}
	
	$c->setOffset($c->getOffset() + count($entries));
	entryPeer::clearInstancePool();
	MetadataPeer::clearInstancePool();
	MetadataProfilePeer::clearInstancePool();
	MetadataProfileFieldPeer::clearInstancePool();
	$entries = entryPeer::doSelect($c, $con);
}

KalturaLog::log('Done');
