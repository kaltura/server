<?php


class kReplacementHelper
{
	const KLOCK_REPLACE_ENTRY_GRAB_TIMEOUT = 5;
	const KLOCK_REPLACE_ENTRY_HOLD_TIMEOUT = 0.5;

	/**
	 * creates a mapping between the new replacing entry assets paramsId and their type to the asset itself
	 *
	 * @param $replacingEntryAssets
	 * @return array
	 */
	public static function buildAssetsToCopyMap($replacingEntryAssets)
	{
		$assetsToCopyMap = array();
		foreach($replacingEntryAssets as $replacingEntryAsset)
		{
			if(!$replacingEntryAsset->shouldCopyOnReplacement())
			{
				KalturaLog::info("Asset defined to not copy on replacement, not adding new asset [{$replacingEntryAsset->getId()}] of type [{$replacingEntryAsset->getType()}]");
				continue;
			}

			//If doesn't exist - create a new array for the current asset's type.
			if (!isset($assetsToCopyMap[$replacingEntryAsset->getType()]))
			{
				$assetsToCopyMap[$replacingEntryAsset->getType()] = array();
			}

			if($replacingEntryAsset->getFlavorParamsId() || $replacingEntryAsset instanceof flavorAsset)
			{
				$assetsToCopyMap[$replacingEntryAsset->getType()][$replacingEntryAsset->getFlavorParamsId()] = $replacingEntryAsset;
				KalturaLog::info("Added new asset [" . $replacingEntryAsset->getId() . "] for asset params [" . $replacingEntryAsset->getFlavorParamsId() . "]");
			}
			else
			{
				$assetsToCopyMap[$replacingEntryAsset->getType()]['asset_' . count($assetsToCopyMap[$replacingEntryAsset->getType()])] = $replacingEntryAsset;
				KalturaLog::info("Added new asset [" . $replacingEntryAsset->getId() . "] with no asset params");
			}
		}

		return $assetsToCopyMap;
	}

	/**
	 * handle flavors switch from old replaced entry flavors to new replacing entry flavors
	 *
	 * @param $replacedEntryAssets
	 * @param $replacingEntryAssets
	 * @param $defaultThumbAssetOld
	 * @param $defaultThumbAssetNew
	 * @param $replacingEntry
	 * @return array
	 * @throws PropelException
	 */
	public static function relinkReplacingEntryAssetsToReplacedEntryAssets($replacedEntryAssets, &$replacingEntryAssets, &$defaultThumbAssetOld, &$defaultThumbAssetNew, $replacingEntry)
	{
		$existingReadyAssetIds = array();
		$existingNonReadyAssetIds = array();

		foreach($replacedEntryAssets as $replacedEntryAsset)
		{
			/* @var $replacedEntryAsset asset */

			//If the replacing newAssets map contains an asset of the same type and paramsId as the current old replaced entry asset,
			// re-link the old asset to the new asset.
			if(isset($replacingEntryAssets[$replacedEntryAsset->getType()]) && isset($replacingEntryAssets[$replacedEntryAsset->getType()][$replacedEntryAsset->getFlavorParamsId()]))
			{
				$newReplacingAsset = $replacingEntryAssets[$replacedEntryAsset->getType()][$replacedEntryAsset->getFlavorParamsId()];
				if ( $replacedEntryAsset->hasTag(assetParams::TAG_RECORDING_ANCHOR) )
				{
					$newReplacingAsset->addTags(array(assetParams::TAG_RECORDING_ANCHOR));
				}

				self::relinkAsset($replacedEntryAsset, $newReplacingAsset);
				$existingReadyAssetIds[] = $replacedEntryAsset->getId();

				unset($replacingEntryAssets[$replacedEntryAsset->getType()][$replacedEntryAsset->getFlavorParamsId()]);


				if ($replacedEntryAsset->hasTag(thumbParams::TAG_DEFAULT_THUMB))
				{
					$defaultThumbAssetNew = $replacedEntryAsset;
					KalturaLog::info("Nominating ThumbAsset [".$replacedEntryAsset->getId()."] as the default ThumbAsset after replacement");
				}
			}
			elseif($replacedEntryAsset instanceof flavorAsset || $replacedEntryAsset instanceof thumbAsset)
			{
				$newReplacingAsset = self::getNonReadyReplacingFlavor($replacingEntry, $replacedEntryAsset->getFlavorParamsId(), $replacedEntryAsset->getType());

				if($replacedEntryAsset instanceof thumbAsset && $replacedEntryAsset->keepOnEntryReplacement())
				{
					KalturaLog::info("KeepManualThumbnails ind is set, manual thumbnail is not deleted [" . $replacedEntryAsset->getId() . "]");
					if($replacedEntryAsset->hasTag(thumbParams::TAG_DEFAULT_THUMB))
					{
						$defaultThumbAssetOld = $replacedEntryAsset;
					}
				}
				// handle flavors that exist in the original replaced entry and have corresponding non ready asset on the replacing entry
				elseif ($newReplacingAsset)
				{
					KalturaLog::info("new asset with paramsId [" . $replacedEntryAsset->getFlavorParamsId() . "] and asset id: [" . $newReplacingAsset->getId() . "] still being created on replacing entry");
					$existingNonReadyAssetIds[] = $replacedEntryAsset->getId();
					self::syncReplacedAssetFields($replacedEntryAsset, $newReplacingAsset);
				}
				elseif(self::shouldDeleteMissingAssetDuringReplacement($replacedEntryAsset))
				{
					KalturaLog::info("Delete old asset [" . $replacedEntryAsset->getId() . "] for paramsId [" . $replacedEntryAsset->getFlavorParamsId() . "]");
					$replacedEntryAsset->setStatus(flavorAsset::ASSET_STATUS_DELETED);
					$replacedEntryAsset->setDeletedAt(time());
					$replacedEntryAsset->save();
				}
			}
		}

		return array($existingReadyAssetIds, $existingNonReadyAssetIds);
	}

	/**
	 * copy new replacing assets to replaced entry and nominate thumb asset
	 *
	 * @param $replaceEntry
	 * @param $newReplacingAssets
	 * @param $defaultThumbAssetNew
	 * @return array
	 */
	public static function copyReplacingAssetsToReplacedEntry($replaceEntry, $newReplacingAssets, &$defaultThumbAssetNew)
	{
		$copiedAssets = array();
		foreach($newReplacingAssets as $newAssetsByTypes)
		{
			foreach ($newAssetsByTypes as $newAsset)
			{
				$createdAsset = $newAsset->copyToEntry($replaceEntry->getId(), $replaceEntry->getPartnerId());
				KalturaLog::info("Copied from new asset [" . $newAsset->getId() . "] to copied asset [" . $createdAsset->getId() . "] for flavor [" . $newAsset->getFlavorParamsId() . "]");
				$copiedAssets[] = $createdAsset->getId();

				if ($createdAsset->hasTag(thumbParams::TAG_DEFAULT_THUMB))
				{
					$defaultThumbAssetNew = $newAsset;
					KalturaLog::info("Nominating ThumbAsset [".$newAsset->getId()."] as the default ThumbAsset after replacement");
				}
			}
		}
		return $copiedAssets;
	}

	/**
	 * replace the default thumb for the original replaced entry
	 *
	 * @param $defaultThumbAssetOld
	 * @param $defaultThumbAssetNew
	 * @param $replacedEntry
	 * @param $replacingEntry
	 * @param bool $linkToReplacing
	 * @throws kCoreException
	 */
	public static function handleThumbReplacement($defaultThumbAssetOld, $defaultThumbAssetNew, $replacedEntry, $replacingEntry, $linkToReplacing = true)
	{
		if($defaultThumbAssetOld)
		{
			KalturaLog::info("Keeping ThumbAsset [". $defaultThumbAssetOld->getId() ."] as the default ThumbAsset");
		}
		elseif ($defaultThumbAssetNew)
		{
			kBusinessConvertDL::setAsDefaultThumbAsset($defaultThumbAssetNew);
			KalturaLog::info("Setting ThumbAsset [". $defaultThumbAssetNew->getId() ."] as the default ThumbAsset");
		}
		elseif($linkToReplacing)
		{
			self::linkDefaultThumbToReplacingEntryThumb($replacedEntry, $replacingEntry);
		}
	}

	/**
	 * updates the fields of the original replaced entry to reflect the end of the replacement
	 *
	 * @param $replacedEntry
	 * @param $replacingEntry
	 */
	public static function updateReplacedEntryFields($replacedEntry, $replacingEntry)
	{
		$replacedEntry->setDimensions($replacingEntry->getWidth(), $replacingEntry->getHeight());
		$replacedEntry->setLengthInMsecs($replacingEntry->getLengthInMsecs());
		$replacedEntry->setConversionProfileId($replacingEntry->getConversionProfileId());
		$replacedEntry->setConversionQuality($replacingEntry->getConversionQuality());
		$replacedEntry->setReplacingEntryId(null);
		$replacedEntry->setReplacementStatus(entryReplacementStatus::NONE);
		$replacedEntry->setReplacementOptions(null);
		$replacedEntry->setStatus($replacingEntry->getStatus());
		$replacedEntry->save();
	}

	/**
	 * create link between the file sync of the new replacing entry asset to the old replaced entry asset
	 *
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
	 * create the ism manifest between the replacing entry and the replaced entry
	 *
	 * @param $replacingEntry
	 * @param $replacedEntry
	 */
	public static function createIsmManifestFileSyncLinkFromReplacingEntry($replacingEntry, $replacedEntry)
	{
		$tempEntryIsmSyncKey = $replacingEntry->getSyncKey(kEntryFileSyncSubType::ISM);
		$tempEntryIsmcSyncKey = $replacingEntry->getSyncKey(kEntryFileSyncSubType::ISMC);
		if(kFileSyncUtils::fileSync_exists($tempEntryIsmSyncKey) && kFileSyncUtils::fileSync_exists($tempEntryIsmcSyncKey))
		{
			$ismVersion = $replacedEntry->incrementIsmVersion();
			$realEntryIsmSyncKey = $replacedEntry->getSyncKey(kEntryFileSyncSubType::ISM, $ismVersion);
			kFileSyncUtils::createSyncFileLinkForKey($realEntryIsmSyncKey, $tempEntryIsmSyncKey);
			$realEntryIsmcSyncKey = $replacedEntry->getSyncKey(kEntryFileSyncSubType::ISMC, $ismVersion);
			kFileSyncUtils::createSyncFileLinkForKey($realEntryIsmcSyncKey, $tempEntryIsmcSyncKey);
		}
	}

	/**
	 * check if we should keep the old asset on the replaced entry
	 *
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
	 * add track entry record for the replacement
	 *
	 * @param $replacedEntry
	 * @param $replacingEntry
	 * @param $existingReadyAssetIds
	 * @param $existingNonReadyAssetIds
	 * @param $nonExistingReadyAssets
	 * @param $nonExistingNonReadyAssets
	 */
	public static function addTrackEntryReplacedEntryEvent($replacedEntry, $replacingEntry, $existingReadyAssetIds, $existingNonReadyAssetIds, $nonExistingReadyAssets, $nonExistingNonReadyAssets)
	{
		$te = new TrackEntry();
		$te->setTrackEventTypeId(TrackEntry::TRACK_ENTRY_EVENT_TYPE_REPLACED_ENTRY);
		$te->setEntryId($replacedEntry->getId());
		$te->setParam1Str($replacingEntry->getId());
		$te->setDescription(__METHOD__ . "[" . __LINE__ . "]");
		$te->setChangedProperties("existingReadyAssetIds: ". implode(", ", $existingReadyAssetIds) . "\n" .
									"existingNonReadyAssetIds: ". implode(", ", $existingNonReadyAssetIds) . "\n" .
									"nonOriginallyExistingReadyAssets: ". implode(", ", $nonExistingReadyAssets) . "\n" .
									"nonOriginallyExistingNonReadyAssets: ". implode(", ", $nonExistingNonReadyAssets));
		TrackEntry::addTrackEntry($te);
	}

	/**
	 * copy remaining ready asset from replacing entry to replaced entry
	 *
	 * @param $replacingEntryAsset
	 * @throws PropelException
	 * @throws kCoreException
	 */
	public static function copyReadyReplacingEntryAssetToReplacedEntry($replacingEntryAsset)
	{
		$defaultThumbAssetOld = null;
		$defaultThumbAssetNew = null;

		$replacingEntry = $replacingEntryAsset->getentry();
		if(!$replacingEntry)
		{
			KalturaLog::info("Replacing entry is missing");
			return;
		}

		$replacedEntry = entryPeer::retrieveByPK($replacingEntry->getReplacedEntryId());
		if(!$replacedEntry)
		{
			KalturaLog::info("Replaced entry is missing");
			return;
		}

		KalturaLog::info("Copying flavor [{$replacingEntryAsset->getId()}] from replacing entry [{$replacingEntry->getId()}] to replaced entry [{$replacedEntry->getId()}]");
		$oldAssets = assetPeer::retrieveByEntryIdAndParams($replacedEntry->getId(), $replacingEntryAsset->getFlavorParamsId());
		if($oldAssets)
		{
			$oldAssets = array($oldAssets);
		}
		$newAssets = kReplacementHelper::buildAssetsToCopyMap(array($replacingEntryAsset));
		kReplacementHelper::relinkReplacingEntryAssetsToReplacedEntryAssets($oldAssets, $newAssets, $defaultThumbAssetOld, $defaultThumbAssetNew, $replacingEntry->getId());
		kReplacementHelper::copyReplacingAssetsToReplacedEntry($replacedEntry, $newAssets, $defaultThumbAssetNew);
		kReplacementHelper::handleThumbReplacement($defaultThumbAssetOld, $defaultThumbAssetNew, $replacedEntry, $replacingEntry, false);
		kReplacementHelper::updateReplacedEntryFields($replacedEntry, $replacingEntry);
		kReplacementHelper::exportReadyReplacedFlavors($replacedEntry->getPartnerId(), $replacingEntry->getId(), $oldAssets);
	}

	/**
	 * relink old replaced entry asset to new replacing entry asset
	 *
	 * @param $oldAsset
	 * @param $newAsset
	 * @throws PropelException
	 */
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

	/**
	 * get replacing entry flavor that is not ready at the point of replacement by type, entry ane params id
	 *
	 * @param $replacingEntryId
	 * @param $flavorParamsId
	 * @param $flavorType
	 * @return asset
	 * @throws PropelException
	 */
	public static function getNonReadyReplacingFlavor($replacingEntryId, $flavorParamsId, $flavorType)
	{
		$invalidAssetStatusArray = array(flavorAsset::ASSET_STATUS_DELETED, flavorAsset::ASSET_STATUS_READY, flavorAsset::ASSET_STATUS_EXPORTING);
		$c = new Criteria();
		$c->add(assetPeer::ENTRY_ID, $replacingEntryId);
		$c->add(assetPeer::STATUS, $invalidAssetStatusArray, Criteria::NOT_IN);
		$c->add(assetPeer::FLAVOR_PARAMS_ID, $flavorParamsId, Criteria::EQUAL);
		$c->add(assetPeer::TYPE, $flavorType, Criteria::EQUAL);
		$newAsset = assetPeer::doSelectOne($c);
		return $newAsset;
	}

	/**
	 * sync the basic info fields and status of the ole replaced entry flavor with the ones on the new replacing entry matching asset
	 *
	 * @param $oldAsset
	 * @param $newAsset
	 */
	public static function syncReplacedAssetFields($oldAsset, $newAsset)
	{
		KalturaLog::info("Sync replaced asset [" . $oldAsset->getId() . "] with the fields from temp replacing asset [" . $newAsset->getId() . "]");
		kEventsManager::enableEvents(false);
		$oldAsset->setBitrate($newAsset->getBitrate());
		$oldAsset->setWidth($newAsset->getWidth());
		$oldAsset->setHeight($newAsset->getHeight());
		$oldAsset->setSize($newAsset->getSize());
		$oldAsset->setVideoCodecId($newAsset->getVideoCodecId());
		$oldAsset->setContainerFormat($newAsset->getContainerFormat());
		$oldAsset->setFileExt($newAsset->getFileExt());
		$oldAsset->setFrameRate($newAsset->getFrameRate());
		$oldAsset->setStatus($newAsset->getStatus());
		$oldAsset->save();
		kEventsManager::enableEvents(true);
	}

	/**
	 * link the replaced entry default thumb to the one from the replacing entry
	 *
	 * @param $replacedEntry
	 * @param $replacingEntry
	 */
	public static function linkDefaultThumbToReplacingEntryThumb($replacedEntry, $replacingEntry)
	{
		KalturaLog::info("No default ThumbAsset found for replacing entry [". $replacingEntry->getId() ."]");
		$replacedEntry->setThumbnail(".jpg"); // thumbnailversion++
		$replacedEntry->save();
		$tempEntrySyncKey = $replacingEntry->getSyncKey(kEntryFileSyncSubType::THUMB);
		$realEntrySyncKey = $replacedEntry->getSyncKey(kEntryFileSyncSubType::THUMB);
		kFileSyncUtils::createSyncFileLinkForKey($realEntrySyncKey, $tempEntrySyncKey);
	}

	/**
	 * check if the current flavor is a flavor remaining from the replacement and need to be synced on the replaced entry
	 *
	 * @param $flavorAsset
	 * @param $entryId
	 * @return bool
	 */
	public static function shouldSyncFlavorInfo($flavorAsset, $entryId)
	{
		$entry = entryPeer::retrieveByPkWithoutInstancePooling($entryId);

		if($flavorAsset->getStatus() == asset::ASSET_STATUS_DELETED || !$entry || !$entry->getReplacedEntryId())
		{
			return false;
		}

		if($entry->getSyncFlavorsOnceReady())
		{
			return true;
		}
		else
		{
			$lock = kLock::create('replacement_' . $entry->getReplacedEntryId() . '_' . $entry->getId());
			if ($lock)
			{
				if(!$lock->lock(self::KLOCK_REPLACE_ENTRY_GRAB_TIMEOUT, self::KLOCK_REPLACE_ENTRY_HOLD_TIMEOUT))
				{
					return false;
				}
				$entry = entryPeer::retrieveByPkWithoutInstancePooling($entryId);
				$lock->unlock();
				if($entry->getSyncFlavorsOnceReady())
				{
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * get the matching original replaced flavor by the entry id and flavor params
	 *
	 * @param $replacingEntry
	 * @param $flavorParamsId
	 * @param $type
	 * @return asset
	 * @throws PropelException
	 */
	public static function getOriginalReplacedFlavorByEntryAndFlavorParams($replacingEntry, $flavorParamsId, $type)
	{
		$c = new Criteria();
		$c->add(assetPeer::ENTRY_ID, $replacingEntry->getReplacedEntryId());
		$c->add(assetPeer::FLAVOR_PARAMS_ID, $flavorParamsId, Criteria::EQUAL);
		$c->add(assetPeer::TYPE, $type, Criteria::EQUAL);
		$newAsset = assetPeer::doSelectOne($c);
		return $newAsset;
	}

	/**
	 * get all the non ready assets on the replacing entry that are missing from the replaced entry and needs to be created and copied
	 * from replacing entry to replaced entry during the replacement
	 *
	 * @param $replacingEntryId
	 * @param $replacedEntryId
	 * @return array
	 * @throws PropelException
	 */
	public static function getNonReadyAssetsFromReplacingEntry($replacingEntryId, $replacedEntryId)
	{
		$missingAssets = array();

		$c = new Criteria();
		$c->add(assetPeer::ENTRY_ID, $replacingEntryId);
		$c->add(assetPeer::STATUS, array(asset::ASSET_STATUS_READY, asset::ASSET_STATUS_EXPORTING), Criteria::NOT_IN);
		$assets = assetPeer::doSelect($c);

		// check if the asset doesnt already exist on the replaced entry
		foreach ($assets as $asset)
		{
			$c = new Criteria();
			$c->add(assetPeer::ENTRY_ID, $replacedEntryId);
			$c->add(assetPeer::FLAVOR_PARAMS_ID, $asset->getFlavorParamsId(), Criteria::EQUAL);
			$c->add(assetPeer::TYPE, $asset->getType(), Criteria::EQUAL);
			$originalAsset = assetPeer::doSelectOne($c);
			if(!$originalAsset)
			{
				$missingAssets[] = $asset;
			}
		}
		return $missingAssets;
	}

	/**
	 * handle all the flavors that should be created later on the replaced entry as part of the replacement but currently
	 * missing from it (replacement using different conversion profile)
	 *
	 * @param $replacedEntry
	 * @param $replacingEntry
	 * @param $defaultThumbAssetNew
	 * @return array
	 * @throws PropelException
	 */
	public static function handleReplacingEntryNonReadyAssetsForNewParams($replacedEntry, $replacingEntry, &$defaultThumbAssetNew)
	{
		$newNonReadyAssets = kReplacementHelper::getNonReadyAssetsFromReplacingEntry($replacingEntry->getId(), $replacedEntry->getId());
		$newNonReadyAssetsMap = kReplacementHelper::buildAssetsToCopyMap($newNonReadyAssets);
		return kReplacementHelper::copyReplacingAssetsToReplacedEntry($replacedEntry, $newNonReadyAssetsMap, $defaultThumbAssetNew);
	}

	public static function exportReadyReplacedFlavors($partnerId, $replacingEntryId, $assets)
	{
		$periodicStorageIds = kStorageExporter::getPeriodicStorageProfileIdsByFlag();
		if(!$periodicStorageIds)
		{
			return;
		}

		KalturaLog::info("Found periodic storage profiles, exporting ready flavors");
		$externalStorages = StorageProfilePeer::retrieveAutomaticByPartnerId($partnerId);
		if(!$externalStorages)
		{
			$externalStorages = kStorageExporter::getPeriodicStorageProfiles();
		}
		foreach($externalStorages as $externalStorage)
		{
			if ($externalStorage->triggerFitsReadyAsset($replacingEntryId))
			{
				kStorageExporter::exportMultipleFlavors($assets, $externalStorage);
			}
		}
	}

}
