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
KAutoloader::setClassMapFilePath(kConf::get("cache_root_path") . '/elastic/' . basename(__FILE__) . '.cache');
KAutoloader::register();

$skipExecutedUpdates = false;
error_reporting(E_ALL);
KalturaLog::setLogger(new KalturaStdoutLogger());

$hostname = (isset($_SERVER["HOSTNAME"]) ? $_SERVER["HOSTNAME"] : gethostname());
$configFile = ROOT_DIR . "/configurations/elastic/populate/$hostname.ini";
if(!file_exists($configFile))
{
    KalturaLog::err("Configuration file [$configFile] not found.");
    exit(-1);
}
$config = parse_ini_file($configFile);
$elasticCluster = $config['elasticCluster'];
$elasticServer = $config['elasticServer'];
$elasticPort = (isset($config['elasticPort']) ? $config['elasticPort'] : 9200);
$systemSettings = kConf::getMap('system');
if(!$systemSettings || !$systemSettings['LOG_DIR'])
{
    KalturaLog::err("LOG_DIR not found in system configuration.");
    exit(-1);
}
$pid = $systemSettings['LOG_DIR'] . '/populate_elastic.pid';
if(file_exists($pid))
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

$sphinxLogReadConn = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_SPHINX_LOG_READ);

$serverLastLogs = SphinxLogServerPeer::retrieveByServer($elasticCluster, $sphinxLogReadConn);


$lastLogs = array();
$handledRecords = array();

foreach($serverLastLogs as $serverLastLog) {
    $lastLogs[$serverLastLog->getDc()] = $serverLastLog;
    $handledRecords[$serverLastLog->getDc()] = array();
}

$elasticClient = new elasticClient($elasticServer, $elasticPort); //take the server and port from config - $elasticServer , $elasticPort

while(true)
{
    $elasticLogs = SphinxLogPeer::retrieveByLastId($lastLogs, $gap, $limit, $handledRecords, $sphinxLogReadConn, SphinxLogType::ELASTIC);

    while(!count($elasticLogs))
    {
        $skipExecutedUpdates = true;
        sleep(1);
        $elasticLogs = SphinxLogPeer::retrieveByLastId($lastLogs, $gap, $limit, $handledRecords, $sphinxLogReadConn, SphinxLogType::ELASTIC);
    }

    if(!elasticSearchUtils::isMaster($elasticClient, $hostname))
    {
        KalturaLog::log('elastic server ['.$hostname.'] is not the master , sleeping for 30 seconds');
        sleep(30);
        continue;
    }

    $ping = $elasticClient->ping();

    if(!$ping)
    {
        KalturaLog::err('cannot connect to elastic cluster with client['.print_r($elasticClient, true).']');
        sleep(5);
        continue;
    }

    foreach($elasticLogs as $elasticLog)
    {
        /* @var $elasticLog SphinxLog */
        $dc = $elasticLog->getDc();
        $executedServerId = $elasticLog->getExecutedServerId();
        $elasticLogId = $elasticLog->getId();

        $serverLastLog = null;

        if(isset($lastLogs[$dc])) {
            $serverLastLog = $lastLogs[$dc];
        } else {
            $serverLastLog = new SphinxLogServer();
            $serverLastLog->setServer($elasticCluster);
            $serverLastLog->setDc($dc);

            $lastLogs[$dc] = $serverLastLog;
        }

        $handledRecords[$dc][] = $elasticLogId;
        KalturaLog::log("Elastic log id $elasticLogId dc [$dc] executed server id [$executedServerId] Memory: [" . memory_get_usage() . "]");

        try
        {
            if ($skipExecutedUpdates && $executedServerId == $serverLastLog->getId())
            {
                KalturaLog::log ("Elastic server is initiated and the command already ran synchronously on this machine. Skipping");
            }
            else
            {
                //we save the elastic command as serialized object in the sql field
                $command = $elasticLog->getSql();
                $command = unserialize($command);
                $action = $command['action'];

                $response = $elasticClient->$action($command);
            }

            // If the record is an historical record, don't take back the last log id
            if($serverLastLog->getLastLogId() < $elasticLogId) {
                $serverLastLog->setLastLogId($elasticLogId);

                // Clear $handledRecords from before last - gap.
                foreach($serverLastLogs as $serverLastLog) {
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
    
    SphinxLogPeer::clearInstancePool();
}

KalturaLog::log('Done');
