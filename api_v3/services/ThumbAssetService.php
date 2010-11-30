<?php

/**
 * Retrieve information and invoke actions on Thumb Asset
 *
 * @service thumbAsset
 * @package api
 * @subpackage services
 */
class ThumbAssetService extends KalturaBaseService
{
	public function initService($partnerId, $puserId, $ksStr, $serviceName, $action)
	{
		parent::initService($partnerId, $puserId, $ksStr, $serviceName, $action);
		
		parent::applyPartnerFilterForClass(new conversionProfile2Peer());
		parent::applyPartnerFilterForClass(thumbParamsOutputPeer::getInstance());
		parent::applyPartnerFilterForClass(thumbParamsPeer::getInstance());
		parent::applyPartnerFilterForClass(thumbAssetPeer::getInstance());
	}
	
	/**
	 * Serves thumbnail by entry id and thumnail params id
	 *  
	 * @action serveByEntryId
	 * @serverOnly
	 * @param string $entryId
	 * @param int $thumbParamId if not set, default thumbnail will be used.
	 * 
	 * @throws KalturaErrors::THUMB_ASSET_IS_NOT_READY
	 * @throws KalturaErrors::THUMB_ASSET_PARAMS_ID_NOT_FOUND
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	public function serveByEntryIdAction($entryId, $thumbParamId = null)
	{
		$entry = entryPeer::retrieveByPK($entryId);
		if (!$entry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		$fileName = $entry->getId() . '.jpg';
		
		$syncKey = null;
		if(is_null($thumbParamId))
		{
			$syncKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB);
			if(!kFileSyncUtils::fileSync_exists($syncKey))
				throw new KalturaAPIException(KalturaErrors::THUMB_ASSET_IS_NOT_READY);
		}
		else
		{
			$thumbAsset = thumbAssetPeer::retrieveByEntryIdAndParams($entryId, $thumbParamId);
			if(!$thumbAsset)
				throw new KalturaAPIException(KalturaErrors::THUMB_ASSET_PARAMS_ID_NOT_FOUND, $thumbParamId);
			
			$syncKey = $thumbAsset->getSyncKey(thumbAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			if(!kFileSyncUtils::fileSync_exists($syncKey))
				throw new KalturaAPIException(KalturaErrors::THUMB_ASSET_IS_NOT_READY, $thumbParamId);
		}
			
		list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($syncKey, true, false);
		return $this->serveThumbToFile($fileSync, $local, $fileName);
	}

	/**
	 * Serves thumbnail by its id
	 *  
	 * @action serve
	 * @serverOnly
	 * @param string $thumbAssetId
	 *  
	 * @throws KalturaErrors::THUMB_ASSET_IS_NOT_READY
	 * @throws KalturaErrors::THUMB_ASSET_ID_NOT_FOUND
	 */
	public function serveAction($thumbAssetId)
	{
		$thumbAsset = thumbAssetPeer::retrieveById($thumbAssetId);
		if (!$thumbAsset)
			throw new KalturaAPIException(KalturaErrors::THUMB_ASSET_ID_NOT_FOUND, $thumbAssetId);

		$ext = $thumbAsset->getFileExt();
		if(is_null($ext))
			$ext = 'jpg';
			
		$fileName = $thumbAsset->getEntryId()."_" . $thumbAsset->getId() . ".$ext";
		
		$syncKey = $thumbAsset->getSyncKey(thumbAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		if(!kFileSyncUtils::fileSync_exists($syncKey))
			throw new KalturaAPIException(KalturaErrors::THUMB_ASSET_IS_NOT_READY, $thumbAsset);

		list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($syncKey, true, false);
		return $this->serveThumbToFile($fileSync, $local, $fileName);
	}

	
	/**
	 * @param FileSync $fileSync
	 * @param bool $local
	 * @param string $fileName
	 * @param bool $forceProxy
	 */
	protected function serveThumbToFile(FileSync $fileSync, $local, $fileName, $forceProxy = false)
	{
		header("Content-Disposition: attachment; filename=\"$fileName\"");
		
		if($local)
		{
			$filePath = $fileSync->getFullPath();
			$mimeType = kFile::mimeType($filePath);
			kFile::dumpFile($filePath, $mimeType);
		}
		else
		{
			$remoteUrl = kDataCenterMgr::getRedirectExternalUrl($fileSync);
			KalturaLog::info("Redirecting to [$remoteUrl]");
			if($forceProxy)
			{
				kFile::dumpUrl($remoteUrl);
			}
			else
			{
				// or redirect if no proxy
				header("Location: $remoteUrl");
			}
		}	
	}
	
	/**
	 * Tags the thumbnail as DEFAULT_THUMB and removes that tag from all other thumbnail assets of the entry.
	 * Create a new file sync link on the entry thumbnail that points to the thumbnail asset file sync.
	 *  
	 * @action setAsDefault
	 * @param string $thumbAssetId
	 * @throws KalturaErrors::THUMB_ASSET_ID_NOT_FOUND
	 */
	public function setAsDefaultAction($thumbAssetId)
	{
		$thumbAsset = thumbAssetPeer::retrieveById($thumbAssetId);
		if (!$thumbAsset)
			throw new KalturaAPIException(KalturaErrors::THUMB_ASSET_ID_NOT_FOUND, $thumbAssetId);
		
		$entry = $thumbAsset->getentry();
		if(!$entry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
			
		$entryThumbAssets = thumbAssetPeer::retrieveByEntryId($thumbAsset->getEntryId());
		foreach($entryThumbAssets as $entryThumbAsset)
		{
			if($entryThumbAsset->getId() == $thumbAsset->getId())
				continue;
				
			if(!$entryThumbAsset->hasTag(thumbParams::TAG_DEFAULT_THUMB))
				continue;
				
			$entryThumbAsset->removeTags(array(thumbParams::TAG_DEFAULT_THUMB));
			$entryThumbAsset->save();
		}
		
		if(!$thumbAsset->hasTag(thumbParams::TAG_DEFAULT_THUMB))
		{
			$thumbAsset->addTags(array(thumbParams::TAG_DEFAULT_THUMB));
			$thumbAsset->save();
		}
		
		$entry->setThumbnail(".jpg");
		$entry->save();
		
		$thumbSyncKey = $thumbAsset->getSyncKey(thumbAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$entrySyncKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB);
		kFileSyncUtils::createSyncFileLinkForKey($entrySyncKey, $thumbSyncKey, false);
	}

	/**
	 * @action generateByEntryId
	 * @param string $entryId
	 * @param int $destThumbParamsId indicate the id of the ThumbParams to be generate this thumbnail by
	 * @return int job id
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::ENTRY_TYPE_NOT_SUPPORTED
	 * @throws KalturaErrors::ENTRY_MEDIA_TYPE_NOT_SUPPORTED
	 * @throws KalturaErrors::THUMB_ASSET_PARAMS_ID_NOT_FOUND
	 * @throws KalturaErrors::INVALID_ENTRY_STATUS
	 * @throws KalturaErrors::FLAVOR_ASSET_IS_NOT_READY
	 */
	public function generateByEntryIdAction($entryId, $destThumbParamsId)
	{
		$entry = entryPeer::retrieveByPK($entryId);
		if(!$entry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
			
		if ($entry->getType() != entryType::MEDIA_CLIP)
			throw new KalturaAPIException(KalturaErrors::ENTRY_TYPE_NOT_SUPPORTED, $entry->getType());
		if ($entry->getMediaType() != entry::ENTRY_MEDIA_TYPE_VIDEO)
			throw new KalturaAPIException(KalturaErrors::ENTRY_MEDIA_TYPE_NOT_SUPPORTED, $entry->getMediaType());
			
		$validStatuses = array(
			entryStatus::ERROR_CONVERTING,
			entryStatus::PRECONVERT,
			entryStatus::READY,
		);
		
		if (!in_array($entry->getStatus(), $validStatuses))
			throw new KalturaAPIException(KalturaErrors::INVALID_ENTRY_STATUS);
			
		$destThumbParams = thumbParamsPeer::retrieveByPK($destThumbParamsId);
		if(!$destThumbParams)
			throw new KalturaAPIException(KalturaErrors::THUMB_ASSET_PARAMS_ID_NOT_FOUND, $destThumbParamsId);

		$job = kBusinessPreConvertDL::decideThumbGenerate($entry, $destThumbParams);
		if($job)
			return $job->getId();
			
		return null;
	}

	/**
	 * @action regenerate
	 * @param string $thumbAssetId
	 * @return int job id
	 * 
	 * @throws KalturaErrors::THUMB_ASSET_ID_NOT_FOUND
	 * @throws KalturaErrors::ENTRY_TYPE_NOT_SUPPORTED
	 * @throws KalturaErrors::ENTRY_MEDIA_TYPE_NOT_SUPPORTED
	 * @throws KalturaErrors::THUMB_ASSET_PARAMS_ID_NOT_FOUND
	 * @throws KalturaErrors::INVALID_ENTRY_STATUS
	 */
	public function regenerateAction($thumbAssetId)
	{
		$thumbAsset = thumbAssetPeer::retrieveById($thumbAssetId);
		if(!$thumbAsset)
			throw new KalturaAPIException(KalturaErrors::THUMB_ASSET_ID_NOT_FOUND, $thumbAssetId);
			
		if(is_null($thumbAsset->getFlavorParamsId()))
			throw new KalturaAPIException(KalturaErrors::THUMB_ASSET_PARAMS_ID_NOT_FOUND, null);
			
		$destThumbParams = thumbParamsPeer::retrieveByPK($thumbAsset->getFlavorParamsId());
		if(!$destThumbParams)
			throw new KalturaAPIException(KalturaErrors::THUMB_ASSET_PARAMS_ID_NOT_FOUND, $thumbAsset->getFlavorParamsId());
			
		$entry = $thumbAsset->getentry();
		if ($entry->getType() != entryType::MEDIA_CLIP)
			throw new KalturaAPIException(KalturaErrors::ENTRY_TYPE_NOT_SUPPORTED, $entry->getType());
		if ($entry->getMediaType() != entry::ENTRY_MEDIA_TYPE_VIDEO)
			throw new KalturaAPIException(KalturaErrors::ENTRY_MEDIA_TYPE_NOT_SUPPORTED, $entry->getMediaType());
			
		$validStatuses = array(
			entryStatus::ERROR_CONVERTING,
			entryStatus::PRECONVERT,
			entryStatus::READY,
		);
		
		if (!in_array($entry->getStatus(), $validStatuses))
			throw new KalturaAPIException(KalturaErrors::INVALID_ENTRY_STATUS);

		$job = kBusinessPreConvertDL::decideThumbGenerate($entry, $destThumbParams);
		if($job)
			return $job->getId();
			
		return null;
	}
	
	/**
	 * @action get
	 * @param string $thumbAssetId
	 * @return KalturaThumbAsset
	 * 
	 * @throws KalturaErrors::THUMB_ASSET_ID_NOT_FOUND
	 */
	public function getAction($thumbAssetId)
	{
		$thumbAssetsDb = thumbAssetPeer::retrieveById($thumbAssetId);
		if(!$thumbAssetsDb)
			throw new KalturaAPIException(KalturaErrors::THUMB_ASSET_ID_NOT_FOUND, $thumbAssetId);
		
		$thumbAssets = new KalturaThumbAsset();
		$thumbAssets->fromObject($thumbAssetsDb);
		return $thumbAssets;
	}
	
	/**
	 * @action getByEntryId
	 * @param string $entryId
	 * @return KalturaThumbAssetArray
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	public function getByEntryIdAction($entryId)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
			
		// get the thumb assets for this entry
		$c = new Criteria();
		$c->add(thumbAssetPeer::ENTRY_ID, $entryId);
		$thumbAssetsDb = thumbAssetPeer::doSelect($c);
		$thumbAssets = KalturaThumbAssetArray::fromDbArray($thumbAssetsDb);
		return $thumbAssets;
	}
	
	/**
	 * @action addFromUrl
	 * @param string $entryId
	 * @param string $url
	 * @return KalturaThumbAsset
	 */
	public function addFromUrlAction($entryId, $url)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
			
		$ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
		
		$dbThumbAsset = new thumbAsset();
		$dbThumbAsset->setPartnerId($dbEntry->getPartnerId());
		$dbThumbAsset->setEntryId($dbEntry->getId());
		$dbThumbAsset->setStatus(thumbAsset::FLAVOR_ASSET_STATUS_QUEUED);
		$dbThumbAsset->setFileExt($ext);
		$dbThumbAsset->incrementVersion();
		$dbThumbAsset->save();
		
		$syncKey = $dbThumbAsset->getSyncKey(thumbAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		kFileSyncUtils::file_put_contents($syncKey, file_get_contents($url));
		
		$finalPath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
		list($width, $height, $type, $attr) = getimagesize($finalPath);
		
		$dbThumbAsset->setWidth($width);
		$dbThumbAsset->setHeight($height);
		$dbThumbAsset->setSize(filesize($finalPath));
		$dbThumbAsset->setStatus(thumbAsset::FLAVOR_ASSET_STATUS_READY);
		$dbThumbAsset->save();
		
		$thumbAssets = new KalturaThumbAsset();
		$thumbAssets->fromObject($dbThumbAsset);
		return $thumbAssets;
	}
	
	/**
	 * @action addFromJpeg
	 * @param string $entryId
	 * @param file $fileData
	 * @return KalturaThumbAsset
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	public function addFromJpegAction($entryId, $fileData)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
			
		$ext = pathinfo($fileData["name"], PATHINFO_EXTENSION);
		
		$dbThumbAsset = new thumbAsset();
		$dbThumbAsset->setPartnerId($dbEntry->getPartnerId());
		$dbThumbAsset->setEntryId($dbEntry->getId());
		$dbThumbAsset->setStatus(thumbAsset::FLAVOR_ASSET_STATUS_QUEUED);
		$dbThumbAsset->setFileExt($ext);
		$dbThumbAsset->incrementVersion();
		$dbThumbAsset->save();
		
		$syncKey = $dbThumbAsset->getSyncKey(thumbAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		kFileSyncUtils::moveFromFile($fileData["tmp_name"], $syncKey);
		
		$finalPath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
		list($width, $height, $type, $attr) = getimagesize($finalPath);
		
		$dbThumbAsset->setWidth($width);
		$dbThumbAsset->setHeight($height);
		$dbThumbAsset->setSize(filesize($finalPath));
		$dbThumbAsset->setStatus(thumbAsset::FLAVOR_ASSET_STATUS_READY);
		$dbThumbAsset->save();
		
		$thumbAssets = new KalturaThumbAsset();
		$thumbAssets->fromObject($dbThumbAsset);
		return $thumbAssets;
	}
	
	/**
	 * @action delete
	 * @param string $thumbAssetId
	 * 
	 * @throws KalturaErrors::THUMB_ASSET_ID_NOT_FOUND
	 */
	public function deleteAction($thumbAssetId)
	{
		$thumbAssetDb = thumbAssetPeer::retrieveById($thumbAssetId);
		if (!$thumbAssetDb)
			throw new KalturaAPIException(KalturaErrors::THUMB_ASSET_ID_NOT_FOUND, $thumbAssetId);
			
		$thumbAssetDb->setStatus(thumbAsset::FLAVOR_ASSET_STATUS_DELETED);
		$thumbAssetDb->setDeletedAt(time());
		$thumbAssetDb->save();
	}
}
