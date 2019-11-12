<?php

class myPackagerUtils
{
	const PACKAGER_LOCAL_LIVE_URL = 'packager_local_live_thumb_capture_url';
	const PACKAGER_MAPPED_URL = 'packager_mapped_thumb_capture_url';
	const PACKAGER_LOCAL_URL = 'packager_local_thumb_capture_url';
	const PACKAGER_REMOTE_URL = 'packager_thumb_capture_url';
	const LOCAL_MAP_NAME = 'local';
	const RECORDING_LIVE_TYPE = 'recording';

	protected static $storageProfiles = array();

	/**
	 * @param entry $entry
	 * @param $capturedThumbPath
	 * @param $calc_vid_sec
	 * @param $flavorAssetId
	 * @param int|null $width
	 * @param int|null $height
	 * @return bool|mixed
	 * @throws Exception
	 */
	public static function captureThumbUsingPackager($entry, $capturedThumbPath, $calc_vid_sec, &$flavorAssetId, $width = null, $height = null)
	{
		if (myEntryUtils::shouldServeVodFromLive($entry))
		{
			return self::captureLiveThumbUsingPackager($entry, self::RECORDING_LIVE_TYPE, $capturedThumbPath, $calc_vid_sec, $width, $height);
		}

		$mappedThumbEntryTypes = array(entryType::PLAYLIST);
		$isPlayList = in_array($entry->getType(), $mappedThumbEntryTypes);
		$id = $entry->getId();
		if ($isPlayList)
		{
			$firstEntry = myPlaylistUtils::getFirstEntryFromPlaylist($entry);
			if (!$firstEntry)
			{
				return false;
			}

			$id = $firstEntry->getId();
		}

		$flavorAsset = self::getFlavorSupportedByPackagerForThumbCapture($id);
		if (!$flavorAsset)
		{
			return false;
		}

		if ($isPlayList || $flavorAsset->getEncryptionKey())
		{
			return self::captureMappedThumbUsingPackager($entry, $flavorAsset, $capturedThumbPath, $calc_vid_sec, $flavorAssetId, $width, $height);
		}
		else
		{
			return self::captureLocalThumbUsingPackager($flavorAsset, $capturedThumbPath, $calc_vid_sec, $flavorAssetId, $width, $height);
		}
	}

	/**
	 * @param entry $entry
	 * @param $orig_image_path
	 * @param $calc_vid_sec
	 * @param $flavorAssetId
	 * @return bool
	 * @throws Exception
	 */
	public static function captureRemoteThumbUsingPackager($entry, $orig_image_path, $calc_vid_sec, &$flavorAssetId)
	{
		$packagerCaptureUrl = self::getPackagerUrlFromConf(kPackagerUrlType::REMOTE);
		if ($packagerCaptureUrl)
		{
			// look for the highest bitrate MBR tagged bitrate (a flavor the packager can parse)
			$flavorAsset = assetPeer::retrieveHighestBitrateByEntryId($entry->getId(), flavorParams::TAG_MBR, null, true);
			if (is_null($flavorAsset))
			{
				return false;
			}

			$flavorAssetId = $flavorAsset->getId();
			$flavorSyncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
			$remoteFS = kFileSyncUtils::getReadyExternalFileSyncForKey($flavorSyncKey);
			if ($remoteFS)
			{

				$dp = DeliveryProfilePeer::getRemoteDeliveryByStorageId(DeliveryProfileDynamicAttributes::init($remoteFS->getDc(), $flavorAsset->getEntryId()), null, $flavorAsset);
				if (is_null($dp))
				{
					return false;
				}

				$url = $dp->getFileSyncUrl($remoteFS);
				if (strpos($url, "://") === false)
				{
					$url = rtrim($dp->getUrl(), "/") . "/" . ltrim($url, '/');
				}

				@list($baseUrl, $queryString) = explode("?", $url, 2);
				$remoteThumbCapture = str_replace(
					array("{url}", "{offset}"),
					array(str_replace("://", "/", $baseUrl), floor($calc_vid_sec * 1000)),
					$packagerCaptureUrl);

				if ($queryString)
				{
					$remoteThumbCapture .= "?$queryString";
				}

				kFile::closeDbConnections();
				KCurlWrapper::getDataFromFile($remoteThumbCapture, $orig_image_path, null, true);
				return true;
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
		if($entry->getType() == entryType::PLAYLIST)
		{
			$url .= '/flavorParamIds/' . $flavorAsset->getFlavorParamsId();
		}
		else
		{
			$url .= '/flavorId/' . $flavorAsset->getId();
		}

		$url .= myEntryUtils::MP4_FILENAME_PARAMETER;
		return $url;
	}

	protected static function getFlavorSupportedByPackagerForThumbCapture($entryId)
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
	 * @param kPackagerUrlType $packagerUrlType
	 * @param flavorAsset $flavorAsset
	 * @return string
	 * @throws PropelException
	 * @throws kFileSyncException
	 */
	protected static function getPackagerUrlByTypeAndFlavorAsset($packagerUrlType, $flavorAsset)
	{
		$fileSyncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
		if(is_null($fileSyncKey->partner_id))
		{
			throw new kFileSyncException("partner id not defined for key [$fileSyncKey]", kFileSyncException::FILE_SYNC_PARTNER_ID_NOT_DEFINED);
		}


		$fileSyncs = kFileSyncUtils::getReadyFileSyncForKey($fileSyncKey, true, false);
		$localDcs = kDataCenterMgr::getDcIds();
		foreach($fileSyncs as $fileSync)
		{
			/* @var $fileSync fileSync */
			$fileDc = $fileSync->getDc();
			if(in_array ($fileDc, $localDcs))
			{
				return self::getPackagerUrlFromConf($packagerUrlType);
			}

			self::prepareStorageProfiles($fileSyncKey->partner_id);
			if(array_key_exists($fileDc, self::$storageProfiles[$fileSyncKey->partner_id]))
			{
				$result = self::getPackagerUrlFromStorageProfile($packagerUrlType, self::$storageProfiles[$fileSyncKey->partner_id][$fileDc]);
				if($result)
				{
					return $result;
				}
			}
		}

		return null;
	}

	/**
	 * Prepare storage profiles array for sorting
	 *
	 * @param int $partnerId
	 * @throws PropelException
	 */
	protected static function prepareStorageProfiles($partnerId)
	{
		if( !isset(self::$storageProfiles[$partnerId]))
		{
			return;
		}

		$criteria = new Criteria();
		$criteria->add(StorageProfilePeer::PARTNER_ID, $partnerId);
		$criteria->add(StorageProfilePeer::DELIVERY_STATUS, StorageProfileDeliveryStatus::BLOCKED, Criteria::NOT_EQUAL);
		$criteria->addAscendingOrderByColumn(StorageProfilePeer::ID);
		$results = StorageProfilePeer::doSelect($criteria);
		self::$storageProfilesOrder[$partnerId] = array();
		foreach ($results as $result)
		{
			self::$storageProfilesOrder[$partnerId][$result->getId()] = $result;
		}
	}

	protected static function getPackagerUrlFromConf($packagerUrlType)
	{
		switch ($packagerUrlType)
		{
			case kPackagerUrlType::REGULAR:
				return kConf::get(self::PACKAGER_LOCAL_URL, self::LOCAL_MAP_NAME, null);
				break;
			case kPackagerUrlType::MAPPED:
				return kConf::get(self::PACKAGER_MAPPED_URL, self::LOCAL_MAP_NAME, null);
				break;
			case kPackagerUrlType::REMOTE:
				return  kConf::get(self::PACKAGER_REMOTE_URL, self::LOCAL_MAP_NAME, null);
				break;
			case kPackagerUrlType::LOCAL_LIVE:
				return  kConf::get(self::PACKAGER_LOCAL_LIVE_URL, self::LOCAL_MAP_NAME, null);
				break;
			default:
				return null;
		}
	}

	/**
	 * @param kPackagerUrlType $packagerUrlType
	 * @param StorageProfile $storageProfile
	 * @return null
	 */
	protected static function getPackagerUrlFromStorageProfile($packagerUrlType, $storageProfile)
	{
		switch ($packagerUrlType)
		{
			case kPackagerUrlType::REGULAR:
				return $storageProfile->getRegularPackagerUrl();
				break;
			case kPackagerUrlType::MAPPED:
				return $storageProfile->getMappedPackagerUrl();
				break;
			default:
				return null;
		}
	}

	/**
	 * @param entry $entry
	 * @param flavorAsset $flavorAsset
	 * @param $capturedThumbPath
	 * @param $calc_vid_sec
	 * @param $flavorAssetId
	 * @param $width
	 * @param $height
	 * @return bool|mixed
	 * @throws Exception
	 */
	protected static function captureMappedThumbUsingPackager($entry, $flavorAsset, $capturedThumbPath, $calc_vid_sec, &$flavorAssetId, $width, $height)
	{
		$packagerCaptureUrl = self::getPackagerUrlByTypeAndFlavorAsset(kPackagerUrlType::MAPPED, $flavorAsset);
		if ($packagerCaptureUrl && $flavorAsset)
		{
			$flavorAssetId = $flavorAsset->getId();
			$flavorParamsId = $flavorAsset->getFlavorParamsId();
			if ($flavorParamsId)
			{
				$flavorUrl = self::buildThumbUrl($entry, $flavorAsset);
				return self::curlThumbUrlWithOffset($flavorUrl, $calc_vid_sec, $packagerCaptureUrl, $capturedThumbPath, $width, $height);
			}
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
	protected static function captureLiveThumbUsingPackager(entry $entry, $liveType, $destThumbPath, $calc_vid_sec, $width = null, $height = null)
	{
		$packagerCaptureUrl = self::getPackagerUrlFromConf(kPackagerUrlType::LOCAL_LIVE);
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
	 * @param flavorAsset $flavorAsset
	 * @param $capturedThumbPath
	 * @param $calc_vid_sec
	 * @param $flavorAssetId
	 * @param $width
	 * @param $height
	 * @return bool|mixed
	 * @throws Exception
	 */
	protected static function captureLocalThumbUsingPackager($flavorAsset, $capturedThumbPath, $calc_vid_sec, &$flavorAssetId, $width, $height)
	{
		$packagerCaptureUrl = self::getPackagerUrlByTypeAndFlavorAsset(kPackagerUrlType::REGULAR, $flavorAsset);
		if ($packagerCaptureUrl)
		{
			$flavorAssetId = $flavorAsset->getId();
			$fileSyncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
			$entry_data_path = kFileSyncUtils::getRelativeFilePathForKey($fileSyncKey);
			$entry_data_path = ltrim($entry_data_path, "/");
			if ($entry_data_path)
			{
				return self::curlThumbUrlWithOffset($entry_data_path, $calc_vid_sec, $packagerCaptureUrl, $capturedThumbPath, $width, $height);
			}
		}

		return false;
	}

	/**
	 * @param $url
	 * @param $calc_vid_sec
	 * @param $packagerCaptureUrl
	 * @param $capturedThumbPath
	 * @param null $width
	 * @param null $height
	 * @param string $offsetPrefix
	 * @return bool|mixed
	 * @throws Exception
	 */
	protected static function curlThumbUrlWithOffset($url, $calc_vid_sec, $packagerCaptureUrl, $capturedThumbPath, $width = null, $height = null, $offsetPrefix = '')
	{
		list($packagerThumbCapture, $tempThumbPath) = KThumbnailCapture::generateThumbUrlWithOffset($url, $calc_vid_sec, $packagerCaptureUrl, $capturedThumbPath, $width, $height, $offsetPrefix);
		kFile::closeDbConnections();
		$success = KCurlWrapper::getDataFromFile($packagerThumbCapture, $tempThumbPath, null, true);
		return $success;
	}
}