<?php

$partnerId = null;
if($argc > 1)
	$partnerId = $argv[1];

require_once(__DIR__ . '/../bootstrap.php');

$entryCriteria = new Criteria();
$entryCriteria->add(entryPeer::STATUS, entry::ENTRY_STATUS_READY);
$entryCriteria->add(entryPeer::MEDIA_TYPE, array(entry::ENTRY_MEDIA_TYPE_AUDIO, entry::ENTRY_MEDIA_TYPE_VIDEO), Criteria::IN);
$entryCriteria->add(entryPeer::LENGTH_IN_MSECS, array(0, null), Criteria::IN);
if(!is_null($partnerId))
	$entryCriteria->add(entryPeer::PARTNER_ID, $partnerId);
$entryCriteria->setLimit(8000);
$entryCriteria->clearSelectColumns();
$entryCriteria->addSelectColumn(entryPeer::ID);

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
$rs = entryPeer::doSelectStmt($entryCriteria, $con);
$entries = $rs->fetchAll(PDO::FETCH_COLUMN);

$saved = 0;
foreach($entries as $entryId)
{
	entryPeer::clearInstancePool();
	assetPeer::clearInstancePool();
	mediaInfoPeer::clearInstancePool();
	
	$flavorAssetCriteria = new Criteria();
	$flavorAssetCriteria->add(assetPeer::STATUS, flavorAsset::FLAVOR_ASSET_STATUS_READY);
	$flavorAssetCriteria->clearSelectColumns();
	$flavorAssetCriteria->addSelectColumn(assetPeer::ID);
	$flavorAssetCriteria->add(assetPeer::ENTRY_ID, $entryId);
	
	$rs = assetPeer::doSelectStmt($flavorAssetCriteria, $con);
	$flavorAssets = $rs->fetchAll(PDO::FETCH_COLUMN);;
	if(!count($flavorAssets))
		continue;
		
	$criteria = new Criteria();
	$criteria->add(mediaInfoPeer::FLAVOR_ASSET_ID, $flavorAssets, Criteria::IN);
	$criteria->addDescendingOrderByColumn(mediaInfoPeer::ID);

	$mediaInfos = mediaInfoPeer::doSelect($criteria, $con);
	if(!count($mediaInfos))
		continue;
	
	$entry = entryPeer::retrieveByPK($entryId, $con);
	if(!$entry)
		continue;
	
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