<?php
set_time_limit(0);
chdir(dirname(__FILE__));

define('ROOT_DIR', realpath(dirname(__FILE__) . '/../../../../../'));
require_once(ROOT_DIR . '/infra/KAutoloader.php');
require_once(ROOT_DIR . '/alpha/config/kConf.php');

// ------------------------------------------------------
class OldLogRecordsFilter
{
	private $logId;

	function __construct($logId)
	{
		$this->logId = $logId;
	}

	function filter($i)
	{
		return $i > $this->logId;
	}
}

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*"));
KAutoloader::setClassMapFilePath(kConf::get("cache_root_path") . '/elastic/' . basename(__FILE__) . '.cache');
KAutoloader::register();

$skipExecutedUpdates = false;
error_reporting(E_ALL);
KalturaLog::setLogger(new KalturaStdoutLogger());

$hostname = (isset($_SERVER["HOSTNAME"]) ? $_SERVER["HOSTNAME"] : gethostname());
$config = kConf::get('elasticPopulateSettings', 'elastic_populate', array());
if (empty($config))
{
	$configFile = ROOT_DIR . "/configurations/elastic/populate/$hostname.ini";

	if (!file_exists($configFile) && !generateConfigFile($configFile))
	{
		KalturaLog::err("Configuration file [$configFile] not found.");
		exit(-1);
	}
	$config = parse_ini_file($configFile);
	KalturaLog::debug("Configuration file [$configFile] loaded successfully - values: " . print_r($config, true));
}

$elasticCluster = $config['elasticCluster'];
$elasticServer = $config['elasticServer'];
$elasticPort = (isset($config['elasticPort']) ? $config['elasticPort'] : 9200);

// add support for opensearch distribution, if 'opensearch' is set in the config we will use elastic 7+ syntax
$distribution = $config['distribution'] ?? null;
if ($distribution === elasticClient::OPENSEARCH_DISTRIBUTION)
{
	KalturaLog::debug("Found distribution config value [$distribution] - using elastic 7 syntax");
	$elasticVersion = elasticClient::ELASTIC_MAJOR_VERSION_7;
}
else
{
	$elasticVersion = isset($config['elasticVersion']) ? $config['elasticVersion'] : elasticClient::ELASTIC_MAJOR_VERSION_5;
}

$processScriptUpdates = (isset($config['processScriptUpdates']) ? $config['processScriptUpdates'] : false);
$systemSettings = kConf::getMap('system');
$shouldUseMaster = (isset($config['shouldUseMaster']) ? $config['shouldUseMaster'] : true);
$explicitPartnerIdsString = kConf::get('explicitPartnerIds','elasticDynamicMap',null);
$explicitPartnerIds = array();
if ($explicitPartnerIdsString)
{
        $explicitPartnerIdsArray = explode(',',$explicitPartnerIdsString);
        $explicitPartnerIds = array_map('trim',$explicitPartnerIdsArray);
}

if (!$systemSettings || !$systemSettings['LOG_DIR'])
{
	KalturaLog::err("LOG_DIR not found in system configuration.");
	exit(-1);
}

$pid = $systemSettings['LOG_DIR'] . '/populate_elastic.pid';
if (file_exists($pid))
{
	KalturaLog::err("Scheduler already running - pid[" . file_get_contents($pid) . "]");
	exit(1);
}
file_put_contents($pid, getmypid());

$dbConf = kConf::getDB();
DbManager::setConfig($dbConf);
DbManager::initialize();

$limit = 1000;
$gap = 500;
$maxIndexHistory = 2000; //The maximum array size to save unique object ids update and their elastic log id

$sphinxLogReadConn = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_SPHINX_LOG_READ);

$serverLastLogs = SphinxLogServerPeer::retrieveByServer($elasticCluster, $sphinxLogReadConn);


$lastLogs = array();
$handledRecords = array();
$objectIdElasticLog = array();

foreach ($serverLastLogs as $serverLastLog)
{
	$lastLogs[$serverLastLog->getDc()] = $serverLastLog;
	$handledRecords[$serverLastLog->getDc()] = array();
}

$elasticClient = new elasticClient($elasticServer, $elasticPort, $elasticVersion); //take the server, port and elasticVersion from config - $elasticServer , $elasticPort

while (true)
{
	if ($shouldUseMaster && !elasticSearchUtils::isMaster($elasticClient, $hostname))
	{
		KalturaLog::log('elastic server [' . $hostname . '] is not the master , sleeping for 30 seconds');
		sleep(30);
		//update the last log ids
		$serverLastLogs = SphinxLogServerPeer::retrieveByServer($elasticCluster, $sphinxLogReadConn);
		foreach ($serverLastLogs as $serverLastLog)
		{
			$lastLogs[$serverLastLog->getDc()] = $serverLastLog;
			$handledRecords[$serverLastLog->getDc()] = array();
		}
		SphinxLogServerPeer::clearInstancePool();
		continue;
	}

	$elasticLogs = SphinxLogPeer::retrieveByLastId($lastLogs, $gap, $limit, $handledRecords, $sphinxLogReadConn, SphinxLogType::ELASTIC);

	while (!count($elasticLogs))
	{
		$skipExecutedUpdates = true;
		sleep(1);
		$elasticLogs = SphinxLogPeer::retrieveByLastId($lastLogs, $gap, $limit, $handledRecords, $sphinxLogReadConn, SphinxLogType::ELASTIC);
	}

	//keeping only the newest elastic log with the same object id and object type
	$assocElasticLogs = array();
	foreach ($elasticLogs as $log)
	{
		$key = $log->getObjectId() . '.' . $log->getObjectType();
		if (array_key_exists($key, $assocElasticLogs))
		{
			KalturaLog::debug('elastic log ' . $log->getId() . ' object id ' . $log->getObjectId() . ' object type ' . $log->getObjectType() . ' found in log id ' . $assocElasticLogs[$key]->getId());
		}
		$assocElasticLogs[$key] = $log;
	}
	$elasticLogs = $assocElasticLogs;

	$healthPing = $elasticClient->checkHealth();
	if (!$healthPing)
	{
		KalturaLog::err('cannot connect to elastic cluster with client[' . print_r($elasticClient, true) . ']');
		sleep(5);
		kMemoryManager::clearMemory();
		$elasticClient = new elasticClient($elasticServer, $elasticPort, $elasticVersion);
		continue;
	}

	foreach ($elasticLogs as $elasticLog)
	{
		/* @var $elasticLog SphinxLog */
		$dc = $elasticLog->getDc();
		$executedServerId = $elasticLog->getExecutedServerId();
		$elasticLogId = $elasticLog->getId();

		$serverLastLog = null;

		if (isset($lastLogs[$dc]))
		{
			$serverLastLog = $lastLogs[$dc];
		}
		else
		{
			$serverLastLog = new SphinxLogServer();
			$serverLastLog->setServer($elasticCluster);
			$serverLastLog->setDc($dc);

			$lastLogs[$dc] = $serverLastLog;
		}

		$handledRecords[$dc][] = $elasticLogId;
		KalturaLog::log("Elastic log id $elasticLogId dc [$dc] executed server id [$executedServerId] Memory: [" . memory_get_usage() . "]");

		try
		{
			$objectId = $elasticLog->getObjectId();
			if ($skipExecutedUpdates && $executedServerId == $serverLastLog->getId())
			{
				KalturaLog::log("Elastic server is initiated and the command already ran synchronously on this machine. Skipping");
			}
			elseif (isset($objectIdElasticLog[$objectId]) && $objectIdElasticLog[$objectId] > $elasticLogId) {
				KalturaLog::log("Found newer update for the same object id, skipping [$objectId] [$elasticLogId] [{$objectIdElasticLog[$objectId]}]");
			}
			else
			{
				//we save the elastic command as serialized object in the sql field
				$command = $elasticLog->getSql();
				$command = unserialize($command);
				$action = $command['action'];
				$index = $command['index'];
				$partnerId = $elasticLog->getPartnerId();
				$command['index'] = kBaseESearch::getSplitIndexNamePerPartner($command['index'], $partnerId);

				if ($action && ($processScriptUpdates || !(strpos($index, ElasticIndexMap::ELASTIC_ENTRY_INDEX)!== false && $action == ElasticMethodType::UPDATE)))
				{
					if (empty($explicitPartnerIds) || in_array($partnerId, $explicitPartnerIds))
					{
						$response = $elasticClient->addToBulk($command);
					}
				}

				unset($objectIdElasticLog[$objectId]);

				if (count($objectIdElasticLog) > $maxIndexHistory)
				{
					reset($objectIdElasticLog);
					$oldestElementKey = key($objectIdElasticLog);
					unset($objectIdElasticLog[$oldestElementKey]);
				}

				$objectIdElasticLog[$objectId] = $elasticLogId;
			}

			// If the record is an historical record, don't take back the last log id
			if ($serverLastLog->getLastLogId() < $elasticLogId)
			{
				$serverLastLog->setLastLogId($elasticLogId);

				// Clear $handledRecords from before last - gap.
				foreach ($serverLastLogs as $serverLastLog)
				{
					$dc = $serverLastLog->getDc();
					$threshold = $serverLastLog->getLastLogId() - $gap;
					$handledRecords[$dc] = array_filter($handledRecords[$dc], array(new OldLogRecordsFilter($threshold), 'filter'));
				}
			}
		}
		catch (Exception $e)
		{
			KalturaLog::err($e->getMessage());
		}
	}

	try
	{
		$response = $elasticClient->flushBulk();
	}
	catch(Exception $e)
	{
		KalturaLog::err($e->getMessage());
	}

	foreach ($lastLogs as $serverLastLog)
	{
		$serverLastLog->save(myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_SPHINX_LOG));
	}

	SphinxLogPeer::clearInstancePool();
	kMemoryManager::clearMemory();
}

KalturaLog::log('Done');

function generateConfigFile($configFile)
{
	$elasticHost = getenv('ELASTIC_HOST_NAME');
	$elasticPort = 9200;
	if (!$elasticHost)
	{
		KalturaLog::err("Missing environment variable 'ELASTIC_HOST_NAME' to point to elastic host");
		return false;
	}
	
	exec("curl -s $elasticHost:$elasticPort", $output);
	$elasticResponse = json_decode(implode('', $output), true);
	
	$elasticCluster = isset($elasticResponse['cluster_name']) ? $elasticResponse['cluster_name'] : null;
	$elasticVersion = isset($elasticResponse['version']['number']) ? $elasticResponse['version']['number'] : null;
	
	if (!$elasticCluster || !$elasticVersion)
	{
		KalturaLog::err("Missing required fields from elastic '$elasticHost' response");
		return false;
	}
	
	$elasticBlock = "[elasticPopulateSettings]\n";
	$elasticBlock .= "elasticCluster = $elasticCluster\n";
	$elasticBlock .= "elasticVersion = $elasticVersion\n";
	$elasticBlock .= "elasticServer = $elasticHost\n";
	$elasticBlock .= "elasticPort = $elasticPort\n";
	$elasticBlock .= "shouldUseMaster = false\n";
	
	if (!file_exists(dirname($configFile)) &&  !mkdir(dirname($configFile), 0755, true))
	{
		KalturaLog::err('Failed to create directory path "' . dirname($configFile) . '"');
		return false;
	}
	
	if (!file_put_contents($configFile, $elasticBlock, LOCK_EX))
	{
		KalturaLog::err("Failed to write to file path '$configFile'");
		return false;
	}
	
	if (!chmod($configFile, 0644))
	{
		KalturaLog::err("Failed to change permissions to file path '$configFile'");
		return false;
	}
	
	return true;
}
