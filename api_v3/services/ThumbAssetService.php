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
		parent::applyPartnerFilterForClass(new thumbParamsPeer());
		parent::applyPartnerFilterForClass(new conversionProfile2Peer());
		parent::applyPartnerFilterForClass(new thumbAssetPeer());
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
		$thumbAsset = flavorAssetPeer::retrieveById($thumbAssetId);
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
		$thumbAsset = flavorAssetPeer::retrieveById($thumbAssetId);
		if (!$thumbAsset)
			throw new KalturaAPIException(KalturaErrors::THUMB_ASSET_ID_NOT_FOUND, $thumbAssetId);
		
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
	}

	/**
	 * @action generateByEntryId
	 * @param string $entryId
	 * @param int $destThumbParamsId indicate the id of the ThumbParams to be generate this thumbnail by
	 * @return int job id
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
	 * @action generate
	 * @param string $thumbAssetId
	 * @throws KalturaErrors::THUMB_ASSET_ID_NOT_FOUND
	 * @throws KalturaErrors::ENTRY_TYPE_NOT_SUPPORTED
	 * @throws KalturaErrors::ENTRY_MEDIA_TYPE_NOT_SUPPORTED
	 * @throws KalturaErrors::THUMB_ASSET_PARAMS_ID_NOT_FOUND
	 * @throws KalturaErrors::INVALID_ENTRY_STATUS
	 */
	public function generateAction($thumbAssetId)
	{
		$thumbAsset = thumbAssetPeer::retrieveByPK($thumbAssetId);
		if(!$thumbAsset)
			throw new KalturaAPIException(KalturaErrors::THUMB_ASSET_ID_NOT_FOUND, $thumbAssetId);
			
		if(is_null($thumbAsset->getFlavorParamsId()))
			throw new KalturaAPIException(KalturaErrors::THUMB_ASSET_PARAMS_ID_NOT_FOUND, null);
			
		$destThumbParams = thumbParamsPeer::retrieveByPK($thumbAsset->getFlavorParamsId());
		if(!$destThumbParams)
			throw new KalturaAPIException(KalturaErrors::THUMB_ASSET_PARAMS_ID_NOT_FOUND, $thumbAsset->getFlavorParamsId());
			
		$entry = $thumbAsset->getEntryId();
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
	
	
	
/*
update action
Generates new thumbnail base on:
�	Thumbnail asset id
�	Time offset
�	Source video flavor params id, if not set, THUMB_SOURCE tagged flavor will be searched, source flavor will be used in not found.
*/

/*
updateByEntryIdFromSourceEntry action
The following attributes should be provided:
�	Source entry id
�	Destination entry id 
�	Source video flavor params id
�	Destination thumbnail params id
*/
	
	
/*
updateFromSourceEntry action
The following attributes should be provided:
�	Thumbnail asset id
�	Destination entry id 
�	Source video flavor params id
*/
	
	
/*
 updateByEntryIdFromUrl action
The following attributes should be provided:
�	Entry id 
�	URL
�	Destination thumbnail params id
*/

/*
updateFromUrl action
The following attributes should be provided:
�	Thumbnail asset id 
�	URL
*/
	
/*
updateByEntryIdJpeg action
The following attributes should be provided:
�	Entry id 
�	File
�	Destination thumbnail params id
*/
	
/*
updateJpeg action
The following attributes should be provided:
�	Thumbnail asset id
�	File
*/
	
/*
deleteByEntryId action
The following attributes should be provided:
�	Entry id
�	Thumbnail params id
*/

/*
delete action
The following attributes should be provided:
�	Thumbnail asset id

 */
}
