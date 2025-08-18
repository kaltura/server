<?php

/**
 * Base class for live streams and live channels
 *
 * @service liveStream
 * @package api
 * @subpackage services
 */
class KalturaLiveEntryService extends KalturaEntryService
{
	//amount of time for attempting to grab kLock
	const KLOCK_CREATE_RECORDED_ENTRY_GRAB_TIMEOUT = 0.1;

	//amount of time for holding kLock
	const KLOCK_CREATE_RECORDED_ENTRY_HOLD_TIMEOUT = 3;

	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);

		$allowedSystemPartners = array(
			Partner::BATCH_PARTNER_ID,
			Partner::MEDIA_SERVER_PARTNER_ID,
		);

		// Allow bacth and media server partner to list all partner entries
		if (in_array($this->getPartnerId(), $allowedSystemPartners) && in_array($actionName, array('list', 'get')))
			myPartnerUtils::resetPartnerFilter('entry');

		if (in_array($this->getPartner()->getStatus(), array(Partner::PARTNER_STATUS_CONTENT_BLOCK, Partner::PARTNER_STATUS_FULL_BLOCK)))
		{
			throw new kCoreException("Partner blocked", kCoreException::PARTNER_BLOCKED);
		}
	}


	protected function partnerRequired($actionName)
	{
		if ($actionName === 'isLive')
		{
			return false;
		}
		return parent::partnerRequired($actionName);
	}

	function dumpApiRequest($entryId, $onlyIfAvailable = true)
	{
		$disableDump = kConf::get('disable_dump_live_api_to_entry_dc', 'runtime_config', 0);
		$entryDc = substr($entryId, 0, 1);
		if (!$disableDump && $entryDc != kDataCenterMgr::getCurrentDcId())
		{
			$remoteDCHost = kDataCenterMgr::getRemoteDcExternalUrlByDcId($entryDc);
			kFileUtils::dumpApiRequest($remoteDCHost, $onlyIfAvailable);
		}
	}

	/**
	 * Append recorded video to live entry
	 *
	 * @action appendRecording
	 * @param string $entryId Live entry id
	 * @param string $assetId Live asset id
	 * @param KalturaEntryServerNodeType $mediaServerIndex
	 * @param KalturaDataCenterContentResource $resource
	 * @param float $duration in seconds
	 * @param bool $isLastChunk Is this the last recorded chunk in the current session (i.e. following a stream stop event)
	 * @return KalturaLiveEntry The updated live entry
	 *
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	function appendRecordingAction($entryId, $assetId, $mediaServerIndex, KalturaDataCenterContentResource $resource, $duration, $isLastChunk = false)
	{
		if (PermissionPeer::isValidForPartner(PermissionName::FEATURE_LIVE_STREAM_KALTURA_RECORDING, kCurrentContext::getCurrentPartnerId()))
		{
			throw new KalturaAPIException(KalturaErrors::KALTURA_RECORDING_ENABLED, kCurrentContext::$partner_id);
		}

		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry || !($dbEntry instanceof LiveEntry))
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		$dbAsset = assetPeer::retrieveById($assetId);
		if (!$dbAsset || !($dbAsset instanceof liveAsset))
			throw new KalturaAPIException(KalturaErrors::ASSET_ID_NOT_FOUND, $assetId);

		$maxRecordingDuration = (kConf::get('max_live_recording_duration_hours') + 1) * 60 * 60 * 1000;
		$currentDuration = $dbEntry->getCurrentDuration($duration, $maxRecordingDuration);
		if ($currentDuration > $maxRecordingDuration)
		{
			throw new KalturaAPIException(KalturaErrors::LIVE_STREAM_EXCEEDED_MAX_RECORDED_DURATION, $entryId);
		}

		$kResource = $resource->toObject();
		$filename = $kResource->getLocalFilePath();
		if (!($resource instanceof KalturaServerFileResource))
		{
			$filename = kConf::get('uploaded_segment_destination') . basename($kResource->getLocalFilePath());
			kFile::moveFile($kResource->getLocalFilePath(), $filename);
			chgrp($filename, kConf::get('content_group'));
			chmod($filename, 0640);
		}

		if ($dbAsset->hasTag(assetParams::TAG_RECORDING_ANCHOR) && $mediaServerIndex == EntryServerNodeType::LIVE_PRIMARY)
		{
			$dbEntry->setLengthInMsecs($currentDuration);

			if ($isLastChunk)
			{
				// Save last elapsed recording time
				$dbEntry->setLastElapsedRecordingTime($currentDuration);
			}

			$dbEntry->save();
		}

		kJobsManager::addConvertLiveSegmentJob(null, $dbAsset, $mediaServerIndex, $filename, $currentDuration);

		if ($mediaServerIndex == EntryServerNodeType::LIVE_PRIMARY)
		{
			if (!$dbEntry->getRecordedEntryId())
			{
				$this->createRecordedEntry($dbEntry, $mediaServerIndex);
			}

			$recordedEntry = entryPeer::retrieveByPK($dbEntry->getRecordedEntryId());
			if ($recordedEntry)
			{
				if ($recordedEntry->getSourceType() !== EntrySourceType::RECORDED_LIVE)
				{
					$recordedEntry->setSourceType(EntrySourceType::RECORDED_LIVE);
					$recordedEntry->save();
				}
				$this->ingestAsset($recordedEntry, $dbAsset, $filename);
			}
		}

		return $this->convertToApiObject($dbEntry);
	}

	private function ingestAsset(entry $entry, $dbAsset, $filename, $shouldCopy = true, $flavorParamsId = null)
	{
		if ($dbAsset)
			$flavorParamsId = $dbAsset->getFlavorParamsId();
		$flavorParams = assetParamsPeer::retrieveByPKNoFilter($flavorParamsId);

		// is first chunk
		$recordedAsset = assetPeer::retrieveByEntryIdAndParams($entry->getId(), $flavorParamsId);
		if ($recordedAsset)
		{
			KalturaLog::info("Asset [" . $recordedAsset->getId() . "] of flavor params id [$flavorParamsId] already exists");
			return;
		}

		// create asset
		$recordedAsset = assetPeer::getNewAsset(assetType::FLAVOR);
		$recordedAsset->setPartnerId($entry->getPartnerId());
		$recordedAsset->setEntryId($entry->getId());
		$recordedAsset->setStatus(asset::FLAVOR_ASSET_STATUS_QUEUED);
		$recordedAsset->setFlavorParamsId($flavorParams->getId());
		$recordedAsset->setFromAssetParams($flavorParams);
		$recordedAsset->incrementVersion();
		if ($dbAsset && $dbAsset->hasTag(assetParams::TAG_RECORDING_ANCHOR))
		{
			$recordedAsset->addTags(array(assetParams::TAG_RECORDING_ANCHOR));
		}

		if ($flavorParams->hasTag(assetParams::TAG_SOURCE))
		{
			$recordedAsset->setIsOriginal(true);
		}

		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		if ($ext)
		{
			$recordedAsset->setFileExt($ext);
		}

		$recordedAsset->save();

		// create file sync
		$recordedAssetKey = $recordedAsset->getSyncKey(flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
		kFileSyncUtils::moveFromFile($filename, $recordedAssetKey, true, $shouldCopy);

		kEventsManager::raiseEvent(new kObjectAddedEvent($recordedAsset));
	}

	/**
	 * Register media server to live entry
	 *
	 * @action registerMediaServer
	 * @param string $entryId Live entry id
	 * @param string $hostname Media server host name
	 * @param KalturaEntryServerNodeType $mediaServerIndex Media server index primary / secondary
	 * @param string $applicationName the application to which entry is being broadcast
	 * @param KalturaEntryServerNodeStatus $liveEntryStatus the status KalturaEntryServerNodeStatus::PLAYABLE | KalturaEntryServerNodeStatus::BROADCASTING
	 * @param bool $shouldCreateRecordedEntry
	 * @return KalturaLiveEntry The updated live entry
	 *
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::SERVER_NODE_NOT_FOUND
	 * @throws KalturaErrors::ENTRY_SERVER_NODE_MULTI_RESULT
	 */
	function registerMediaServerAction($entryId, $hostname, $mediaServerIndex, $applicationName = null, $liveEntryStatus = KalturaEntryServerNodeStatus::PLAYABLE, $shouldCreateRecordedEntry = true)
	{
		kApiCache::disableConditionalCache();
		KalturaLog::debug("Entry [$entryId] from mediaServerIndex [$mediaServerIndex] with liveEntryStatus [$liveEntryStatus]");

		$dbLiveEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbLiveEntry || !($dbLiveEntry instanceof LiveEntry))
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		$this->setMediaServerWrapper($dbLiveEntry, $mediaServerIndex, $hostname, $liveEntryStatus, $applicationName);

		$dbLiveEntry->save();

		if ($shouldCreateRecordedEntry && $this->shouldCreateRecordedEntry($dbLiveEntry, $mediaServerIndex, $liveEntryStatus, true))
		{
			$this->createRecordedEntryWrapper($dbLiveEntry, $mediaServerIndex);
		}

		return $this->convertToApiObject($dbLiveEntry);
	}

	protected function setMediaServerWrapper($dbLiveEntry, $mediaServerIndex, $hostname, $liveEntryStatus, $applicationName)
	{
		/* @var $dbLiveEntry LiveEntry */
		try
		{
			return $dbLiveEntry->setMediaServer($mediaServerIndex, $hostname, $liveEntryStatus, $applicationName);
		} catch (kCoreException $ex)
		{
			$code = $ex->getCode();
			switch ($code)
			{
				case kCoreException::MEDIA_SERVER_NOT_FOUND :
					throw new KalturaAPIException(KalturaErrors::MEDIA_SERVER_NOT_FOUND, $hostname);
				default:
					throw $ex;
			}
		}
	}

	/**
	 * @param LiveEntry $dbEntry
	 * @param EntryServerNodeType $mediaServerIndex
	 * @return entry
	 * @throws Exception
	 * @throws PropelException
	 */
	private function createRecordedEntry(LiveEntry $dbEntry, $mediaServerIndex)
	{
		KalturaLog::info("Creating a recorded entry for " . $dbEntry->getId());
		$lock = kLock::create("live_record_" . $dbEntry->getId());

		if ($lock && !$lock->lock(self::KLOCK_CREATE_RECORDED_ENTRY_GRAB_TIMEOUT, self::KLOCK_CREATE_RECORDED_ENTRY_HOLD_TIMEOUT))
		{
			return;
		}

		// If while we were waiting for the lock, someone has updated the recorded entry id - we should use it.
		$dbEntry->reload();
		if (($dbEntry->getRecordStatus() != RecordStatus::PER_SESSION) && ($dbEntry->getRecordedEntryId()))
		{
			$recordedEntry = entryPeer::retrieveByPK($dbEntry->getRecordedEntryId());
			if ($recordedEntry)
			{
				$lock->unlock();
				return $recordedEntry;
			}
		}

		$recordedEntry = null;
		try
		{
			$recordedEntryName = $dbEntry->getName();
			if ($dbEntry->getRecordStatus() == RecordStatus::PER_SESSION)
				$recordedEntryName .= ' ' . ($dbEntry->getRecordedEntryIndex() + 1);

			$recordedEntry = new entry();
			$recordedEntry->setType(entryType::MEDIA_CLIP);
			$recordedEntry->setMediaType(entry::ENTRY_MEDIA_TYPE_VIDEO);
			$recordedEntry->setRootEntryId($dbEntry->getId());
			$recordedEntry->setName($recordedEntryName);
			$recordedEntry->setDescription($dbEntry->getDescription());
			$recordedEntry->setSourceType(EntrySourceType::KALTURA_RECORDED_LIVE);
			$recordedEntry->setAccessControlId($dbEntry->getAccessControlId());
			$recordedEntry->setKuserId($dbEntry->getKuserId());
			$recordedEntry->setPartnerId($dbEntry->getPartnerId());
			$recordedEntry->setModerationStatus($dbEntry->getModerationStatus());
			$recordedEntry->setIsRecordedEntry(true);
			$recordedEntry->setTags($dbEntry->getTags());
			$recordedEntry->setStatus(entryStatus::NO_CONTENT);
			multiLingualUtils::copyMultiLingualValues($recordedEntry, $dbEntry);
			if ($dbEntry->getLicenseType() !== null)
			{
				$recordedEntry->setLicenseType($dbEntry->getLicenseType());
			}
			else
			{
				$recordedEntry->setLicenseType(KalturaLicenseType::UNKNOWN);
			}
			$recordedEntry->setConversionProfileId($dbEntry->getRecordedEntryConversionProfile());

			// make the recorded entry to be "hidden" in search so it won't return in entry list action
			if ($dbEntry->getRecordingOptions() && $dbEntry->getRecordingOptions()->getShouldMakeHidden())
			{
				$recordedEntry->setDisplayInSearch(EntryDisplayInSearchType::SYSTEM);
			}
			if ($dbEntry->getRecordingOptions() && $dbEntry->getRecordingOptions()->getShouldCopyScheduling())
			{
				$recordedEntry->setStartDate($dbEntry->getStartDate());
				$recordedEntry->setEndDate($dbEntry->getEndDate());
			}

			$recordedEntry->save();

			$dbEntry->setRecordedEntryId($recordedEntry->getId());
			$dbEntry->save();

			$assets = assetPeer::retrieveByEntryId($dbEntry->getId(), array(assetType::LIVE));
			foreach ($assets as $asset)
			{
				/* @var $asset liveAsset */
				$asset->incLiveSegmentVersion($mediaServerIndex);
				$asset->save();
			}
		} catch (Exception $e)
		{
			$lock->unlock();
			throw $e;
		}

		if ($lock)
		{
			$lock->unlock();
		}

		return $recordedEntry;
	}

	/**
	 * Unregister media server from live entry
	 *
	 * @action unregisterMediaServer
	 * @param string $entryId Live entry id
	 * @param string $hostname Media server host name
	 * @param KalturaEntryServerNodeType $mediaServerIndex Media server index primary / secondary
	 * @return KalturaLiveEntry The updated live entry
	 *
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::SERVER_NODE_NOT_FOUND
	 * @throws KalturaErrors::ENTRY_SERVER_NODE_MULTI_RESULT
	 */
	function unregisterMediaServerAction($entryId, $hostname, $mediaServerIndex)
	{
		$this->dumpApiRequest($entryId, true);

		KalturaLog::debug("Entry [$entryId] from mediaServerIndex [$mediaServerIndex] with hostname [$hostname]");

		/* @var $dbLiveEntry LiveEntry */
		$dbLiveEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbLiveEntry || !($dbLiveEntry instanceof LiveEntry))
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		$dbServerNode = ServerNodePeer::retrieveActiveMediaServerNode($hostname);
		if (!$dbServerNode)
			throw new KalturaAPIException(KalturaErrors::SERVER_NODE_NOT_FOUND, $hostname);

		$dbLiveEntryServerNode = EntryServerNodePeer::retrieveByEntryIdAndServerType($entryId, $mediaServerIndex);
		if (!$dbLiveEntryServerNode)
			throw new KalturaAPIException(KalturaErrors::ENTRY_SERVER_NODE_NOT_FOUND, $entryId, $mediaServerIndex);

		$dbLiveEntryServerNode->deleteOrMarkForDeletion();

		return $this->convertToApiObject($dbLiveEntry);
	}

	/**
	 * Validates all registered media servers
	 *
	 * @action validateRegisteredMediaServers
	 * @param string $entryId Live entry id
	 *
	 * @throws KalturaAPIException
	 */
	function validateRegisteredMediaServersAction($entryId)
	{
		KalturaResponseCacher::disableCache();

		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry || !($dbEntry instanceof LiveEntry))
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		/* @var $dbEntry LiveEntry */
		$dbEntry->validateMediaServers();
	}

	/**
	 * Set recorded video to live entry
	 *
	 * @action setRecordedContent
	 * @param string $entryId Live entry id
	 * @param KalturaEntryServerNodeType $mediaServerIndex
	 * @param KalturaDataCenterContentResource $resource
	 * @param float $duration in seconds
	 * @param string $recordedEntryId Recorded entry Id
	 * @param int $flavorParamsId Recorded entry Id
	 * @return KalturaLiveEntry The updated live entry
	 *
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::RECORDED_ENTRY_LIVE_MISMATCH
	 * @throws KalturaErrors::KALTURA_RECORDING_DISABLED
	 * @throws KalturaErrors::FLAVOR_PARAMS_ID_NOT_FOUND

	 */
	function setRecordedContentAction($entryId, $mediaServerIndex, KalturaDataCenterContentResource $resource, $duration, $recordedEntryId = null, $flavorParamsId = null)
	{
		if (!PermissionPeer::isValidForPartner(PermissionName::FEATURE_LIVE_STREAM_KALTURA_RECORDING, kCurrentContext::getCurrentPartnerId()))
		{
			throw new KalturaAPIException(KalturaErrors::KALTURA_RECORDING_DISABLED, kCurrentContext::$partner_id);
		}

		$dbLiveEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbLiveEntry || !($dbLiveEntry instanceof LiveEntry))
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		if ($mediaServerIndex != EntryServerNodeType::LIVE_PRIMARY)
		{
			return $this->convertToApiObject($dbLiveEntry);
		}

		$recordedEntry = null;
		$createRecordedEntry = false;
		if ($recordedEntryId)
		{
			$recordedEntry = entryPeer::retrieveByPK($recordedEntryId);
			if ($recordedEntry && $recordedEntry->getRootEntryId() != $entryId)
				throw new KalturaAPIException(KalturaErrors::RECORDED_ENTRY_LIVE_MISMATCH, $entryId, $recordedEntryId);

			if ($recordedEntry && $recordedEntry->getSourceType() != EntrySourceType::KALTURA_RECORDED_LIVE)
			{
				$recordedEntry = null;
				$createRecordedEntry = true;
				$dbLiveEntry->setRecordedEntryId(null);
				$dbLiveEntry->save();
			}
		} else if ($dbLiveEntry->getRecordedEntryId())
		{
			$recordedEntry = entryPeer::retrieveByPK($dbLiveEntry->getRecordedEntryId());
			if (!$recordedEntry)
				$createRecordedEntry = true;
		} else
		{
			$createRecordedEntry = true;
		}

		if ($createRecordedEntry)
			$recordedEntry = $this->createRecordedEntry($dbLiveEntry, $mediaServerIndex);

		if (!$recordedEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $recordedEntryId);

		if ($recordedEntry->getFlowType() != EntryFlowType::LIVE_CLIPPING)
		{
			$totalDuration = (int)($duration * 1000);
			$dbLiveEntry->setLengthInMsecs($totalDuration);
			$dbLiveEntry->save();
		}

		$this->handleRecording($dbLiveEntry, $recordedEntry, $resource, $flavorParamsId);

		return $this->convertToApiObject($dbLiveEntry);
	}

	private function handleRecording(LiveEntry $dbLiveEntry, entry $recordedEntry, KalturaDataCenterContentResource $resource, $flavorParamsId = null)
	{
		// in case we get null in the $flavorParamsId it mean that live will upload the source and let the BE convert it.
		if (!$flavorParamsId)
		{
			// check if the conversion profile is already VOD if not set it to default.
			KalturaLog::info('In handleRecording with flavor params id null entry ' . $dbLiveEntry->getId());
			if ($recordedEntry->getconversionProfile2()->getType() == ConversionProfileType::LIVE_STREAM)
			{
				$recordedEntry->setConversionProfileId($this->getPartner()->getDefaultConversionProfileId());
				$recordedEntry->save();
			}
			$service = new MediaService();
			$service->initService('media', 'media', 'updateContent');
			$service->updateContentAction($recordedEntry->getId(), $resource);
			return;
		}

		//In case conversion profile was changed we need to fetch passed streamed assets as well
		$dbAsset = assetPeer::retrieveByEntryIdAndParamsNoFilter($dbLiveEntry->getId(), $flavorParamsId);
		if (!$dbAsset)
		{
			$flavorParamConversionProfile = flavorParamsConversionProfilePeer::retrieveByFlavorParamsAndConversionProfile($flavorParamsId, $dbLiveEntry->getConversionProfileId());
			if (!$flavorParamConversionProfile)
				throw new KalturaAPIException(KalturaErrors::FLAVOR_PARAMS_ID_NOT_FOUND, $flavorParamsId);
		}

		$kResource = $resource->toObject();
		/* @var $kResource kLocalFileResource */
		$filename = $kResource->getLocalFilePath();
		$keepOriginalFile = $kResource->getKeepOriginalFile();

		$lockKey = "create_replacing_entry_" . $recordedEntry->getId();
		$replacingEntry = kLock::runLocked($lockKey, array('kFlowHelper', 'getReplacingEntry'), array($recordedEntry, 0, $dbAsset, $flavorParamsId));
		$this->ingestAsset($replacingEntry, $dbAsset, $filename, $keepOriginalFile, $flavorParamsId);
	}

	/**
	 * Create recorded entry id if it doesn't exist and make sure it happens on the DC that the live entry was created on.
	 * @action createRecordedEntry
	 * @param string $entryId Live entry id
	 * @param KalturaEntryServerNodeType $mediaServerIndex Media server index primary / secondary
	 * @param KalturaEntryServerNodeStatus $liveEntryStatus the status KalturaEntryServerNodeStatus::PLAYABLE | KalturaEntryServerNodeStatus::BROADCASTING
	 * @return KalturaLiveEntry The updated live entry
	 * @throws KalturaAPIException
	 */
	public function createRecordedEntryAction($entryId, $mediaServerIndex, $liveEntryStatus)
	{
		$this->dumpApiRequest($entryId, true);
		$dbLiveEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbLiveEntry || !($dbLiveEntry instanceof LiveEntry))
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		if ($this->shouldCreateRecordedEntry($dbLiveEntry, $mediaServerIndex, $liveEntryStatus, false))
		{
			$this->createRecordedEntryWrapper($dbLiveEntry, $mediaServerIndex);
		}

		return $this->convertToApiObject($dbLiveEntry);
	}

	/**
	 * Checking whether need to create recorded entry for the live entry
	 * @param LiveEntry $dbLiveEntry
	 * @param KalturaEntryServerNodeType $mediaServerIndex
	 * @param KalturaEntryServerNodeStatus $liveEntryStatus
	 * @param bool $forcePrimaryValidation
	 * @return bool
	 * @throws Exception
	 */
	protected function shouldCreateRecordedEntry($dbLiveEntry, $mediaServerIndex, $liveEntryStatus, $forcePrimaryValidation)
	{
		KalturaLog::info("Checking if recorded entry needs to be created for entry " . $dbLiveEntry->getId());
		if (!$dbLiveEntry->getRecordStatus() || $dbLiveEntry->getRecordStatus() === RecordStatus::DISABLED)
		{
			return false;
		}
		if (!in_array($liveEntryStatus, array(EntryServerNodeStatus::BROADCASTING, EntryServerNodeStatus::PLAYABLE)))
		{
			return false;
		}
		if ($forcePrimaryValidation && $mediaServerIndex != EntryServerNodeType::LIVE_PRIMARY)
		{
			return false;
		}

		if (!$dbLiveEntry->getRecordedEntryId())
		{
			return true;
		}

		$dbRecordedEntry = entryPeer::retrieveByPK($dbLiveEntry->getRecordedEntryId());
		if (!$dbRecordedEntry)
		{
			return true;
		}

		if ($dbLiveEntry->getRecordStatus() == RecordStatus::PER_SESSION)
		{
			return $this->shouldCreateRecordedEntryForPerSession($dbRecordedEntry, $dbLiveEntry);
		}

		if ($dbLiveEntry->getRecordStatus() == RecordStatus::APPENDED)
		{
			return $this->shouldCreateRecordedEntryForAppend($dbRecordedEntry);
		}

		return false;
	}

	/**
	 * Checking whether should create recorded entry for the "append" record status case
	 * @param entry $dbRecordedEntry
	 * @return bool
	 * @throws Exception
	 */
	protected function shouldCreateRecordedEntryForAppend(entry $dbRecordedEntry)
	{
		$recordedEntryCreationTime = $dbRecordedEntry->getCreatedAt(null);
		$isKalturaRecordedLive = $dbRecordedEntry->getSourceType() == EntrySourceType::KALTURA_RECORDED_LIVE; // probably can be removed
		if (!$isKalturaRecordedLive)
		{
			KalturaLog::debug("recorded entry source type is not KALTURA_RECORDED_LIVE [recordedEntryId = {$dbRecordedEntry->getId()}]");
		}
		$maxAppendTimeReached = ($recordedEntryCreationTime + kTimeConversion::WEEK) < time();
		$recordingMoreThanLimit = $dbRecordedEntry->getDuration() > kConf::get('recording_limit_length', 'local', kTimeConversion::DAY);

		KalturaLog::debug("maxAppendTimeReached [$maxAppendTimeReached] recordedEntryCreationTime [$recordedEntryCreationTime] recordingMoreThenLimit [$recordingMoreThanLimit] isKalturaRecordedLive [$isKalturaRecordedLive]");
		return $isKalturaRecordedLive && ($maxAppendTimeReached || $recordingMoreThanLimit);
	}

	/**
	 * Checking whether should create recorded entry for the "per session" record status case
	 * @param entry $dbRecordedEntry
	 * @param LiveEntry $dbLiveEntry
	 * @return bool
	 * @throws Exception
	 */
	protected function shouldCreateRecordedEntryForPerSession(entry $dbRecordedEntry, LiveEntry $dbLiveEntry)
	{
		$recordedEntryCreationTime = $dbRecordedEntry->getCreatedAt(null);
		$liveSessionReconnectTimeout = kConf::get('live_session_reconnect_timeout', 'local', 180);
		$isNewSession = $dbLiveEntry->getLastBroadcastEndTime() + $liveSessionReconnectTimeout < $dbLiveEntry->getCurrentBroadcastStartTime();
		$recordedEntryNotYetCreatedForCurrentSession = $recordedEntryCreationTime + $liveSessionReconnectTimeout < $dbLiveEntry->getCurrentBroadcastStartTime();
		KalturaLog::debug("isNewSession [$isNewSession] getLastBroadcastEndTime [{$dbLiveEntry->getLastBroadcastEndTime()}] getCurrentBroadcastStartTime [{$dbLiveEntry->getCurrentBroadcastStartTime()}]");
		KalturaLog::debug("recordedEntryNotYetCreatedForCurrentSession [$recordedEntryNotYetCreatedForCurrentSession] recordedEntryCreationTime [$recordedEntryCreationTime] getCurrentBroadcastStartTime [{$dbLiveEntry->getCurrentBroadcastStartTime()}]");
		return $isNewSession && $recordedEntryNotYetCreatedForCurrentSession;
	}

	/**
	 * Creating recorded entry. If the record status is "APPENDED" - the recorderEntryId will be set to null.
	 * @param LiveEntry $dbLiveEntry
	 * @param KalturaEntryServerNodeType $mediaServerIndex
	 * @throws Exception
	 */
	protected function createRecordedEntryWrapper(LiveEntry $dbLiveEntry, $mediaServerIndex)
	{
		if ($dbLiveEntry->getRecordStatus() === RecordStatus::APPENDED)
		{
			$dbLiveEntry->setRecordedEntryId(null);
			$dbLiveEntry->save();
		}
		$this->createRecordedEntry($dbLiveEntry, $mediaServerIndex);
	}

	/**
	 * Converting from dbEntry to the appropriate api entry
	 * @param entry $dbEntry
	 * @return KalturaBaseEntry
	 * @throws Exception
	 */
	protected function convertToApiObject(entry $dbEntry)
	{
		$entry = KalturaEntryFactory::getInstanceByType($dbEntry->getType());
		$entry->fromObject($dbEntry, $this->getResponseProfile());
		return $entry;
	}
}
