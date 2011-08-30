<?php
error_reporting ( E_ALL );

define ( 'SF_ROOT_DIR', realpath ( dirname ( __FILE__ ) . '/../../alpha/' ) );
define ( 'SF_APP', 'kaltura' );
define ( 'SF_DEBUG', true );

require_once (SF_ROOT_DIR . DIRECTORY_SEPARATOR . 'apps' . DIRECTORY_SEPARATOR . SF_APP . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php');
require_once (SF_ROOT_DIR . '/../infra/bootstrap_base.php');

DbManager::setConfig ( kConf::getDB () );
DbManager::initialize ();

$dbh = myDbHelper::getConnection ( myDbHelper::DB_HELPER_CONN_DWH );
$sql = 'CALL get_data_for_operational()';
foreach ( $dbh->query ( $sql )->fetchAll () as $row ) {
	$entry = entryPeer::retrieveByPK ( $row ['entry_id'] );
	if (! is_null ( $entry )) {
		$entry->setViews ( $row ['views'] );
		$entry->setPlays ( $row ['plays'] );
		if (! $entry->save ()) {
			KalturaLog::err ( "Error while saving entry [$row [entry_id]]" );
		} else {
			KalturaLog::debug ( "Successfully saved entry [$row [entry_id]]" );
		}
	} else {
		KalturaLog::debug ( "Couldn't find entry [$row [entry_id]]" );
	}
}