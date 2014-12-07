<?php

// this chdir can be changed according to environment
chdir(__DIR__ . '/../');
require_once 'bootstrap.php';

$subTypes = array(uiConf::FILE_SYNC_UICONF_SUB_TYPE_DATA, uiConf::FILE_SYNC_UICONF_SUB_TYPE_FEATURES, uiConf::FILE_SYNC_UICONF_SUB_TYPE_CONFIG);

function getFileSyncs($uiConf)
{
	GLOBAL $subTypes;

	$fileSyncs = array();
	$fileSyncsResult = array();
	$uiConfId = $uiConf->getId();

	$criteria=new Criteria();
	$criteria->add(FileSyncPeer::PARTNER_ID, $GLOBALS['partnerId']);
	$criteria->add(FileSyncPeer::OBJECT_ID, $uiConfId);
	$criteria->add(FileSyncPeer::OBJECT_TYPE, FileSyncObjectType::UICONF);
	
	foreach($subTypes as $type)
	{
		$criteria->add(FileSyncPeer::OBJECT_SUB_TYPE , $type);
		
		$version = getVersionBySubType($uiConf , $type);
		$criteria->add(FileSyncPeer::VERSION , $version);
		
		FileSyncPeer::setUseCriteriaFilter(false);
		$fileSyncsResult = FileSyncPeer::doSelect($criteria);
		FileSyncPeer::setUseCriteriaFilter(true);
		
		if (!empty($fileSyncsResult))
			$fileSyncs = array_merge ($fileSyncs , $fileSyncsResult);
	}
	return $fileSyncs;

}

function getVersionBySubType($uiConf , $subType)
{
	switch($subType){
		case uiConf::FILE_SYNC_UICONF_SUB_TYPE_DATA:
			return $uiConf->getConfFileVersion();
		case uiConf::FILE_SYNC_UICONF_SUB_TYPE_FEATURES:
			return $uiConf->getConfFileFeaturesVersion();
		case uiConf::FILE_SYNC_UICONF_SUB_TYPE_CONFIG:
			return $uiConf->getVersion();
	}
}

if($argc != 4)
{
	KalturaLog::DEBUG("Usage: [UIConf file name] [partnerId] [realRun]");
	die("Not enough parameters" . PHP_EOL);
}

if(!file_exists($argv[1]))
	die('problems with file' . PHP_EOL);

//should the script save() ? by default will not save
$dryRun= $argv[3] !== 'realRun';
KalturaStatement::setDryRun($dryRun);
if ($dryRun)
		KalturaLog::debug('dry run --- in order to save, give real_run as a second parameter');

$UIConffile = $argv[1];
$uiConfIds = file($UIConffile);
$uiconfIds = array_map('trim',$uiConfIds);

$partnerId = $argv[2];

foreach ($uiConfIds as $uiConfId)
{
	uiConfPeer::setUseCriteriaFilter(false);
	$dbUiConf = uiConfPeer::retrieveByPK($uiConfId);
	uiConfPeer::setUseCriteriaFilter(true);
	
	if (!$dbUiConf)
	{
		KalturaLog::debug("ERR1 - ui conf id " . $uiConfId . " not found");
		continue;
	}

	if ($dbUiConf->getPartnerId() != $partnerId)
	{
		KalturaLog::debug("ERR2 - ui conf id " . $uiConfId . " belongs to a different partner");
		continue;
	}
	
	$fileSyncs = getFileSyncs($dbUiConf);

	foreach ( $fileSyncs as $fileSync)
	{
		$fileSyncId = $fileSync->getId();
		KalturaLog::debug('saving file sync ID: ' . $fileSyncId);
		$fileSync->setStatus(FileSync::FILE_SYNC_STATUS_READY);
		$fileSync->save();
	}
	
	KalturaLog::debug('saving UIConf ID: ' . $uiConfId);
	$dbUiConf->setStatus(uiConf::UI_CONF_STATUS_READY);
	$dbUiConf->save();
	KEventsManager::flushEvents();
}

