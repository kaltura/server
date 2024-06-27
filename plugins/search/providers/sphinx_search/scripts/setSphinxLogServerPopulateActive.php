<?php

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

$populateActiveValue = intval($argv[1]);
$hostname = $argv[2] ?? ($_SERVER["HOSTNAME"] ?? gethostname());


define('ROOT_DIR', realpath(dirname(__FILE__) . '/../../../../../'));
require_once(ROOT_DIR . '/infra/KAutoloader.php');
require_once(ROOT_DIR . '/alpha/config/kConf.php');

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*"));
KAutoloader::setClassMapFilePath(kConf::get("cache_root_path") . '/sphinx/' . basename(__FILE__) . '.cache');
KAutoloader::register();

error_reporting(E_ALL);
KalturaLog::setLogger(new KalturaStdoutLogger());

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

$dbConf = kConf::getDB();
DbManager::setConfig($dbConf);
DbManager::initialize();

$sphinxReadConn = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_SPHINX_LOG_READ);
$sphinxWriteConn = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_SPHINX_LOG);

$sphinxLogServers = SphinxLogServerPeer::retrieveByServer($hostname, $sphinxReadConn);

if (!count($sphinxLogServers))
{
	KalturaLog::err("Error: could not find sphinx_log_server records for server hostname [$hostname]");
	exit(1);
}

KalturaLog::log("Updating all sphinx_log_server.server [$hostname] sphinx_log_server.populate_active to [$populateActiveValue]");

foreach ($sphinxLogServers as $sphinxLogServer)
{
	/* @var SphinxLogServer $sphinxLogServer */
	$sphinxLogServer->setPopulateActive($populateActiveValue);
	$sphinxLogServer->save($sphinxWriteConn);
}

KalturaLog::log("Successfully updated all sphinx_log_server.server [$hostname] sphinx_log_server.populate_active to [$populateActiveValue]");

// explicitly return 0 status code to indicate 'success'
exit(0);
