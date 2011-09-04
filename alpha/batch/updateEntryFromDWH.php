<?php
error_reporting ( E_ALL );
set_time_limit(0);

ini_set("memory_limit","700M");

define('ROOT_DIR', realpath(dirname(__FILE__) . '/../../'));
require_once(ROOT_DIR . '/alpha/config/kConf.php');
require_once(ROOT_DIR . '/infra/bootstrap_base.php');
require_once(ROOT_DIR . '/infra/KAutoloader.php');

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

$syncType = 'entry';
$dbh = myDbHelper::getConnection ( myDbHelper::DB_HELPER_CONN_DWH );
$sql = "CALL get_data_for_operational($syncType)";
$count = 0;
$rows = $dbh->query ( $sql )->fetchAll ();
foreach ( $rows as $row ) {
	$entry = entryPeer::retrieveByPK ( $row ['entry_id'] );
	if (is_null ( $entry )) {
		KalturaLog::err ( 'Couldn\'t find entry [' . $row ['entry_id'] . ']' );
		continue;
	}
	$entry->setViews ( $row ['views'] );
	$entry->setPlays ( $row ['plays'] );
	if ($entry->save ()) {
		$count ++;
		KalturaLog::debug ( 'Successfully saved entry [' . $row ['entry_id'] . ']' );
	} else {
		KalturaLog::err ( 'Error while saving entry [' . $row ['entry_id'] . ']' );
	}
	if ($count % 500)
		entryPeer::clearInstancePool ();
}
$sql = "CALL mark_operational_sync_as_done($syncType)";
$dbh->query ( $sql );
KalturaLog::debug ( "Done updating entries from DWH to operational DB" );