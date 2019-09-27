<?php


class kReplacementHelper
{

	/**
	 * creates a mapping between the new assets paramsId and their type to the asset itself
	 *
	 * @param $tempAssets
	 * @return array
	 */
	public static function buildNewAssetsMap($tempAssets)
	{
		$newAssets = array();
		foreach($tempAssets as $newAsset)
		{
			if($newAsset->getStatus() != asset::FLAVOR_ASSET_STATUS_READY)
			{
				KalturaLog::info("Do not add new asset [" . $newAsset->getId() . "] to flavor [" . $newAsset->getFlavorParamsId() . "] status [" . $newAsset->getStatus() . "]");
				continue;
			}

			if(!$newAsset->shouldCopyOnReplacement())
			{
				KalturaLog::info("Asset defined to not copy on replacement, not adding new asset [{$newAsset->getId()}] of type [{$newAsset->getType()}]");
				continue;
			}

			//If doesn't exist - create a new array for the current asset's type.
			if (!isset($newAssets[$newAsset->getType()]))
			{
				$newAssets[$newAsset->getType()] = array();
			}

			if($newAsset->getFlavorParamsId() || $newAsset instanceof flavorAsset)
			{
				$newAssets[$newAsset->getType()][$newAsset->getFlavorParamsId()] = $newAsset;
				KalturaLog::info("Added new asset [" . $newAsset->getId() . "] for asset params [" . $newAsset->getFlavorParamsId() . "]");
			}
			else
			{
				$newAssets[$newAsset->getType()]['asset_' . count($newAssets[$newAsset->getType()])] = $newAsset;
				KalturaLog::info("Added new asset [" . $newAsset->getId() . "] with no asset params");
			}
		}

		return $newAssets;
	}

	/**
	 * handle flavors switch from old entry flavors to new replacing entry flavors
	 * return nominates for default thumb asset
	 *
	 * @param $oldAssets
	 * @param $newAssets
	 * @param $defaultThumbAssetOld
	 * @param $defaultThumbAssetNew
	 * @return array
	 * @throws PropelException
	 */
	public static function relinkOldAssetsToNewAssetsFromTempEntry($oldAssets, &$newAssets, &$defaultThumbAssetOld, &$defaultThumbAssetNew, $tempEntryId)
	{
		foreach($oldAssets as $oldAsset)
		{
			/* @var $oldAsset asset */

			//If the newAssets map contains an asset of the same type and paramsId as the current old asset,
			// re-link the old asset to the new asset.
			if(isset($newAssets[$oldAsset->getType()]) && isset($newAssets[$oldAsset->getType()][$oldAsset->getFlavorParamsId()]))
			{
				$newAsset = $newAssets[$oldAsset->getType()][$oldAsset->getFlavorParamsId()];
				if ( $oldAsset->hasTag(assetParams::TAG_RECORDING_ANCHOR) )
				{
					$newAsset->addTags(array(assetParams::TAG_RECORDING_ANCHOR));
				}

				self::relinkAsset($oldAsset, $newAsset);

				unset($newAssets[$oldAsset->getType()][$oldAsset->getFlavorParamsId()]);

				if ($oldAsset->hasTag(thumbParams::TAG_DEFAULT_THUMB))
				{
					$defaultThumbAssetNew = $oldAsset;
					KalturaLog::info("Nominating ThumbAsset [".$oldAsset->getId()."] as the default ThumbAsset after replacent");
				}

			}
			elseif($oldAsset instanceof flavorAsset || $oldAsset instanceof thumbAsset)
			{
				$newAsset = self::getNewConvertingFlavor($tempEntryId, $oldAsset->getFlavorParamsId(), $oldAsset->getType());
				if($oldAsset instanceof thumbAsset && $oldAsset->keepOnEntryReplacement())
				{
					KalturaLog::info("KeepManualThumbnails ind is set, manual thumbnail is not deleted [" . $oldAsset->getId() . "]");
					if($oldAsset->hasTag(thumbParams::TAG_DEFAULT_THUMB))
					{
						$defaultThumbAssetOld = $oldAsset;
					}
				}
				elseif ($newAsset)
				{
					KalturaLog::info("new asset with paramsId [" . $oldAsset->getFlavorParamsId() . "] and asset id: [" . $newAsset->getId() . "] still converting on temp entry");
					self::updateOldFlavorFields($oldAsset, $newAsset);
				}
				elseif(self::shouldDeleteMissingAssetDuringReplacement($oldAsset))
				{
					KalturaLog::info("Delete old asset [" . $oldAsset->getId() . "] for paramsId [" . $oldAsset->getFlavorParamsId() . "]");
					$oldAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_DELETED);
					$oldAsset->setDeletedAt(time());
					$oldAsset->save();
				}
			}
		}

		return array ($newAssets, $defaultThumbAssetOld, $defaultThumbAssetNew);
	}

	/**
	 * copy new assets to entry and nominate thumb asset
	 *
	 * @param $entry
	 * @param $newAssets
	 * @param $defaultThumbAssetNew
	 */
	public static function copyAssetsToOriginalEntry($entry, $newAssets, &$defaultThumbAssetNew)
	{
		foreach($newAssets as $newAssetsByTypes)
		{
			foreach ($newAssetsByTypes as $newAsset)
			{
				$createdAsset = $newAsset->copyToEntry($entry->getId(), $entry->getPartnerId());
				KalturaLog::info("Copied from new asset [" . $newAsset->getId() . "] to copied asset [" . $createdAsset->getId() . "] for flavor [" . $newAsset->getFlavorParamsId() . "]");

				if ($createdAsset->hasTag(thumbParams::TAG_DEFAULT_THUMB))
				{
					$defaultThumbAssetNew = $newAsset;
					KalturaLog::info("Nominating ThumbAsset [".$newAsset->getId()."] as the default ThumbAsset after replacent");
				}
			}
		}
	}

	/**
	 * replace the default thumb for the original entry
	 *
	 * @param $defaultThumbAssetOld
	 * @param $defaultThumbAssetNew
	 * @param $entry
	 * @param $tempEntry
	 * @throws kCoreException
	 */
	public static function handleThumbReplacement($defaultThumbAssetOld, $defaultThumbAssetNew, $entry, $tempEntry)
	{
		if($defaultThumbAssetOld)
		{
			KalturaLog::info("Kepping ThumbAsset [". $defaultThumbAssetOld->getId() ."] as the default ThumbAsset");
		}
		elseif ($defaultThumbAssetNew)
		{
			kBusinessConvertDL::setAsDefaultThumbAsset($defaultThumbAssetNew);
			KalturaLog::info("Setting ThumbAsset [". $defaultThumbAssetNew->getId() ."] as the default ThumbAsset");
		}
		else
		{
			KalturaLog::info("No default ThumbAsset found for replacing entry [". $tempEntry->getId() ."]");
			$entry->setThumbnail(".jpg"); // thumbnailversion++
			$entry->save();
			$tempEntrySyncKey = $tempEntry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB);
			$realEntrySyncKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB);
			kFileSyncUtils::createSyncFileLinkForKey($realEntrySyncKey, $tempEntrySyncKey);
		}
	}

	/**
	 * updates the fields for the original entry to reflect the end of the replacement
	 *
	 * @param $entry
	 * @param $tempEntry
	 */
	public static function updateOriginalEntryFields($entry, $tempEntry)
	{
		$entry->setDimensions($tempEntry->getWidth(), $tempEntry->getHeight());
		$entry->setLengthInMsecs($tempEntry->getLengthInMsecs());
		$entry->setConversionProfileId($tempEntry->getConversionProfileId());
		$entry->setConversionQuality($tempEntry->getConversionQuality());
		$entry->setReplacingEntryId(null);
		$entry->setReplacementStatus(entryReplacementStatus::NONE);
		$entry->setReplacementOptions(null);
		$entry->setStatus($tempEntry->getStatus());
		$entry->save();
	}

	/**
	 * @param $oldAsset
	 * @param $newAsset
	 * @param $fileSyncSubType
	 */
	protected static function createFileSyncLinkFromReplacingAsset($oldAsset, $newAsset, $fileSyncSubType)
	{
		$oldFileSync = $oldAsset->getSyncKey($fileSyncSubType);
		$newFileSync = $newAsset->getSyncKey($fileSyncSubType);
		if(kFileSyncUtils::fileSync_exists($newFileSync))
			kFileSyncUtils::createSyncFileLinkForKey($oldFileSync, $newFileSync);
	}

	/**
	 * @param $tempEntry
	 * @param $realEntry
	 */
	public static function createIsmManifestFileSyncLinkFromReplacingEntry($tempEntry, $realEntry)
	{
		$tempEntryIsmSyncKey = $tempEntry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ISM);
		$tempEntryIsmcSyncKey = $tempEntry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ISMC);
		if(kFileSyncUtils::fileSync_exists($tempEntryIsmSyncKey) && kFileSyncUtils::fileSync_exists($tempEntryIsmcSyncKey))
		{
			$ismVersion = $realEntry->incrementIsmVersion();
			$realEntryIsmSyncKey = $realEntry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ISM, $ismVersion);
			kFileSyncUtils::createSyncFileLinkForKey($realEntryIsmSyncKey, $tempEntryIsmSyncKey);
			$realEntryIsmcSyncKey = $realEntry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ISMC, $ismVersion);
			kFileSyncUtils::createSyncFileLinkForKey($realEntryIsmcSyncKey, $tempEntryIsmcSyncKey);
		}
	}

	/**
	 * @param $oldAsset
	 * @return bool
	 */
	protected static function shouldDeleteMissingAssetDuringReplacement($oldAsset)
	{
		// In case of live recording entry Don't drop the old asset
		if($oldAsset instanceof flavorAsset && $oldAsset->getKeepOldAssetOnEntryReplacement())
			return false;

		return true;
	}

	/**
	 * @param $entry
	 * @param $tempEntry
	 */
	public static function addTrackEntryReplacedEntryEvent($entry, $tempEntry)
	{
		$te = new TrackEntry();
		$te->setTrackEventTypeId(TrackEntry::TRACK_ENTRY_EVENT_TYPE_REPLACED_ENTRY);
		$te->setEntryId($entry->getId());
		$te->setParam1Str($tempEntry->getId());
		$te->setDescription(__METHOD__ . "[" . __LINE__ . "]");
		TrackEntry::addTrackEntry($te);
	}

	public static function addRemainingFlavorToOriginalEntry($currentFlavorAsset)
	{
		$defaultThumbAssetOld = null;
		$defaultThumbAssetNew = null;
		KalturaLog::info("Copying flavor [{$currentFlavorAsset->getId()}]");
		$tempEntry = $currentFlavorAsset->getentry();
		$originalEntry = entryPeer::retrieveByPK($tempEntry->getReplacedEntryId());
		$oldAssets = assetPeer::retrieveByEntryIdAndParams($originalEntry->getId(), $currentFlavorAsset->getFlavorParamsId());
		$oldAssets = array($oldAssets);
		$newAssets = kReplacementHelper::buildNewAssetsMap(array($currentFlavorAsset));
		kReplacementHelper::relinkOldAssetsToNewAssetsFromTempEntry($oldAssets, $newAssets, $defaultThumbAssetOld, $defaultThumbAssetNew);
		kReplacementHelper::copyAssetsToOriginalEntry($originalEntry, $newAssets, $defaultThumbAssetNew);
	}

	public static function relinkAsset($oldAsset, $newAsset)
	{
		/* @var $newAsset asset */
		KalturaLog::info("Create link from new asset [" . $newAsset->getId() . "] to old asset [" . $oldAsset->getId() . "] for flavor [" . $oldAsset->getFlavorParamsId() . "]");

		$oldAsset->linkFromAsset($newAsset);
		$oldAsset->save();

		self::createFileSyncLinkFromReplacingAsset($oldAsset, $newAsset, asset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		self::createFileSyncLinkFromReplacingAsset($oldAsset, $newAsset, asset::FILE_SYNC_ASSET_SUB_TYPE_ISM);
		self::createFileSyncLinkFromReplacingAsset($oldAsset, $newAsset, asset::FILE_SYNC_ASSET_SUB_TYPE_ISMC);
		self::createFileSyncLinkFromReplacingAsset($oldAsset, $newAsset, asset::FILE_SYNC_ASSET_SUB_TYPE_MPD);

		$newFlavorMediaInfo = mediaInfoPeer::retrieveByFlavorAssetId($newAsset->getId());
		if($newFlavorMediaInfo)
		{
			$oldFlavorNewMediaInfo = $newFlavorMediaInfo->copy();
			$oldFlavorNewMediaInfo->setFlavorAssetId($oldAsset->getId());
			$oldFlavorNewMediaInfo->setFlavorAssetVersion($oldAsset->getVersion());
			$oldFlavorNewMediaInfo->save();
		}
	}

	public static function getNewConvertingFlavor($tempEntryId, $flavorParamsId, $flavorType)
	{
		$invalidAssetStatusArray = array(flavorAsset::ASSET_STATUS_DELETED, flavorAsset::ASSET_STATUS_ERROR, flavorAsset::ASSET_STATUS_NOT_APPLICABLE, flavorAsset::ASSET_STATUS_READY);
		$c = new Criteria();
		$c->add(assetPeer::ENTRY_ID, $tempEntryId);
		$c->add(assetPeer::STATUS, $invalidAssetStatusArray, Criteria::NOT_IN);
		$c->add(assetPeer::FLAVOR_PARAMS_ID, $flavorParamsId, Criteria::EQUAL);
		$c->add(assetPeer::TYPE, $flavorType, Criteria::EQUAL);
		$newAsset = assetPeer::doSelectOne($c);
		return $newAsset;
	}

	public static function updateOldFlavorFields($oldAsset, $newAsset)
	{
		$oldAsset->setBitrate($newAsset->getBitrate());
		$oldAsset->setWidth($newAsset->getWidth());
		$oldAsset->setHeight($newAsset->getHeight());
		$oldAsset->setSize($newAsset->getSize());
		$oldAsset->setVideoCodecId($newAsset->getVideoCodecId());
		$oldAsset->setContainerFormat($newAsset->getContainerFormat());
		$oldAsset->setFileExt($newAsset->getFileExt());
		$oldAsset->setFrameRate($newAsset->getFrameRate());
		$oldAsset->setStatus(flavorAsset::ASSET_STATUS_CONVERTING);
		$oldAsset->save();
	}


}