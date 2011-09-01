<?php
error_reporting ( E_ALL );

define ( 'SF_ROOT_DIR', realpath ( dirname ( __FILE__ ) . '/../../alpha/' ) );
define ( 'SF_APP', 'kaltura' );
define ( 'SF_DEBUG', true );

require_once (SF_ROOT_DIR . DIRECTORY_SEPARATOR . 'apps' . DIRECTORY_SEPARATOR . SF_APP . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php');
require_once (SF_ROOT_DIR . '/../infra/bootstrap_base.php');

DbManager::setConfig ( kConf::getDB () );
DbManager::initialize ();

$syncType = 'entry';
$dbh = myDbHelper::getConnection ( myDbHelper::DB_HELPER_CONN_DWH );
$sql = "CALL get_data_for_operational('$syncType')";
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
	$entry->save();
	$count++;
	KalturaLog::debug ( 'Successfully saved entry [' . $row ['entry_id'] . ']' );
	if ($count % 500)
		entryPeer::clearInstancePool ();
}
if ($count == count($rows)) {
	$sql = "CALL mark_operational_sync_as_done($syncType)";
	$dbh->query ( $sql );
}
KalturaLog::debug ( "Done updating $count entries from DWH to operational DB" );