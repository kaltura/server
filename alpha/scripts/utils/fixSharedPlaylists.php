<?php
require_once(dirname(__FILE__).'/../bootstrap.php');


$playlists = entryPeer::retrieveByPKs(array('_KMCSPL1', '_KMCSPL2'));
foreach($playlists as $playlist)
{
	/* @var $playlist entry */
	
	$criteria = new Criteria();
	$criteria->add(FileSyncPeer::OBJECT_TYPE, FileSyncObjectType::ENTRY);
	$criteria->add(FileSyncPeer::OBJECT_SUB_TYPE, kEntryFileSyncSubType::DATA);
	$criteria->add(FileSyncPeer::OBJECT_ID, $playlist->getId());
	$criteria->add(FileSyncPeer::PARTNER_ID, 0);
	$criteria->add(FileSyncPeer::DC, 0);
	$criteria->add(FileSyncPeer::ORIGINAL, true);
	$criteria->add(FileSyncPeer::STATUS, FileSync::FILE_SYNC_STATUS_READY);
	
	$criteria->addDescendingOrderByColumn(FileSyncPeer::ID);
	
	$fileSync = FileSyncPeer::doSelectOne($criteria);
	$playlist->setData($fileSync->getVersion(), true);
	$playlist->save();
}

KalturaLog::debug('Done');
