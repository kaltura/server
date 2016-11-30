<?php
class kEntryPlayingDataHelper
{
	const MEDIA_PROTOCOL_HTTP = 'http';
	const MEDIA_PROTOCOL_HTTPS = 'https';

	/**
	 * @param $dbEntry
	 * @param $partner
	 * @param $contextDataParams
	 * @return kContextDataHelper
	 */
	public function initContextDataHelper($dbEntry, $partner, $contextDataParams)
	{
		$asset = null;
		if ($contextDataParams->flavorAssetId)
		{
			$asset = assetPeer::retrieveById($contextDataParams->flavorAssetId);
			if (!$asset)
				throw new KalturaAPIException(KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND, $contextDataParams->flavorAssetId);
		}

		$contextDataHelper = new kContextDataHelper($dbEntry, $partner, $asset);

		if ($dbEntry->getAccessControl() && $dbEntry->getAccessControl()->hasRules())
			$accessControlScope = $dbEntry->getAccessControl()->getScope();
		else
			$accessControlScope = new accessControlScope();
		$contextDataParams->toObject($accessControlScope);

		$contextDataHelper->buildContextDataResult($accessControlScope, $contextDataParams->flavorTags, $contextDataParams->streamerType, $contextDataParams->mediaProtocol);
		if ($contextDataHelper->getDisableCache())
			KalturaResponseCacher::disableCache();

		return $contextDataHelper;
	}

	/**
	 * @param kContextDataHelper $contextDataHelper
	 * @return boolean
	 */
	public function hasBlockAction($contextDataHelper)
	{
		$actions = $contextDataHelper->getContextDataResult()->getActions();

		foreach ($actions as $action)
		{
			/* @var $action kAccessControlAction */
			if ($action->getType() == RuleActionType::BLOCK)
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * @param kContextDataHelper $contextDataHelper
	 * @return array
	 */
	public function getProfileIdsToFilter($contextDataHelper)
	{
		$actions = $contextDataHelper->getContextDataResult()->getActions();
		$deliveryProfileIds = null;
		$deliveryProfilesParamsNotIn = false;

		foreach ($actions as $action)
		{
			/* @var $action kAccessControlAction */
			if ($action->getType() == RuleActionType::LIMIT_DELIVERY_PROFILES)
			{
				/* @var $action kAccessControlLimitDeliveryProfilesAction */
				$deliveryProfileIds = explode(',', $action->getDeliveryProfileIds());
				$deliveryProfilesParamsNotIn = $action->getIsBlockedList();
			}
		}
		return array($deliveryProfileIds, $deliveryProfilesParamsNotIn);
	}

	/**
	 * @param kContextDataHelper $contextDataHelper
	 * @return array
	 */
	public function getFlavorsToFilter($contextDataHelper)
	{
		$actions = $contextDataHelper->getContextDataResult()->getActions();
		$flavorsIds = null;
		$flavorsParamsNotIn = false;

		foreach ($actions as $action)
		{
			/* @var $action kAccessControlAction */
			if ($action->getType() == RuleActionType::LIMIT_FLAVORS)
			{
				/* @var $action kAccessControlLimitFlavorsAction */
				$flavorsIds = explode(',', $action->getFlavorParamsIds());
				$flavorsParamsNotIn = $action->getIsBlockedList();
			}
		}
		return array($flavorsIds, $flavorsParamsNotIn);
	}


	/**
	 * @param $dbEntry
	 * @param kContextDataHelper $contextDataHelper
	 * @return KalturaFlavorAssetArray
	 */
	public function getRelevantFlavorAssets($dbEntry, kContextDataHelper $contextDataHelper)
	{
		$parentEntryId = $dbEntry->getSecurityParentId();
		if ($parentEntryId)
		{
			$dbEntry = $dbEntry->getParentEntry();
			if (!$dbEntry)
				throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $parentEntryId);
		}

		$flavorAssets = $contextDataHelper->getAllowedFlavorAssets();

		list($flavorsIdsToFilter, $flavorsParamsNotIn) = $this->getFlavorsToFilter($contextDataHelper);
		if (count($flavorsIdsToFilter))
			$this->filterFlavorAssets($flavorAssets, $flavorsIdsToFilter, $flavorsParamsNotIn);

		return $flavorAssets;
	}


	/**
	 * @param $dbEntry
	 * @param $flavorAssets
	 * @return array
	 */
	public function getFlavorsMapping($dbEntry, $flavorAssets)
	{
		// get flavors availability
		$servePriority = $dbEntry->getPartner()->getStorageServePriority();

		$localFlavors = array();
		$remoteFlavorsByDc = array();
		$remoteDeliveryProfileIds = array();
		$remoteDcByDeliveryProfile = array();
		$remoteFileSyncs = array();

		foreach ($flavorAssets as $flavorAsset)
		{
			$flavorId = $flavorAsset->getId();
			$key = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);

			$c = new Criteria();
			$c = FileSyncPeer::getCriteriaForFileSyncKey($key);
			$c->addAnd(FileSyncPeer::STATUS, FileSync::FILE_SYNC_STATUS_READY);

			switch ($servePriority)
			{
				case StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_ONLY:
					$c->addAnd(FileSyncPeer::FILE_TYPE, FileSync::FILE_SYNC_FILE_TYPE_URL, Criteria::NOT_EQUAL);
					break;

				case StorageProfile::STORAGE_SERVE_PRIORITY_EXTERNAL_ONLY:
					$c->add(FileSyncPeer::FILE_TYPE, FileSync::FILE_SYNC_FILE_TYPE_URL);
					break;
			}

			$fileSyncs = FileSyncPeer::doSelect($c);

			foreach ($fileSyncs as $fileSync)
			{
				if ($fileSync->getFileType() == FileSync::FILE_SYNC_FILE_TYPE_URL)
				{
					$dc = $fileSync->getDc();
					$remoteFlavorsByDc[$dc] [] = $flavorAsset;
					$remoteFileSyncs[$dc][$flavorId] = $fileSync;
				} else
				{
					$localFlavors[$fileSync->getObjectId()] = $flavorAsset;
				}
			}
		}

		// get Active remote profiles
		if ($remoteFileSyncs)
		{
			$storageProfileIds = array_keys($remoteFileSyncs);
			$storageProfiles = StorageProfilePeer::retrieveExternalByPartnerId($dbEntry->getPartnerId(), $storageProfileIds);

			$activeStorageProfileIds = array();
			foreach ($storageProfiles as $storageProfile)
			{
				$activeStorageProfileIds[] = $storageProfile->getId();
				$deliveryProfilesIds = call_user_func_array('array_merge', $storageProfile->getFromCustomData("delivery_profile_ids"));

				foreach ($deliveryProfilesIds as $deliveryProfileId)
					$remoteDcByDeliveryProfile[$deliveryProfileId] = $storageProfile->getId();

				$remoteDeliveryProfileIds = array_merge($remoteDeliveryProfileIds, $deliveryProfilesIds);
			}

			foreach ($storageProfileIds as $storageProfileId)
			{
				if (in_array($storageProfileId, $activeStorageProfileIds))
					continue;

				unset($remoteFileSyncs[$storageProfileId]);
				unset($remoteFlavorsByDc[$storageProfileId]);
			}

			return array($localFlavors, $remoteFlavorsByDc, $remoteDeliveryProfileIds, $remoteDcByDeliveryProfile);
		}
		return array($localFlavors, $remoteFlavorsByDc, $remoteDeliveryProfileIds, $remoteDcByDeliveryProfile);
	}


	/**
	 * @param $entryId
	 * @param $dbEntry
	 * @param $localFlavors
	 * @param $contextDataHelper
	 * @return KalturaPlayingSourceArray
	 */
	public function constructLocalSources($entryId, $dbEntry, $localFlavors, $contextDataHelper)
	{
		$sources = array();

		if (!count($localFlavors))
			return $sources;

		$deliveryAttributes = DeliveryProfileDynamicAttributes::init(null, $entryId, null);
		$localDeliveryProfileIds = call_user_func_array('array_merge', $dbEntry->getPartner()->getDeliveryProfileIds());
		$localDeliveryProfiles = DeliveryProfilePeer::getDeliveriesByIds($dbEntry, $localDeliveryProfileIds, $dbEntry->getPartner(), $deliveryAttributes);

		if ($dbEntry->getPartner()->getEnforceDelivery())
		{
			$streamsTypesToExclude = $this->getStreamsTypeToExclude($localDeliveryProfiles);
			$defaultDeliveryProfiles = DeliveryProfilePeer::getDefaultDeliveriesFilteredByStreamerTypes($dbEntry, $dbEntry->getPartner(), $streamsTypesToExclude);
			$localDeliveryProfiles = array_merge($localDeliveryProfiles, $defaultDeliveryProfiles);
		}

		list($deliveryProfileIds, $deliveryProfilesParamsNotIn) = $this->getProfileIdsToFilter($contextDataHelper);

		if (count($deliveryProfileIds))
			$this->filterDeliveryProfiles($localDeliveryProfiles, $deliveryProfileIds, $deliveryProfilesParamsNotIn);

		foreach ($localDeliveryProfiles as $deliveryProfile)
		{
			$deliveryProfileFlavors = $localFlavors;
			$drmData = $this->getDrmData($dbEntry, $deliveryProfileFlavors, $deliveryProfile);

			if (count($deliveryProfileFlavors))
				$sources[] = new KalturaPlayingSource($deliveryProfile->getId(), $deliveryProfile->getStreamerType(), $deliveryProfile->getPriority(), $this->constructProtocol($deliveryProfile), array_keys($deliveryProfileFlavors), null, $drmData);
		}

		return $sources;
	}

	/**
	 * @param $entryId
	 * @param $dbEntry
	 * @param $remoteDeliveryProfileIds
	 * @param $remoteDcByDeliveryProfile
	 * @param $remoteFlavorsByDc
	 * @param $contextDataHelper
	 * @return KalturaPlayingSourceArray
	 */
	public function constructRemoteSources($entryId, $dbEntry, $remoteDeliveryProfileIds, $remoteDcByDeliveryProfile, $remoteFlavorsByDc, $contextDataHelper)
	{
		$sources = array();
		if (!count($remoteFlavorsByDc))
			return $sources;

		$deliveryAttributes = DeliveryProfileDynamicAttributes::init(null, $entryId, null);
		$remoteDeliveryProfiles = DeliveryProfilePeer::getDeliveriesByIds($dbEntry, $remoteDeliveryProfileIds, $dbEntry->getPartner(), $deliveryAttributes);

		list($deliveryProfileIds, $deliveryProfilesParamsNotIn) = $this->getProfileIdsToFilter($contextDataHelper);
		if (count($deliveryProfileIds))
			$this->filterDeliveryProfiles($remoteDeliveryProfiles, $deliveryProfileIds, $deliveryProfilesParamsNotIn);

		foreach ($remoteDeliveryProfiles as $deliveryProfile)
		{
			$dcFlavorIds = array();
			$dc = $remoteDcByDeliveryProfile[$deliveryProfile->getId()];
			$flavorAssetsForDc = $remoteFlavorsByDc[$dc];

			if (count($flavorAssetsForDc))
			{
				$deliveryProfileFlavorsForDc = $flavorAssetsForDc;

				$flavorToDrmData = $this->getDrmData($dbEntry, $deliveryProfileFlavorsForDc, $deliveryProfile);

				if (count($deliveryProfileFlavorsForDc))
				{
					foreach ($deliveryProfileFlavorsForDc as $flavorAssetForDc)
						$dcFlavorIds[] = $flavorAssetForDc->getId();
					$sources[] = new KalturaPlayingSource($deliveryProfile->getId(), $deliveryProfile->getStreamerType(), $deliveryProfile->getPriority(), $this->constructProtocol($deliveryProfile), $dcFlavorIds, null, $flavorToDrmData);
				}
			}
		}

		return $sources;
	}

	/**
	 * @param $flavorAssets
	 * @param $flavorAssetsIds
	 * @param $flavorAssetsParamsNotIn
	 */
	protected function filterFlavorAssets(&$flavorAssets, $flavorAssetsIds, $flavorAssetsParamsNotIn)
	{
		foreach ($flavorAssets as $key => $flavorAsset)
		{
			if (in_array($flavorAsset->getId(), $flavorAssetsIds))
			{
				if ($flavorAssetsParamsNotIn)
					unset($flavorAssets[$key]);
			} else
			{
				if (!$flavorAssetsParamsNotIn)
					unset($flavorAssets[$key]);
			}
		}
	}

	/**
	 * @param $deliveryProfiles
	 * @param $deliveryProfileIds
	 * @param $deliveryProfilesParamsNotIn
	 */
	protected function filterDeliveryProfiles(&$deliveryProfiles, $deliveryProfileIds, $deliveryProfilesParamsNotIn)
	{
		foreach ($deliveryProfiles as $key => $deliveryProfile)
		{
			if (in_array($deliveryProfile->getId(), $deliveryProfileIds))
			{
				if ($deliveryProfilesParamsNotIn)
				{
					unset($deliveryProfiles[$key]);
				}
			} else
			{
				if (!$deliveryProfilesParamsNotIn)
					unset($deliveryProfiles[$key]);
			}
		}
	}

	protected function getStreamsTypeToExclude($localCustomDeliveries)
	{
		$streamTypes = array();
		foreach ($localCustomDeliveries as $deliveryProfile)
			$streamTypes[] = $deliveryProfile->getStreamerType();

		return $streamTypes;
	}

	/* @param $dbEntry
	 * @param $flavorAssets
	 * @param $deliveryProfile
	 * @return KalturaEntryPlayingDataResult
	 */
	protected function getDrmData($dbEntry, &$flavorAssets, $deliveryProfile)
	{
		$playingDataParams = new KalturaEntryPlayingDataParams();
		$playingDataParams->deliverProfile = $deliveryProfile;
		$playingDataParams->flavors = KalturaFlavorAssetArray::fromDbArray(array_values($flavorAssets));

		$result = new KalturaEntryPlayingDataResult();
		$result->pluginData = new KalturaPluginDataArray();

		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaEntryPlayingDataContributor');
		foreach ($pluginInstances as $pluginInstance)
		{
			$pluginInstance->contributeToEntryPlayingDataResult($dbEntry, $playingDataParams, $result);
		}

		if (count($result->flavorIdsToRemove))
			$this->filterFlavorAssets($flavorAssets, $result->flavorIdsToRemove, true);

		return $result->pluginData;
	}


	public function sortSources($localSources, $remoteSources, $servePriority)
	{
		$this->sortSourcesByPriority($localSources);
		$this->sortSourcesByPriority($remoteSources);

		switch ($servePriority)
		{
			case KalturaStorageServePriority::KALTURA_ONLY:
				return $localSources;
				break;
			case KalturaStorageServePriority::KALTURA_FIRST:
				return array_merge($localSources, $remoteSources);
				break;
			case KalturaStorageServePriority::EXTERNAL_ONLY:
				return $remoteSources;
				break;
			case KalturaStorageServePriority::EXTERNAL_FIRST:
				return array_merge($remoteSources, $localSources);
				break;
			default:
		}
	}

	protected function sortSourcesByPriority(&$sourcesArray)
	{
		usort($sourcesArray, function ($a, $b)
		{
			/* @var $a KalturaPlayingSource */
			/* @var $b KalturaPlayingSource */
			return (intval($a->priority) - intval($b->priority));
		});
	}

	public function filterFlavorsBySources(&$flavorAssets, $sources)
	{
		$flavorAssetsIds = array();
		foreach ($sources as $source)
		{
			/* @var $source KalturaPlayingSource */
			$flavorAssetsIds = array_merge($flavorAssetsIds,$source->flavors);
		}

		$this->filterFlavorAssets($flavorAssets, $flavorAssetsIds, false );
	}

	/**
	 * @param $deliveryProfile
	 * @return array
	 * */
	protected function constructProtocol($deliveryProfile)
	{
		if (is_null($deliveryProfile->getMediaProtocols()))
		{
			if ($deliveryProfile->getStreamerType() == PlaybackProtocol::RTMP)
				return array(PlaybackProtocol::RTMP);
			else
				return array(self::MEDIA_PROTOCOL_HTTP, self::MEDIA_PROTOCOL_HTTPS);
		}
		return explode(",",$deliveryProfile->getMediaProtocols());
	}

}