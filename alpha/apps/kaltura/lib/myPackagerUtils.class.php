<?php

class myPackagerUtils
{
	const PACKAGER_LOCAL_LIVE_THUMB_URL = 'packager_local_live_thumb_capture_url';
	const PACKAGER_MAPPED_THUMB_URL = 'packager_mapped_thumb_capture_url';
	const PACKAGER_LOCAL_THUMB_URL = 'packager_local_thumb_capture_url';
	const PACKAGER_REMOTE_THUMB_URL = 'packager_thumb_capture_url';
	const PACKAGER_MAPPED_VOLUME_MAP_URL = 'packager_mapped_volume_map_url';
	const PACKAGER_LOCAL_VOLUME_MAP_URL = 'packager_local_volume_map_url';
	const PACKAGER_REMOTE_VOLUME_MAP_URL = 'packager_remote_volume_map_url';
	const PACKAGER_URL = "packager_url";
	const LOCAL_MAP_NAME = 'local';
	const RECORDING_LIVE_TYPE = 'recording';
	const MP4_FILENAME_PARAMETER = "/name/a.mp4";
	
	protected static $flavorSupportedByPackager = array();

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
	public static function captureThumb($entry, $capturedThumbPath, $calc_vid_sec, &$flavorAssetId, $width = null, $height = null)
	{
		if(myEntryUtils::shouldServeVodFromLive($entry))
		{
			return self::captureLiveThumb($entry, self::RECORDING_LIVE_TYPE, $capturedThumbPath, $calc_vid_sec, $width, $height);
		}
		else if ($entry->getType() == entryType::PLAYLIST)
		{
			return self::capturePlaylistThumb($entry, $capturedThumbPath, $calc_vid_sec, $flavorAssetId, $width, $height);
		}

		$flavorAsset = self::getFlavorSupportedByPackagerForThumbCapture($entry->getEntryId());
		if(!$flavorAsset)
		{
			KalturaLog::info("No suitable packager flavor found for entry {$entry->getEntryId()}");
			return false;
		}

		$flavorAssetId = $flavorAsset->getId();
		KalturaLog::info("Found flavor asset {$flavorAssetId}");

		if($flavorAsset->getEncryptionKey())
		{
			return self::captureMappedThumb($entry, $flavorAsset, $capturedThumbPath, $calc_vid_sec, $width, $height);
		}

		$fileSyncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
		$currentDcId = kDataCenterMgr::getCurrentDcId();
		$preferredStorageId = self::getPreferredStorageId($currentDcId);
		list ($fileSync, $path, $sourceType) = kFileSyncUtils::getFileSyncServeFlavorFields($fileSyncKey, $flavorAsset, $preferredStorageId, null);
		if(!$fileSync)
		{
			return self::captureRemoteThumbByDeliveryProfile($capturedThumbPath, $calc_vid_sec, $flavorAsset, $width, $height);
		}

		if(myCloudUtils::isCloudDc($currentDcId) || $fileSync->getDc() != $currentDcId)
		{
			if(in_array($fileSync->getDc(), kDataCenterMgr::getDcIds()) || in_array($fileSync->getDc(), kStorageExporter::getPeriodicStorageIds()))
			{
				return self::captureRemoteThumb($path, $capturedThumbPath, $calc_vid_sec, $width, $height);
			}
			else
			{
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

	protected static function getFlavorSupportedByPackagerForThumbCapture($entryId)
	{
		if(isset(self::$flavorSupportedByPackager[$entryId]))
		{
			return self::$flavorSupportedByPackager[$entryId];
		}
		
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

		self::$flavorSupportedByPackager[$entryId] = $flavorAsset;
		return $flavorAsset;
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
			$packagerUrl = kConf::get(self::PACKAGER_URL,self::LOCAL_MAP_NAME, null);
		}

		switch ($packagerUrlType)
		{
			case kPackagerUrlType::REGULAR_THUMB:
				$result = $packagerUrl . kConf::get(self::PACKAGER_LOCAL_THUMB_URL, self::LOCAL_MAP_NAME, null);
				break;
			case kPackagerUrlType::MAPPED_THUMB:
				$result = $packagerUrl . kConf::get(self::PACKAGER_MAPPED_THUMB_URL, self::LOCAL_MAP_NAME, null);
				break;
			case kPackagerUrlType::REMOTE_THUMB:
				$result = kConf::get(self::PACKAGER_URL,self::LOCAL_MAP_NAME, null) . kConf::get(self::PACKAGER_REMOTE_THUMB_URL, self::LOCAL_MAP_NAME, null);
				break;
			case kPackagerUrlType::LOCAL_LIVE_THUMB:
				$result = kConf::get(self::PACKAGER_URL,self::LOCAL_MAP_NAME, null) . kConf::get(self::PACKAGER_LOCAL_LIVE_THUMB_URL, self::LOCAL_MAP_NAME, null);
				break;
			case kPackagerUrlType::REGULAR_VOLUME_MAP:
				$result = $packagerUrl . kConf::get(self::PACKAGER_LOCAL_VOLUME_MAP_URL, self::LOCAL_MAP_NAME, null);
				break;
			case kPackagerUrlType::MAPPED_VOLUME_MAP:
				$result = $packagerUrl . kConf::get(self::PACKAGER_MAPPED_VOLUME_MAP_URL, self::LOCAL_MAP_NAME, null);
				break;
			case kPackagerUrlType::REMOTE_VOLUME_MAP:
				$result = $packagerUrl . kConf::get(self::PACKAGER_REMOTE_VOLUME_MAP_URL, self::LOCAL_MAP_NAME, null);
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
	 * @param $liveType
	 * @param $destThumbPath
	 * @param $calc_vid_sec
	 * @param int|null $width
	 * @param int|null $height
	 * @return bool|mixed
	 * @throws Exception
	 */
	protected static function captureLiveThumb(entry $entry, $liveType, $destThumbPath, $calc_vid_sec, $width = null, $height = null)
	{
		$packagerCaptureUrl = self::getPackagerUrlFromConf(kPackagerUrlType::LOCAL_LIVE_THUMB);
		if (!$packagerCaptureUrl)
		{
			return false;
		}

		$dc = myEntryUtils::getLiveEntryDcId($entry->getRootEntryId(), EntryServerNodeType::LIVE_PRIMARY);
		if (is_null($dc))
		{
			return false;
		}

		$url = 'p/' . $entry->getPartnerId() . '/e/' . $entry->getId();
		$packagerCaptureUrl = str_replace(array ( "{dc}", "{liveType}"), array ( $dc, $liveType) , $packagerCaptureUrl );
		if (!$calc_vid_sec) //Temp until packager support time 0
		{
			$calc_vid_sec = myEntryUtils::DEFAULT_THUMB_SEC_LIVE;
		}

		return self::curlThumbUrlWithOffset($url, $calc_vid_sec, $packagerCaptureUrl, $destThumbPath, $width, $height, '+');
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
	 * @return bool
	 * @throws Exception
	 */
	protected static function curlThumbUrlWithOffset($url, $calc_vid_sec, $packagerCaptureUrl, $capturedThumbPath, $width = null, $height = null, $offsetPrefix = '', $postFix = '')
	{
		list($packagerThumbCapture, $tempThumbPath) = KThumbnailCapture::generateThumbUrlWithOffset($url, $calc_vid_sec, $packagerCaptureUrl, $capturedThumbPath, $width, $height, $offsetPrefix, $postFix);
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
		$packagerCaptureUrl = self::getPackagerUrlFromConf(kPackagerUrlType::REMOTE_THUMB);
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

		if(!$preferredStorageId)
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
}