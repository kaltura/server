<?php
set_time_limit(0);
ini_set("memory_limit","700M");
chdir(dirname(__FILE__));

define('ROOT_DIR', realpath(dirname(__FILE__) . '/../../../../../'));
require_once(ROOT_DIR . '/infra/KAutoloader.php');
require_once(ROOT_DIR . '/alpha/config/kConf.php');

// ------------------------------------------------------
class OldLogRecordsFilter {
	private $logId;

	function __construct($logId) {
		$this->logId = $logId;
	}

	function filter($i) {
		return $i > $this->logId;
	}
}

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*"));
KAutoloader::setClassMapFilePath(kConf::get("cache_root_path") . '/sphinx/' . basename(__FILE__) . '.cache');
KAutoloader::register();

$skipExecutedUpdates = false;
error_reporting(E_ALL);
KalturaLog::setLogger(new KalturaStdoutLogger());

$hostname = (isset($_SERVER["HOSTNAME"]) ? $_SERVER["HOSTNAME"] : gethostname());
$config = kConf::get('sphinxPopulateSettings', 'sphinx_populate', array());
if (empty($config))
{
	$configFile = ROOT_DIR . "/configurations/sphinx/populate/$hostname.ini";
	if(!file_exists($configFile))
	{
		KalturaLog::err("Configuration file [$configFile] not found.");
		exit(-1);
	}
	$config = parse_ini_file($configFile);
}

$sphinxServer = isset($config['sphinxServer']) ? $config['sphinxServer'] : $hostname;
$sphinxPort = (isset($config['sphinxPort']) ? $config['sphinxPort'] : 9312);
$isSharded = isset($config['sharded']) ? $config['sharded'] : false;
$processSqlUpdates = (isset($config['processSqlUpdates']) ? $config['processSqlUpdates'] : false);
$systemSettings = kConf::getMap('system');
if(!$systemSettings || !$systemSettings['LOG_DIR'])
{
	KalturaLog::err("LOG_DIR not found in system configuration.");
	exit(-1);
}
$pid = $systemSettings['LOG_DIR'] . '/populate.pid';
if(file_exists($pid))
{
	KalturaLog::err("Scheduler already running - pid[" . file_get_contents($pid) . "]");
	exit(1);
}
file_put_contents($pid, getmypid());

$dbConf = kConf::getDB();
DbManager::setConfig($dbConf);
DbManager::initialize();

$splitIndexSettings = null;
if(isset($dbConf['sphinx_split_index']) && $dbConf['sphinx_split_index']['enabled'] == true)
{
	$splitIndexSettings = $dbConf['sphinx_split_index'];
}

$limit = 1000; 	// The number of sphinxLog records we want to query
$gap = 500;	// The gap from 'getLastLogId' we want to query
$maxIndexHistory = 2000; //The maximum array size to save unique object ids update and their sphinx log id

$sphinxReadConn = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_SPHINX_LOG_READ);

$serverLastLogs = SphinxLogServerPeer::retrieveByServer($sphinxServer, $sphinxReadConn);
$lastLogs = array();
$handledRecords = array();
$sphinxRtTables = array();
$objectIdSphinxLog = array();

foreach($serverLastLogs as $serverLastLog)
{
	$lastLogs[$serverLastLog->getDc()] = $serverLastLog;
	$handledRecords[$serverLastLog->getDc()] = array();
}

while(true)
{
	$sphinxLogs = SphinxLogPeer::retrieveByLastId($lastLogs, $gap, $limit, $handledRecords, $sphinxReadConn, SphinxLogType::SPHINX);
	
	while(!count($sphinxLogs))
	{
		$skipExecutedUpdates = true;
		sleep(1);
		$sphinxLogs = SphinxLogPeer::retrieveByLastId($lastLogs, $gap, $limit, $handledRecords, $sphinxReadConn, SphinxLogType::SPHINX);
	}

	$sphinxCon = null;
	try
	{
		$sphinxCon = DbManager::createSphinxConnection($sphinxServer,$sphinxPort);
		if(!count($sphinxRtTables))
		{
			$sphinxRtTables = getSphinxRtTables($sphinxCon);
		}
		KalturaLog::log("sphinxServer [$sphinxServer], running rt index names [" . implode(",", $sphinxRtTables) . "]");
	}
	catch(Exception $e)
	{
		KalturaLog::err($e->getMessage());
		sleep(5);
		continue;
	}

	foreach($sphinxLogs as $sphinxLog)
	{
		/* @var $sphinxLog SphinxLog */
		$dc = $sphinxLog->getDc();
		$executedServerId = $sphinxLog->getExecutedServerId();
		$sphinxLogId = $sphinxLog->getId();
		$sphinxLogIndexName = $sphinxLog->getIndexName();
		if($isSharded && preg_match('~[0-9]~', $sphinxLogIndexName) == 0 && $splitIndexSettings && isset($splitIndexSettings[$sphinxLog->getObjectType()]))
		{
			$splitFactor = $splitIndexSettings[$sphinxLog->getObjectType()];
			$sphinxLogIndexName = $sphinxLogIndexName . "_" . ($sphinxLog->getPartnerId()/$splitFactor)%$splitFactor;
		}
		
		if($isSharded && $sphinxLogIndexName && !in_array($sphinxLogIndexName, $sphinxRtTables))
		{
			KalturaLog::log("Sphinx log id [$sphinxLogId] index name [$sphinxLogIndexName] not in rt table list, continue to next one");
			continue;
		}
		
		$serverLastLog = null;
		if(isset($lastLogs[$dc]))
		{
			$serverLastLog = $lastLogs[$dc];
		}
		else
		{
			$serverLastLog = new SphinxLogServer();
			$serverLastLog->setServer($sphinxServer);
			$serverLastLog->setDc($dc);
			
			$lastLogs[$dc] = $serverLastLog;
		}
		
		$handledRecords[$dc][] = $sphinxLogId;
		KalturaLog::log("Sphinx log id $sphinxLogId dc [$dc] executed server id [$executedServerId] Memory: [" . memory_get_usage() . "]");

		try
		{
			$objectId = $sphinxLog->getObjectId();
			if ($skipExecutedUpdates && $executedServerId == $serverLastLog->getId())
			{
				KalturaLog::log ("Sphinx server is initiated and the command already ran synchronously on this machine. Skipping");
			}
			elseif(isset($objectIdSphinxLog[$objectId]) && $objectIdSphinxLog[$objectId] > $sphinxLogId )
			{
				KalturaLog::log ("Found newer update for the same object id, skipping [$objectId] [$sphinxLogId] [{$objectIdSphinxLog[$objectId]}]");
			}
			else
			{
				$sql = $sphinxLog->getSql();
				if($isSharded)
				{
					$sql = preg_replace('/replace into (kaltura_.*?) /', "replace into $sphinxLogIndexName ", $sql);
				}
				
				// sql update commands are created only via an external script for updating entries plays count
				// by default these won't be updated by this script
				if ($processSqlUpdates || substr($sql, 0, 6) != "update")
				{
					$affected = $sphinxCon->exec($sql);
					if(!$affected)
					{
						$errorInfo = $sphinxCon->errorInfo();
						KalturaLog::err("Failed to run sphinx update query for sphinxLogId [$sphinxLogId] with error [" . $errorInfo . "]");
					}
					
					unset($objectIdSphinxLog[$objectId]);
					
					if(count($objectIdSphinxLog) > $maxIndexHistory)
					{
						reset($objectIdSphinxLog);
						$oldestElementKey = key($objectIdSphinxLog);
						unset($objectIdSphinxLog[$oldestElementKey]);
					}
					$objectIdSphinxLog[$objectId] = $sphinxLogId;
				}
			}
			
			// If the record is an historical record, don't take back the last log id
			if($serverLastLog->getLastLogId() < $sphinxLogId)
			{
				$serverLastLog->setLastLogId($sphinxLogId);
				
				// Clear $handledRecords from before last - gap.
				foreach($serverLastLogs as $serverLastLog)
				{
					$dc = $serverLastLog->getDc();
					$threshold = $serverLastLog->getLastLogId() - $gap;
					$handledRecords[$dc] = array_filter($handledRecords[$dc], array(new OldLogRecordsFilter($threshold), 'filter'));
				}
			}
		}
		catch(Exception $e)
		{
			KalturaLog::err($e->getMessage());
		}
	}
	
	foreach ($lastLogs as $serverLastLog)
	{
		$serverLastLog->save(myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_SPHINX_LOG));
	}
	
	unset($sphinxCon);

	SphinxLogPeer::clearInstancePool();
}

KalturaLog::log('Done');

function getSphinxRtTables($sphinxCon)
{
	$sphinxRtTables = array();
	$query = $sphinxCon->query("SHOW TABLES");
	$sphinxTablesData = $query->fetchAll();
	
	foreach ($sphinxTablesData as $sphinxTableData)
	{
		if($sphinxTableData['Type'] == "rt")
		{
			$sphinxRtTables[] = $sphinxTableData['Index'];
		}
	}
	
	return $sphinxRtTables;
}
