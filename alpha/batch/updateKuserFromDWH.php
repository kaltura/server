<?php
error_reporting ( E_ALL );

define ( 'SF_ROOT_DIR', realpath ( dirname ( __FILE__ ) . '/../../alpha/' ) );
define ( 'SF_APP', 'kaltura' );
define ( 'SF_DEBUG', true );

require_once (SF_ROOT_DIR . DIRECTORY_SEPARATOR . 'apps' . DIRECTORY_SEPARATOR . SF_APP . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php');
require_once (SF_ROOT_DIR . '/../infra/bootstrap_base.php');

DbManager::setConfig ( kConf::getDB () );
DbManager::initialize ();

$syncType = 'kuser';
$dbh = myDbHelper::getConnection ( myDbHelper::DB_HELPER_CONN_DWH );
$sql = "CALL get_data_for_operational($syncType)";
$count = 0;
$rows = $dbh->query ( $sql )->fetchAll ();
foreach ( $rows as $row ) {
	$kuser = KuserPeer::retrieveByPK ( $row ['kuser_id'] );
	if (is_null ( $kuser )) {
		KalturaLog::err ( 'Couldn\'t find kuser [' . $row ['kuser_id'] . ']' );
		continue;
	}
	$kuser->setStorageSize ( $row ['storage_size'] );
	if ($kuser->save ()) {
		$count ++;
		KalturaLog::debug ( 'Successfully saved kuser [' . $row ['kuser_id'] . ']' );
	} else {
		KalturaLog::err ( 'Error while saving kuser [' . $row ['kuser_id'] . ']' );
	}
	if ($count % 500)
		kuserPeer::clearInstancePool ();
}
if ($count == count($rows)) {
	$sql = "CALL mark_operational_sync_as_done($syncType)";
	$dbh->query ( $sql );
}
KalturaLog::debug ( "Done updating $count kusers from DWH to operational DB" );