<?php

/**
 * Media service lets you upload and manage media files (images / videos & audio)
 *
 * @service media
 * @package api
 * @subpackage services
 */
class MediaService extends KalturaEntryService
{
	protected function kalturaNetworkAllowed($actionName)
	{
		if ($actionName === 'get') {
			return true;
		}
		if ($actionName === 'convert') {
			return true;
		}
		if ($actionName === 'addFromEntry') {
			return true;
		}
		if ($actionName === 'addFromFlavorAsset') {
			return true;
		}
		return parent::kalturaNetworkAllowed($actionName);
	}
	
	protected function partnerRequired($actionName)
	{
		if ($actionName === 'flag') {
			return false;
		}
		return parent::partnerRequired($actionName);
	}

	
    /**
     * Generic add entry, should be used when the uploaded entry type is not known
     *
     * @action add
     * @param KalturaMediaEntry $entry
     * @param KalturaResource $resource
     * @return KalturaMediaEntry
     */
    function addAction(KalturaMediaEntry $entry, KalturaResource $resource = null)
    {
    	$dbEntry = parent::add($entry);
    	
    	if(!$resource)
    	{
    		$dbEntry->setStatus(entryStatus::NO_CONTENT);
    		$dbEntry->save();
    		
			$entry->fromObject($dbEntry);
			return $entry;
    	}
    	
    	$this->attachResource($resource, $dbEntry);
    	
		if(!$dbEntry || !$dbEntry->getId())
			return null;
			
//    	$this->convert($dbEntry->getId());
    	myNotificationMgr::createNotification(kNotificationJobData::NOTIFICATION_TYPE_ENTRY_ADD, $dbEntry, $dbEntry->getPartnerId(), null, null, null, $dbEntry->getId());
		
		$entry = new KalturaMediaEntry();
		$entry->fromObject($dbEntry);
		return $entry;
    }
    
    /**
     * @param KalturaUploadedFileResource $resource
     * @param entry $dbEntry
     * @param asset $dbAsset
     * @return asset
     * @throws KalturaErrors::UPLOAD_ERROR
     */
    protected function attachUploadedFileResource(KalturaUploadedFileResource $resource, entry $dbEntry, asset $dbAsset = null)
    {
		$dbEntry->setSource(KalturaSourceType::FILE);
		$dbEntry->save();
		
		$ext = pathinfo($resource->fileData['name'], PATHINFO_EXTENSION);
		
		$uploadPath = $resource->fileData['tmp_name'];
		$tempPath = myContentStorage::getFSUploadsPath() . '/' . uniqid(time()) . '.' . $ext;
		$moved = kFile::moveFile($uploadPath, $tempPath, true);
		if(!$moved)
			throw new KalturaAPIException(KalturaErrors::UPLOAD_ERROR);
			 
		return $this->attachFile($tempPath, $dbEntry, $dbAsset);
    }
    
    /**
     * @param KalturaLocalFileResource $resource
     * @param entry $dbEntry
     * @param asset $dbAsset
     * @return asset
     */
    protected function attachLocalFileResource(KalturaLocalFileResource $resource, entry $dbEntry, asset $dbAsset = null)
    {
		$dbEntry->setSource(KalturaSourceType::FILE);
		$dbEntry->save();
		
		return $this->attachFile($resource->localFilePath, $dbEntry, $dbAsset);
    }
    
    /**
     * @param KalturaUploadedFileTokenResource $resource
     * @param entry $dbEntry
     * @param asset $dbAsset
     * @return asset
     * @throws KalturaErrors::UPLOAD_TOKEN_INVALID_STATUS_FOR_ADD_ENTRY
     * @throws KalturaErrors::UPLOADED_FILE_NOT_FOUND_BY_TOKEN
     */
    protected function attachUploadedFileTokenResource(KalturaUploadedFileTokenResource $resource, entry $dbEntry, asset $dbAsset = null)
    {
		try
		{
		    $entryFullPath = kUploadTokenMgr::getFullPathByUploadTokenId($resource->token);
		}
		catch(kCoreException $ex)
		{
		    if ($ex->getCode() == kUploadTokenException::UPLOAD_TOKEN_INVALID_STATUS);
			    throw new KalturaAPIException(KalturaErrors::UPLOAD_TOKEN_INVALID_STATUS_FOR_ADD_ENTRY);
			    
		    throw $ex;
		}
				
		if(!file_exists($entryFullPath))
		{
			$remoteDCHost = kUploadTokenMgr::getRemoteHostForUploadToken($resource->token, kDataCenterMgr::getCurrentDcId());
			if($remoteDCHost)
			{
				kFile::dumpApiRequest($remoteDCHost);
			}
			else
			{
				throw new KalturaAPIException(KalturaErrors::UPLOADED_FILE_NOT_FOUND_BY_TOKEN);
			}
		}
		
		$dbEntry->setSource(KalturaSourceType::FILE);
		$dbEntry->save();
		
		$dbAsset = $this->attachFile($entryFullPath, $dbEntry, $dbAsset);
		kUploadTokenMgr::closeUploadTokenById($resource->token);
		
		return $dbAsset;
    }
    
    /**
     * @param string $entryFullPath
     * @param entry $dbEntry
     * @param asset $dbAsset
     * @return asset
     * @throws KalturaErrors::UPLOAD_TOKEN_INVALID_STATUS_FOR_ADD_ENTRY
     * @throws KalturaErrors::UPLOADED_FILE_NOT_FOUND_BY_TOKEN
     */
    protected function attachFile($entryFullPath, entry $dbEntry, asset $dbAsset = null)
    {
		$isNewAsset = false;
		if(!$dbAsset)
		{
			$isNewAsset = true;
 			KalturaLog::debug("Creating original flavor asset from file token");
			$dbAsset = kFlowHelper::createOriginalFlavorAsset($this->getPartnerId(), $dbEntry->getId());
		}
		
		if(!$dbAsset)
		{
			$dbEntry->setStatus(entryStatus::ERROR_CONVERTING);
			$dbEntry->save();
		}
		
		$ext = pathinfo($entryFullPath, PATHINFO_EXTENSION);
		$dbAsset->setFileExt($ext);
		$dbAsset->save();
		
		$syncKey = $dbAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		
		try {
			kFileSyncUtils::moveFromFile($entryFullPath, $syncKey);
		}
		catch (Exception $e) {
			$dbEntry->setStatus(entryStatus::ERROR_CONVERTING);
			$dbEntry->save();
			
			$dbAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_ERROR);
			$dbAsset->save();												
			throw $e;
		}
		
		if($isNewAsset)
			kEventsManager::raiseEvent(new kObjectAddedEvent($dbAsset));
		else
			kEventsManager::raiseEvent(new kObjectUpdatedEvent($dbAsset));
			
		return $dbAsset;
    }
    
    /**
     * @param KalturaWebcamTokenResource $resource
     * @param entry $dbEntry
     * @param asset $dbAsset
     * @return asset
     * @throws KalturaErrors::RECORDED_WEBCAM_FILE_NOT_FOUND
     */
    protected function attachWebcamTokenResource(KalturaWebcamTokenResource $resource, entry $dbEntry, asset $dbAsset = null)
    {
	    $content = myContentStorage::getFSContentRootPath();
	    $entryFullPath = "{$content}/content/webcam/{$resource->token}.flv";
	    
		if(!file_exists($entryFullPath))
			throw new KalturaAPIException(KalturaErrors::RECORDED_WEBCAM_FILE_NOT_FOUND);
					
		$entryFixedFullPath = $entryFullPath . '.fixed.flv';
 		KalturaLog::debug("Fix webcam full path from [$entryFullPath] to [$entryFixedFullPath]");
		myFlvStaticHandler::fixRed5WebcamFlv($entryFullPath, $entryFixedFullPath);
				
		$entryNewFullPath = $entryFullPath . '.clipped.flv';
 		KalturaLog::debug("Clip webcam full path from [$entryFixedFullPath] to [$entryNewFullPath]");
		myFlvStaticHandler::clipToNewFile($entryFixedFullPath, $entryNewFullPath, 0, 0);
		$entryFullPath = $entryNewFullPath ;
				
		if(!file_exists($entryFullPath))
			throw new KalturaAPIException(KalturaErrors::RECORDED_WEBCAM_FILE_NOT_FOUND);
					
		$dbEntry->setSource(KalturaSourceType::WEBCAM);
		$dbEntry->save();
		
		$dbAsset = $this->attachFile($entryFullPath, $dbEntry, $dbAsset);
		
		$syncKey = $dbAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$sourceFilePath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
		
		// call mediaInfo for file
		$mediaInfo = new mediaInfo();
		try
		{
			$mediaInfoParser = new KMediaInfoMediaParser($sourceFilePath, kConf::get('bin_path_mediainfo'));
			$KalturaMediaInfo = new KalturaMediaInfo();
			$KalturaMediaInfo = $mediaInfoParser->getMediaInfo();
			$mediaInfo = $KalturaMediaInfo->toInsertableObject($mediaInfo);
			$mediaInfo->setFlavorAssetId($dbAsset->getId());
			$mediaInfo->save();
			
			KDLWrap::ConvertMediainfoCdl2FlavorAsset($mediaInfo, $dbAsset);
			$flavorTags = KDLWrap::CDLMediaInfo2Tags($mediaInfo, array(flavorParams::TAG_WEB, flavorParams::TAG_MBR));
			$dbAsset->setTags(implode(',', $flavorTags));
		}
		catch(Exception $e)
		{
			KalturaLog::err("Getting media info: " . $e->getMessage());
		}
		
		$dbAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_READY);
		$dbAsset->save();
		
		$dbEntry->setStatus(entryStatus::READY);
		$dbEntry->save();
		
		return $dbAsset;
    }
    
    /**
     * @param KalturaAssetResource $resource
     * @param entry $dbEntry
     * @param asset $dbAsset
     * @return asset
     * @throws KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND
     * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
     * @throws KalturaErrors::ORIGINAL_FLAVOR_ASSET_NOT_CREATED
     */
    protected function attachAssetResource(KalturaAssetResource $resource, entry $dbEntry, asset $dbAsset = null)
    {
		$srcFlavorAsset = flavorAssetPeer::retrieveById($resource->assetId);
		if (!$srcFlavorAsset)
			throw new KalturaAPIException(KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND, $resource->assetId);
		
		return $this->attachAsset($srcFlavorAsset, $dbEntry, $dbAsset);
    }
    
    /**
     * @param asset $srcFlavorAsset
     * @param entry $dbEntry
     * @param asset $dbAsset
     * @return asset
     * @throws KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND
     * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
     * @throws KalturaErrors::ORIGINAL_FLAVOR_ASSET_NOT_CREATED
     */
    protected function attachAsset(asset $srcFlavorAsset, entry $dbEntry, asset $dbAsset = null)
    {
		$sourceEntryId = $srcFlavorAsset->getEntryId();
		$srcEntry = entryPeer::retrieveByPK($sourceEntryId);
		if (!$srcEntry || $srcEntry->getType() != $dbEntry->getType())
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $sourceEntryId);
		
    	$dbEntry->setSource(KalturaSearchProviderType::KALTURA);
      	$dbEntry->setSourceId($srcEntry->getId());
      	
      	$isNewAsset = false;
      	if(!$dbAsset)
      	{
      		$isNewAsset = true;
        	$dbAsset = kFlowHelper::createOriginalFlavorAsset($this->getPartnerId(), $dbEntry->getId());
      		$dbEntry->setMediaType($srcEntry->getMediaType());
      	}
      	$dbEntry->save();
      	
        if(!$dbAsset)
        {
			KalturaLog::err("Flavor asset not created for entry [" . $dbEntry->getId() . "]");
			
			$dbEntry->setStatus(entryStatus::ERROR_CONVERTING);
			$dbEntry->save();
			
			throw new KalturaAPIException(KalturaErrors::ORIGINAL_FLAVOR_ASSET_NOT_CREATED);
        }
                
        $srcSyncKey = $srcFlavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
        $newSyncKey = $dbAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
        kFileSyncUtils::createSyncFileLinkForKey($newSyncKey, $srcSyncKey, false);

        if($isNewAsset)
			kEventsManager::raiseEvent(new kObjectAddedEvent($dbAsset));
		else
			kEventsManager::raiseEvent(new kObjectUpdatedEvent($dbAsset));
			
		return $dbAsset;
    }
    
    /**
     * @param KalturaEntryResource $resource
     * @param entry $dbEntry
     * @param asset $dbAsset
     * @return asset
     * @throws KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND
     * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
     * @throws KalturaErrors::ORIGINAL_FLAVOR_ASSET_NOT_CREATED
     */
    protected function attachEntryResource(KalturaEntryResource $resource, entry $dbEntry, asset $dbAsset = null)
    {
    	$srcFlavorAsset = null;
    	assetPeer::resetInstanceCriteriaFilter();
    	if(is_null($resource->flavorParamsId))
			$srcFlavorAsset = assetPeer::retrieveOriginalByEntryId($resource->entryId);
		else
			$srcFlavorAsset = assetPeer::retrieveByEntryIdAndParams($resource->entryId, $resource->flavorParamsId);

		if (!$srcFlavorAsset)
			throw new KalturaAPIException(KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND, $resource->assetId);
		
		return $this->attachAsset($srcFlavorAsset, $dbEntry, $dbAsset);
    }

    /**
     * @param KalturaUrlResource $resource
     * @param entry $dbEntry
     * @param asset $dbAsset
     * @return asset
     */
    protected function attachUrlResource(KalturaUrlResource $resource, entry $dbEntry, asset $dbAsset = null)
    {
		$dbEntry->setSource(KalturaSourceType::URL);
		$dbEntry->save();

		kJobsManager::addImportJob(null, $dbEntry->getId(), $this->getPartnerId(), $resource->url, $dbAsset);
		
		return $dbAsset;
    }
    
    /**
     * @param KalturaBulkResource $resource
     * @param entry $dbEntry
     * @param asset $dbAsset
     * @return asset
     */
    protected function attachBulkResource(KalturaBulkResource $resource, entry $dbEntry, asset $dbAsset = null)
    {
		$dbEntry->setBulkUploadId($resource->bulkUploadId);

		return $this->attachUrlResource($resource, $dbEntry, $dbAsset);
    }
    
    /**
     * @param KalturaSearchResultsResource $resource
     * @param entry $dbEntry
     * @param asset $dbAsset
     * @return asset
     */
    protected function attachSearchResultsResource(KalturaSearchResultsResource $resource, entry $dbEntry, asset $dbAsset = null)
    {
     	$resource->result->validatePropertyNotNull("searchSource");
     	
		if (!$dbEntry->getName())
			$dbEntry->setName($resource->result->title);
			
		if (!$dbEntry->getMediaType())
			$dbEntry->setMediaType($resource->result->mediaType);

        if (!$dbEntry->getDescription())
        	$dbEntry->setDescription($resource->result->description);
        
        if (!$dbEntry->getSiteUrl())
        	$dbEntry->setSiteUrl($resource->result->sourceLink);
        	
       	if (!$dbEntry->getCredit())
       		$dbEntry->setCredit($resource->result->credit);
       		
     	if (!$dbEntry->getTags())
      		$dbEntry->setTags($resource->result->tags);
    
		$dbEntry->setSource($resource->result->searchSource);
     	$dbEntry->setSourceId($resource->result->id);
		$dbEntry->save();
		
     	
		if ($dbEntry->getSource() == entry::ENTRY_MEDIA_SOURCE_KALTURA ||
			$dbEntry->getSource() == entry::ENTRY_MEDIA_SOURCE_KALTURA_PARTNER ||
			$dbEntry->getSource() == entry::ENTRY_MEDIA_SOURCE_KALTURA_PARTNER_KSHOW ||
			$dbEntry->getSource() == entry::ENTRY_MEDIA_SOURCE_KALTURA_KSHOW ||
			$dbEntry->getSource() == entry::ENTRY_MEDIA_SOURCE_KALTURA_USER_CLIPS)
		{
			$sourceEntryId = $resource->result->id;
			$copyDataResult = myEntryUtils::copyData($sourceEntryId, $dbEntry);
			
			if (!$copyDataResult) // will be false when the entry id was not found
				throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $sourceEntryId);
				
			$dbEntry->setStatusReady();
			$dbEntry->save();
		}
		else
		{
			kJobsManager::addImportJob(null, $dbEntry->getId(), $this->getPartnerId(), $resource->result->url, $dbAsset);
		}
		return $dbAsset;
    }
    
    /**
     * @param KalturaAssetsParamsResourceContainers $resource
     * @param entry $dbEntry
     * @param asset $dbAsset
     * @return asset
     */
    protected function attachAssetsParamsResourceContainers(KalturaAssetsParamsResourceContainers $resource, entry $dbEntry, asset $dbAsset = null)
    {
    	$ret = null;
    	foreach($resource->resources as $assetParamsResourceContainer)
    	{
    		$dbAsset = $this->attachAssetParamsResourceContainer($assetParamsResourceContainer, $dbEntry, $dbAsset);
    		if($dbAsset->getIsOriginal())
    			$ret = $dbAsset;
    	}
    	return $ret;
    }
    
    /**
     * @param KalturaAssetParamsResourceContainer $resource
     * @param entry $dbEntry
     * @param asset $dbAsset
     * @return asset
     */
    protected function attachAssetParamsResourceContainer(KalturaAssetParamsResourceContainer $resource, entry $dbEntry, asset $dbAsset = null)
    {
    	assetPeer::resetInstanceCriteriaFilter();
    	if(!$dbAsset)
    		$dbAsset = assetPeer::retrieveByEntryIdAndParams($dbEntry->getId(), $resource->assetParamsId);
    		
    	if(!$dbAsset)
    	{
			$dbAsset = new flavorAsset();
			$dbAsset->setPartnerId($dbEntry->getPartnerId());
			$dbAsset->setEntryId($dbEntry->getId());
			
			assetParamsPeer::resetInstanceCriteriaFilter();
			$assetParams = assetParamsPeer::retrieveByPK($resource->assetParamsId);
			if($assetParams)
			{
				$dbAsset->setFlavorParamsId($resource->assetParamsId);
				if($assetParams->hasTag(assetParams::TAG_SOURCE))
					$dbAsset->setIsOriginal(true);
			}
    	}
		$dbAsset->incrementVersion();
		$dbAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_QUEUED);
		$dbAsset->save();
		
		return $this->attachResource($resource->resource, $dbEntry, $dbAsset);
    }
    
    /**
     * @param KalturaResource $resource
     * @param entry $dbEntry
     * @param asset $dbAsset
     * @return asset
     * @throws KalturaErrors::UPLOAD_TOKEN_INVALID_STATUS_FOR_ADD_ENTRY
     * @throws KalturaErrors::UPLOADED_FILE_NOT_FOUND_BY_TOKEN
     * @throws KalturaErrors::RECORDED_WEBCAM_FILE_NOT_FOUND
     * @throws KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND
     * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
     * @throws KalturaErrors::ORIGINAL_FLAVOR_ASSET_NOT_CREATED
     * @throws KalturaErrors::UPLOAD_ERROR
     */
    protected function attachResource(KalturaResource $resource, entry $dbEntry, asset $dbAsset = null)
    {
    	switch(get_class($resource))
    	{
			case 'KalturaAssetsParamsResourceContainers':
				return $this->attachAssetsParamsResourceContainers($resource, $dbEntry, $dbAsset);
				
			case 'KalturaAssetParamsResourceContainer':
				return $this->attachAssetParamsResourceContainer($resource, $dbEntry, $dbAsset);
				
			case 'KalturaUploadedFileTokenResource':
				return $this->attachUploadedFileTokenResource($resource, $dbEntry, $dbAsset);
				
			case 'KalturaWebcamTokenResource':
				return $this->attachWebcamTokenResource($resource, $dbEntry, $dbAsset);
				
			case 'KalturaAssetResource':
				return $this->attachAssetResource($resource, $dbEntry, $dbAsset);
				
			case 'KalturaEntryResource':
				return $this->attachEntryResource($resource, $dbEntry, $dbAsset);
				
			case 'KalturaUrlResource':
				return $this->attachUrlResource($resource, $dbEntry, $dbAsset);
				
			case 'KalturaBulkResource':
				return $this->attachBulkResource($resource, $dbEntry, $dbAsset);
				
			case 'KalturaSearchResultsResource':
				return $this->attachSearchResultsResource($resource, $dbEntry, $dbAsset);
				
			case 'KalturaLocalFileResource':
				return $this->attachLocalFileResource($resource, $dbEntry, $dbAsset);
				
			case 'KalturaUploadedFileResource':
				return $this->attachUploadedFileResource($resource, $dbEntry, $dbAsset);
				
			case 'KalturaRemoteStorageResource':
			case 'KalturaDropFolderFileResource':
			case 'KalturaFileSyncResource':
				// TODO
				return null;
    	}
    }
    
	/**
	 * Adds new media entry by importing an HTTP or FTP URL.
	 * The entry will be queued for import and then for conversion.
	 * This action should be exposed only to the batches
	 * 
	 * @action addFromBulk
	 * @param KalturaMediaEntry $mediaEntry Media entry metadata  
	 * @param string $url An HTTP or FTP URL
	 * @param int $bulkUploadId The id of the bulk upload job
	 * @return KalturaMediaEntry The new media entry
	 * 
	 * @throws KalturaErrors::PROPERTY_VALIDATION_MIN_LENGTH
	 * @throws KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL
	 */
	function addFromBulkAction(KalturaMediaEntry $mediaEntry, $url, $bulkUploadId)
	{
		return $this->addDbFromUrl($mediaEntry, $url, $bulkUploadId);
	}
	
	/**
	 * Adds new media entry by importing an HTTP or FTP URL.
	 * The entry will be queued for import and then for conversion.
	 * 
	 * @action addFromUrl
	 * @param KalturaMediaEntry $mediaEntry Media entry metadata  
	 * @param string $url An HTTP or FTP URL
	 * @return KalturaMediaEntry The new media entry
	 * 
	 * @throws KalturaErrors::PROPERTY_VALIDATION_MIN_LENGTH
	 * @throws KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL
	 */
	function addFromUrlAction(KalturaMediaEntry $mediaEntry, $url)
	{
		return $this->addDbFromUrl($mediaEntry, $url);
	}
	
	private function addDbFromUrl(KalturaMediaEntry $mediaEntry, $url, $bulkUploadId = null)
	{
		$dbEntry = $this->prepareEntryForInsert($mediaEntry);
		if($bulkUploadId)
			$dbEntry->setBulkUploadId($bulkUploadId);
		
        $kshowId = $dbEntry->getKshowId();
		
		// setup the needed params for my insert entry helper
		$paramsArray = array (
			"entry_media_source" => KalturaSourceType::URL,
            "entry_media_type" => $dbEntry->getMediaType(),
			"entry_url" => $url,
			"entry_license" => $dbEntry->getLicenseType(),
			"entry_credit" => $dbEntry->getCredit(),
			"entry_source_link" => $dbEntry->getSourceLink(),
			"entry_tags" => $dbEntry->getTags(),
		);
		
		$token = $this->getKsUniqueString();
		$insert_entry_helper = new myInsertEntryHelper(null , $dbEntry->getKuserId(), $kshowId, $paramsArray);
		$insert_entry_helper->setPartnerId($this->getPartnerId(), $this->getPartnerId() * 100);
		$insert_entry_helper->insertEntry($token, $dbEntry->getType(), $dbEntry->getId(), $dbEntry->getName(), $dbEntry->getTags(), $dbEntry);
		$dbEntry = $insert_entry_helper->getEntry();

		myNotificationMgr::createNotification( kNotificationJobData::NOTIFICATION_TYPE_ENTRY_ADD, $dbEntry, $this->getPartnerId(), null, null, null, $dbEntry->getId());

		// FIXME: need to remove something from cache? in the old code the kshow was removed
		$mediaEntry->fromObject($dbEntry);
		return $mediaEntry;
	}

	/**
	 * Adds new media entry by importing the media file from a search provider. 
	 * This action should be used with the search service result.
	 *
	 * @action addFromSearchResult
	 * @param KalturaMediaEntry $mediaEntry Media entry metadata
	 * @param KalturaSearchResult $searchResult Result object from search service
	 * @return KalturaMediaEntry The new media entry
	 * 
	 * @throws KalturaErrors::PROPERTY_VALIDATION_MIN_LENGTH
	 * @throws KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL
	 */
	function addFromSearchResultAction(KalturaMediaEntry $mediaEntry = null, KalturaSearchResult $searchResult = null)
	{
		if ($mediaEntry === null)
			$mediaEntry = new KalturaMediaEntry();
			
		if ($searchResult === null)
			$searchResult = new KalturaSearchResult();
			
		// copy the fields from search result if they are missing in media entry 
		// this should be checked before prepareEntry method call
		if ($mediaEntry->name === null)
			$mediaEntry->name = $searchResult->title;
			
		if ($mediaEntry->mediaType === null)
			$mediaEntry->mediaType = $searchResult->mediaType;

        if ($mediaEntry->description === null)
        	$mediaEntry->description = $searchResult->description;
        
        if ($mediaEntry->creditUrl === null)
        	$mediaEntry->creditUrl = $searchResult->sourceLink;
        	
       	if ($mediaEntry->creditUserName === null)
       		$mediaEntry->creditUserName = $searchResult->credit;
       		
     	if ($mediaEntry->tags === null)
      		$mediaEntry->tags = $searchResult->tags;

     	$searchResult->validatePropertyNotNull("searchSource");
     	
    	$mediaEntry->sourceType = KalturaSourceType::SEARCH_PROVIDER;
     	$mediaEntry->searchProviderType = $searchResult->searchSource;
     	$mediaEntry->searchProviderId = $searchResult->id;
     	
		$dbEntry = $this->prepareEntryForInsert($mediaEntry);
      	$dbEntry->setSourceId( $searchResult->id );
      	
        $kshowId = $dbEntry->getKshowId();
        	
       	// $searchResult->licenseType; // FIXME, No support for licenseType
        // FIXME - no need to clone entry if $dbEntry->getSource() == entry::ENTRY_MEDIA_SOURCE_KALTURA_USER_CLIPS
		if ($dbEntry->getSource() == entry::ENTRY_MEDIA_SOURCE_KALTURA ||
			$dbEntry->getSource() == entry::ENTRY_MEDIA_SOURCE_KALTURA_PARTNER ||
			$dbEntry->getSource() == entry::ENTRY_MEDIA_SOURCE_KALTURA_PARTNER_KSHOW ||
			$dbEntry->getSource() == entry::ENTRY_MEDIA_SOURCE_KALTURA_KSHOW ||
			$dbEntry->getSource() == entry::ENTRY_MEDIA_SOURCE_KALTURA_USER_CLIPS)
		{
			$sourceEntryId = $searchResult->id;
			$copyDataResult = myEntryUtils::copyData($sourceEntryId, $dbEntry);
			
			if (!$copyDataResult) // will be false when the entry id was not found
				throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $sourceEntryId);
				
			$dbEntry->setStatusReady();
			$dbEntry->save();
		}
		else
		{
			// setup the needed params for my insert entry helper
			$paramsArray = array (
				"entry_media_source" => $dbEntry->getSource(),
	            "entry_media_type" => $dbEntry->getMediaType(),
				"entry_url" => $searchResult->url,
				"entry_license" => $dbEntry->getLicenseType(),
				"entry_credit" => $dbEntry->getCredit(),
				"entry_source_link" => $dbEntry->getSourceLink(),
				"entry_tags" => $dbEntry->getTags(),
			);
			
			$token = $this->getKsUniqueString();
			$insert_entry_helper = new myInsertEntryHelper(null , $dbEntry->getKuserId(), $kshowId, $paramsArray);
			$insert_entry_helper->setPartnerId($this->getPartnerId(), $this->getPartnerId() * 100);
			$insert_entry_helper->insertEntry($token, $dbEntry->getType(), $dbEntry->getId(), $dbEntry->getName(), $dbEntry->getTags(), $dbEntry);
			$dbEntry = $insert_entry_helper->getEntry();
		}

		myNotificationMgr::createNotification( kNotificationJobData::NOTIFICATION_TYPE_ENTRY_ADD, $dbEntry);

		$mediaEntry->fromObject($dbEntry);
		return $mediaEntry;
	}
	
	/**
	 * Add new entry after the specific media file was uploaded and the upload token id exists
	 *
	 * @action addFromUploadedFile
	 * @param KalturaMediaEntry $mediaEntry Media entry metadata
	 * @param string $uploadTokenId Upload token id
	 * @return KalturaMediaEntry The new media entry
	 * 
	 * @throws KalturaErrors::PROPERTY_VALIDATION_MIN_LENGTH
	 * @throws KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL
	 * @throws KalturaErrors::UPLOADED_FILE_NOT_FOUND_BY_TOKEN
	 */
	function addFromUploadedFileAction(KalturaMediaEntry $mediaEntry, $uploadTokenId)
	{
		try
		{
		    // check that the uploaded file exists
			    $entryFullPath = kUploadTokenMgr::getFullPathByUploadTokenId($uploadTokenId);
		}
		catch(kCoreException $ex)
		{
		    if ($ex->getCode() == kUploadTokenException::UPLOAD_TOKEN_INVALID_STATUS);
			    throw new KalturaAPIException(KalturaErrors::UPLOAD_TOKEN_INVALID_STATUS_FOR_ADD_ENTRY);
			    
		    throw $ex;
		}
		
		if (!file_exists($entryFullPath))
		{
			$remoteDCHost = kUploadTokenMgr::getRemoteHostForUploadToken($uploadTokenId, kDataCenterMgr::getCurrentDcId());
			if($remoteDCHost)
			{
				kFile::dumpApiRequest($remoteDCHost);
			}
			else
			{
				throw new KalturaAPIException(KalturaErrors::UPLOADED_FILE_NOT_FOUND_BY_TOKEN);
			}
		}
			
		$dbEntry = $this->prepareEntryForInsert($mediaEntry);
		
        $kshowId = $dbEntry->getKshowId();
			
		// setup the needed params for my insert entry helper
		$paramsArray = array (
			"entry_media_source" => KalturaSourceType::FILE,
			"entry_media_type" => $dbEntry->getMediaType(),
			"entry_full_path" => $entryFullPath,
			"entry_license" => $dbEntry->getLicenseType(),
			"entry_credit" => $dbEntry->getCredit(),
			"entry_source_link" => $dbEntry->getSourceLink(),
			"entry_tags" => $dbEntry->getTags(),
		);
		
		$token = $this->getKsUniqueString();
		$insert_entry_helper = new myInsertEntryHelper(null , $dbEntry->getKuserId(), $kshowId, $paramsArray);
		$insert_entry_helper->setPartnerId($this->getPartnerId(), $this->getPartnerId() * 100);
		$insert_entry_helper->insertEntry($token, $dbEntry->getType(), $dbEntry->getId(), $dbEntry->getName(), $dbEntry->getTags(), $dbEntry);
		$dbEntry = $insert_entry_helper->getEntry();

		kUploadTokenMgr::closeUploadTokenById($uploadTokenId);
		
		$ret = new KalturaMediaEntry();
		if($dbEntry)
		{
			myNotificationMgr::createNotification( kNotificationJobData::NOTIFICATION_TYPE_ENTRY_ADD, $dbEntry, $dbEntry->getPartnerId(), null, null, null, $dbEntry->getId());
			$ret->fromObject($dbEntry);
		}
		
		return $ret;
	}
	
	/**
	 * Add new entry after the file was recored on the server and the token id exists
	 *
	 * @action addFromRecordedWebcam
	 * @param KalturaMediaEntry $mediaEntry Media entry metadata
	 * @param string $webcamTokenId Token id for the recored webcam file 
	 * @return KalturaMediaEntry The new media entry
	 * 
	 * @throws KalturaErrors::PROPERTY_VALIDATION_MIN_LENGTH
	 * @throws KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL
	 * @throws KalturaErrors::RECORDED_WEBCAM_FILE_NOT_FOUND
	 */
	function addFromRecordedWebcamAction(KalturaMediaEntry $mediaEntry, $webcamTokenId)
	{
	    // check that the webcam file exists
	    $content = myContentStorage::getFSContentRootPath();
	    $webcamBasePath = $content."/content/webcam/".$webcamTokenId; // filesync ok
		$entryFullPath = $webcamBasePath.'.flv';
		if (!file_exists($entryFullPath))
			throw new KalturaAPIException(KalturaErrors::RECORDED_WEBCAM_FILE_NOT_FOUND);
			
		$dbEntry = $this->prepareEntryForInsert($mediaEntry);
		
        $kshowId = $dbEntry->getKshowId();
			
		// setup the needed params for my insert entry helper
		$paramsArray = array (
			"entry_media_source" => KalturaSourceType::WEBCAM,
            "entry_media_type" => $dbEntry->getMediaType(),
			"webcam_suffix" => $webcamTokenId,
			"entry_license" => $dbEntry->getLicenseType(),
			"entry_credit" => $dbEntry->getCredit(),
			"entry_source_link" => $dbEntry->getSourceLink(),
			"entry_tags" => $dbEntry->getTags(),
		);
		
		$token = $this->getKsUniqueString();
		$insert_entry_helper = new myInsertEntryHelper(null , $dbEntry->getKuserId(), $kshowId, $paramsArray);
		$insert_entry_helper->setPartnerId($this->getPartnerId(), $this->getPartnerId() * 100);
		$insert_entry_helper->insertEntry($token, $dbEntry->getType(), $dbEntry->getId(), $dbEntry->getName(), $dbEntry->getTags(), $dbEntry);
		$dbEntry = $insert_entry_helper->getEntry();

		myNotificationMgr::createNotification( kNotificationJobData::NOTIFICATION_TYPE_ENTRY_ADD, $dbEntry);

		$mediaEntry->fromObject($dbEntry);
		return $mediaEntry;
	}
	
	/**
	 * Copy entry into new entry
	 * 
	 * @action addFromEntry
	 * @param string $sourceEntryId Media entry id to copy from
	 * @param KalturaMediaEntry $mediaEntry Media entry metadata
	 * @param int $sourceFlavorParamsId The flavor to be used as the new entry source, source flavor will be used if not specified
	 * @return KalturaMediaEntry The new media entry
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::ORIGINAL_FLAVOR_ASSET_IS_MISSING
	 * @throws KalturaErrors::FLAVOR_PARAMS_NOT_FOUND
	 * @throws KalturaErrors::ORIGINAL_FLAVOR_ASSET_NOT_CREATED
	 */
	function addFromEntryAction($sourceEntryId, KalturaMediaEntry $mediaEntry = null, $sourceFlavorParamsId = null)
	{
		$srcEntry = entryPeer::retrieveByPK($sourceEntryId);

		if (!$srcEntry || $srcEntry->getType() != entryType::MEDIA_CLIP)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $sourceEntryId);
		
		$srcFlavorAsset = null;
		if(is_null($sourceFlavorParamsId))
		{
			$srcFlavorAsset = flavorAssetPeer::retreiveOriginalByEntryId($sourceEntryId);
			if(!$srcFlavorAsset)
				throw new KalturaAPIException(KalturaErrors::ORIGINAL_FLAVOR_ASSET_IS_MISSING);
		}
		else
		{
			$srcFlavorAssets = flavorAssetPeer::retreiveReadyByEntryIdAndFlavorParams($sourceEntryId, array($sourceFlavorParamsId));
			if(count($srcFlavorAssets))
			{
				$srcFlavorAsset = reset($srcFlavorAssets);
			}
			else
			{
				throw new KalturaAPIException(KalturaErrors::FLAVOR_PARAMS_NOT_FOUND);
			}
		}
		
		if ($mediaEntry === null)
			$mediaEntry = new KalturaMediaEntry();
			
		$mediaEntry->mediaType = $srcEntry->getMediaType();
			
		return $this->addEntryFromFlavorAsset($mediaEntry, $srcEntry, $srcFlavorAsset);
	}
	
	/**
	 * Copy flavor asset into new entry
	 * 
	 * @action addFromFlavorAsset
	 * @param string $sourceFlavorAssetId Flavor asset id to be used as the new entry source
	 * @param KalturaMediaEntry $mediaEntry Media entry metadata
	 * @return KalturaMediaEntry The new media entry
	 * @throws KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::ORIGINAL_FLAVOR_ASSET_NOT_CREATED
	 */
	function addFromFlavorAssetAction($sourceFlavorAssetId, KalturaMediaEntry $mediaEntry = null)
	{
		$srcFlavorAsset = flavorAssetPeer::retrieveById($sourceFlavorAssetId);

		if (!$srcFlavorAsset)
			throw new KalturaAPIException(KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND, $sourceFlavorAssetId);
		
		$sourceEntryId = $srcFlavorAsset->getEntryId();
		$srcEntry = entryPeer::retrieveByPK($sourceEntryId);

		if (!$srcEntry || $srcEntry->getType() != entryType::MEDIA_CLIP)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $sourceEntryId);
		
		if ($mediaEntry === null)
			$mediaEntry = new KalturaMediaEntry();
			
		$mediaEntry->mediaType = $srcEntry->getMediaType();
			
		return $this->addEntryFromFlavorAsset($mediaEntry, $srcEntry, $srcFlavorAsset);
	}
	
	/**
	 * Convert entry
	 * 
	 * @action convert
	 * @param string $entryId Media entry id
	 * @param int $conversionProfileId
	 * @param KalturaConversionAttributeArray $dynamicConversionAttributes
	 * @return int job id
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::CONVERSION_PROFILE_ID_NOT_FOUND
	 * @throws KalturaErrors::FLAVOR_PARAMS_NOT_FOUND
	 */
	function convertAction($entryId, $conversionProfileId = null, KalturaConversionAttributeArray $dynamicConversionAttributes = null)
	{
		return $this->convert($entryId, $conversionProfileId, $dynamicConversionAttributes);
	}
	
	/**
	 * Get media entry by ID.
	 * 
	 * @action get
	 * @param string $entryId Media entry id
	 * @param int $version Desired version of the data
	 * @return KalturaMediaEntry The requested media entry
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	function getAction($entryId, $version = -1)
	{
		return $this->getEntry($entryId, $version, KalturaEntryType::MEDIA_CLIP);
	}

	/**
	 * Update media entry. Only the properties that were set will be updated.
	 * 
	 * @action update
	 * @param string $entryId Media entry id to update
	 * @param KalturaMediaEntry $mediaEntry Media entry metadata to update
	 * @return KalturaMediaEntry The updated media entry
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	function updateAction($entryId, KalturaMediaEntry $mediaEntry)
	{
		return $this->updateEntry($entryId, $mediaEntry, KalturaEntryType::MEDIA_CLIP);
	}

	/**
	 * Delete a media entry.
	 *
	 * @action delete
	 * @param string $entryId Media entry id to delete
	 * 
 	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	function deleteAction($entryId)
	{
		$this->deleteEntry($entryId, KalturaEntryType::MEDIA_CLIP);
	}
	
	/**
	 * List media entries by filter with paging support.
	 * 
	 * @action list
     * @param KalturaMediaEntryFilter $filter Media entry filter
	 * @param KalturaFilterPager $pager Pager
	 * @return KalturaMediaListResponse Wrapper for array of media entries and total count
	 */
	function listAction(KalturaMediaEntryFilter $filter = null, KalturaFilterPager $pager = null)
	{
	    myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL3;
		

	    if (!$filter)
			$filter = new KalturaMediaEntryFilter();
			
	    $filter->typeEqual = KalturaEntryType::MEDIA_CLIP;
	    list($list, $totalCount) = parent::listEntriesByFilter($filter, $pager);
	    
	    $newList = KalturaMediaEntryArray::fromEntryArray($list);
		$response = new KalturaMediaListResponse();
		$response->objects = $newList;
		$response->totalCount = $totalCount;
		return $response;
	}
	
	/**
	 * Count media entries by filter.
	 * 
	 * @action count
     * @param KalturaMediaEntryFilter $filter Media entry filter
	 * @return int
	 */
	function countAction(KalturaMediaEntryFilter $filter = null)
	{
	    if (!$filter)
			$filter = new KalturaMediaEntryFilter();
			
		$filter->typeEqual = KalturaEntryType::MEDIA_CLIP;
		
		return parent::countEntriesByFilter($filter);
	}

	/**
	 * Upload a media file to Kaltura, then the file can be used to create a media entry. 
	 * 
	 * @action upload
	 * @param file $fileData The file data
	 * @return string Upload token id
	 */
	function uploadAction($fileData)
	{
		$ksUnique = $this->getKsUniqueString();
		
		$uniqueId = substr(base_convert(md5(uniqid(rand(), true)), 16, 36), 1, 20);
		
		$ext = pathinfo($fileData["name"], PATHINFO_EXTENSION);
		$token = $ksUnique."_".$uniqueId.".".$ext;
		
		$res = myUploadUtils::uploadFileByToken($fileData, $token, "", null, true);
	
		return $res["token"];
	}

	/**
	 * Update media entry thumbnail by a specified time offset (In seconds)
	 * If flavor params id not specified, source flavor will be used by default
	 * 
	 * @action updateThumbnail
	 * @param string $entryId Media entry id
	 * @param int $timeOffset Time offset (in seconds)
	 * @param int $flavorParamsId The flavor params id to be used
	 * @return KalturaMediaEntry The media entry
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::PERMISSION_DENIED_TO_UPDATE_ENTRY
	 * 
	 * @deprecated
	 */
	function updateThumbnailAction($entryId, $timeOffset, $flavorParamsId = null)
	{
		return parent::updateThumbnailForEntryFromSourceEntry($entryId, $entryId, $timeOffset, KalturaEntryType::MEDIA_CLIP, $flavorParamsId);
	}
	
	/**
	 * Update media entry thumbnail from a different entry by a specified time offset (In seconds)
	 * If flavor params id not specified, source flavor will be used by default
	 * 
	 * @action updateThumbnailFromSourceEntry
	 * @param string $entryId Media entry id
	 * @param string $sourceEntryId Media entry id
	 * @param int $timeOffset Time offset (in seconds)
	 * @param int $flavorParamsId The flavor params id to be used
	 * @return KalturaMediaEntry The media entry
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::PERMISSION_DENIED_TO_UPDATE_ENTRY
	 * 
	 * @deprecated
	 */
	function updateThumbnailFromSourceEntryAction($entryId, $sourceEntryId, $timeOffset, $flavorParamsId = null)
	{
		return parent::updateThumbnailForEntryFromSourceEntry($entryId, $sourceEntryId, $timeOffset, KalturaEntryType::MEDIA_CLIP, $flavorParamsId);
	}	

	/**
	 * Update media entry thumbnail using a raw jpeg file
	 * 
	 * @action updateThumbnailJpeg
	 * @param string $entryId Media entry id
	 * @param file $fileData Jpeg file data
	 * @return KalturaMediaEntry The media entry
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::PERMISSION_DENIED_TO_UPDATE_ENTRY
	 * 
	 * @deprecated
	 */
	function updateThumbnailJpegAction($entryId, $fileData)
	{
		return parent::updateThumbnailJpegForEntry($entryId, $fileData, KalturaEntryType::MEDIA_CLIP);
	}
	
	/**
	 * Update entry thumbnail using url
	 * 
	 * @action updateThumbnailFromUrl
	 * @param string $entryId Media entry id
	 * @param string $url file url
	 * @return KalturaBaseEntry The media entry
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::PERMISSION_DENIED_TO_UPDATE_ENTRY
	 * 
	 * @deprecated
	 */
	function updateThumbnailFromUrlAction($entryId, $url)
	{
		return parent::updateThumbnailForEntryFromUrl($entryId, $url, KalturaEntryType::MEDIA_CLIP);
	}
	
	/**
	 * Request a new conversion job, this can be used to convert the media entry to a different format
	 * 
	 * @action requestConversion
	 * @param string $entryId Media entry id
	 * @param string $fileFormat Format to convert
	 * @return int The queued job id
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	public function requestConversionAction($entryId, $fileFormat)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);

		if (!$dbEntry || $dbEntry->getType() != KalturaEntryType::MEDIA_CLIP)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
		
		if ($dbEntry->getMediaType() == KalturaMediaType::AUDIO)
		{
			// for audio - force format flv regardless what the user really asked for
			$fileFormat = "flv";
		}

//		$job = myBatchDownloadVideoServer::addJob($this->getKuser()->getPuserId(), $dbEntry, null, $fileFormat);
		$flavorParams = myConversionProfileUtils::getFlavorParamsFromFileFormat ( $this->getPartnerId() , $fileFormat );
		
		$err = null;
		$job = kBusinessPreConvertDL::decideAddEntryFlavor(null, $dbEntry->getId(), $flavorParams->getId(), $err);
		
		if ( $job )	
			return $job->getId();
		else
			return null;
	}
	
	/**
	 * Flag inappropriate media entry for moderation
	 *
	 * @action flag
	 * @param string $entryId
	 * @param KalturaModerationFlag $moderationFlag
	 * 
 	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	public function flagAction(KalturaModerationFlag $moderationFlag)
	{
		KalturaResponseCacher::disableCache();		
		return parent::flagEntry($moderationFlag, KalturaEntryType::MEDIA_CLIP);
	}
	
	/**
	 * Reject the media entry and mark the pending flags (if any) as moderated (this will make the entry non playable)
	 *
	 * @action reject
	 * @param string $entryId
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	public function rejectAction($entryId)
	{
		parent::rejectEntry($entryId, KalturaEntryType::MEDIA_CLIP);
	}
	
	/**
	 * Approve the media entry and mark the pending flags (if any) as moderated (this will make the entry playable) 
	 *
	 * @action approve
	 * @param string $entryId
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	public function approveAction($entryId)
	{
		parent::approveEntry($entryId, KalturaEntryType::MEDIA_CLIP);
	}
	
	/**
	 * List all pending flags for the media entry
	 *
	 * @action listFlags
	 * @param string $entryId
	 * @param KalturaFilterPager $pager
	 * @return KalturaModerationFlagListResponse
	 */
	public function listFlags($entryId, KalturaFilterPager $pager = null)
	{
		return parent::listFlagsForEntry($entryId, $pager);
	}
	
	/**
	 * Anonymously rank a media entry, no validation is done on duplicate rankings
	 *  
	 * @action anonymousRank
	 * @param string $entryId
	 * @param int $rank
	 */
	public function anonymousRankAction($entryId, $rank)
	{
		return parent::anonymousRankEntry($entryId, KalturaEntryType::MEDIA_CLIP, $rank);
	}
	
	/**
	 * @param KalturaBaseEntry $entry
	 * @return entry
	 */
	protected function prepareEntryForInsert(KalturaBaseEntry $entry)
	{
		// first validate the input object
		//$entry->validatePropertyMinLength("name", 1);
		$entry->validatePropertyNotNull("mediaType");
		
		$dbEntry = parent::prepareEntryForInsert($entry);
		
		if ( $this->getConversionQualityFromRequest() )
			$dbEntry->setConversionQuality( $this->getConversionQualityFromRequest() );
			
		$kshow = $this->createDummyKShow();
        $kshowId = $kshow->getId();
        
        $dbEntry->setKshowId($kshowId);
		$dbEntry->save();
		
		return $dbEntry;
	}
	
	// TODO - this is because the re ia a GLOBAL parameter sent in the request "conversionquality" that
	// hack due to KCW of version  from KMC
	private function getConversionQualityFromRequest () 
	{
		if(isset($_REQUEST["conversionquality"]))
			return $_REQUEST["conversionquality"];
			
		return null;
	}	
}
