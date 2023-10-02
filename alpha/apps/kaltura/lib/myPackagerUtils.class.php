<?php

class myPackagerUtils
{
	const PACKAGER_LIVE_THUMB_NAME = 'packager_live_thumb_name';
	const PACKAGER_LIVE_THUMB_URL = 'packager_live_thumb_url';
	const PACKAGER_MAPPED_THUMB_URL = 'packager_mapped_thumb_capture_url';
	const PACKAGER_LOCAL_THUMB_URL = 'packager_local_thumb_capture_url';
	const PACKAGER_REMOTE_THUMB_URL = 'packager_thumb_capture_url';
	const PACKAGER_MAPPED_VOLUME_MAP_URL = 'packager_mapped_volume_map_url';
	const PACKAGER_LOCAL_VOLUME_MAP_URL = 'packager_local_volume_map_url';
	const PACKAGER_REMOTE_VOLUME_MAP_URL = 'packager_remote_volume_map_url';
	const PACKAGER_URL = 'packager_url';
	const LIVE_PACKAGER_URL = 'live_packager_url';
	const RECORDING_LIVE_TYPE = 'recording';
	const MP4_FILENAME_PARAMETER = '/name/a.mp4';

	protected static $sessionCache = array();

	/**
	 * @param entry $entry
	 * @param $capturedThumbPath
	 * @param $calc_vid_sec
	 * @param $flavorAssetId
	 * @param int|null $width
	 * @param int|null $height
	 * @return bool
	 * @throws Exception
	 */
	public static function captureThumb($entry, $capturedThumbPath, $calc_vid_sec, &$flavorAssetId, $width = null, $height = null, $maxWidth = null, $serveVodFromLive = false)
	{
		if($serveVodFromLive)
		{
			return self::captureLiveThumb($entry, $capturedThumbPath, $calc_vid_sec, $width, $height);
		}
		else if ($entry->getType() == entryType::PLAYLIST)
		{
			return self::capturePlaylistThumb($entry, $capturedThumbPath, $calc_vid_sec, $flavorAssetId, $width, $height);
		}

		if(is_null($flavorAssetId) || ($flavorAssetId && !isset(self::$sessionCache["flavorAsset_".$flavorAssetId])))
		{
			$flavorAsset = self::getFlavorSupportedByPackagerForThumbCapture($entry->getEntryId());
			if(!$flavorAsset)
			{
				KalturaLog::info("No suitable packager flavor found for entry {$entry->getEntryId()}");
				return false;
			}

			$flavorAssetId = $flavorAsset->getId();
			self::$sessionCache["flavorAsset_".$flavorAssetId] = $flavorAsset;
			KalturaLog::info("Found flavor asset {$flavorAssetId}");
		}
		else
		{
			$flavorAsset = self::$sessionCache["flavorAsset_".$flavorAssetId];
		}

		if(!$width && !$height && $maxWidth && $flavorAsset->getWidth() > $maxWidth)
		{
			$width = $maxWidth;
		}

		if($flavorAsset->getEncryptionKey())
		{
			return self::captureMappedThumb($entry, $flavorAsset, $capturedThumbPath, $calc_vid_sec, $width, $height);
		}

		$fileSyncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
		$currentDcId = kDataCenterMgr::getCurrentDcId();
		$preferredStorageId = self::getPreferredStorageId($currentDcId);

		if(!isset(self::$sessionCache['assetInfo_'.$flavorAssetId]))
		{
			list ($fileSync, $path, $sourceType) = kFileSyncUtils::getFileSyncServeFlavorFields($fileSyncKey, $flavorAsset, $preferredStorageId, null);
			self::$sessionCache['assetInfo_'.$flavorAssetId] = array ($fileSync, $path, $sourceType);
		}
		else
		{
			list ($fileSync, $path, $sourceType) = self::$sessionCache['assetInfo_'.$flavorAssetId];
		}

		if(!$fileSync)
		{
			return self::captureRemoteThumbByDeliveryProfile($capturedThumbPath, $calc_vid_sec, $flavorAsset, $width, $height);
		}

		KalturaLog::debug("TTT: currentDcId [$$currentDcId] fileSync DC [{$fileSync->getDc()}] isCloudDc [" .myCloudUtils::isCloudDc($currentDcId) . "]");
		if(myCloudUtils::isCloudDc($currentDcId) || $fileSync->getDc() != $currentDcId)
		{
			KalturaLog::debug("TTT: shred dc id " . print_r(kDataCenterMgr::getSharedStorageProfileIds(), true) . " getDcIds " . print_r(kDataCenterMgr::getDcIds(), true));
			if(in_array($fileSync->getDc(), kDataCenterMgr::getDcIds()) || in_array($fileSync->getDc(), kDataCenterMgr::getSharedStorageProfileIds($fileSync->getPartnerId())))
			{
				KalturaLog::debug("TTT: In a");
				return self::captureRemoteThumb($path, $capturedThumbPath, $calc_vid_sec, $width, $height);
			}
			else
			{
				KalturaLog::debug("TTT: In b");
				return self::captureRemoteThumbByDeliveryProfile($capturedThumbPath, $calc_vid_sec, $flavorAsset, $width, $height);
			}
		}
		else
		{
			$entry_data_path = $fileSync->getFilePath();
			$entry_data_path = ltrim($entry_data_path, '/');
			return self::captureLocalThumb($entry_data_path, $capturedThumbPath, $calc_vid_sec, $width, $height);
		}

	}

	/**
	 * @param $capturedThumbPath
	 * @param $calc_vid_sec
	 * @param $flavorAsset
	 * @param $width
	 * @param $height
	 * @return bool
	 * @throws Exception
	 */
	protected static function captureRemoteThumbByDeliveryProfile($capturedThumbPath, $calc_vid_sec, $flavorAsset, $width = null, $height = null)
	{
		$packagerCaptureUrl = self::getPackagerUrlFromConf(kPackagerUrlType::REMOTE_THUMB);
		if ($packagerCaptureUrl)
		{
			$flavorSyncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
			$remoteFS = kFileSyncUtils::getReadyExternalFileSyncForKey($flavorSyncKey);
			if ($remoteFS)
			{
				$deliveryProfile = DeliveryProfilePeer::getRemoteDeliveryByStorageId(DeliveryProfileDynamicAttributes::init($remoteFS->getDc(), $flavorAsset->getEntryId()), null, $flavorAsset);
				if (is_null($deliveryProfile))
				{
					return false;
				}

				$url = $deliveryProfile->getFileSyncUrl($remoteFS);
				if (strpos($url, "://") === false)
				{
					$url = rtrim($deliveryProfile->getUrl(), "/") . "/" . ltrim($url, '/');
				}

				@list($baseUrl, $queryString) = explode("?", $url, 2);
				$postFix = '';
				if ($queryString)
				{
					$postFix = "?$queryString";
				}

				$baseUrl = str_replace("://", "/", $baseUrl);
				return self::curlThumbUrlWithOffset($baseUrl, $calc_vid_sec, $packagerCaptureUrl, $capturedThumbPath, $width, $height, $postFix);
			}
		}

		return false;
	}

	/**
	 * @param flavorAsset $flavorAsset
	 * @param bool $excludeAudioFlavors
	 * @return bool
	 */
	public static function isFlavorSupportedByPackager($flavorAsset, $excludeAudioFlavors = true)
	{
		if($excludeAudioFlavors)
		{
			if (!$flavorAsset->getVideoCodecId() || ($flavorAsset->getWidth() == 0) || ($flavorAsset->getHeight() == 0))
			{
				return false;
			}
		}

		if($flavorAsset->hasTag(flavorParams::TAG_WEB) && myEntryUtils::isSupportedContainerFormat($flavorAsset))
		{
			// SUP-24966: Validate that container format is not mp3 via MediaInfo, since it's more accurate information
			if(!$excludeAudioFlavors)
			{
				$mediaInfo = mediaInfoPeer::retrieveByFlavorAssetId($flavorAsset->getId());
				if($mediaInfo && $mediaInfo->getContainerFormat() === assetParams::CONTAINER_FORMAT_MP3)
				{
					return false;
				}
			}
			return true;
		}

		return false;
	}

	public static function getFlavorSupportedByPackagerForVolumeMap($entryId)
	{
		$flavorAsset = assetPeer::retrieveLowestBitrateByEntryId($entryId);
		if (is_null($flavorAsset) || !self::isFlavorSupportedByPackager($flavorAsset, false))
		{
			// look for the lowest bitrate flavor the packager can parse
			$flavorAsset = assetPeer::retrieveLowestBitrateByEntryId($entryId, flavorParams::TAG_MBR);
			if (is_null($flavorAsset) || !self::isFlavorSupportedByPackager($flavorAsset, false))
			{
				//retrieve original ready
				$flavorAsset = assetPeer::retrieveOriginalReadyByEntryId($entryId);
				if (is_null($flavorAsset) || !self::isFlavorSupportedByPackager($flavorAsset, false))
				{
					return null;
				}
			}
		}

		return $flavorAsset;
	}

	/**
	 * @param entry $entry
	 * @param flavorAsset $flavorAsset
	 * @return string
	 */
	protected static function buildThumbUrl($entry, $flavorAsset)
	{
		$partnerId = $flavorAsset->getPartnerId();
		$subpId = $entry->getSubpId();
		$partnerPath = myPartnerUtils::getUrlForPartner($partnerId, $subpId);
		$entryVersion = $entry->getVersion();
		$url = "$partnerPath/serveFlavor/entryId/".$entry->getId();
		$url .= ($entryVersion ? "/v/$entryVersion" : '');
		$url .= '/flavorId/' . $flavorAsset->getId();
		$url .= self::MP4_FILENAME_PARAMETER;
		return $url;
	}

	/**
	 * @param entry $entry
	 * @param flavorAsset $flavorAsset
	 * @return string
	 */
	protected static function buildPlaylistThumbUrl($entry, $flavorAsset)
	{
		$partnerId = $flavorAsset->getPartnerId();
		$subpId = $entry->getSubpId();
		$partnerPath = myPartnerUtils::getUrlForPartner($partnerId, $subpId);
		$entryVersion = $entry->getVersion();
		$url = "$partnerPath/serveFlavor/entryId/".$entry->getId();
		$url .= ($entryVersion ? "/v/$entryVersion" : '');
		$url .= '/flavorParamIds/' . $flavorAsset->getFlavorParamsId();
		$url .= self::MP4_FILENAME_PARAMETER;
		return $url;
	}

	public static function getFlavorSupportedByPackagerForThumbCapture($entryId)
	{
		//look for the highest bitrate flavor tagged with thumbsource
		$flavorAsset = assetPeer::retrieveHighestBitrateByEntryId($entryId, flavorParams::TAG_THUMBSOURCE);
		if(is_null($flavorAsset) || !self::isFlavorSupportedByPackager($flavorAsset))
		{
			// look for the highest bitrate flavor the packager can parse
			$flavorAsset = assetPeer::retrieveHighestBitrateByEntryId($entryId, flavorParams::TAG_MBR);
			if (is_null($flavorAsset) || !self::isFlavorSupportedByPackager($flavorAsset))
			{
				//retrieve original ready
				$flavorAsset = assetPeer::retrieveOriginalReadyByEntryId($entryId);
				if(is_null($flavorAsset) || !self::isFlavorSupportedByPackager($flavorAsset))
				{
					return null;
				}
			}
		}

		return $flavorAsset;
	}

	/**
	 * load storage profiles array for sorting
	 *
	 * @param int $partnerId
	 * @return array
	 * @throws PropelException
	 */
	protected static function loadStorageProfiles($partnerId)
	{
		$criteria = new Criteria();
		$criteria->add(StorageProfilePeer::PARTNER_ID, $partnerId);
		$criteria->add(StorageProfilePeer::DELIVERY_STATUS, StorageProfileDeliveryStatus::BLOCKED, Criteria::NOT_EQUAL);
		$criteria->addAscendingOrderByColumn(StorageProfilePeer::ID);
		$results = StorageProfilePeer::doSelect($criteria);
		$storageProfiles = array();
		foreach ($results as $result)
		{
			$storageProfiles[$result->getId()] = $result;
		}

		return $storageProfiles;
	}

	protected static function getPackagerUrlFromConf($packagerUrlType, $packagerUrl = null)
	{
		$result = null;

		if (!$packagerUrl)
		{
			$packagerUrl = kConf::get(self::PACKAGER_URL,kConfMapNames::LOCAL_SETTINGS, null);
		}

		KalturaLog::debug("TTT: getPackagerUrlFromConf packagerUrl [$packagerUrl] packagerUrlType [$packagerUrlType]");
		switch ($packagerUrlType)
		{
			case kPackagerUrlType::REGULAR_THUMB:
				$result = $packagerUrl . kConf::get(self::PACKAGER_LOCAL_THUMB_URL, kConfMapNames::LOCAL_SETTINGS, null);
				break;
			case kPackagerUrlType::MAPPED_THUMB:
				$result = $packagerUrl . kConf::get(self::PACKAGER_MAPPED_THUMB_URL, kConfMapNames::LOCAL_SETTINGS, null);
				break;
			case kPackagerUrlType::REMOTE_THUMB:
				$result = kConf::get(self::PACKAGER_URL,kConfMapNames::LOCAL_SETTINGS, null) . kConf::get(self::PACKAGER_REMOTE_THUMB_URL, kConfMapNames::LOCAL_SETTINGS, null);
				break;
			case kPackagerUrlType::LIVE_THUMB:
				$result = kConf::get(self::LIVE_PACKAGER_URL,kConfMapNames::LOCAL_SETTINGS, null) . kConf::get(self::PACKAGER_LIVE_THUMB_URL, kConfMapNames::LOCAL_SETTINGS, null);
				break;
			case kPackagerUrlType::REGULAR_VOLUME_MAP:
				$result = $packagerUrl . kConf::get(self::PACKAGER_LOCAL_VOLUME_MAP_URL, kConfMapNames::LOCAL_SETTINGS, null);
				break;
			case kPackagerUrlType::MAPPED_VOLUME_MAP:
				$result = $packagerUrl . kConf::get(self::PACKAGER_MAPPED_VOLUME_MAP_URL, kConfMapNames::LOCAL_SETTINGS, null);
				break;
			case kPackagerUrlType::REMOTE_VOLUME_MAP:
				$result = $packagerUrl . kConf::get(self::PACKAGER_REMOTE_VOLUME_MAP_URL, kConfMapNames::LOCAL_SETTINGS, null);
				break;
			default:
		}

		return $result;
	}

	/**
	 * @param entry $entry
	 * @param flavorAsset $flavorAsset
	 * @param string $capturedThumbPath
	 * @param $calc_vid_sec
	 * @param int|null $width
	 * @param int|null $height
	 * @return bool|mixed
	 * @throws Exception
	 */
	protected static function captureMappedThumb($entry, $flavorAsset, $capturedThumbPath, $calc_vid_sec, $width, $height)
	{
		$packagerCaptureUrl = self::getPackagerUrlFromConf(kPackagerUrlType::MAPPED_THUMB);
		if ($packagerCaptureUrl)
		{
			$flavorUrl = self::buildThumbUrl($entry, $flavorAsset);
			return self::curlThumbUrlWithOffset($flavorUrl, $calc_vid_sec, $packagerCaptureUrl, $capturedThumbPath, $width, $height);
		}

		return false;
	}

	/**
	 * @param entry $entry
	 * @param $destThumbPath
	 * @param $calc_vid_sec
	 * @param int|null $width
	 * @param int|null $height
	 * @return bool|mixed
	 * @throws Exception
	 */
	protected static function captureLiveThumb(entry $entry, $destThumbPath, $calc_vid_sec, $width = null, $height = null)
	{
		$liveCaptureUrl = self::getPackagerUrlFromConf(kPackagerUrlType::LIVE_THUMB);
		if (!$liveCaptureUrl)
		{
			return false;
		}
		
		$currentEntryServerNodes = EntryServerNodePeer::retrieveByEntryIdAndServerTypes($entry->getRootEntryId(), array(EntryServerNodeType::LIVE_PRIMARY, EntryServerNodeType::LIVE_BACKUP));
		usort($currentEntryServerNodes, function ($a, $b) {return $a->getServerType() - $b->getServerType();}); // Primary first and secondary last
		
		if (!$currentEntryServerNodes)
		{
			return false;
		}

		$thumbName = kConf::get(myPackagerUtils::PACKAGER_LIVE_THUMB_NAME, kConfMapNames::LOCAL_SETTINGS, null);
		if (!$thumbName)
		{
			return false;
		}

		if (!$calc_vid_sec)
		{
			$calc_vid_sec = myEntryUtils::DEFAULT_THUMB_SEC_LIVE;
		}

		foreach ($currentEntryServerNodes as $entryServerNode)
		{
			$serverNode = ServerNodePeer::retrieveActiveMediaServerNode(null, $entryServerNode->getServerNodeId());
			if (!$serverNode)
			{
				continue;
			}

			$serverNodeUrl = $serverNode->createThumbUrl($liveCaptureUrl, $entry, $entryServerNode);

			$serverNodeUrl .= $thumbName;

			$streams = $entryServerNode->getStreams();
			foreach ($streams as $liveParam)
			{
				if (is_null($liveParam->getWidth()))
				{
					continue;
				}

				if (self::curlThumbUrlWithOffset('', $calc_vid_sec, $serverNodeUrl, $destThumbPath, $width, $height, '+', '', "-s{$liveParam->getFlavorId()}"))
				{
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * @param $entry_data_path
	 * @param $capturedThumbPath
	 * @param $calc_vid_sec
	 * @param $width
	 * @param $height
	 * @return bool
	 * @throws Exception
	 */
	protected static function captureLocalThumb($entry_data_path, $capturedThumbPath, $calc_vid_sec, $width, $height)
	{
		$packagerCaptureUrl = self::getPackagerUrlFromConf(kPackagerUrlType::REGULAR_THUMB);
		if ($packagerCaptureUrl && $entry_data_path)
		{
			return self::curlThumbUrlWithOffset($entry_data_path, $calc_vid_sec, $packagerCaptureUrl, $capturedThumbPath, $width, $height);
		}

		return false;
	}

	/**
	 * @param $url
	 * @param $calc_vid_sec
	 * @param $packagerCaptureUrl
	 * @param $capturedThumbPath
	 * @param int $width
	 * @param int $height
	 * @param string $offsetPrefix
	 * @param string $postFix
	 * @param string $offsetPostfix
	 * @return bool
	 * @throws Exception
	 */
	protected static function curlThumbUrlWithOffset($url, $calc_vid_sec, $packagerCaptureUrl, $capturedThumbPath, $width = null, $height = null, $offsetPrefix = '', $postFix = '', $offsetPostfix = '')
	{
		list($packagerThumbCapture, $tempThumbPath) = KThumbnailCapture::generateThumbUrlWithOffset($url, $calc_vid_sec, $packagerCaptureUrl, $capturedThumbPath, $width, $height, $offsetPrefix, $postFix, $offsetPostfix);
		KalturaLog::debug("TTT: packagerThumbCapture [$packagerThumbCapture] tempThumbPath [$tempThumbPath]");

		kFile::closeDbConnections();
		$success = KCurlWrapper::getDataFromFile($packagerThumbCapture, $tempThumbPath, null, true);
		if($success)
		{
			$success = kFile::fileSize($tempThumbPath) > 0;
		}

		return $success;
	}

	/**
	 * @param $url
	 * @param $capturedThumbPath
	 * @param $calc_vid_sec
	 * @param $width
	 * @param $height
	 * @return bool
	 * @throws Exception
	 */
	protected static function captureRemoteThumb($url, $capturedThumbPath, $calc_vid_sec, $width, $height)
	{
		KalturaLog::debug("TTT: captureRemoteThumb url [$url]");
		$packagerCaptureUrl = self::getPackagerUrlFromConf(kPackagerUrlType::REMOTE_THUMB);
		KalturaLog::debug("TTT: captureRemoteThumb $packagerCaptureUrl url [$packagerCaptureUrl]");
		if($packagerCaptureUrl && $url)
		{
			return self::curlThumbUrlWithOffset($url, $calc_vid_sec, $packagerCaptureUrl, $capturedThumbPath, $width, $height);
		}

		return false;
	}

	/**
	 * @param $entry
	 * @param $capturedThumbPath
	 * @param $calc_vid_sec
	 * @param $flavorAssetId
	 * @param null $width
	 * @param null $height
	 * @return bool
	 * @throws Exception
	 */
	protected static function capturePlaylistThumb($entry, $capturedThumbPath, $calc_vid_sec, &$flavorAssetId, $width, $height)
	{
		$firstEntry = myPlaylistUtils::getFirstEntryFromPlaylist($entry);
		if (!$firstEntry)
		{
			return false;
		}

		$firstEntryId = $firstEntry->getId();
		$flavorAsset = self::getFlavorSupportedByPackagerForThumbCapture($firstEntryId);
		if (!$flavorAsset)
		{
			return false;
		}

		$packagerCaptureUrl = self::getPackagerUrlFromConf(kPackagerUrlType::MAPPED_THUMB);
		if ($packagerCaptureUrl)
		{
			$flavorAssetId = $flavorAsset->getId();
			$flavorParamsId = $flavorAsset->getFlavorParamsId();
			if ($flavorParamsId)
			{
				$flavorUrl = self::buildPlaylistThumbUrl($entry, $flavorAsset);
				return self::curlThumbUrlWithOffset($flavorUrl, $calc_vid_sec, $packagerCaptureUrl, $capturedThumbPath, $width, $height);
			}
		}

		return false;
	}

	public static function getPreferredStorageId($dcId)
	{
		$preferredStorageId = null;
		if(myCloudUtils::isCloudDc($dcId))
		{
			$preferredStorageId = myCloudUtils::getCloudPreferredStorage();
		}

		if(!$preferredStorageId && myCloudUtils::isEnvironmentWithCloudStorage())
		{
			$preferredStorageId = $dcId;
		}

		return $preferredStorageId;
	}

	public static function retrieveVolumeMapFromPackager($flavorAsset)
	{
		if ($flavorAsset->getEncryptionKey())
		{
			return self::retrieveMappedVolumeMapFromPackager($flavorAsset);
		}

		$fileSyncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
		$currentDcId = kDataCenterMgr::getCurrentDcId();
		$preferredStorageId = self::getPreferredStorageId($currentDcId);
		list ($fileSync, $path, $sourceType) = kFileSyncUtils::getFileSyncServeFlavorFields($fileSyncKey, $flavorAsset, $preferredStorageId, null);
		if(!$fileSync)
		{
			return null;
		}

		if(myCloudUtils::isCloudDc($currentDcId) || $fileSync->getDc() != $currentDcId)
		{
			return self::retrieveRemoteVolumeMapFromPackager($path);
		}
		
		return self::retrieveLocalVolumeMapFromPackager($flavorAsset);
	}


	protected static function retrieveRemoteVolumeMapFromPackager($path)
	{
		$packagerRemoteVolumeMapUrlPattern = myPackagerUtils::getPackagerUrlFromConf(kPackagerUrlType::REMOTE_VOLUME_MAP);
		if (!$packagerRemoteVolumeMapUrlPattern)
		{
			throw new KalturaAPIException(KalturaErrors::VOLUME_MAP_NOT_CONFIGURED);
		}

		$content = self::curlVolumeMapUrl($path, $packagerRemoteVolumeMapUrlPattern);
		if(!$content)
		{
			return false;
		}

		return $content;
	}

	protected static function curlVolumeMapUrl($url, $packagerVolumeMapUrlPattern)
	{
		$packagerVolumeMapUrl = str_replace(array("{url}"), array($url), $packagerVolumeMapUrlPattern);
		kFile::closeDbConnections();
		$content = KCurlWrapper::getDataFromFile($packagerVolumeMapUrl, null, null, true);
		return $content;
	}

	protected static function buildVolumeMapPath($entry, $flavorAsset)
	{
		$partnerId = $flavorAsset->getPartnerId();
		$subpId = $entry->getSubpId();
		$partnerPath = myPartnerUtils::getUrlForPartner($partnerId, $subpId);
		$entryVersion = $entry->getVersion();

		$url = "$partnerPath/serveFlavor/entryId/".$entry->getId();
		$url .= ($entryVersion ? "/v/$entryVersion" : '');
		$url .= "/flavorId/".$flavorAsset->getId();
		$url .= self::MP4_FILENAME_PARAMETER;
		return $url;
	}

	protected static function retrieveLocalVolumeMapFromPackager($flavorAsset)
	{
		$packagerVolumeMapUrlPattern = self::getPackagerUrlFromConf(kPackagerUrlType::REGULAR_VOLUME_MAP);
		if (!$packagerVolumeMapUrlPattern)
		{
			throw new KalturaAPIException(KalturaErrors::VOLUME_MAP_NOT_CONFIGURED);
		}

		$fileSyncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
		$entry_data_path = kFileSyncUtils::getRelativeFilePathForKey($fileSyncKey);
		$entry_data_path = ltrim($entry_data_path, "/");
		if (!$entry_data_path)
		{
			return null;
		}

		$content = self::curlVolumeMapUrl($entry_data_path, $packagerVolumeMapUrlPattern);
		if(!$content)
		{
			return false;
		}

		return $content;
	}

	protected static function retrieveMappedVolumeMapFromPackager($flavorAsset)
	{
		$packagerVolumeMapUrlPattern = self::getPackagerUrlFromConf(kPackagerUrlType::MAPPED_VOLUME_MAP);
		if (!$packagerVolumeMapUrlPattern)
		{
			throw new KalturaAPIException(KalturaErrors::VOLUME_MAP_NOT_CONFIGURED);
		}

		$entry = entryPeer::retrieveByPK($flavorAsset->getEntryId());
		if (!$entry)
		{
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND);
		}

		$volumeMapUrl = self::buildVolumeMapPath($entry, $flavorAsset);

		$content = self::curlVolumeMapUrl($volumeMapUrl, $packagerVolumeMapUrlPattern);
		if(!$content)
		{
			return false;
		}

		return $content;
	}

	public static function generateLivePackagerToken($url, $signingDomain = '')
	{
		$livePackagerToken = kConf::get("live_packager_secure_token");

		if(!empty($signingDomain))
		{
			$domain = parse_url($url, PHP_URL_HOST);
			if($domain && $domain != '')
			{
				$url = str_replace($domain, $signingDomain, $url);
			}
			else
			{
				KalturaLog::debug("Failed to parse domain from original url, signed domain will not be modified");
			}
		}

		$strippedUrl = preg_replace('#^https?://#', '', $url);

		$token = md5("$livePackagerToken $strippedUrl", true);
		$token = rtrim(strtr(base64_encode($token), '+/', '-_'), '=');
		return $token;
	}
}
