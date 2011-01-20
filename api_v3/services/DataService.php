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
		
		$this->checkAndSetValidUser($dataEntry, $dbEntry);
		$this->checkAdminOnlyInsertProperties($dataEntry);
		$this->validateAccessControlId($dataEntry);
		$this->validateEntryScheduleDates($dataEntry);
		
		$dbEntry->setPartnerId($this->getPartnerId());
		$dbEntry->setSubpId($this->getPartnerId() * 100);
		$dbEntry->setStatus(KalturaEntryStatus::READY);
		$dbEntry->setMediaType(entry::ENTRY_MEDIA_TYPE_AUTOMATIC); 
		$dbEntry->save();
		
		$dataEntry->fromObject($dbEntry);
		
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
		$dataEntry->fromObject($dbEntry);

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
	    
	    $newList = KalturaDataEntryArray::fromEntryArray($list);
		$response = new KalturaDataListResponse();
		$response->objects = $newList;
		$response->totalCount = $totalCount;
		return $response;
	}
	
	/**
	 * serve action returan the file from dataContent field.
	 * 
	 * @action serve
	 * @param string $entryId Data entry id
	 * @param int $version Desired version of the data
	 * @param bool $forceProxy force to get the content without redirect
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
		$securyEntryHelper = new KSecureEntryHelper($dbEntry, $ks, null);
		$securyEntryHelper->validateForDownload();	
		
		if ( ! $version || $version == -1 ) $version = null;
		
		$fileName = $dbEntry->getName();
		
		$syncKey = $dbEntry->getSyncKey( entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA , $version);
		list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($syncKey, true, false);
		
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
}
