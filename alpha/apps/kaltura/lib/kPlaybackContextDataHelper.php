<?php
class kPlaybackContextDataHelper
{
	/**
	 * @var kPlaybackContext
	 */
	private $playbackContext;

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
	private $playbackCaptions = array();
	private $localPlaybackSources = array();
	private $remotePlaybackSources = array();
	private $kalturaStoragePlaybackSources = array();
	private $storageIds = array();

	public function getPlaybackContext()
	{
		return $this->playbackContext;
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
		$this->playbackContext = new kPlaybackContext();
		$this->storageIds = kStorageExporter::getPeriodicStorageIds();

		$this->generateRestrictedMessages($contextDataHelper);

		if ($this->hasBlockAction($contextDataHelper))
			return;

		$this->getRelevantFlavorAssets($contextDataHelper);

		if (myEntryUtils::shouldServeVodFromLive($dbEntry))
		{
			$rootEntryId = $dbEntry->getRootEntryId();
			$rootEntry = entryPeer::retrieveByPK($rootEntryId);
			if (!$rootEntry)
				throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $rootEntryId);

			$this->constructLivePlaybackSources($rootEntry, $contextDataHelper, $dbEntry);
		} elseif ($dbEntry->getType() == entryType::LIVE_STREAM)
		{
			$this->constructLivePlaybackSources($dbEntry, $contextDataHelper);
		} else
		{
			$this->createFlavorsMapping($dbEntry);
			$this->constructLocalPlaybackSources($dbEntry, $contextDataHelper);
			$this->constructRemotePlaybackSources($dbEntry, $contextDataHelper);
			$this->constructPlaybackCaptions($dbEntry, $contextDataHelper);
		}

		$this->setPlaybackSources($dbEntry->getPartner()->getStorageServePriority());
		$this->filterFlavorsBySources();
		$this->playbackContext->setFlavorAssets($this->flavorAssets);
		$this->playbackContext->setPlaybackCaptions($this->playbackCaptions);
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
		$playbackAccessContorlMessages = array();

		foreach ($contextDataHelper->getContextDataResult()->getRulesCodesMap() as $code => $messages)
		{
			foreach ($messages as $message)
				$playbackAccessContorlMessages[] = new kAccessControlMessage($code, $message);
		}

		if ($contextDataHelper->getContextDataResult()->getIsCountryRestricted())
			$playbackAccessContorlMessages[] = new kAccessControlMessage(RuleRestrictions::COUNTRY_RESTRICTED_CODE, RuleRestrictions::COUNTRY_RESTRICTED);
		if ($contextDataHelper->getContextDataResult()->getIsIpAddressRestricted())
			$playbackAccessContorlMessages[] = new kAccessControlMessage(RuleRestrictions::IP_RESTRICTED_CODE, RuleRestrictions::IP_RESTRICTED);
		if ($contextDataHelper->getContextDataResult()->getIsSessionRestricted()
			&& ($contextDataHelper->getContextDataResult()->getPreviewLength() == -1 || is_null($contextDataHelper->getContextDataResult()->getPreviewLength())))
			$playbackAccessContorlMessages[] = new kAccessControlMessage(RuleRestrictions::SESSION_RESTRICTED_CODE, RuleRestrictions::SESSION_RESTRICTED);
		if ($contextDataHelper->getContextDataResult()->getIsUserAgentRestricted())
			$playbackAccessContorlMessages[] = new kAccessControlMessage(RuleRestrictions::USER_AGENT_RESTRICTED_CODE, RuleRestrictions::USER_AGENT_RESTRICTED);
		if ($contextDataHelper->getContextDataResult()->getIsSiteRestricted())
			$playbackAccessContorlMessages[] = new kAccessControlMessage(RuleRestrictions::SITE_RESTRICTED_CODE, RuleRestrictions::SITE_RESTRICTED);
		if (!$this->isScheduledNow)
			$playbackAccessContorlMessages[] = new kAccessControlMessage(RuleRestrictions::SCHEDULED_RESTRICTED_CODE, RuleRestrictions::SCHEDULED_RESTRICTED);

		$this->playbackContext->setMessages($playbackAccessContorlMessages);
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
	private function getWhiteListedDeliveryProfileIds(kContextDataHelper $contextDataHelper)
	{
		$actions = $contextDataHelper->getContextDataResult()->getActions();
		foreach ($actions as $action)
		{
			/* @var $action kAccessControlAction */
			if ($action->getType() == RuleActionType::LIMIT_DELIVERY_PROFILES && !$action->getIsBlockedList() && $action->getDeliveryProfileIds())
			{
				/* @var $action kAccessControlLimitDeliveryProfilesAction */
				return explode(',', $action->getDeliveryProfileIds());
			}
		}
		return array();
	}

	/**
	 * @param kContextDataHelper $contextDataHelper
	 * @return array
	 */
	private function getFlavorParamsIdsToFilter(kContextDataHelper $contextDataHelper)
	{
		$actions = $contextDataHelper->getContextDataResult()->getActions();
		$flavorParamsIds = null;
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
		return array($flavorParamsIds, $flavorsParamsNotIn);
	}


	/**
	 * @param entry $dbEntry
	 * @param kContextDataHelper $contextDataHelper
	 */
	private function getRelevantFlavorAssets(kContextDataHelper $contextDataHelper)
	{
		$flavorAssets = $contextDataHelper->getAllowedFlavorAssets();

		list($flavorParamsIdsToFilter, $flavorsParamsNotIn) = $this->getFlavorParamsIdsToFilter($contextDataHelper);

		if($flavorParamsIdsToFilter && count($flavorParamsIdsToFilter) || $flavorsParamsNotIn)
			self::filterFlavorAssetsByFlavorParamsIds($flavorAssets, $flavorParamsIdsToFilter, $flavorsParamsNotIn);

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

		if($dbEntry->getType() == entryType::LIVE_STREAM)
			return;

		foreach ($this->flavorAssets as $flavorAsset)
		{
			$flavorId = $flavorAsset->getId();
			$key = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);

			$fileSyncs = kFileSyncUtils::getFileSyncsByStoragePriority($key,$servePriority, $this->storageIds );

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

				$deliveryProfilesIds = array();
				$ids = $storageProfile->getFromCustomData("delivery_profile_ids");
				if(!empty($ids))
					$deliveryProfilesIds = call_user_func_array('array_merge', $ids);

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
	private function constructLivePlaybackSources(entry $dbEntry, kContextDataHelper $contextDataHelper, $replacementEntry = null)
	{
		$deliveryAttributes = DeliveryProfileDynamicAttributes::init(null, $dbEntry->getId(), null);

		$liveDeliveryProfileIds = array();

		$customDeliveryProfilesIds = DeliveryProfilePeer::getCustomDeliveryProfileIds($dbEntry, $dbEntry->getPartner(), $deliveryAttributes);
		if (count($customDeliveryProfilesIds))
			$liveDeliveryProfileIds = call_user_func_array('array_merge', $customDeliveryProfilesIds);

		$liveDeliveryProfiles = DeliveryProfilePeer::getDeliveryProfilesByIds($dbEntry, $liveDeliveryProfileIds, $dbEntry->getPartner(), $deliveryAttributes);

		if (!$dbEntry->getPartner()->getEnforceDelivery() && !in_array($dbEntry->getSource(), array(EntrySourceType::MANUAL_LIVE_STREAM, EntrySourceType::AKAMAI_UNIVERSAL_LIVE)))
			$liveDeliveryProfiles = $this->getAllowedDeliveryProfiles($dbEntry, $contextDataHelper, $liveDeliveryProfiles);

		list($deliveryProfileIds, $deliveryProfilesParamsNotIn) = $this->getProfileIdsToFilter($contextDataHelper);

		if (count($deliveryProfileIds) || $deliveryProfilesParamsNotIn)
			$this->filterDeliveryProfiles($liveDeliveryProfiles, $deliveryProfileIds, $deliveryProfilesParamsNotIn);

		$this->filterDeliveryProfilesByStreamerType($liveDeliveryProfiles, $contextDataHelper);

		$flavorAssets = $contextDataHelper->getAllowedFlavorAssets();

		foreach ($liveDeliveryProfiles as $deliveryProfile)
		{
			list($drmData, $playbackFlavors) = self::getDrmData($dbEntry, $flavorAssets, $deliveryProfile, $contextDataHelper);
			$playbackFlavorParamsIds = array();
			foreach ($playbackFlavors as $playbackFlavor)
			{
				/* @var $playbackFlavor flavorAsset */
				$playbackFlavorParamsIds [] = $playbackFlavor->getId();
			}

			$protocols = $this->constructProtocols($deliveryProfile, $contextDataHelper);
			if (!empty($protocols))
			{
				if ($replacementEntry)
					$manifestUrl = myEntryUtils::buildManifestUrl($replacementEntry, $protocols, $deliveryProfile->getStreamerType(), $playbackFlavors, $deliveryProfile->getId());
				else
					$manifestUrl = myEntryUtils::buildManifestUrl($dbEntry, $protocols, $deliveryProfile->getStreamerType(), $playbackFlavors, $deliveryProfile->getId());
				$this->localPlaybackSources[] = new kPlaybackSource($deliveryProfile->getId(), $deliveryProfile->getStreamerType(), implode(",", $protocols), implode(",", $playbackFlavorParamsIds), $manifestUrl, $drmData);
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

		$customDeliveryProfilesIds = DeliveryProfilePeer::getCustomDeliveryProfileIds($dbEntry, $dbEntry->getPartner(), $deliveryAttributes);
		if (count($customDeliveryProfilesIds))
			$localDeliveryProfileIds = call_user_func_array('array_merge', $customDeliveryProfilesIds);

		$localDeliveryProfiles = DeliveryProfilePeer::getDeliveryProfilesByIds($dbEntry, $localDeliveryProfileIds, $dbEntry->getPartner(), $deliveryAttributes);

		if (!$dbEntry->getPartner()->getEnforceDelivery())
			$localDeliveryProfiles = $this->getAllowedDeliveryProfiles($dbEntry, $contextDataHelper, $localDeliveryProfiles);

		list($deliveryProfileIds, $deliveryProfilesParamsNotIn) = $this->getProfileIdsToFilter($contextDataHelper);

		if ($deliveryProfileIds && count($deliveryProfileIds) || $deliveryProfilesParamsNotIn)
			$this->filterDeliveryProfiles($localDeliveryProfiles, $deliveryProfileIds, $deliveryProfilesParamsNotIn, $contextDataHelper);

		$this->filterDeliveryProfilesByStreamerType($localDeliveryProfiles, $contextDataHelper);

		foreach ($localDeliveryProfiles as $deliveryProfile)
		{
			$deliveryProfileFlavors = $this->localFlavors;

			$flavorTagsArrayByPriority = $this->getTagsByFormat($deliveryProfile->getStreamerType());
			$deliveryProfileFlavors = $this->filterFlavorsByTags($flavorTagsArrayByPriority, $deliveryProfileFlavors);

			list($drmData, $playbackFlavors) = self::getDrmData($dbEntry, $deliveryProfileFlavors, $deliveryProfile, $contextDataHelper);

			if (count($playbackFlavors))
			{
				$protocols = $this->constructProtocols($deliveryProfile, $contextDataHelper);
				if (!empty($protocols))
				{
					$manifestUrl = myEntryUtils::buildManifestUrl($dbEntry, $protocols, $deliveryProfile->getStreamerType(), $playbackFlavors, $deliveryProfile->getId());
					$this->localPlaybackSources[] = new kPlaybackSource($deliveryProfile->getId(), $deliveryProfile->getStreamerType(), implode(",", $protocols), implode(",", array_keys($playbackFlavors)), $manifestUrl, $drmData);
				}
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
		if (!empty($deliveryProfileIds) || $deliveryProfilesParamsNotIn)
			$this->filterDeliveryProfiles($remoteDeliveryProfiles, $deliveryProfileIds, $deliveryProfilesParamsNotIn, $contextDataHelper);

		$this->filterDeliveryProfilesByStreamerType($remoteDeliveryProfiles, $contextDataHelper);

		foreach ($remoteDeliveryProfiles as $deliveryProfile)
		{
			$dcFlavorIds = array();
			$dcId = $this->remoteDcByDeliveryProfile[$deliveryProfile->getId()];
			$flavorAssetsForDc = $this->remoteFlavorsByDc[$dcId];

			if (count($flavorAssetsForDc))
			{
				$deliveryProfileFlavorsForDc = $flavorAssetsForDc;

				$flavorTagsArrayByPriority = $this->getTagsByFormat($deliveryProfile->getStreamerType());
				$deliveryProfileFlavorsForDc = $this->filterFlavorsByTags($flavorTagsArrayByPriority, $deliveryProfileFlavorsForDc);

				list($flavorToDrmData, $filteredDeliveryProfileFlavorsForDc) = self::getDrmData($dbEntry, $deliveryProfileFlavorsForDc, $deliveryProfile, $contextDataHelper);

				if (count($filteredDeliveryProfileFlavorsForDc))
				{
					foreach ($filteredDeliveryProfileFlavorsForDc as $flavorAssetForDc)
						$dcFlavorIds[] = $flavorAssetForDc->getId();

					$protocols = $this->constructProtocols($deliveryProfile, $contextDataHelper);
					if (!empty($protocols))
					{
						$manifestUrl = myEntryUtils::buildManifestUrl($dbEntry, $protocols, $deliveryProfile->getStreamerType(), $filteredDeliveryProfileFlavorsForDc, $deliveryProfile->getId());
						$playbackSource = new kPlaybackSource($deliveryProfile->getId(), $deliveryProfile->getStreamerType(), implode(",", $protocols), implode(",", array_values($dcFlavorIds)), $manifestUrl, $flavorToDrmData);
						if (in_array($dcId, $this->storageIds))
						{
							$this->kalturaStoragePlaybackSources[] = $playbackSource;
						}
						else
						{
							$this->remotePlaybackSources[] = $playbackSource;
						}
					}
				}
			}
		}
	}

	/**
	 * @param entry $dbEntry
	 */
	private function constructPlaybackCaptions(entry $dbEntry, $contextDataHelper)
	{
		$this->playbackCaptions = self::getPlaybackCaptionsData($dbEntry, $contextDataHelper);
	}

	private function getTagsByFormat($format)
	{
		switch ($format)
		{
			case PlaybackProtocol::SILVER_LIGHT:
				return array(
					array(assetParams::TAG_ISM),
				);

			case PlaybackProtocol::MPEG_DASH:
				return array(
					array('dash', 'h265'),
					array('ipadnew', 'iphonenew'),
					array('ipad', 'iphone'),
				);

			case PlaybackProtocol::APPLE_HTTP:
				return array(
					array(assetParams::TAG_APPLEMBR),
					array('ipadnew', 'iphonenew', 'h265', 'dash'),
					array('ipad', 'iphone'),
				);
			case PlaybackProtocol::HDS:
				return array(
					array(assetParams::TAG_APPLEMBR),
					array('ipadnew', 'iphonenew'),
					array('ipad', 'iphone'),
				);
			case PlaybackProtocol::HTTP:
				return array(
					array('widevine', 'widevine_mbr'),
					array(assetParams::TAG_MBR),
					array(assetParams::TAG_WEB),
				);
			default:
				return array(
					array(assetParams::TAG_MBR),
					array(assetParams::TAG_WEB),
				);
		}
	}

	/**
	 * @param array $flavorTagsArrayByPriority
	 * @param array<asset|assetParams> $flavors
	 * @return array
	 */
	private function filterFlavorsByTags($flavorTagsArrayByPriority, $flavors)
	{
		foreach ($flavorTagsArrayByPriority as $tagsFallback)
		{
			$curFlavors = array();

			foreach ($flavors as $flavor)
			{
				foreach ($tagsFallback as $tagOption)
				{
					if (!$flavor->hasTag($tagOption))
						continue;
					$curFlavors[$flavor->getId()] = $flavor;
					break;
				}
			}

			if ($curFlavors)
				return $curFlavors;
		}
		return array();
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
	 * @param $flavorAssets
	 * @param $flavorParamsIdsToFilter
	 * @param $flavorAssetsParamsNotIn
	 */
	private static function filterFlavorAssetsByFlavorParamsIds(&$flavorAssets, $flavorParamsIdsToFilter, $flavorAssetsParamsNotIn)
	{
		foreach ($flavorAssets as $key => $flavorAsset)
		{
			/* @var flavorAsset $flavorAsset */
			if (in_array($flavorAsset->getFlavorParamsId(), $flavorParamsIdsToFilter))
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

	/**
	 * @param $deliveryProfiles
	 * @param $contextDataHelper
	 */
	private function filterDeliveryProfilesByStreamerType(&$deliveryProfiles, kContextDataHelper $contextDataHelper)
	{
		$streamerTypes = $contextDataHelper->getStreamerType();
		if (!is_null($streamerTypes))
		{
			$streamerTypes = explode(",", $streamerTypes);
			foreach ($deliveryProfiles as $key => $deliveryProfile)
			{
				if (!in_array($deliveryProfile->getStreamerType(), $streamerTypes))
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
	private static function getDrmData(entry $dbEntry, $flavorAssets, $deliveryProfile, $contextDataHelper)
	{
		$playbackContextDataParams = new kPlaybackContextDataParams();
		$playbackContextDataParams->setDeliveryProfile($deliveryProfile);
		$playbackContextDataParams->setFlavors(array_values($flavorAssets));
		$playbackContextDataParams->setType('drm');

		$result = new kPlaybackContextDataResult();
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaPlaybackContextDataContributor');
		foreach ($pluginInstances as $pluginInstance)
			$pluginInstance->contributeToPlaybackContextDataResult($dbEntry, $playbackContextDataParams, $result, $contextDataHelper);

		if (count($result->getFlavorIdsToRemove()))
			self::filterFlavorAssets($flavorAssets, $result->getFlavorIdsToRemove(), true);

		return array($result->getPluginData(), $flavorAssets);
	}

	/* @param entry $dbEntry
	 * @return array
	 */
	protected static function getPlaybackCaptionsData(entry $dbEntry, $contextDataHelper)
	{
		$playbackContextDataParams = new kPlaybackContextDataParams();
		$playbackContextDataParams->setType('caption');
		$result = new kPlaybackContextDataResult();
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaPlaybackContextDataContributor');
		foreach ($pluginInstances as $pluginInstance)
		{
			$pluginInstance->contributeToPlaybackContextDataResult($dbEntry, $playbackContextDataParams, $result, $contextDataHelper);
		}

		return $result->getPlaybackCaptions();
	}

	private function setPlaybackSources($servePriority)
	{
		switch ($servePriority)
		{
			case StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_ONLY:
				$this->playbackContext->setSources($this->getPlaybackSourcesForKalturOnlyPriority());
				break;
			case StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_FIRST:
				$this->playbackContext->setSources(array_merge($this->getPlaybackSourcesForKalturOnlyPriority(), $this->remotePlaybackSources));
				break;
			case StorageProfile::STORAGE_SERVE_PRIORITY_EXTERNAL_ONLY:
				$this->playbackContext->setSources($this->remotePlaybackSources);
				break;
			case StorageProfile::STORAGE_SERVE_PRIORITY_EXTERNAL_FIRST:
				$this->playbackContext->setSources(array_merge($this->remotePlaybackSources, $this->getPlaybackSourcesForKalturOnlyPriority()));
				break;
			default:
				$this->playbackContext->setSources(array());
				break;
		}
	}

	private function getPlaybackSourcesForKalturOnlyPriority()
	{
		/** Since Kaltura only can now have remote source (when starting use S3 storage as kaltura local storage)
		 *  we need to prioritize storage sources over local sources.
		 */
		return array_merge($this->kalturaStoragePlaybackSources, $this->localPlaybackSources);
	}

	private function filterFlavorsBySources()
	{
		$flavorAssetsIds = array();
		foreach ($this->playbackContext->getSources() as $source)
		{
			/* @var $source kPlaybackSource */
			$flavorAssetsIds = array_merge($flavorAssetsIds, explode(",", $source->getFlavorIds()));
		}

		self::filterFlavorAssets($this->flavorAssets, $flavorAssetsIds, false);
	}

	/**
	 * @param $deliveryProfile
	 * @param $contextDataHelper
	 * @return string
	 * */
	private function constructProtocols($deliveryProfile, kContextDataHelper $contextDataHelper)
	{
		$protocols = array();
		if (is_null($deliveryProfile->getMediaProtocols()))
		{
			if ($deliveryProfile->getStreamerType() == PlaybackProtocol::RTMP)
				$protocols[] = PlaybackProtocol::RTMP;
			else
			{
				$protocols[] = infraRequestUtils::PROTOCOL_HTTP;
				$protocols[] = infraRequestUtils::PROTOCOL_HTTPS;
			}
		} else
			$protocols = explode(",", $deliveryProfile->getMediaProtocols());

		$mediaProtocols = $contextDataHelper->getMediaProtocol();

		if (is_null($mediaProtocols))
			return $protocols;

		$mediaProtocols = explode(",", $mediaProtocols);
		foreach ($mediaProtocols as $mediaProtocol)
		{
			if (!in_array($mediaProtocol, $protocols))
				return array();
		}

		return $mediaProtocols;
	}

	/**
	 * @param entry $dbEntry
	 * @param kContextDataHelper $contextDataHelper
	 * @param $deliveryProfiles
	 * @return array
	 */
	private function getAllowedDeliveryProfiles(entry $dbEntry, kContextDataHelper $contextDataHelper, $deliveryProfiles)
	{
		$streamsTypesToExclude = $this->getStreamsTypeToExclude($deliveryProfiles);
		$whiteListedDeliveryProfileIds = $this->getWhiteListedDeliveryProfileIds($contextDataHelper);
		$defaultDeliveryProfiles = DeliveryProfilePeer::getDefaultOrWhiteListedDeliveries($dbEntry, $dbEntry->getPartner(), $streamsTypesToExclude, $whiteListedDeliveryProfileIds);
		$deliveryProfiles = array_merge($deliveryProfiles, $defaultDeliveryProfiles);
		return $deliveryProfiles;
	}

}
