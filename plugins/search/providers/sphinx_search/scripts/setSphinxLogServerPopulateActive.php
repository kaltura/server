<?php

/**
 * This script will set sphinx_log_server.populate_active column to either 0 (disabled) or 1 (enabled)
 *
 * We do this so that 'populateFromLog.php' script could run on a different machine than the machine that runs Sphinx-Search
 * Because when a Sphinx server wants to do a resnap - it needs to stop 'populateFromLog.php'
 * Up until today, it was done via the shell script that would call 'kaltura_populate.sh stop' locally
 * But now, we can run 'populateFromLog.php' not locally and still control if it will process sphinx_log records or hold as long as sphinx_log_server.populate_active is 0
 */

if ($argc < 2)
{
	echo "Error: missing required parameter [ 0 | 1 ]" . PHP_EOL;
	echo "To execute: $argv[0] [ 0 | 1 ] { sphinx_log_server.server }" . PHP_EOL;
	exit(1);
}

if (!is_numeric($argv[1]) || !in_array($argv[1], array(0, 1)))
{
	echo "Error: parameter must be either 0 (disable) or 1 (enable)" . PHP_EOL;
	exit(1);
}

define('ROOT_DIR', realpath(dirname(__FILE__) . '/../../../../../'));
require_once(ROOT_DIR . '/infra/KAutoloader.php');
require_once(ROOT_DIR . '/alpha/config/kConf.php');

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*"));
KAutoloader::setClassMapFilePath(kConf::get("cache_root_path") . '/sphinx/' . basename(__FILE__) . '.cache');
KAutoloader::register();

error_reporting(E_ALL);
KalturaLog::setLogger(new KalturaStdoutLogger());

$populateActiveValue = intval($argv[1]);
$hostname = $argv[2] ?? ($_SERVER["HOSTNAME"] ?? gethostname());

$config = kConf::get('sphinxPopulateSettings', 'sphinx_populate', array());
if (empty($config))
{
	$configFile = ROOT_DIR . "/configurations/sphinx/populate/$hostname.ini";
	if(!file_exists($configFile))
	{
		KalturaLog::err("Configuration file [$configFile] not found.");
		exit(1);
	}
	$config = parse_ini_file($configFile);
}

$sphinxServer = $config['sphinxServer'] ?? $hostname;

KalturaLog::log("Starting to set [$sphinxServer] populate_active to [$populateActiveValue]");

$dbConf = kConf::getDB();
DbManager::setConfig($dbConf);
DbManager::initialize();

$sphinxReadConn = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_SPHINX_LOG_READ);
$sphinxWriteConn = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_SPHINX_LOG);
$sphinxLogServers = SphinxLogServerPeer::retrieveByServer($sphinxServer, $sphinxReadConn);

if (!count($sphinxLogServers))
{
	KalturaLog::err("Error: could not find sphinx_log_server records for server hostname [$sphinxServer]");
	exit(1);
}

KalturaLog::log("Updating all sphinx_log_server.server [$sphinxServer] sphinx_log_server.populate_active to [$populateActiveValue]");

$lastLogId = array();
$keepChecking = true;
$retry = 0;
$retryAttempts = 3;
$sleepTime = 5;

// save new value
foreach ($sphinxLogServers as $sphinxLogServer)
{
	/* @var SphinxLogServer $sphinxLogServer */
	// set populate_active new value
	$sphinxLogServer->setPopulateActive($populateActiveValue);
	
	try
	{
		$sphinxLogServer->save($sphinxWriteConn);
	}
	catch (Exception $e)
	{
		KalturaLog::err("Error trying to save sphinx_log_server, error: [" . $e->getMessage() . "]");
		exit(1);
	}
}

KalturaLog::log("Successfully updated all sphinx_log_server.server [$sphinxServer] sphinx_log_server.populate_active to [$populateActiveValue]");
KalturaLog::log("Sleeping for 5 seconds to wait for 'populateFromLog.php' to identify the change to sphinx_log_server for [$sphinxServer]");
sleep(5);

// verify last_log_id progress or not progress
SphinxLogServerPeer::clearInstancePool();
$sphinxLogServers = SphinxLogServerPeer::retrieveByServer($sphinxServer, $sphinxReadConn);
foreach ($sphinxLogServers as $sphinxLogServer)
{
	$lastLogId[$sphinxLogServer->getDc()] = $sphinxLogServer->getLastLogId();
}

// if we disabled populate_active - verify last_log_id stopped progressing
// we do not if we enabled populate_active because in low traffic system it will not necessarily progress
if ($populateActiveValue === 0)
{
	while ($keepChecking && $retry < $retryAttempts)
	{
		SphinxLogServerPeer::clearInstancePool();
		$sphinxLogServers = SphinxLogServerPeer::retrieveByServer($sphinxServer, $sphinxReadConn);
		
		foreach ($sphinxLogServers as $sphinxLogServer)
		{
			$dc = $sphinxLogServer->getDc();
			
			// verify by checking that previous last_log_id == current last_log_id
			if ($lastLogId[$dc] !== $sphinxLogServer->getLastLogId())
			{
				// store the current last_log_id number per dc
				$lastLogId[$dc] = $sphinxLogServer->getLastLogId();
				
				KalturaLog::log("Server [$sphinxServer] dc [$dc] current last_log_id [$lastLogId[$dc]] is still progressing - sleeping for [$sleepTime] seconds");
				sleep($sleepTime);
				$retry++;
				
				continue 2;
			}
		}
		
		KalturaLog::log("Successfully verified that last_log_id stopped progressing for [$sphinxServer], last_log_id by dc: " . print_r($lastLogId, true));
		$keepChecking = false;
	}
	
	// if we got out of while-loop due to max retry attempts reached - exist code 1
	if ($retry === $retryAttempts)
	{
		$totalRetryTime = $retryAttempts * $sleepTime;
		KalturaLog::err("ERROR: last_log_id for server [$sphinxServer] dc [$dc] keeps progressing after [$totalRetryTime] seconds - terminating with failure");
		exit(1);
	}
}

KalturaLog::log("Finished setting [$sphinxServer] populate_active to [$populateActiveValue]");
// explicitly return 0 status code to indicate 'success'
exit(0);
