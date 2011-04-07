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
				kFile::dumpApiRequest($remoteDCHost);
			}
			else
			{
				throw new KalturaAPIException(KalturaErrors::UPLOADED_FILE_NOT_FOUND_BY_TOKEN);
			}
		}
			
		$dbEntry = $this->prepareEntryForInsert($documentEntry);
		$dbEntry->setSource(KalturaSourceType::FILE);
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

		$documentEntry->fromObject($dbEntry);
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
		assetPeer::resetInstanceCriteriaFilter();
		if(is_null($sourceFlavorParamsId))
		{
			$srcFlavorAsset = assetPeer::retreiveOriginalByEntryId($sourceEntryId);
			if(!$srcFlavorAsset)
				throw new KalturaAPIException(KalturaErrors::ORIGINAL_FLAVOR_ASSET_IS_MISSING);
		}
		else
		{
			$srcFlavorAssets = assetPeer::retreiveReadyByEntryIdAndFlavorParams($sourceEntryId, array($sourceFlavorParamsId));
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
		assetPeer::resetInstanceCriteriaFilter();
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
		$docEntry->fromObject($dbEntry);

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
	    
	    $newList = KalturaDocumentEntryArray::fromEntryArray($list);
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

		assetPeer::resetInstanceCriteriaFilter();
		$flavorAsset = assetPeer::retrieveOriginalByEntryId($entryId);
		if (is_null($flavorAsset) || $flavorAsset->getStatus() != flavorAsset::FLAVOR_ASSET_STATUS_READY)
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
				// take input params and add to URL
				$queryArr = array(
					'service' => 'document',
					'action' => 'convertPptToSwf',
					'entryId' => $entryId,
					'format' => $this->params["format"],
					'ks' => $this->getKs()->toSecureString()
				);
				$get_query = http_build_query($queryArr, '', '&');

				$remote_url = kDataCenterMgr::getRedirectExternalUrl ( $fileSync , $_SERVER['REQUEST_URI'] );
				$url = (strpos($remote_url, '?') === FALSE)? $remote_url.'?'.$get_query: $remote_url.'&'.$get_query;
				// prxoy request to other DC
				KalturaLog::log ( __METHOD__ . ": redirecting to [$url]" );
				kFile::dumpUrl($url);
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
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::FLAVOR_ASSET_IS_NOT_READY
	 * @throws KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND
	 */
	public function serveAction($entryId, $flavorAssetId = null, $forceProxy = false)
	{
		KalturaResponseCacher::disableCache();
		
		entryPeer::setDefaultCriteriaFilter();
		$dbEntry = entryPeer::retrieveByPK($entryId);

		if (!$dbEntry || ($dbEntry->getType() != entryType::DOCUMENT))
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
		
		$ksObj = $this->getKs();
		$ks = ($ksObj) ? $ksObj->getOriginalString() : null;
		$securyEntryHelper = new KSecureEntryHelper($dbEntry, $ks, null);
		$securyEntryHelper->validateForDownload();	
					
		$flavorAsset = null;
		assetPeer::resetInstanceCriteriaFilter();
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
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::FLAVOR_ASSET_IS_NOT_READY
	 * @throws KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND
	 */
	public function serveByFlavorParamsIdAction($entryId, $flavorParamsId = null, $forceProxy = false)
	{
		KalturaResponseCacher::disableCache();
		
		entryPeer::setDefaultCriteriaFilter();
		$dbEntry = entryPeer::retrieveByPK($entryId);

		if (!$dbEntry || ($dbEntry->getType() != entryType::DOCUMENT))
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
					
		$ksObj = $this->getKs();
		$ks = ($ksObj) ? $ksObj->getOriginalString() : null;
		$securyEntryHelper = new KSecureEntryHelper($dbEntry, $ks, null);
		$securyEntryHelper->validateForDownload();			
			
		$flavorAsset = null;
		assetPeer::resetInstanceCriteriaFilter();
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
