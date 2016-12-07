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
	 * @var bool
	 */
	private $isScheduledNow;

	/**
	 * @var array
	 */
	private $flavorAssets = array();
	private $localFlavors = array();
	private $remoteFlavorsByDc = array();
	private $remoteDeliveryProfileIds = array();
	private $remoteDcByDeliveryProfile = array();

	private $localPlaybackSources = array();
	private $remotePlaybackSources = array();

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

		$this->getRelevantFlavorAssets($contextDataHelper);
		$this->createFlavorsMapping($dbEntry);
		$this->constructLocalPlaybackSources($dbEntry, $contextDataHelper);
		$this->constructRemotePlaybackSources($dbEntry,$contextDataHelper);
		$this->sortAndSetPlaybackSources($dbEntry->getPartner()->getStorageServePriority());
		$this->filterFlavorsBySources();
		$this->playbackContextResult->setFlavorAssets($this->flavorAssets);
	}

	/**
	 * @param kContextDataHelper $contextDataHelper
	 * @return boolean
	 */
	private function hasBlockAction(kContextDataHelper $contextDataHelper)
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
	private function generateRestrictedMessages($contextDataHelper)
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
	private function getProfileIdsToFilter(kContextDataHelper $contextDataHelper)
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
	private function getFlavorsToFilter(kContextDataHelper $contextDataHelper)
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
	 */
	private function getRelevantFlavorAssets(kContextDataHelper $contextDataHelper)
	{
		$flavorAssets = $contextDataHelper->getAllowedFlavorAssets();

		list($flavorsIdsToFilter, $flavorsParamsNotIn) = $this->getFlavorsToFilter($contextDataHelper);

		if(count($flavorsIdsToFilter) || $flavorsParamsNotIn)
			self::filterFlavorAssets($flavorAssets, $flavorsIdsToFilter, $flavorsParamsNotIn);

		$this->flavorAssets = $flavorAssets;
	}


	/**
	 * @param entry $dbEntry
	 * @return array
	 */
	private function createFlavorsMapping(entry $dbEntry)
	{
		// get flavors availability
		$servePriority = $dbEntry->getPartner()->getStorageServePriority();

		$remoteFileSyncs = array();

		foreach ($this->flavorAssets as $flavorAsset)
		{
			$flavorId = $flavorAsset->getId();
			$key = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);

			$c = FileSyncPeer::getCriteriaForFileSyncKey($key);
			$c->addAnd(FileSyncPeer::STATUS, FileSync::FILE_SYNC_STATUS_READY);

			switch ($servePriority)
			{
				case StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_ONLY:
					$c->addAnd(FileSyncPeer::FILE_TYPE, FileSync::FILE_SYNC_FILE_TYPE_URL, Criteria::NOT_EQUAL);
					break;

				case StorageProfile::STORAGE_SERVE_PRIORITY_EXTERNAL_ONLY:
					$c->addAnd(FileSyncPeer::FILE_TYPE, FileSync::FILE_SYNC_FILE_TYPE_URL);
					break;
				default:
					break;
			}

			$fileSyncs = FileSyncPeer::doSelect($c);

			foreach ($fileSyncs as $fileSync)
			{
				if ($fileSync->getFileType() == FileSync::FILE_SYNC_FILE_TYPE_URL)
				{
					$dc = $fileSync->getDc();
					$this->remoteFlavorsByDc[$dc] [] = $flavorAsset;
					$remoteFileSyncs[$dc][$flavorId] = $fileSync;
				} else
				{
					$this->localFlavors[$fileSync->getObjectId()] = $flavorAsset;
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
					$this->remoteDcByDeliveryProfile[$deliveryProfileId] = $storageProfile->getId();

				$this->remoteDeliveryProfileIds = array_merge($this->remoteDeliveryProfileIds, $deliveryProfilesIds);
			}

			foreach ($storageProfileIds as $storageProfileId)
			{
				if (in_array($storageProfileId, $activeStorageProfileIds))
					continue;

				unset($remoteFileSyncs[$storageProfileId]);
				unset($this->remoteFlavorsByDc[$storageProfileId]);
			}
		}
	}


	/**
	 * @param entry $dbEntry
	 * @param kContextDataHelper $contextDataHelper
	 * @return array
	 */
	private function constructLocalPlaybackSources(entry $dbEntry, kContextDataHelper $contextDataHelper)
	{
		if (!count($this->localFlavors))
			return;

		$deliveryAttributes = DeliveryProfileDynamicAttributes::init(null, $dbEntry->getId(), null);

		$localDeliveryProfileIds = array();
		if (count($dbEntry->getPartner()->getDeliveryProfileIds()))
			$localDeliveryProfileIds = call_user_func_array('array_merge', $dbEntry->getPartner()->getDeliveryProfileIds());

		$localDeliveryProfiles = DeliveryProfilePeer::getDeliveryProfilesByIds($dbEntry, $localDeliveryProfileIds, $dbEntry->getPartner(), $deliveryAttributes);

		if ($dbEntry->getPartner()->getEnforceDelivery())
		{
			$streamsTypesToExclude = $this->getStreamsTypeToExclude($localDeliveryProfiles);
			$defaultDeliveryProfiles = DeliveryProfilePeer::getDefaultDeliveriesFilteredByStreamerTypes($dbEntry, $dbEntry->getPartner(), $streamsTypesToExclude);
			$localDeliveryProfiles = array_merge($localDeliveryProfiles, $defaultDeliveryProfiles);
		}

		list($deliveryProfileIds, $deliveryProfilesParamsNotIn) = $this->getProfileIdsToFilter($contextDataHelper);

		if (count($deliveryProfileIds) || $deliveryProfilesParamsNotIn)
			$this->filterDeliveryProfiles($localDeliveryProfiles, $deliveryProfileIds, $deliveryProfilesParamsNotIn);

		foreach ($localDeliveryProfiles as $deliveryProfile)
		{
			$deliveryProfileFlavors = $this->localFlavors;
			list($drmData, $playbackFlavors) = self::getDrmData($dbEntry, $deliveryProfileFlavors, $deliveryProfile);

			if (count($playbackFlavors))
			{
				$manifestUrl = myEntryUtils::buildManifestUrl($dbEntry, $deliveryProfile->getStreamerType(), $playbackFlavors, null);
				$this->localPlaybackSources[] = new kPlaybackSource($deliveryProfile->getId(), $deliveryProfile->getStreamerType(), $deliveryProfile->getPriority(), $this->constructProtocol($deliveryProfile), array_keys($playbackFlavors), $manifestUrl, $drmData);
			}
		}
	}

	/**
	 * @param entry $dbEntry
	 * @param kContextDataHelper $contextDataHelper
	 * @return array
	 */
	private function constructRemotePlaybackSources(entry $dbEntry, kContextDataHelper $contextDataHelper)
	{
		if (!count($this->remoteFlavorsByDc))
			return;

		$deliveryAttributes = DeliveryProfileDynamicAttributes::init(null, $dbEntry->getId(), null);
		$remoteDeliveryProfiles = DeliveryProfilePeer::getDeliveryProfilesByIds($dbEntry, $this->remoteDeliveryProfileIds, $dbEntry->getPartner(), $deliveryAttributes);

		list($deliveryProfileIds, $deliveryProfilesParamsNotIn) = $this->getProfileIdsToFilter($contextDataHelper);
		if (count($deliveryProfileIds) || $deliveryProfilesParamsNotIn)
			$this->filterDeliveryProfiles($remoteDeliveryProfiles, $deliveryProfileIds, $deliveryProfilesParamsNotIn);

		foreach ($remoteDeliveryProfiles as $deliveryProfile)
		{
			$dcFlavorIds = array();
			$dcId = $this->remoteDcByDeliveryProfile[$deliveryProfile->getId()];
			$flavorAssetsForDc = $this->remoteFlavorsByDc[$dcId];

			if (count($flavorAssetsForDc))
			{
				$deliveryProfileFlavorsForDc = $flavorAssetsForDc;

				list($flavorToDrmData, $filteredDeliveryProfileFlavorsForDc) = self::getDrmData($dbEntry, $deliveryProfileFlavorsForDc, $deliveryProfile);

				if (count($filteredDeliveryProfileFlavorsForDc))
				{
					foreach ($filteredDeliveryProfileFlavorsForDc as $flavorAssetForDc)
						$dcFlavorIds[] = $flavorAssetForDc->getId();

					$manifestUrl = myEntryUtils::buildManifestUrl($dbEntry, $deliveryProfile->getStreamerType(), $filteredDeliveryProfileFlavorsForDc, $dcId);
					$this->remotePlaybackSources[] = new kPlaybackSource($deliveryProfile->getId(), $deliveryProfile->getStreamerType(), $deliveryProfile->getPriority(), $this->constructProtocol($deliveryProfile), $dcFlavorIds, $manifestUrl, $flavorToDrmData);
				}
			}
		}
	}

	/**
	 * @param $flavorAssets
	 * @param $flavorAssetsIdsToFilter
	 * @param $flavorAssetsParamsNotIn
	 */
	private static function filterFlavorAssets(&$flavorAssets, $flavorAssetsIdsToFilter, $flavorAssetsParamsNotIn)
	{
		foreach ($flavorAssets as $key => $flavorAsset)
		{
			if (in_array($flavorAsset->getId(), $flavorAssetsIdsToFilter))
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
	 * @param $deliveryProfileIdsToFilter
	 * @param $deliveryProfilesParamsNotIn
	 */
	private function filterDeliveryProfiles(&$deliveryProfiles, $deliveryProfileIdsToFilter, $deliveryProfilesParamsNotIn)
	{
		foreach ($deliveryProfiles as $key => $deliveryProfile)
		{
			if (in_array($deliveryProfile->getId(), $deliveryProfileIdsToFilter))
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

	private function getStreamsTypeToExclude($localCustomDeliveries)
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
	private static function getDrmData(entry $dbEntry, $flavorAssets, $deliveryProfile)
	{
		$playbackContextDataParams = new kPlaybackContextDataParams();
		$playbackContextDataParams->setDeliveryProfile($deliveryProfile);
		$playbackContextDataParams->setFlavors(array_values($flavorAssets));

		$result = new kPlaybackContextDataResult();
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaPlaybackContextDataContributor');
		foreach ($pluginInstances as $pluginInstance)
			$pluginInstance->contributeToPlaybackContextDataResult($dbEntry, $playbackContextDataParams, $result);

		if (count($result->getFlavorIdsToRemove()))
			self::filterFlavorAssets($flavorAssets, $result->getFlavorIdsToRemove(), true);

		return array($result->getPluginData(),$flavorAssets) ;
	}

	private function sortAndSetPlaybackSources($servePriority)
	{
		self::sortSourcesByPriority($this->localPlaybackSources);
		self::sortSourcesByPriority($this->remotePlaybackSources);

		switch ($servePriority)
		{
			case StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_ONLY:
				$this->playbackContextResult->setSources($this->localPlaybackSources);
				break;
			case StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_FIRST:
				$this->playbackContextResult->setSources(array_merge($this->localPlaybackSources,$this->remotePlaybackSources));
				break;
			case StorageProfile::STORAGE_SERVE_PRIORITY_EXTERNAL_ONLY:
				$this->playbackContextResult->setSources($this->remotePlaybackSources);
				break;
			case StorageProfile::STORAGE_SERVE_PRIORITY_EXTERNAL_FIRST:
				$this->playbackContextResult->setSources(array_merge($this->remotePlaybackSources,$this->localPlaybackSources));
				break;
			default:
				$this->playbackContextResult->setSources(array());
				break;
		}
	}

	private static function sortSourcesByPriority(&$sourcesArray)
	{
		usort($sourcesArray, function ($a, $b)
		{
			/* @var $a kPlaybackSource */
			/* @var $b kPlaybackSource */
			return (intval($a->getPriority()) - intval($b->getPriority()));
		});
	}

	private function filterFlavorsBySources()
	{
		$flavorAssetsIds = array();
		foreach ($this->playbackContextResult->getSources() as $source)
		{
			/* @var $source kPlaybackSource */
			$flavorAssetsIds = array_merge($flavorAssetsIds,$source->getFlavors());
		}

		self::filterFlavorAssets($this->flavorAssets, $flavorAssetsIds, false );
	}

	/**
	 * @param $deliveryProfile
	 * @return array
	 * */
	private function constructProtocol($deliveryProfile)
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