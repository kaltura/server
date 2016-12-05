<?php
class kPlaybackContextDataHelper
{
	const MEDIA_PROTOCOL_HTTP = 'http';
	const MEDIA_PROTOCOL_HTTPS = 'https';

	/**
	 * @var kPlaybackContextResult
	 */
	private $playbackContextResult;

	/**
	 * @var array
	 */
	private $restrictedMessages;

	/**
	 * @var bool
	 */
	private $isScheduledNow;

	public function getPlaybackContextResult()
	{
		return $this->playbackContextResult;
	}


	public function setIsScheduledNow($isScheduledNow)
	{
		$this->isScheduledNow = $isScheduledNow;
	}

	public function getIsScheduledNow()
	{
		$this->isScheduledNow;
	}

	/**
	 * @param kContextDataHelper $contextDataHelper
	 * @param entry $dbEntry
	 * @throws kCoreException
	 */
	public function constructPlaybackContextResult(kContextDataHelper $contextDataHelper, entry $dbEntry)
	{
		$this->playbackContextResult = new kPlaybackContextResult();
		$this->generateRestrictedMessages($contextDataHelper);

		if ($this->hasBlockAction($contextDataHelper))
			return;

		$flavorAssets = $this->getRelevantFlavorAssets($dbEntry, $contextDataHelper);

		list($localFlavors, $remoteFlavorsByDc, $remoteDeliveryProfileIds, $remoteDcByDeliveryProfile) = $this->getFlavorsMapping($dbEntry, $flavorAssets);

		$localSources = $this->constructLocalSources($dbEntry, $localFlavors, $contextDataHelper);
		$remoteSources = $this->constructRemoteSources($dbEntry, $remoteDeliveryProfileIds, $remoteDcByDeliveryProfile, $remoteFlavorsByDc, $contextDataHelper);

		$sources = $this->sortSources($localSources, $remoteSources, $dbEntry->getPartner()->getStorageServePriority());
		$this->filterFlavorsBySources($flavorAssets, $sources);

		$this->playbackContextResult->setSources($sources);
		$this->playbackContextResult->setFlavorAssets($flavorAssets);
	}

	/**
	 * @param kContextDataHelper $contextDataHelper
	 * @return boolean
	 */
	public function hasBlockAction(kContextDataHelper $contextDataHelper)
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
	 * @return boolean
	 */
	public function generateRestrictedMessages($contextDataHelper)
	{
		$playbackRestrictions = array();

		foreach ($contextDataHelper->getContextDataResult()->getRulesCodesMap() as $code => $messages)
		{
			foreach ($messages as $message)
				$playbackRestrictions[] = new kPlaybackRestriction($code, $message);
		}

		if ($contextDataHelper->getContextDataResult()->getIsCountryRestricted())
			$playbackRestrictions[] = new kPlaybackRestriction(RuleRestrictions::COUNTRY_RESTRICTED_CODE, RuleRestrictions::COUNTRY_RESTRICTED);
		if ($contextDataHelper->getContextDataResult()->getIsIpAddressRestricted())
			$playbackRestrictions[] = new kPlaybackRestriction(RuleRestrictions::IP_RESTRICTED_CODE, RuleRestrictions::IP_RESTRICTED);
		if ($contextDataHelper->getContextDataResult()->getIsSessionRestricted()
			&& ($contextDataHelper->getContextDataResult()->getPreviewLength() == -1 || is_null($contextDataHelper->getContextDataResult()->getPreviewLength())))
			$playbackRestrictions[] = new kPlaybackRestriction(RuleRestrictions::SESSION_RESTRICTED_CODE, RuleRestrictions::SESSION_RESTRICTED);
		if ($contextDataHelper->getContextDataResult()->getIsUserAgentRestricted())
			$playbackRestrictions[] = new kPlaybackRestriction(RuleRestrictions::USER_AGENT_RESTRICTED_CODE, RuleRestrictions::USER_AGENT_RESTRICTED);
		if ($contextDataHelper->getContextDataResult()->getIsSiteRestricted())
			$playbackRestrictions[] = new kPlaybackRestriction(RuleRestrictions::SITE_RESTRICTED_CODE, RuleRestrictions::SITE_RESTRICTED);
		if (!$this->isScheduledNow)
			$playbackRestrictions[] = new kPlaybackRestriction(RuleRestrictions::SCHEDULED_RESTRICTED_CODE, RuleRestrictions::SCHEDULED_RESTRICTED);

		$this->playbackContextResult->setRestrictions($playbackRestrictions);
	}

	/**
	 * @param kContextDataHelper $contextDataHelper
	 * @return array
	 */
	public function getProfileIdsToFilter(kContextDataHelper $contextDataHelper)
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
	public function getFlavorsToFilter(kContextDataHelper $contextDataHelper)
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
	 * @param entry $dbEntry
	 * @param kContextDataHelper $contextDataHelper
	 * @return array
	 */
	public function getRelevantFlavorAssets(entry $dbEntry, kContextDataHelper $contextDataHelper)
	{
		$parentEntryId = $dbEntry->getSecurityParentId();
		if ($parentEntryId)
		{
			$dbEntry = $dbEntry->getParentEntry();
			if (!$dbEntry)
				throw new APIException(APIErrors::ENTRY_ID_NOT_FOUND, $parentEntryId);
		}

		$flavorAssets = $contextDataHelper->getAllowedFlavorAssets();

		list($flavorsIdsToFilter, $flavorsParamsNotIn) = $this->getFlavorsToFilter($contextDataHelper);
		if (count($flavorsIdsToFilter))
			$this->filterFlavorAssets($flavorAssets, $flavorsIdsToFilter, $flavorsParamsNotIn);

		return $flavorAssets;
	}


	/**
	 * @param entry $dbEntry
	 * @param array $flavorAssets
	 * @return array
	 */
	public function getFlavorsMapping(entry $dbEntry, $flavorAssets)
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
	 * @param entry $dbEntry
	 * @param $localFlavors
	 * @param kContextDataHelper $contextDataHelper
	 * @return array
	 */
	public function constructLocalSources(entry $dbEntry, $localFlavors, kContextDataHelper $contextDataHelper)
	{
		$sources = array();

		if (!count($localFlavors))
			return $sources;

		$deliveryAttributes = DeliveryProfileDynamicAttributes::init(null, $dbEntry->getId(), null);
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
				$sources[] = new kPlaybackSource($deliveryProfile->getId(), $deliveryProfile->getStreamerType(), $deliveryProfile->getPriority(), $this->constructProtocol($deliveryProfile), array_keys($deliveryProfileFlavors), null, $drmData);
		}

		return $sources;
	}

	/**
	 * @param entry $dbEntry
	 * @param $remoteDeliveryProfileIds
	 * @param $remoteDcByDeliveryProfile
	 * @param $remoteFlavorsByDc
	 * @param kContextDataHelper $contextDataHelper
	 * @return array
	 */
	public function constructRemoteSources(entry $dbEntry, $remoteDeliveryProfileIds, $remoteDcByDeliveryProfile, $remoteFlavorsByDc, kContextDataHelper $contextDataHelper)
	{
		$sources = array();
		if (!count($remoteFlavorsByDc))
			return $sources;

		$deliveryAttributes = DeliveryProfileDynamicAttributes::init(null, $dbEntry->getId(), null);
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
					$sources[] = new kPlaybackSource($deliveryProfile->getId(), $deliveryProfile->getStreamerType(), $deliveryProfile->getPriority(), $this->constructProtocol($deliveryProfile), $dcFlavorIds, null, $flavorToDrmData);
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

	/* @param entry $dbEntry
	 * @param $flavorAssets
	 * @param $deliveryProfile
	 * @return array
	 */
	protected function getDrmData(entry $dbEntry, &$flavorAssets, $deliveryProfile)
	{
		$playbackContextDataParams = new kPlaybackContextDataParams();
		$playbackContextDataParams->setDeliveryProfile($deliveryProfile);
		$playbackContextDataParams->setFlavors(array_values($flavorAssets));

		$result = new kPlaybackContextDataResult();
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaPlaybackContextDataContributor');
		foreach ($pluginInstances as $pluginInstance)
			$pluginInstance->contributeToPlaybackContextDataResult($dbEntry, $playbackContextDataParams, $result);

		if (count($result->getFlavorIdsToRemove()))
			$this->filterFlavorAssets($flavorAssets, $result->getFlavorIdsToRemove(), true);

		return $result->getPluginData();
	}

	public function sortSources($localSources, $remoteSources, $servePriority)
	{
		$this->sortSourcesByPriority($localSources);
		$this->sortSourcesByPriority($remoteSources);

		switch ($servePriority)
		{
			case StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_ONLY:
				return $localSources;
				break;
			case StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_FIRST:
				return array_merge($localSources, $remoteSources);
				break;
			case StorageProfile::STORAGE_SERVE_PRIORITY_EXTERNAL_ONLY:
				return $remoteSources;
				break;
			case StorageProfile::STORAGE_SERVE_PRIORITY_EXTERNAL_FIRST:
				return array_merge($remoteSources, $localSources);
				break;
			default:
		}
	}

	protected function sortSourcesByPriority(&$sourcesArray)
	{
		usort($sourcesArray, function ($a, $b)
		{
			/* @var $a kPlaybackSource */
			/* @var $b kPlaybackSource */
			return (intval($a->getPriority()) - intval($b->getPriority()));
		});
	}

	public function filterFlavorsBySources(&$flavorAssets, $sources)
	{
		$flavorAssetsIds = array();
		foreach ($sources as $source)
		{
			/* @var $source kPlaybackSource */
			$flavorAssetsIds = array_merge($flavorAssetsIds,$source->getFlavors());
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