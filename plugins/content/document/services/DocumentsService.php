<?php

/**
 * Document service lets you upload and manage document files
 *
 * @service documents
 * @package plugins.document
 * @subpackage api.services
 */
class DocumentsService extends KalturaEntryService
{
    /**
     * @param kResource $resource
     * @param entry $dbEntry
     * @param asset $dbAsset
     * @return asset
     */
    protected function attachResource(kResource $resource, entry $dbEntry, asset $dbAsset = null)
    {
    	switch($resource->getType())
    	{
			case 'kAssetsParamsResourceContainers':
				return $this->attachAssetsParamsResourceContainers($resource, $dbEntry);
				
			case 'kAssetParamsResourceContainer':
				return $this->attachAssetParamsResourceContainer($resource, $dbEntry, $dbAsset);
				
			case 'kUrlResource':
				return $this->attachUrlResource($resource, $dbEntry, $dbAsset);
				
			case 'kLocalFileResource':
				return $this->attachLocalFileResource($resource, $dbEntry, $dbAsset);
				
			case 'kFileSyncResource':
				return $this->attachFileSyncResource($resource, $dbEntry, $dbAsset);
				
			case 'kRemoteStorageResource':
			case 'kRemoteStorageResources':
				return $this->attachRemoteStorageResource($resource, $dbEntry, $dbAsset);
				
			default:
				KalturaLog::err("Resource of type [" . get_class($resource) . "] is not supported");
				$dbEntry->setStatus(entryStatus::ERROR_IMPORTING);
				$dbEntry->save();
				
				throw new KalturaAPIException(KalturaErrors::RESOURCE_TYPE_NOT_SUPPORTED, get_class($resource));
    	}
    }
    
	/**
	 * Add new document entry after the specific document file was uploaded and the upload token id exists
	 *
	 * @action addFromUploadedFile
	 * @param KalturaDocumentEntry $documentEntry Document entry metadata
	 * @param string $uploadTokenId Upload token id
	 * @return KalturaDocumentEntry The new document entry
	 * 
	 * @throws KalturaErrors::PROPERTY_VALIDATION_MIN_LENGTH
	 * @throws KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL
	 * @throws KalturaErrors::UPLOADED_FILE_NOT_FOUND_BY_TOKEN
	 */
	function addFromUploadedFileAction(KalturaDocumentEntry $documentEntry, $uploadTokenId)
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
				kFileUtils::dumpApiRequest($remoteDCHost);
			}
			else
			{
				throw new KalturaAPIException(KalturaErrors::UPLOADED_FILE_NOT_FOUND_BY_TOKEN);
			}
		}
			
		$dbEntry = $this->prepareEntryForInsert($documentEntry);
		$dbEntry->setSource(EntrySourceType::FILE);
		$dbEntry->setSourceLink("file:$entryFullPath");
		$dbEntry->save();
	
		$te = new TrackEntry();
		$te->setEntryId($dbEntry->getId());
		$te->setTrackEventTypeId(TrackEntry::TRACK_ENTRY_EVENT_TYPE_ADD_ENTRY);
		$te->setDescription(__METHOD__ . ":" . __LINE__ . "::ENTRY_MEDIA_SOURCE_FILE");
		TrackEntry::addTrackEntry( $te );
    
		$msg = null;
		
		$flavorAsset = kFlowHelper::createOriginalFlavorAsset($this->getPartnerId(), $dbEntry->getId(), $msg);
		if(!$flavorAsset)
		{
			KalturaLog::err("Flavor asset not created for entry [" . $dbEntry->getId() . "] reason [$msg]");
			
			$dbEntry->setStatus(entryStatus::ERROR_CONVERTING);
			$dbEntry->save();
		}
		else
		{
			$ext = pathinfo($entryFullPath, PATHINFO_EXTENSION);	
			KalturaLog::info("Uploaded file extension: $ext");
			$flavorAsset->setFileExt($ext);
			$flavorAsset->save();
			
			$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			kFileSyncUtils::moveFromFile($entryFullPath, $syncKey);
			
			kEventsManager::raiseEvent(new kObjectAddedEvent($flavorAsset));
		}
 		
			
		kUploadTokenMgr::closeUploadTokenById($uploadTokenId);
		
		myNotificationMgr::createNotification( kNotificationJobData::NOTIFICATION_TYPE_ENTRY_ADD, $dbEntry);

		$documentEntry->fromObject($dbEntry, $this->getResponseProfile());
		return $documentEntry;
		
	}
	
	/**
	 * Copy entry into new entry
	 * 
	 * @action addFromEntry
	 * @param string $sourceEntryId Document entry id to copy from
	 * @param KalturaDocumentEntry $documentEntry Document entry metadata
	 * @param int $sourceFlavorParamsId The flavor to be used as the new entry source, source flavor will be used if not specified
	 * @return KalturaDocumentEntry The new document entry
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::ORIGINAL_FLAVOR_ASSET_IS_MISSING
	 * @throws KalturaErrors::FLAVOR_PARAMS_NOT_FOUND
	 * @throws KalturaErrors::ORIGINAL_FLAVOR_ASSET_NOT_CREATED
	 */
	function addFromEntryAction($sourceEntryId, KalturaDocumentEntry $documentEntry = null, $sourceFlavorParamsId = null)
	{
		$srcEntry = entryPeer::retrieveByPK($sourceEntryId);

		if (!$srcEntry || $srcEntry->getType() != entryType::DOCUMENT)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $sourceEntryId);
		
		$srcFlavorAsset = null;
		if(is_null($sourceFlavorParamsId))
		{
			$srcFlavorAsset = assetPeer::retrieveOriginalByEntryId($sourceEntryId);
			if(!$srcFlavorAsset)
				throw new KalturaAPIException(KalturaErrors::ORIGINAL_FLAVOR_ASSET_IS_MISSING);
		}
		else
		{
			$srcFlavorAssets = assetPeer::retrieveReadyByEntryIdAndFlavorParams($sourceEntryId, array($sourceFlavorParamsId));
			if(count($srcFlavorAssets))
			{
				$srcFlavorAsset = reset($srcFlavorAssets);
			}
			else
			{
				throw new KalturaAPIException(KalturaErrors::FLAVOR_PARAMS_NOT_FOUND);
			}
		}
		
		if ($documentEntry === null)
			$documentEntry = new KalturaDocumentEntry();
			
		$documentEntry->documentType = $srcEntry->getMediaType();
			
		return $this->addEntryFromFlavorAsset($documentEntry, $srcEntry, $srcFlavorAsset);
	}
	
	/**
	 * Copy flavor asset into new entry
	 * 
	 * @action addFromFlavorAsset
	 * @param string $sourceFlavorAssetId Flavor asset id to be used as the new entry source
	 * @param KalturaDocumentEntry $documentEntry Document entry metadata
	 * @return KalturaDocumentEntry The new document entry
	 * @throws KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::ORIGINAL_FLAVOR_ASSET_NOT_CREATED
	 */
	function addFromFlavorAssetAction($sourceFlavorAssetId, KalturaDocumentEntry $documentEntry = null)
	{
		$srcFlavorAsset = assetPeer::retrieveById($sourceFlavorAssetId);

		if (!$srcFlavorAsset)
			throw new KalturaAPIException(KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND, $sourceFlavorAssetId);
		
		$sourceEntryId = $srcFlavorAsset->getEntryId();
		$srcEntry = entryPeer::retrieveByPK($sourceEntryId);

		if (!$srcEntry || $srcEntry->getType() != entryType::DOCUMENT)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $sourceEntryId);
		
		if ($documentEntry === null)
			$documentEntry = new KalturaDocumentEntry();
			
		$documentEntry->documentType = $srcEntry->getMediaType();
			
		return $this->addEntryFromFlavorAsset($documentEntry, $srcEntry, $srcFlavorAsset);
	}
	
	/**
	 * Convert entry
	 * 
	 * @action convert
	 * @param string $entryId Document entry id
	 * @param int $conversionProfileId
	 * @param KalturaConversionAttributeArray $dynamicConversionAttributes
	 * @return bigint job id
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::CONVERSION_PROFILE_ID_NOT_FOUND
	 * @throws KalturaErrors::FLAVOR_PARAMS_NOT_FOUND
	 */
	function convertAction($entryId, $conversionProfileId = null, KalturaConversionAttributeArray $dynamicConversionAttributes = null)
	{
		return $this->convert($entryId, $conversionProfileId, $dynamicConversionAttributes);
	}
	
	/**
	 * Get document entry by ID.
	 * 
	 * @action get
	 * @param string $entryId Document entry id
	 * @param int $version Desired version of the data
	 * @return KalturaDocumentEntry The requested document entry
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	function getAction($entryId, $version = -1)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);

		if (!$dbEntry || $dbEntry->getType() != KalturaEntryType::DOCUMENT)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		if ($version !== -1)
			$dbEntry->setDesiredVersion($version);
			
		$docEntry = new KalturaDocumentEntry();
		$docEntry->fromObject($dbEntry, $this->getResponseProfile());

		return $docEntry;
	}
	
	/**
	 * Update document entry. Only the properties that were set will be updated.
	 * 
	 * @action update
	 * @param string $entryId Document entry id to update
	 * @param KalturaDocumentEntry $documentEntry Document entry metadata to update
	 * @return KalturaDocumentEntry The updated document entry
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @validateUser entry entryId edit
	 */
	function updateAction($entryId, KalturaDocumentEntry $documentEntry)
	{
		return $this->updateEntry($entryId, $documentEntry, KalturaEntryType::DOCUMENT);
	}
	
	/**
	 * Delete a document entry.
	 *
	 * @action delete
	 * @param string $entryId Document entry id to delete
	 * 
 	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
 	 * @validateUser entry entryId edit
	 */
	function deleteAction($entryId)
	{
		$this->deleteEntry($entryId, KalturaEntryType::DOCUMENT);
	}
	
	/**
	 * List document entries by filter with paging support.
	 * 
	 * @action list
     * @param KalturaDocumentEntryFilter $filter Document entry filter
	 * @param KalturaFilterPager $pager Pager
	 * @return KalturaDocumentListResponse Wrapper for array of document entries and total count
	 */
	function listAction(KalturaDocumentEntryFilter $filter = null, KalturaFilterPager $pager = null)
	{
	    if (!$filter)
			$filter = new KalturaDocumentEntryFilter();
			
	    $filter->typeEqual = KalturaEntryType::DOCUMENT;
	    list($list, $totalCount) = parent::listEntriesByFilter($filter, $pager);
	    
	    $newList = KalturaDocumentEntryArray::fromDbArray($list, $this->getResponseProfile());
		$response = new KalturaDocumentListResponse();
		$response->objects = $newList;
		$response->totalCount = $totalCount;
		return $response;
	}
	
	/**
	 * Upload a document file to Kaltura, then the file can be used to create a document entry. 
	 * 
	 * @action upload
	 * @param file $fileData The file data
	 * @return string Upload token id
	 * 
	 * @deprecated use upload.upload or uploadToken.add instead
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
	 * This will queue a batch job for converting the document file to swf
	 * Returns the URL where the new swf will be available 
	 * 
	 * @action convertPptToSwf
	 * @param string $entryId
	 * @return string
	 */
	function convertPptToSwf($entryId)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);

		if (!$dbEntry || $dbEntry->getType() != KalturaEntryType::DOCUMENT)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		$flavorAsset = assetPeer::retrieveOriginalByEntryId($entryId);
		if (is_null($flavorAsset) || !$flavorAsset->isLocalReadyStatus())
			throw new KalturaAPIException(KalturaErrors::ORIGINAL_FLAVOR_ASSET_IS_MISSING);
		
		$sync_key = null;
		$sync_key = $flavorAsset->getSyncKey( flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET );

		if ( ! kFileSyncUtils::file_exists( $sync_key ) )
		{
			// if not found local file - perhaps wasn't created here and wasn't synced yet
			// try to see if remote exists - and proxy the request if it is.
			list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($sync_key, true, true);
			if(!$local)
			{
    			$remoteDCHost = kDataCenterMgr::getRemoteDcExternalUrl($fileSync);
				kFileUtils::dumpApiRequest($remoteDCHost);
			}
			
			KalturaLog::log("convertPptToSwf sync key doesn't exists");
			return; 
		}	
			
		$flavorParams = myConversionProfileUtils::getFlavorParamsFromFileFormat($dbEntry->getPartnerId(), flavorParams::CONTAINER_FORMAT_SWF);
		$flavorParamsId = $flavorParams->getId();
		$puserId = $this->getKuser()->getPuserId();
			
		$err = "";
		kBusinessPreConvertDL::decideAddEntryFlavor(null, $dbEntry->getId(), $flavorParamsId, $err);
		
		$downloadPath = $dbEntry->getDownloadUrl();
				
		//TODO: once api_v3 will support parameters with '/' instead of '?', we can change this to war with api_v3
		return $downloadPath.'/direct_serve/true/type/download/forceproxy/true/format/swf';
	}
	

	
	/**
	 * Serves the file content
	 * 
	 * @action serve
	 * @param string $entryId Document entry id
	 * @param string $flavorAssetId Flavor asset id
	 * @param bool $forceProxy force to get the content without redirect
	 * @return file
	 * @ksOptional
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::FLAVOR_ASSET_IS_NOT_READY
	 * @throws KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND
	 */
	public function serveAction($entryId, $flavorAssetId = null, $forceProxy = false)
	{
		KalturaResponseCacher::disableCache();
		
		myPartnerUtils::resetPartnerFilter('entry');
		$dbEntry = entryPeer::retrieveByPK($entryId);

		if (!$dbEntry || ($dbEntry->getType() != entryType::DOCUMENT))
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
		
		$ksObj = $this->getKs();
		$ks = ($ksObj) ? $ksObj->getOriginalString() : null;
		$securyEntryHelper = new KSecureEntryHelper($dbEntry, $ks, null, ContextType::DOWNLOAD);
		$securyEntryHelper->validateForDownload();	
					
		$flavorAsset = null;
		if($flavorAssetId)
		{
			$flavorAsset = assetPeer::retrieveById($flavorAssetId);
			if(!$flavorAsset)
				throw new KalturaAPIException(KalturaErrors::FLAVOR_ASSET_IS_NOT_READY, $flavorAssetId);
		}
		else
		{
			$flavorAsset = assetPeer::retrieveOriginalByEntryId($entryId);
			if(!$flavorAsset)
				throw new KalturaAPIException(KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND, $flavorAssetId);
		}
		
		if(!$securyEntryHelper->isAssetAllowed($flavorAsset))
			throw new KalturaAPIException(KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND, $flavorAssetId);
			
		$fileName = $dbEntry->getName() . '.' . $flavorAsset->getFileExt();
		
		return $this->serveFlavorAsset($flavorAsset, $fileName, $forceProxy);
	}
	
	
	/**
	 * Serves the file content
	 * 
	 * @action serveByFlavorParamsId
	 * @param string $entryId Document entry id
	 * @param string $flavorParamsId Flavor params id
	 * @param bool $forceProxy force to get the content without redirect
	 * @return file
	 * @ksOptional
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::FLAVOR_ASSET_IS_NOT_READY
	 * @throws KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND
	 */
	public function serveByFlavorParamsIdAction($entryId, $flavorParamsId = null, $forceProxy = false)
	{
		// temporary workaround for getting the referrer from a url with the format ....&forceProxy/true/referrer/...
		$referrer = null;
		if (isset($_GET["forceProxy"]) && kString::beginsWith($_GET["forceProxy"], "true/referrer/"))
		{
			$referrer = substr($_GET["forceProxy"], strlen("true/referrer/"));
			$referrer = base64_decode($referrer);
		}

		KalturaResponseCacher::disableCache();
		
		myPartnerUtils::resetPartnerFilter('entry');
		$dbEntry = entryPeer::retrieveByPK($entryId);

		if (!$dbEntry || ($dbEntry->getType() != entryType::DOCUMENT))
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
					
		$ksObj = $this->getKs();
		$ks = ($ksObj) ? $ksObj->getOriginalString() : null;
		$securyEntryHelper = new KSecureEntryHelper($dbEntry, $ks, $referrer, ContextType::DOWNLOAD);
		$securyEntryHelper->validateForDownload();			
			
		$flavorAsset = null;
		if($flavorParamsId)
		{
			$flavorAsset = assetPeer::retrieveByEntryIdAndParams($entryId, $flavorParamsId);
			if(!$flavorAsset)
				throw new KalturaAPIException(KalturaErrors::FLAVOR_ASSET_IS_NOT_READY, $flavorParamsId);
		}
		else
		{
			$flavorAsset = assetPeer::retrieveOriginalByEntryId($entryId);
			if(!$flavorAsset)
				throw new KalturaAPIException(KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND, $flavorParamsId);
		}
		
		if(!$securyEntryHelper->isAssetAllowed($flavorAsset))
			throw new KalturaAPIException(KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND, $flavorParamsId);
			
		$fileName = $dbEntry->getName() . '.' . $flavorAsset->getFileExt();
		
		return $this->serveFlavorAsset($flavorAsset, $fileName, $forceProxy);
	}
	
	
	/**
	 * Serves the file content
	 * 
	 * @action serve
	 * @param flavorAsset $flavorAsset
	 * @param string $fileName
	 * @param bool $forceProxy
	 * @return file
	 * 
	 * @throws KalturaErrors::FLAVOR_ASSET_IS_NOT_READY
	 */
	protected function serveFlavorAsset(flavorAsset $flavorAsset, $fileName, $forceProxy = false)
	{
		$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($syncKey, true, false);
		
		if(!$fileSync)
			throw new KalturaAPIException(KalturaErrors::FLAVOR_ASSET_IS_NOT_READY, $flavorAsset->getId());

		/* @var $fileSync FileSync */
		if ($fileSync->getFileExt() != assetParams::CONTAINER_FORMAT_SWF)	
		    header("Content-Disposition: attachment; filename=\"$fileName\"");
		
		if($local)
		{
			$filePath = $fileSync->getFullPath();
			$mimeType = kFile::mimeType($filePath);
			return $this->dumpFile($filePath, $mimeType, $fileSync->getKey());
		}
		else
		{
			$remoteUrl = kDataCenterMgr::getRedirectExternalUrl($fileSync);
			KalturaLog::info("Redirecting to [$remoteUrl]");
			if($forceProxy)
			{
				kFileUtils::dumpUrl($remoteUrl);
			}
			else
			{
				// or redirect if no proxy
				header("Location: $remoteUrl");
				die;
			}
		}	
	}
	
	/**
	 * Replace content associated with the given document entry.
	 *
	 * @action updateContent
	 * @param string $entryId document entry id to update
	 * @param KalturaResource $resource Resource to be used to replace entry doc content
	 * @param int $conversionProfileId The conversion profile id to be used on the entry
	 * @return KalturaDocumentEntry The updated doc entry
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::ENTRY_REPLACEMENT_ALREADY_EXISTS
     * @throws KalturaErrors::INVALID_OBJECT_ID
     * @validateUser entry entryId edit
	 */
	function updateContentAction($entryId, KalturaResource $resource, $conversionProfileId = null)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);

		if (!$dbEntry || $dbEntry->getType() != KalturaEntryType::DOCUMENT)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		$this->replaceResource($resource, $dbEntry, $conversionProfileId);
		
		return $this->getEntry($entryId);
	}
	
	/**
	 * @param KalturaResource $resource
	 * @param entry $dbEntry
	 * @param int $conversionProfileId
	 */
	protected function replaceResource(KalturaResource $resource, entry $dbEntry, $conversionProfileId = null)
	{
		if($dbEntry->getStatus() == KalturaEntryStatus::NO_CONTENT)
		{
			$resource->validateEntry($dbEntry);
	
			if($conversionProfileId)
			{
				$dbEntry->setConversionQuality($conversionProfileId);
				$dbEntry->save();
			}
	
			$kResource = $resource->toObject();
			$this->attachResource($kResource, $dbEntry);
		}
		else
		{
	
			$tempDocEntry = new KalturaDocumentEntry();
			$tempDocEntry->type = $dbEntry->getType();
			$tempDocEntry->mediaType = $dbEntry->getMediaType();
			$tempDocEntry->documentType = $dbEntry->getDocumentType();
			$tempDocEntry->conversionProfileId = $dbEntry->getConversionQuality();
	
			if($conversionProfileId)
				$tempDocEntry->conversionProfileId = $conversionProfileId;
	
			$this->replaceResourceByEntry($dbEntry, $resource, $tempDocEntry);
		}
		$resource->entryHandled($dbEntry);
	}
	
	/**
	 * Approves document replacement
	 *
	 * @action approveReplace
	 * @param string $entryId document entry id to replace
	 * @return KalturaDocumentEntry The replaced media entry
	 *
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	function approveReplaceAction($entryId)
	{
		return $this->approveReplace($entryId, KalturaEntryType::DOCUMENT);
	}
	
	/**
	 * Cancels document replacement
	 *
	 * @action cancelReplace
	 * @param string $entryId Document entry id to cancel
	 * @return KalturaDocumentEntry The canceled media entry
	 *
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	function cancelReplaceAction($entryId)
	{
		return $this->cancelReplace($entryId, KalturaEntryType::DOCUMENT);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaEntryService::prepareEntryForInsert()
	 */
	protected function prepareEntryForInsert(KalturaBaseEntry $entry, entry $dbEntry = null)
	{
		// first validate the input object
		//$entry->validatePropertyMinLength("name", 1);
		$entry->validatePropertyNotNull("documentType");
		
		$dbEntry = parent::prepareEntryForInsert($entry);
	
		if ($entry->conversionProfileId) 
		{
			$dbEntry->setStatus(entryStatus::PRECONVERT);
		}
		else 
		{
			$dbEntry->setStatus(entryStatus::READY);
		}
			
		$dbEntry->setDefaultModerationStatus();
		$dbEntry->save();
		
		return $dbEntry;
	}
}
