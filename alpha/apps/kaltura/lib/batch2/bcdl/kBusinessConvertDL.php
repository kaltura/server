<?php

class kBusinessConvertDL
{


	private static function shouldDeleteMissingAssetDuringReplacement($oldAsset)
	{		
		// In case of live recording entry Don't drop the old asset
		if($oldAsset instanceof flavorAsset && $oldAsset->getKeepOldAssetOnEntryReplacement())
			return false;
	
		return true;
	}

	/**
	 * @param entry $entry
	 * @param entry $tempEntry
	 */
	public static function replaceEntry(entry $entry, entry $tempEntry = null)
	{
		if(!$tempEntry)
			$tempEntry = entryPeer::retrieveByPK($entry->getReplacingEntryId());

		if(!$tempEntry)
		{
			KalturaLog::err("Temp entry id [" . $entry->getReplacingEntryId() . "] not found");
			return;
		}
		//Extract all assets of the temp entry
		$tempAssets = assetPeer::retrieveByEntryId($tempEntry->getId());


		//Extract all assets of the existing entry
		$oldAssets = assetPeer::retrieveByEntryId($entry->getId());
		$newAssets = array();

		//Loop which creates a mapping between the new assets' paramsId and their type to the asset itself
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

		$defaultThumbAssetNew = null;
		$defaultThumbAssetOld = null;
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
				unset($newAssets[$oldAsset->getType()][$oldAsset->getFlavorParamsId()]);

				if ($oldAsset->hasTag(thumbParams::TAG_DEFAULT_THUMB))
				{
					$defaultThumbAssetNew = $oldAsset;
					KalturaLog::info("Nominating ThumbAsset [".$oldAsset->getId()."] as the default ThumbAsset after replacent");
				}

			}
			elseif($oldAsset instanceof flavorAsset || $oldAsset instanceof thumbAsset)
			{
				if($oldAsset instanceof thumbAsset && $oldAsset->keepOnEntryReplacement())
				{
					KalturaLog::info("KeepManualThumbnails ind is set, manual thumbnail is not deleted [" . $oldAsset->getId() . "]");
					if($oldAsset->hasTag(thumbParams::TAG_DEFAULT_THUMB))
					{
						$defaultThumbAssetOld = $oldAsset;
					}
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

		self::createIsmManifestFileSyncLinkFromReplacingEntry($tempEntry, $entry);
		
		$entry->setDimensions($tempEntry->getWidth(), $tempEntry->getHeight());
		$entry->setLengthInMsecs($tempEntry->getLengthInMsecs());
		$entry->setConversionProfileId($tempEntry->getConversionProfileId());
		$entry->setConversionQuality($tempEntry->getConversionQuality());
		$entry->setReplacingEntryId(null);
		$entry->setReplacementStatus(entryReplacementStatus::NONE);
		$entry->setReplacementOptions(null);
		$entry->setStatus($tempEntry->getStatus());
		$entry->save();

		//flush deffered events to re-index sphinx before temp entry deletion
		kEventsManager::flushEvents();

		kBusinessConvertDL::checkForPendingLiveClips($entry);
		kEventsManager::raiseEvent(new kObjectReplacedEvent($entry, $tempEntry));

		myEntryUtils::deleteEntry($tempEntry,null,true);

		$te = new TrackEntry();
		$te->setTrackEventTypeId(TrackEntry::TRACK_ENTRY_EVENT_TYPE_REPLACED_ENTRY);
		$te->setEntryId($entry->getId());
		$te->setParam1Str($tempEntry->getId());
		$te->setDescription(__METHOD__ . "[" . __LINE__ . "]");
		TrackEntry::addTrackEntry($te);
	}

	public static function checkForPendingLiveClips(entry $entry)
	{
		if($entry->getSource() != EntrySourceType::RECORDED_LIVE)
		{
			KalturaLog::notice("Entry [" . $entry->getId() . "] is not a recorded live");
			return;
		}
	
		$liveEntry = entryPeer::retrieveByPKNoFilter($entry->getRootEntryId());
		if(!$liveEntry || $liveEntry->getStatus() == entryStatus::DELETED || !($liveEntry instanceof LiveEntry))
		{
			KalturaLog::notice("Entry root [" . $entry->getRootEntryId() . "] is not a valid live entry");
			return;
		}
		/* @var $liveEntry LiveEntry */
		
		$pendingMediaEntries = $liveEntry->getAttachedPendingMediaEntries();
		foreach($pendingMediaEntries as $pendingMediaEntry)
		{
			/* @var $pendingMediaEntry kPendingMediaEntry */
			
			if($pendingMediaEntry->getRequiredDuration() && $pendingMediaEntry->getRequiredDuration() > $entry->getLengthInMsecs())
			{
				KalturaLog::info("Pending entry [" . $pendingMediaEntry->getEntryId() . "] required duration [" . $pendingMediaEntry->getRequiredDuration() . "] while entry duration [" . $entry->getLengthInMsecs() . "] is too short");
				continue;
			}
			$liveEntry->dettachPendingMediaEntry($pendingMediaEntry->getEntryId());
			
			$pendingEntry = entryPeer::retrieveByPK($pendingMediaEntry->getEntryId());
			if(!$pendingEntry)
			{
				KalturaLog::info("Pending entry [" . $pendingMediaEntry->getEntryId() . "] not found");
				continue;
			}
			
			$sourceAsset = assetPeer::retrieveOriginalByEntryId($entry->getId());
 			if(!$sourceAsset)
 			{
 				$sourceAssets = assetPeer::retrieveReadyFlavorsByEntryId($entry->getId());
 				$sourceAsset = array_pop($sourceAssets);
 			}
			if(!$sourceAsset)
			{
				KalturaLog::info("Pending entry [" . $pendingMediaEntry->getEntryId() . "] source asset not found");
				continue;
			}
 			/* @var $sourceAsset flavorAsset */
 			
 			$operationAttributes = new kClipAttributes();
 			$operationAttributes->setOffset($pendingMediaEntry->getOffset());
 			$operationAttributes->setDuration($pendingMediaEntry->getDuration());
 			
			$targetAsset = assetPeer::retrieveOriginalByEntryId($pendingMediaEntry->getEntryId());
			if(!$targetAsset)
			{
				$targetAsset = kFlowHelper::createOriginalFlavorAsset($entry->getPartnerId(), $pendingMediaEntry->getEntryId());
			}
			$targetAsset->setFileExt($sourceAsset->getFileExt());
			$targetAsset->save();
			
			$sourceSyncKey = $sourceAsset->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
			$targetSyncKey = $targetAsset->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
			
			kFileSyncUtils::createSyncFileLinkForKey($targetSyncKey, $sourceSyncKey);
			
			$errDescription = '';
 			kBusinessPreConvertDL::decideAddEntryFlavor(null, $pendingMediaEntry->getEntryId(), $operationAttributes->getAssetParamsId(), $errDescription, $targetAsset->getId(), array($operationAttributes));
		}
		
		$liveEntry->save();
	}

	private static function createFileSyncLinkFromReplacingAsset($oldAsset, $newAsset, $fileSyncSubType)
	{
		$oldFileSync = $oldAsset->getSyncKey($fileSyncSubType);
		$newFileSync = $newAsset->getSyncKey($fileSyncSubType);
		if(kFileSyncUtils::fileSync_exists($newFileSync))
			kFileSyncUtils::createSyncFileLinkForKey($oldFileSync, $newFileSync);		
	}
	private static function createIsmManifestFileSyncLinkFromReplacingEntry($tempEntry, $realEntry)
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
	public static function setAsDefaultThumbAsset($thumbAsset)
	{
		/* @var $thumbAsset thumbAsset */
		$entry = $thumbAsset->getentry();
		if (!$entry)
			throw new kCoreException("Could not retrieve entry ID [".$thumbAsset->getEntryId()."] from ThumbAsset ID [".$thumbAsset->getId()."]", APIErrors::ENTRY_ID_NOT_FOUND);

		if(!$thumbAsset->hasTag(thumbParams::TAG_DEFAULT_THUMB))
		{
			/* @var $thumbAsset KalturaThumbAsset */
			$thumbAsset->addTags(array(thumbParams::TAG_DEFAULT_THUMB));
			$thumbAsset->save();
			KalturaLog::info("Setting entry [". $thumbAsset->getEntryId() ."] default ThumbAsset to [". $thumbAsset->getId() ."]");
		}

		$entry->setThumbnail(".jpg");
		$entry->setCreateThumb(false, $thumbAsset);
		$entry->save();

		$thumbSyncKey = $thumbAsset->getSyncKey(thumbAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$entrySyncKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB);
		kFileSyncUtils::createSyncFileLinkForKey($entrySyncKey, $thumbSyncKey);
	}

	public static function parseFlavorDescription(flavorParamsOutputWrap $flavor)
	{
		$description = '';
		if(is_array($flavor->_errors) && count($flavor->_errors))
		{
			$errDesc = '';
			foreach($flavor->_errors as $section => $errors)
				$errDesc .= "$section errors: " . join("; ", $errors) . "\n";

			KalturaLog::log("Flavor errors: $errDesc");
			$description .= $errDesc;
		}

		if(is_array($flavor->_warnings) && count($flavor->_warnings))
		{
			$errDesc = '';
			foreach($flavor->_warnings as $section => $errors)
				$errDesc .= "$section warnings: " . join("; ", $errors) . "\n";

			KalturaLog::log("Flavor warnings: $errDesc");
			$description .= $errDesc;
		}
		return $description;
	}

	protected static function isFlavorLower(flavorParams $target, flavorParams $compare)
	{
		// currently check only the bitrate
		return ($target->getVideoBitrate() < $compare->getVideoBitrate());
	}

	public static function filterTagFlavors(array $flavors)
	{
		KalturaLog::log("Filter Tag Flavors, " . count($flavors) . " flavors supplied");

		// check if there is a complete flavor
		$hasComplied = false;
		$hasForced = false;
		$hasCreateAnyway = false;
		$originalFlavorParamsIds = array();
		foreach($flavors as $flavorParamsId => $flavor)
		{
			$originalFlavorParamsIds[] = $flavor->getFlavorParamsId();
			if(!$flavor->_isNonComply)
				$hasComplied = true;

			if($flavor->_force)
				$hasForced = true;

			if($flavor->_create_anyway)
				$hasCreateAnyway = true;
		}

		$originalFlavorParams = array();
		$dbOriginalFlavorParams = assetParamsPeer::retrieveByPKs($originalFlavorParamsIds);
		foreach($dbOriginalFlavorParams as $dbFlavorParams)
			$originalFlavorParams[$dbFlavorParams->getId()] = $dbFlavorParams;

		// return only complete flavors
		if($hasComplied)
			KalturaLog::log("Has complied flavors");
		if($hasForced)
			KalturaLog::log("Has forced flavors");
		if($hasCreateAnyway)
			KalturaLog::log("Has createAnyway flavors");
		if($hasComplied || $hasForced || $hasCreateAnyway)
			return $flavors;

		// find the lowest flavor
		$lowestFlavorParamsId = null;
		foreach($flavors as $flavorParamsId => $flavor)
		{
			if(!$flavor->IsValid())
				continue;

			// is lower than the selected
			if(!isset($originalFlavorParams[$flavor->getFlavorParamsId()]))
				continue;

			$currentOriginalFlavor = $originalFlavorParams[$flavor->getFlavorParamsId()];

			// is first flavor to check
			if(is_null($lowestFlavorParamsId))
			{
				$lowestFlavorParamsId = $flavorParamsId;
				continue;
			}

			$lowestOriginalFlavor = $originalFlavorParams[$flavors[$lowestFlavorParamsId]->getFlavorParamsId()];
			if(self::isFlavorLower($currentOriginalFlavor, $lowestOriginalFlavor))
				$lowestFlavorParamsId = $flavorParamsId;
		}

		if($lowestFlavorParamsId)
		{
			KalturaLog::log("Lowest flavor selected [$lowestFlavorParamsId]");
			$flavors[$lowestFlavorParamsId]->_create_anyway = true;
		}

		return $flavors;
	}

	/**
	 * compareFlavors compares to flavorParamsOutput and decide which should be performed first
	 *
	 * @param flavorParamsOutput $a
	 * @param flavorParamsOutput $b
	 */
	public static function compareFlavors(flavorParamsOutput $a, flavorParamsOutput $b)
	{
		$flavorA = $a->getId();
		$flavorB = $b->getId();

		$isSourceFlavor = self::isSourceFlavor($a, $b);
		if($isSourceFlavor == 1)
		{
			return 1;
		}
		if($isSourceFlavor == -1)
		{
			return -1;
		}

		if($a->getReadyBehavior() == flavorParamsConversionProfile::READY_BEHAVIOR_NO_IMPACT && $b->getReadyBehavior() > flavorParamsConversionProfile::READY_BEHAVIOR_NO_IMPACT)
		{
			return 1;
		}

		if($a->getReadyBehavior() > flavorParamsConversionProfile::READY_BEHAVIOR_NO_IMPACT && $b->getReadyBehavior() == flavorParamsConversionProfile::READY_BEHAVIOR_NO_IMPACT)
		{
			return -1;
		}

		if($a->getReadyBehavior() == flavorParamsConversionProfile::READY_BEHAVIOR_OPTIONAL && $b->getReadyBehavior() == flavorParamsConversionProfile::READY_BEHAVIOR_REQUIRED)
		{
			return 1;
		}

		if($a->getReadyBehavior() == flavorParamsConversionProfile::READY_BEHAVIOR_REQUIRED && $b->getReadyBehavior() == flavorParamsConversionProfile::READY_BEHAVIOR_OPTIONAL)
		{
			return -1;
		}

		if($a->getVideoBitrate() > $b->getVideoBitrate())
		{
			return 1;
		}

		return -1;
	}

	private static function isSourceFlavor(flavorParamsOutput $a, flavorParamsOutput $b)
	{
		$aSources = explode(',', $a->getSourceAssetParamsIds());
		$bSources = explode(',',$b->getSourceAssetParamsIds());

		if(in_array($a->getFlavorParamsId(), $bSources))
		{
			KalturaLog::info('Flavor '.$a->getId().' is source of flavor '.$b->getId());
			return -1;
		}
		if(in_array($b->getFlavorParamsId(), $aSources))
		{
			KalturaLog::info('Flavor '.$b->getId().' is source of flavor '.$a->getId());
			return 1;
		}

		return 0;
	}
	
	public static function decideLiveProfile(LiveEntry $entry)
	{
		// find all live assets of the entry
		$c = new Criteria();
		$c->add(assetPeer::PARTNER_ID, $entry->getPartnerId());
		$c->add(assetPeer::ENTRY_ID, $entry->getId());
		$c->add(assetPeer::TYPE, assetType::LIVE);
		// include deleted assets
		assetPeer::setUseCriteriaFilter(false); 
		$liveAssets = assetPeer::doSelect($c);
		assetPeer::setUseCriteriaFilter(true);
		
		// build array of all assets with asset params id as key
		$liveAssetsParams = array();
		foreach($liveAssets as $liveAsset)
		{
			/* @var $liveAsset liveAsset */
			$flavorParamsId = is_null($liveAsset->getFlavorParamsId()) ? $liveAsset->getId() : $liveAsset->getFlavorParamsId();
			$liveAssetsParams[$flavorParamsId] = $liveAsset;
		}
		
		$flavorParamsConversionProfileArray = flavorParamsConversionProfilePeer::retrieveByConversionProfile($entry->getConversionProfileId());
		
		$liveParamIdsArray = array();
		foreach ($flavorParamsConversionProfileArray as $flavorParamsConversionProfile)
		{
			/* @var $flavorParamsConversionProfile flavorParamsConversionProfile */
			$liveParamIdsArray[] = $flavorParamsConversionProfile->getFlavorParamsId();
		}
			
		asort($liveParamIdsArray);
		$liveParamIds = implode(",", $liveParamIdsArray);
		if($liveParamIds == $entry->getFlavorParamsIds())
			return;
		
		$streamBitrates = array();
		$definedRecordingAnchor = false;
		foreach ($flavorParamsConversionProfileArray as $flavorParamsConversionProfile)
		{
			/* @var $flavorParamsConversionProfile flavorParamsConversionProfile */
			$liveParams = $flavorParamsConversionProfile->getassetParams();
			if($liveParams instanceof liveParams)
			{
				if($flavorParamsConversionProfile->getOrigin() == assetParamsOrigin::INGEST)
				{
					$streamBitrate = array('bitrate' => $liveParams->getVideoBitrate(), 'width' => $liveParams->getWidth(), 'height' => $liveParams->getHeight(), 'tags' => $liveParams->getTags());
					$streamBitrates[] = $streamBitrate;
				}
				
				// check if asset already exists
				if(isset($liveAssetsParams[$liveParams->getId()]))
				{
					$liveAsset = $liveAssetsParams[$liveParams->getId()];
					$liveAsset->setDeletedAt(null);
	
					// remove the asset from the list, the left assets will be deleted later
					unset($liveAssetsParams[$liveParams->getId()]);
				}
				else
				{
					// create a new asset
					$liveAsset = new liveAsset();
					$liveAsset->setType(assetType::LIVE);
					$liveAsset->setPartnerId($entry->getPartnerId());
					$liveAsset->setFlavorParamsId($liveParams->getId());
					$liveAsset->setFromAssetParams($liveParams);
					$liveAsset->setEntryId($entry->getId());
					
					if ($entry->getRecordStatus() && !$definedRecordingAnchor) 
					{
						// We specifically add a flag that does NOT exist on the live asset, since we can't predict which
						// live params the conversion profile is going to contain.
						$liveAsset->addTags(array(assetParams::TAG_RECORDING_ANCHOR));
						$definedRecordingAnchor = true;
					}
					self::setLanguageAndDefault($entry, $liveParams, $liveAsset);
				}
				
				// set the status according to the entry status
				if($entry->getStatus() == entryStatus::READY)
					$liveAsset->setStatus( asset::ASSET_STATUS_READY);
				else
					$liveAsset->setStatus( asset::ASSET_STATUS_IMPORTING);
					
				$liveAsset->save();
			}
		}
		
		// delete all left assets
		foreach($liveAssetsParams as $liveAsset)
		{
			/* @var $liveAsset liveAsset */
			$liveAsset->setDeletedAt(time());
			$liveAsset->setStatus(asset::ASSET_STATUS_DELETED);
			$liveAsset->save();
		}
		
		if(!count($streamBitrates))
		{
			$streamBitrate = array('bitrate' => 900, 'width' => 640, 'height' => 480);
			$streamBitrates[] = $streamBitrate;
		}
		
		$entry->setStreamBitrates($streamBitrates);
		$entry->save();
	}

	public static function generateAdStitchingCmdline($flavorParams, $flavorParamsOutput, $ffprobeJson = null, $duration = null)
	{
		if($ffprobeJson){
			$parser = new KFFMpegMediaParserAdStitchHelper($ffprobeJson);
			$srcMedInf = $parser->getMediaInfo();
			$srcMedSet = KFFMpegMediaParserAdStitchHelper::mediaInfoToKDL($srcMedInf);
			$isAdImage = false;
			$srcContainer = $srcMedSet->_container->_format;
			if(strstr($srcContainer,"image")!==false || strstr($srcContainer,"jpeg")!==false || strstr($srcContainer,"jpg")!==false || strstr($srcContainer,"png")!==false){
				$isAdImage = true;
			}
		}
		/*
		 * Nulled 'ffprobeJson' ==> 'filler-case', create black and silent video
		 */
		else {
			$srcMedSet = new KDLMediaDataSet();
			$isAdImage = false;
		}
		$isAdAudio = isset($srcMedSet->_audio);
		$isAdVideo = isset($srcMedSet->_video);
		/*
		 * For image AD or in 'filler-case',
		 * make sure that the 'duration' is set,
		 * otherwise - set to default 10 sec
		 */
		if(!($duration) && ($isAdImage || !($isAdAudio && $isAdVideo)) ) {
			$duration = 10;
		}

		/**
		 * To match from flavor params output-
		 * - frame size
		 * - fps
		 * - gop
		 * - audio params
		 * Other matching
		 * - AvoidVideoShrinkFramesizeToSource = on
		 * - two pass - off
		 * - encypted - off
		 * - letter boxing
		 * - other extra params (from live sticthing)
		 */
		$kdlFlavor = KDLWrap::ConvertFlavorCdl2Kdl($flavorParams);
		/*
		 * Verify flavor params settings
		 */
		{
			if(!isset($kdlFlavor->_video->_id) || $kdlFlavor->_video->_id==KDLVideoTarget::COPY){
				throw new kCoreException("Flavor params missing correct video settings");
			}
			if(!isset($kdlFlavor->_audio->_id) || $kdlFlavor->_audio->_id==KDLAudioTarget::COPY){
				throw new kCoreException("Flavor params missing correct audio settings");
			}
		}
		$kdlFlavor->_isTwoPass = false;
		$kdlFlavor->_isEncrypted = false;
		$kdlFlavor->_video->_arProcessingMode = 2; // letter boxing
		$kdlFlavor->_video->_isShrinkFramesizeToSource = false;
		$kdlFlavor->_transcoders[0]->_extra.= " -x264opts colorprim=undef:transfer=undef:colormatrix=undef -movflags +faststart";
		if($flavorParamsOutput->getWidth()){
			$kdlFlavor->_video->_width = $flavorParamsOutput->getWidth();
		}
		if($flavorParamsOutput->getHeight()){
			$kdlFlavor->_video->_height = $flavorParamsOutput->getHeight();
		}
		if($flavorParamsOutput->getGopSize()){
			$kdlFlavor->_video->_gop = $flavorParamsOutput->getGopSize();
		}
		if($flavorParamsOutput->getFrameRate()){
			$kdlFlavor->_video->_frameRate = $flavorParamsOutput->getFrameRate();
		}
		if($flavorParamsOutput->getAudioBitrate()){
			$kdlFlavor->_audio->_bitRate = $flavorParamsOutput->getAudioBitrate();
		}
		if($flavorParamsOutput->getAudioSampleRate()){
			$kdlFlavor->_audio->_sampleRate = $flavorParamsOutput->getAudioSampleRate();
		}
		if($duration){
			if($isAdImage) {
				$kdlFlavor->_transcoders[0]->_extra.= " -t $duration";
			}
			else {
				$kdlFlavor->_clipDur = $duration*1000;
			}
		}

		{
			/*
			 * KDL does not support (yet ...) image-2-video generation,
			 * meanwhile following dummy '_audio' & '_video' initializations
			 * imitate 'normal' aud/vid behaviour
			 */
			if($isAdAudio==false){
				$srcMedSet->_audio = clone($kdlFlavor->_audio);
			}
			if($isAdVideo==false){
				$srcMedSet->_video = clone($kdlFlavor->_video);
			}
		}

		$target = $kdlFlavor->GenerateTarget($srcMedSet);
		/*
		 * Validate resultant target flavor
		 */
		if(!isset($target->_transcoders) || count($target->_transcoders)==0 || !isset($target->_transcoders[0]->_cmd)){
			$errDesc = '';
			if(is_array($target->_errors) && count($target->_errors))			{
				foreach($target->_errors as $section => $errors){
					$errDesc .= "$section errors: " . join("; ", $errors) . "\n";
				}
			}
			throw new kCoreException("Failed to generate appropriate command line ($errDesc)");
		}
		$cmdLine = $target->_transcoders[0]->_cmd;
		// 'image' source needs 'looping'
		if($isAdImage){
			$cmdLine = str_replace(" -i "," -loop 1 -i ", $cmdLine);
		}

		// Add 'silent' source, if no AD audio source
		if($isAdAudio==false){
			$cmdLine = str_replace(array(KDLCmdlinePlaceholders::InFileName),
				array(KDLCmdlinePlaceholders::InFileName." -f s16le -acodec pcm_s16le -i /dev/zero"),
				$cmdLine);
		}

		// Add 'black' source, if no AD video source
		if($isAdVideo==false){
			$cmdLine = " -f rawvideo -pix_fmt rgb24 -s 480x270".str_replace(array(KDLCmdlinePlaceholders::InFileName),
					array("/dev/zero"),
					$cmdLine);
		}
		return $cmdLine;
	}

	/**
	 * @param LiveEntry $entry
	 * @param $liveParams
	 * @param $liveAsset
	 */
	protected static function setLanguageAndDefault(LiveEntry $entry, $liveParams, $liveAsset)
	{
		$multiStream = $liveParams->getMultiStream();
		if (isset($multiStream))
		{
			$multiStreamObj = json_decode($multiStream);
			if (isset($multiStreamObj))
			{
				if (isset($multiStreamObj->audio) && isset($multiStreamObj->audio->languages) && count($multiStreamObj->audio->languages) > 0)
				{
					$flavorLang = $multiStreamObj->audio->languages[0];
					$liveAsset->setLanguage($flavorLang);
					$conversionProfile = $entry->getconversionProfile2();
					if ($conversionProfile->getDefaultAudioLang() == $flavorLang)
					{
						$liveAsset->setDefault(true);
					}
				}
			}
		}
	}
}
