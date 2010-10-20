<?php

$partnerId = null;
if($argc > 1)
	$partnerId = $argv[1];

set_time_limit(0);
ini_set("memory_limit","1024M");

define('ROOT_DIR', realpath(dirname(__FILE__) . '/../../'));
require_once(ROOT_DIR . '/infra/bootstrap_base.php');
require_once(ROOT_DIR . '/infra/KAutoloader.php');

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "metadata", "*"));
KAutoloader::setClassMapFilePath('../cache/classMap.cache');
KAutoloader::register();

error_reporting(E_ALL);
KalturaLog::setLogger(new KalturaStdoutLogger());

date_default_timezone_set(kConf::get('date_default_timezone'));

$dbConf = kConf::getDB();
DbManager::setConfig($dbConf);
DbManager::initialize();

$entryCriteria = new Criteria();
$entryCriteria->add(entryPeer::STATUS, entry::ENTRY_STATUS_READY);
$entryCriteria->add(entryPeer::MEDIA_TYPE, array(entry::ENTRY_MEDIA_TYPE_AUDIO, entry::ENTRY_MEDIA_TYPE_VIDEO), Criteria::IN);
$entryCriteria->add(entryPeer::LENGTH_IN_MSECS, 0);
if(!is_null($partnerId))
	$entryCriteria->add(entryPeer::PARTNER_ID, $partnerId);
//$entryCriteria->setLimit(5000);
$entryCriteria->clearSelectColumns();
$entryCriteria->addSelectColumn(entryPeer::ID);
	
$rs = entryPeer::doSelectStmt($entryCriteria);
$entries = $rs->fetchAll(PDO::FETCH_COLUMN);

$flavorAssetCriteria = new Criteria();
$flavorAssetCriteria->add(flavorAssetPeer::STATUS, flavorAsset::FLAVOR_ASSET_STATUS_READY);
$flavorAssetCriteria->clearSelectColumns();
$flavorAssetCriteria->addSelectColumn(flavorAssetPeer::ID);
$saved = 0;
foreach($entries as $entryId)
{
	entryPeer::clearInstancePool();
	flavorAssetPeer::clearInstancePool();
	mediaInfoPeer::clearInstancePool();
	
	$c = clone $flavorAssetCriteria;
	$c->add(flavorAssetPeer::ENTRY_ID, $entryId);
	
	$rs = flavorAssetPeer::doSelectStmt($c);
	$flavorAssets = $rs->fetchAll(PDO::FETCH_COLUMN);;
	if(!count($flavorAssets))
		continue;
		
	$entry = entryPeer::retrieveByPK($entryId);
	if(!$entry)
		continue;
	
	$criteria = new Criteria();
	$criteria->add(mediaInfoPeer::FLAVOR_ASSET_ID, $flavorAssets, Criteria::IN);
	$criteria->addDescendingOrderByColumn(mediaInfoPeer::ID);

	$mediaInfos = mediaInfoPeer::doSelect($criteria);
	foreach($mediaInfos as $mediaInfo)
	{
		if($entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_VIDEO && $mediaInfo->getVideoDuration())
		{
			$entry->setLengthInMsecs($mediaInfo->getVideoDuration());
			$entry->save();
			$saved++;
			break;
		}
	
		if($entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_AUDIO && $mediaInfo->getAudioDuration())
		{
			$entry->setLengthInMsecs($mediaInfo->getAudioDuration());
			$entry->save();
			$saved++;
			break;
		}
	
		if($mediaInfo->getContainerDuration())
		{
			$entry->setLengthInMsecs($mediaInfo->getContainerDuration());
			$entry->save();
			$saved++;
			break;
		}
	}
}

KalturaLog::info(count($entries) . " entries handled, $saved fixed");