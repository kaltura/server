<?php
error_reporting ( E_ALL );
set_time_limit(0);

ini_set("memory_limit","700M");

define("KALTURA_ROOT_PATH", realpath(__DIR__ . '/../../'));

require_once(KALTURA_ROOT_PATH . '/alpha/config/kConf.php');
require_once(KALTURA_ROOT_PATH . '/infra/KAutoloader.php');

$sf_symfony_lib_dir = realpath(dirname(__FILE__).'/../../vendor/symfony');
$sf_symfony_data_dir = realpath(dirname(__FILE__).'/../../vendor/symfony-data');

$include_path = realpath(dirname(__FILE__).'/../../vendor/ZendFramework/library') . PATH_SEPARATOR . get_include_path();
set_include_path($include_path);

require_once($sf_symfony_lib_dir.'/util/sfCore.class.php');
sfCore::bootstrap($sf_symfony_lib_dir, $sf_symfony_data_dir);

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::setClassMapFilePath(kConf::get("cache_root_path") . '/scripts/' . basename(__FILE__) . '.cache');
KAutoloader::register();

date_default_timezone_set(kConf::get("date_default_timezone"));

$loggerConfigPath = KALTURA_ROOT_PATH . '/scripts/logger.ini';
$config = new Zend_Config_Ini($loggerConfigPath);
KalturaLog::initLog($config);
KalturaLog::setContext(basename(__FILE__));
KalturaLog::info("Starting script");

KalturaLog::info("Initializing database...");
DbManager::setConfig(kConf::getDB());
DbManager::initialize();
KalturaLog::info("Database initialized successfully");

$syncType = 'kuser';
$dbh = myDbHelper::getConnection ( myDbHelper::DB_HELPER_CONN_DWH );
$sql = "CALL get_data_for_operational('$syncType')";
$count = 0;
$rows = $dbh->query ( $sql )->fetchAll ();
foreach ( $rows as $row ) {
	$kuser = kuserPeer::retrieveByPK ( $row ['kuser_id'] );
	if (is_null ( $kuser )) {
		KalturaLog::err ( 'Couldn\'t find kuser [' . $row ['kuser_id'] . ']' );
		continue;
	}
	$kuser->setStorageSize ( $row ['storage_size'] );
	$kuser->save ();
	$count ++;
	KalturaLog::debug ( 'Successfully saved kuser [' . $row ['kuser_id'] . ']' );
	if ($count % 500)
		kuserPeer::clearInstancePool ();
}
$sql = "CALL mark_operational_sync_as_done('$syncType')";
$dbh->query ( $sql );
KalturaLog::debug ( "Done updating $count kusers from DWH to operational DB" );