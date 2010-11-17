<?php

/**
 * Retrieve information and invoke actions on Thumb Asset
 *
 * @service thumbAsset
 * @package api
 * @subpackage services
 */
class thumbAssetService extends KalturaBaseService
{
	public function initService($partnerId, $puserId, $ksStr, $serviceName, $action)
	{
		parent::initService($partnerId, $puserId, $ksStr, $serviceName, $action);
//		parent::applyPartnerFilterForClass(new flavorParamsPeer());
//		parent::applyPartnerFilterForClass(new conversionProfile2Peer());
//		parent::applyPartnerFilterForClass(new flavorAssetPeer());
	}
	
/*
 * serveByEntryId action
Serves image based on:
•	Entry id
•	Thumbnail params id, if not set, default thumbnail will be used.
*/
	/**
	 *  ServeByEntryIdAction
	 *  
	 *  @action serveByEntryId
	 *  @param string $entryId
	 *  @param string $paramId
	 *  @return file
	 *  
	 *  @throws KalturaErrors::THUMB_IS_NOT_READY
	 *  @throws KalturaErrors::THUMB_ASSET_ID_NOT_FOUND
	 *  @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	public function serveByEntryIdAction($entryId, $paramId=null)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);


		$fileName = $dbEntry->getName() . '.jpg';
		
		if(!isset($paramId) || $paramId==null) {
			$syncKey = $dbEntry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB);
			list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($syncKey, true, false);
			return $this->serveThumbToFile($fileSync, $local, $fileName);
		}
		
		// get the assets for this entry
		$c = new Criteria();
		$c->add(flavorAssetPeer::ENTRY_ID, $entryId);
		$c->add(flavorAssetPeer::FLAVOR_PARAMS_ID, $paramId);
		$c->add(flavorAssetPeer::STATUS, array(thumbAsset::FLAVOR_ASSET_STATUS_DELETED, thumbAsset::FLAVOR_ASSET_STATUS_TEMP), Criteria::NOT_IN);
		$thumbAssetsDb = flavorAssetPeer::doSelect($c);
		
		if(count($thumbAssetsDb)==0){
			throw new KalturaAPIException(KalturaErrors::THUMB_ASSET_ID_NOT_FOUND, $paramId);
		}

		$syncKey = $thumbAssetsDb[0]->getSyncKey(thumbAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);

		list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($syncKey, true, false);
		return $this->serveThumbToFile($fileSync, $local, $fileName);
	}

	/**
	 *  Serve Action
	 *  
	 *  @action serve
	 *  @param string $assetId
	 *  @return file
	 *  
	 *  @throws KalturaErrors::THUMB_IS_NOT_READY
	 *  @throws KalturaErrors::THUMB_ASSET_ID_NOT_FOUND
	 */
	public function serveAction($assetId)
	{
		$dbAsset = flavorAssetPeer::retrieveById($assetId);
		if (!$dbAsset)
			throw new KalturaAPIException(KalturaErrors::THUMB_ASSET_ID_NOT_FOUND, $assetId);

		$ext=$dbAsset->getFileExt();
		if($ext==null)
			$ext=".jpg";
		$fileName = $dbAsset->getEntryId()."_" . $dbAsset->getId() . $ext;
		
		$syncKey = $dbAsset->getSyncKey(thumbAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);

		list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($syncKey, true, false);
		return $this->serveThumbToFile($fileSync, $local, $fileName);
	}
	
/*
setAsDefault action
Tags the thumbnail as DEFAULT_THUMB and removes that tag from all other thumbnail assets of the entry.
Create a new file sync link on the entry thumbnail that points to the thumbnail asset file sync.
The following attributes should be provided:
•	Thumbnail asset id
*/
	/**
	 *  setAsDefault
	 *  
	 *  @action setAsDefault
	 *  @param string $assetId
	 */
	public function setAsDefaultAction($assetId)
	{
			throw new KalturaAPIException(KalturaErrors::THUMB_ASSET_ID_NOT_FOUND, $assetId);
	}

	/**
	 *  updateByEntryId
	 *  Source video flavor params id, if not set, THUMB_SOURCE tagged flavor will be searched, source flavor will be used in not found.
	 *  Destination thumbnail params id, indicate the id of the ThumbParams to be associated with this thumbnail, if not specified, the id will be selected according to the dimensions
	 *  
	 *  @action updateByEntryId
	 *  @param string $entryId
	 *  @param string $timeOffset
	 *  @param string $srcParamsId
	 *  @param string $dstParamsId 
	 */
	public function updateByEntryIdAction($entryId, $timeOffset, $srcParamsId=null, $dstParamsId=null)
	{
		return parent::updateThumbnailForEntryFromSourceEntry($entryId, $entryId, $timeOffset, KalturaEntryType::MEDIA_CLIP, $flavorParamsId);
	}

	
	/**
	 * serveDefaultThumb
	 * 
	 * @action serveThumbToFile
	 * @param fileSync $fileSync
	 * @param string $local
	 * @param string $fileName
	 * @param bool $forceProxy
	 * @return file
	 * 
	 * @throws KalturaErrors::THUMB_IS_NOT_READY
	 */
	protected function serveThumbToFile($fileSync, $local, $fileName, $forceProxy = false)
	{
//		$syncKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB);
//		list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($syncKey, true, false);
		
		if(!$fileSync)
			throw new KalturaAPIException(KalturaErrors::THUMB_IS_NOT_READY, $entry->getId());
			
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
	
	
	
	
/*
update action
Generates new thumbnail base on:
•	Thumbnail asset id
•	Time offset
•	Source video flavor params id, if not set, THUMB_SOURCE tagged flavor will be searched, source flavor will be used in not found.
*/

/*
updateByEntryIdFromSourceEntry action
The following attributes should be provided:
•	Source entry id
•	Destination entry id 
•	Source video flavor params id
•	Destination thumbnail params id
*/
	
	
/*
updateFromSourceEntry action
The following attributes should be provided:
•	Thumbnail asset id
•	Destination entry id 
•	Source video flavor params id
*/
	
	
/*
 updateByEntryIdFromUrl action
The following attributes should be provided:
•	Entry id 
•	URL
•	Destination thumbnail params id
*/

/*
updateFromUrl action
The following attributes should be provided:
•	Thumbnail asset id 
•	URL
*/
	
/*
updateByEntryIdJpeg action
The following attributes should be provided:
•	Entry id 
•	File
•	Destination thumbnail params id
*/
	
/*
updateJpeg action
The following attributes should be provided:
•	Thumbnail asset id
•	File
*/
	
/*
deleteByEntryId action
The following attributes should be provided:
•	Entry id
•	Thumbnail params id
*/

/*
delete action
The following attributes should be provided:
•	Thumbnail asset id

 */
}
