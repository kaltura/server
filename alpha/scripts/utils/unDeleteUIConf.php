<?php
require_once(dirname(__FILE__).'/../bootstrap.php');

if(!isset($argv[1]))
	die('UI-Conf ID argument is required');

$uiConfId = $argv[1];

$dryRun = (!isset($argv[2]) || $argv[2] != 'realrun');
KalturaStatement::setDryRun($dryRun);
KalturaLog::debug($dryRun ? "Dry Run" : "REAL RUN");

uiConfPeer::setUseCriteriaFilter(false);
FileSyncPeer::setUseCriteriaFilter(false);

$uiConf = uiConfPeer::retrieveByPK($uiConfId);
if(!$uiConf)
	die("UI-Conf ID [$uiConfId] not found");
	
$fileSyncs = array();

$fileSync = kFileSyncUtils::getLocalFileSyncForKey($uiConf->getSyncKey(uiConf::FILE_SYNC_UICONF_SUB_TYPE_DATA), false);
if($fileSync)
        $fileSyncs[] = $fileSync;

$fileSync = kFileSyncUtils::getLocalFileSyncForKey($uiConf->getSyncKey(uiConf::FILE_SYNC_UICONF_SUB_TYPE_CONFIG), false);
if($fileSync)
	$fileSyncs[] = $fileSync;
	
$fileSync = kFileSyncUtils::getLocalFileSyncForKey($uiConf->getSyncKey(uiConf::FILE_SYNC_UICONF_SUB_TYPE_FEATURES), false);
if($fileSync)
	$fileSyncs[] = $fileSync;
	
if(empty($fileSyncs))
    die("No file_sync found for UI-Conf ID [" .$uiConfId. "]");

uiConfPeer::setUseCriteriaFilter(true);
FileSyncPeer::setUseCriteriaFilter(true);
	
foreach($fileSyncs as $fileSync)
{
	if(!file_exists($fileSync->getFullPath()))
		die("UI-Conf file ID [" . $fileSync->getId() . "] file not found");
}

foreach($fileSyncs as $fileSync)
{
	if(method_exists($fileSync, 'setDeletedId'))
		$fileSync->setDeletedId(0);
		
	$fileSync->setStatus(FileSync::FILE_SYNC_STATUS_READY);
	$fileSync->save();
}

$uiConf->setStatus(uiConf::UI_CONF_STATUS_READY);
$uiConf->save();

KalturaLog::debug('Done');
