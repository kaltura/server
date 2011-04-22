<?php

/**
 * DropFolder service lets you create and manage drop folders
 * @service dropFolder
 * @package plugins.dropFolder
 * @subpackage api.services
 */
class DropFolderService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		
		if (!in_array($this->getPartnerId(), array(Partner::ADMIN_CONSOLE_PARTNER_ID, Partner::BATCH_PARTNER_ID)))
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN, $this->serviceName.'->'.$this->actionName);
			
		myPartnerUtils::addPartnerToCriteria(new DropFolderPeer(), $this->getPartnerId(), $this->private_partner_data, $this->partnerGroup());
		myPartnerUtils::addPartnerToCriteria(new DropFolderFilePeer(), $this->getPartnerId(), $this->private_partner_data, $this->partnerGroup());
	}
		
	
	
	/**
	 * Allows you to add a new KalturaDropFolder object
	 * 
	 * @action add
	 * @param KalturaDropFolder $dropFolder
	 * @return KalturaDropFolder
	 * 
	 * @throws KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL
	 * @throws KalturaErrors::INGESTION_PROFILE_ID_NOT_FOUND
	 * @throws KalturaDropFolderErrors::DROP_FOLDER_ALREADY_EXISTS
	 * @throws KalturaErrors::DATA_CENTER_ID_NOT_FOUND
	 */
	public function addAction(KalturaDropFolder $dropFolder)
	{
		// check for required parameters
		$dropFolder->validatePropertyNotNull('name');
		$dropFolder->validatePropertyNotNull('status');
		$dropFolder->validatePropertyNotNull('type');
		$dropFolder->validatePropertyNotNull('dc');
		$dropFolder->validatePropertyNotNull('path');
		$dropFolder->validatePropertyNotNull('partnerId');
		$dropFolder->validatePropertyMinValue('fileSizeCheckInterval', 0);
		$dropFolder->validatePropertyNotNull('fileHandlerType');
		$dropFolder->validatePropertyNotNull('fileNamePatterns');
		$dropFolder->validatePropertyNotNull('fileHandlerConfig');
		
		// validate values
		
		if (!kDataCenterMgr::dcExists($dropFolder->dc)) {
			throw new KalturaAPIException(KalturaErrors::DATA_CENTER_ID_NOT_FOUND, $dropFolder->dc);
		}
				
		$existingDropFolder = DropFolderPeer::retrieveByPathDefaultFilter($dropFolder->path);
		if ($existingDropFolder) {
			throw new KalturaAPIException(KalturaDropFolderErrors::DROP_FOLDER_ALREADY_EXISTS, $dropFolder->path);
		}
		
		
		if (!is_null($dropFolder->ingestionProfileId)) {
			$conversionProfileDb = conversionProfile2Peer::retrieveByPK($dropFolder->ingestionProfileId);
			if (!$conversionProfileDb) {
				throw new KalturaAPIException(KalturaErrors::INGESTION_PROFILE_ID_NOT_FOUND, $dropFolder->ingestionProfileId);
			}
		}
		
		// save in database
		$dbDropFolder = $dropFolder->toInsertableObject();
		$dbDropFolder->save();
		
		// return the saved object
		$dropFolder = new KalturaDropFolder();
		$dropFolder->fromObject($dbDropFolder);
		return $dropFolder;
		
	}
	
	/**
	 * Retrieve a KalturaDropFolder object by ID
	 * 
	 * @action get
	 * @param int $dropFolderId 
	 * @return KalturaDropFolder
	 * 
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */		
	public function getAction($dropFolderId)
	{
		$dbDropFolder = DropFolderPeer::retrieveByPK($dropFolderId);
		
		if (!$dbDropFolder) {
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $dropFolderId);
		}
			
		$dropFolder = new KalturaDropFolder();
		$dropFolder->fromObject($dbDropFolder);
		
		return $dropFolder;
	}
	

	/**
	 * Update an existing KalturaDropFolder object
	 * 
	 * @action update
	 * @param int $dropFolderId
	 * @param KalturaDropFolder $dropFolder
	 * @return KalturaDropFolder
	 *
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 * @throws KalturaErrors::INGESTION_PROFILE_ID_NOT_FOUND
	 * @throws KalturaErrors::DATA_CENTER_ID_NOT_FOUND
	 */	
	public function updateAction($dropFolderId, KalturaDropFolder $dropFolder)
	{
		$dbDropFolder = DropFolderPeer::retrieveByPK($dropFolderId);
		
		if (!$dbDropFolder) {
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $dropFolderId);
		}
		
		$dropFolder->validatePropertyMinValue('fileSizeCheckInterval', 0, true);
		$dropFolder->validatePropertyMinValue('autoFileDeleteDays', 0, true);
		
		if (!is_null($dropFolder->path)) {
			$existingDropFolder = DropFolderPeer::retrieveByPathDefaultFilter($dropFolder->path);
			if ($existingDropFolder) {
				throw new KalturaAPIException(KalturaDropFolderErrors::DROP_FOLDER_ALREADY_EXISTS, $dropFolder->path);
			}
		}
		
		if (!is_null($dropFolder->dc)) {
			if (!kDataCenterMgr::dcExists($dropFolder->dc)) {
				throw new KalturaAPIException(KalturaErrors::DATA_CENTER_ID_NOT_FOUND, $dropFolder->dc);
			}
		}
		
		if (!is_null($dropFolder->ingestionProfileId)) {
			$conversionProfileDb = conversionProfile2Peer::retrieveByPK($dropFolder->ingestionProfileId);
			if (!$conversionProfileDb) {
				throw new KalturaAPIException(KalturaErrors::INGESTION_PROFILE_ID_NOT_FOUND, $dropFolder->ingestionProfileId);
			}
		}
					
		$dbDropFolder = $dropFolder->toUpdatableObject($dbDropFolder);
		$dbDropFolder->save();
	
		$dropFolder = new KalturaDropFolder();
		$dropFolder->fromObject($dbDropFolder);
		
		return $dropFolder;
	}

	/**
	 * Mark the KalturaDropFolder object as deleted
	 * 
	 * @action delete
	 * @param int $dropFolderId 
	 * @return KalturaDropFolder
	 *
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */		
	public function deleteAction($dropFolderId)
	{
		$dbDropFolder = DropFolderPeer::retrieveByPK($dropFolderId);
		
		if (!$dbDropFolder) {
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $dropFolderId);
		}

		$dbDropFolder->setStatus(DropFolderStatus::DELETED);
		$dbDropFolder->save();
			
		$dropFolder = new KalturaDropFolder();
		$dropFolder->fromObject($dbDropFolder);
		
		return $dropFolder;
	}
	
	/**
	 * List KalturaDropFolder objects
	 * 
	 * @action list
	 * @param KalturaDropFolderFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaDropFolderListResponse
	 */
	public function listAction(KalturaDropFolderFilter  $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaDropFolderFilter();
			
		$dropFolderFilter = $filter->toObject();

		$c = new Criteria();
		$dropFolderFilter->attachToCriteria($c);
		$count = DropFolderPeer::doCount($c);
		
		if ($pager)
			$pager->attachToCriteria($c);
		$list = DropFolderPeer::doSelect($c);
		
		$response = new KalturaDropFolderListResponse();
		$response->objects = KalturaDropFolderArray::fromDbArray($list);
		$response->totalCount = $count;
		
		return $response;
	}
	
}
