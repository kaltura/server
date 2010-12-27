<?php

if($argc <= 1)
{
	echo "Config file not specified\n";
	echo "Usage: php populateFromLog.php {path to config php file}\n";
	echo "For example\n";
	echo "	php populateFromLog.php /opt/kaltua/app/scripts/sphinx/configs/pa-sphinx.php\n";
	exit;
}

$configFile = $argv[1];
if(!file_exists($configFile))
{
	echo "Config file [$configFile] doesn't exist\n";
	echo "Usage: php populateFromLog.php {path to config php file}\n";
	echo "For example\n";
	echo "	php populateFromLog.php /opt/kaltua/app/scripts/sphinx/configs/pa-sphinx.php\n";
	exit;
}

set_time_limit(0);
ini_set("memory_limit","700M");
chdir(dirname(__FILE__));

$sphinxServer = null;
require_once $configFile;

define('ROOT_DIR', realpath(dirname(__FILE__) . '/../../../'));
require_once(ROOT_DIR . '/infra/bootstrap_base.php');
require_once(ROOT_DIR . '/infra/KAutoloader.php');

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*"));
KAutoloader::setClassMapFilePath(kConf::get("cache_root_path") . '/sphinx/classMap.cache');
KAutoloader::register();

error_reporting(E_ALL);
KalturaLog::setLogger(new KalturaStdoutLogger());

$dbConf = kConf::getDB();
DbManager::setConfig($dbConf);
DbManager::initialize();

$serverLastLogs = SphinxLogServerPeer::retrieveByServer($sphinxServer);
$lastLogs = array();
foreach($serverLastLogs as $serverLastLog)
	$lastLogs[$serverLastLog->getDc()] = $serverLastLog;

$sphinxLogs = SphinxLogPeer::retrieveByLastId($lastLogs);	
while(true)
{
	while(!count($sphinxLogs))
	{
		sleep(1);
		$sphinxLogs = SphinxLogPeer::retrieveByLastId($lastLogs);
	}
	
	$sphinxCon = null;
	try
	{
		$sphinxCon = DbManager::createSphinxConnection($sphinxServer);
	}
	catch(Exception $e)
	{
		KalturaLog::err($e->getMessage());
		sleep(5);
		continue;
	}
	
	foreach($sphinxLogs as $sphinxLog)
	{
		$dc = $sphinxLog->getDc();
		KalturaLog::log('Sphinx log id ' . $sphinxLog->getId() . " dc [$dc] Memory: [" . memory_get_usage() . "]");
		
		if(isset($lastLogs[$dc]))
		{
			$serverLastLog = $lastLogs[$dc];
			
			if($serverLastLog->getLastLogId() >= $sphinxLog->getId())
			{
				KalturaLog::debug('Last log id [' . $serverLastLog->getLastLogId() . "] dc [$dc] is larger than id [" . $sphinxLog->getId() . "]");
				continue;
			}
		}
		else
		{
			$serverLastLog = new SphinxLogServer();
			$serverLastLog->setServer($sphinxServer);
			$serverLastLog->setDc($dc);
			
			$lastLogs[$dc] = $serverLastLog;
		}
		
		try{
			$sql = $sphinxLog->getSql();
			$affected = $sphinxCon->exec($sql);
			
			if(!$affected)
			{
				$errorInfo = $sphinxCon->errorInfo();
//				if(!preg_match('/^duplicate id/', $errorInfo[2]))
//					die("No affected records [" . $sphinxCon->errorCode() . "]\n" . print_r($sphinxCon->errorInfo(), true));
			}
			
			$serverLastLog->setLastLogId($sphinxLog->getId());
			$serverLastLog->save(myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_SPHINX_LOG));
		}
		catch(Exception $e){
			KalturaLog::err($e->getMessage());
		}
	}
	unset($sphinxCon);

	SphinxLogPeer::clearInstancePool();
	$sphinxLogs = SphinxLogPeer::retrieveByLastId($lastLogs);
}

KalturaLog::log('Done');