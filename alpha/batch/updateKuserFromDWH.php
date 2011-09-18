<?php
error_reporting ( E_ALL );
set_time_limit(0);

ini_set("memory_limit","700M");

define('ROOT_DIR', realpath(dirname(__FILE__) . '/../../'));
require_once(ROOT_DIR . '/infra/kConf.php');
require_once(ROOT_DIR . '/infra/bootstrap_base.php');
require_once(ROOT_DIR . '/infra/KAutoloader.php');
require_once (SF_ROOT_DIR . DIRECTORY_SEPARATOR . 'apps' . DIRECTORY_SEPARATOR . SF_APP . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php');

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::setClassMapFilePath(kConf::get("cache_root_path") . '/scripts/classMap.cache');
KAutoloader::register();

date_default_timezone_set(kConf::get("date_default_timezone"));

$loggerConfigPath = ROOT_DIR.'/scripts/logger.ini';
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