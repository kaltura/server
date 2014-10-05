<?php

require_once (dirname ( __FILE__ ) . '/../bootstrap.php');


$uiconfId = $argv[1];
uiConfPeer::setUseCriteriaFilter(false);
$uiconf = uiConfPeer::retrieveByPK($uiconfId);
uiConfPeer::setUseCriteriaFilter(true);

if ($uiconf)
{
	$uiconf->setStatus(uiConf::UI_CONF_STATUS_READY);
FileSyncPeer::setUseCriteriaFilter(false);
	$uiconf->save();
	$dataSyncKey = $uiconf->getSyncKey(uiConf::FILE_SYNC_UICONF_SUB_TYPE_DATA);
	$fileSyncs = FileSyncPeer::retrieveAllByFileSyncKey($dataSyncKey);
	foreach ($fileSyncs as $fileSync)
	{
		$fileSync->setStatus(FileSync::FILE_SYNC_STATUS_READY);
		$fileSync->save();
	}
	
	$configSyncKey = $uiconf->getSyncKey(uiConf::FILE_SYNC_UICONF_SUB_TYPE_CONFIG);
	$fileSyncs = FileSyncPeer::retrieveAllByFileSyncKey($configSyncKey);
	foreach ($fileSyncs as $fileSync)
	{
		$fileSync->setStatus(FileSync::FILE_SYNC_STATUS_READY);
		$fileSync->save();
	}
	
	$featuresSyncKey = $uiconf->getSyncKey(uiConf::FILE_SYNC_UICONF_SUB_TYPE_FEATURES);
	
	$fileSyncs = FileSyncPeer::retrieveAllByFileSyncKey($dataSyncKey);
	foreach ($fileSyncs as $fileSync)
	{
		$fileSync->setStatus(FileSync::FILE_SYNC_STATUS_READY);
		$fileSync->save();
	}
	FileSyncPeer::setUseCriteriaFilter(true);
}

die('complete');

