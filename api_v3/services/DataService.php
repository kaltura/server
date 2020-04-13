<?php

/**
 * Data service lets you manage data content (textual content)
 *
 * @service data
 * @package api
 * @subpackage services
 */
class DataService extends KalturaEntryService
{
	
	protected function kalturaNetworkAllowed($actionName)
	{
		if ($actionName === 'get') {
			return true;
		}
		return parent::kalturaNetworkAllowed($actionName);
	}
	
	
	/**
	 * Adds a new data entry
	 * 
	 * @action add
	 * @param KalturaDataEntry $dataEntry Data entry
	 * @return KalturaDataEntry The new data entry
	 */
	function addAction(KalturaDataEntry $dataEntry)
	{
		$dbEntry = $dataEntry->toObject(new entry());
		
		$this->checkAndSetValidUserInsert($dataEntry, $dbEntry);
		$this->checkAdminOnlyInsertProperties($dataEntry);
		$this->validateAccessControlId($dataEntry);
		$this->validateEntryScheduleDates($dataEntry, $dbEntry);
		
		$dbEntry->setPartnerId($this->getPartnerId());
		$dbEntry->setSubpId($this->getPartnerId() * 100);
		$dbEntry->setStatus(KalturaEntryStatus::READY);
		$dbEntry->setMediaType(entry::ENTRY_MEDIA_TYPE_AUTOMATIC); 
		$dbEntry->save();
		
		$trackEntry = new TrackEntry();
		$trackEntry->setEntryId($dbEntry->getId());
		$trackEntry->setTrackEventTypeId(TrackEntry::TRACK_ENTRY_EVENT_TYPE_ADD_ENTRY);
		$trackEntry->setDescription(__METHOD__ . ":" . __LINE__ . "::ENTRY_DATA");
		TrackEntry::addTrackEntry($trackEntry);
		
		$dataEntry->fromObject($dbEntry, $this->getResponseProfile());
		
		myNotificationMgr::createNotification(kNotificationJobData::NOTIFICATION_TYPE_ENTRY_ADD, $dbEntry);
		
		return $dataEntry;
	}
	
	/**
	 * Get data entry by ID.
	 * 
	 * @action get
	 * @param string $entryId Data entry id
	 * @param int $version Desired version of the data
	 * @return KalturaDataEntry The requested data entry
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	function getAction($entryId, $version = -1)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);

		if (!$dbEntry || $dbEntry->getType() != KalturaEntryType::DATA)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		if ($version !== -1)
			$dbEntry->setDesiredVersion($version);
			
		$dataEntry = new KalturaDataEntry();
		$dataEntry->fromObject($dbEntry, $this->getResponseProfile());

		return $dataEntry;
	}
	
	/**
	 * Update data entry. Only the properties that were set will be updated.
	 * 
	 * @action update
	 * @param string $entryId Data entry id to update
	 * @param KalturaDataEntry $documentEntry Data entry metadata to update
	 * @return KalturaDataEntry The updated data entry
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * validateUser entry $entryId edit
	 */
	function updateAction($entryId, KalturaDataEntry $documentEntry)
	{
		return $this->updateEntry($entryId, $documentEntry, KalturaEntryType::DATA);
	}
	
	/**
	 * Delete a data entry.
	 *
	 * @action delete
	 * @param string $entryId Data entry id to delete
	 * 
 	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
 	 * @validateUser entry entryId edit
	 */
	function deleteAction($entryId)
	{
		$this->deleteEntry($entryId, KalturaEntryType::DATA);
	}
	
	/**
	 * List data entries by filter with paging support.
	 * 
	 * @action list
     * @param KalturaDataEntryFilter $filter Document entry filter
	 * @param KalturaFilterPager $pager Pager
	 * @return KalturaDataListResponse Wrapper for array of document entries and total count
	 */
	function listAction(KalturaDataEntryFilter $filter = null, KalturaFilterPager $pager = null)
	{
	    if (!$filter)
			$filter = new KalturaDataEntryFilter();
			
	    $filter->typeEqual = KalturaEntryType::DATA;
	    list($list, $totalCount) = parent::listEntriesByFilter($filter, $pager);
	    
	    $newList = KalturaDataEntryArray::fromDbArray($list, $this->getResponseProfile());
		$response = new KalturaDataListResponse();
		$response->objects = $newList;
		$response->totalCount = $totalCount;
		return $response;
	}
	
	/**
	 * return the file from dataContent field.
	 * 
	 * @action serve
	 * @param string $entryId Data entry id
	 * @param int $version Desired version of the data
	 * @param bool $forceProxy force to get the content without redirect
	 * @return file
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	function serveAction($entryId, $version = -1, $forceProxy = false)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);

		if (!$dbEntry || $dbEntry->getType() != KalturaEntryType::DATA)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		$ksObj = $this->getKs();
		$ks = ($ksObj) ? $ksObj->getOriginalString() : null;
		$securyEntryHelper = new KSecureEntryHelper($dbEntry, $ks, null, ContextType::DOWNLOAD);
		$securyEntryHelper->validateForDownload();	
		
		if ( ! $version || $version == -1 ) $version = null;
		
		$fileName = $dbEntry->getName();
		
		$syncKey = $dbEntry->getSyncKey( kEntryFileSyncSubType::DATA , $version);
		list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($syncKey, true, false);
		
		header("Content-Disposition: attachment; filename=\"$fileName\"");

		if($local)
		{
			$filePath = $fileSync->getFullPath();
			$mimeType = kFile::mimeType($filePath);
			$key = $fileSync->isEncrypted() ? $fileSync->getEncryptionKey() : null;
			$iv = $key ? $fileSync->getIv() : null;
			return $this->dumpFile($filePath, $mimeType, $key, $iv);
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
	* Update the dataContent of data entry using a resource
	*
	* @action addContent
	* @param string $entryId
	* @param KalturaGenericDataCenterContentResource $resource
	* @return string
	* @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	* @validateUser entry entryId edit
	*/
	function addContentAction($entryId, KalturaGenericDataCenterContentResource $resource)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);

		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		if ($dbEntry->getType() != KalturaEntryType::DATA)
			throw new KalturaAPIException(KalturaErrors::INVALID_ENTRY_TYPE,$entryId, $dbEntry->getType(), entryType::DATA);

		$resource->validateEntry($dbEntry);
		$kResource = $resource->toObject();
		$this->attachResource($kResource, $dbEntry);
		$resource->entryHandled($dbEntry);

		return $this->getEntry($entryId);
	}

	/**
	* @param kResource $resource
	* @param entry $dbEntry
	* @param asset $dbAsset
	* @return asset
	*/
	protected function attachResource(kResource $resource, entry $dbEntry, asset $dbAsset = null)
	{
		if(($resource->getType() == 'kLocalFileResource') && (!isset($resource->getSourceType) ||  $resource->getSourceType != KalturaSourceType::WEBCAM))
		{
			$file_path = $resource->getLocalFilePath();
			$fileType = kFile::mimeType($file_path);
			if((substr($fileType, 0, 5) == 'text/') || ($fileType == 'application/xml')) {
				$dbEntry->setDataContent(kFile::getFileContent($file_path));
			}
			else{
				KalturaLog::err("Resource of type [" . get_class($resource) . "] with file type ". $fileType. " is not supported");
				throw new KalturaAPIException(KalturaErrors::FILE_TYPE_NOT_SUPPORTED, $fileType);
			}
		}
		else
		{
			KalturaLog::err("Resource of type [" . get_class($resource) . "] is not supported");
			throw new KalturaAPIException(KalturaErrors::RESOURCE_TYPE_NOT_SUPPORTED, get_class($resource));
		}
	}
}
