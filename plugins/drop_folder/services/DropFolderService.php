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
		
		if ($this->getPartnerId() !== Partner::ADMIN_CONSOLE_PARTNER_ID)
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
	 */
	public function addAction(KalturaDropFolder $dropFolder)
	{
		// check for required parameters
		$dropFolder->validatePropertyNotNull('name');
		$dropFolder->validatePropertyNotNull('status');
		$dropFolder->validatePropertyNotNull('type');
		$dropFolder->validatePropertyNotNull('dc');
		$dropFolder->validatePropertyNotNull('path');
		$dropFolder->validatePropertyMinValue('fileSizeCheckInterval', 0);
		$dropFolder->validatePropertyMinValue('autoFileDeleteDays', 0);
		
		self::errorIfDcNotExists($dropFolder->dc);
		
		DropFolderPeer::setUseCriteriaFilter(false);
		$existingDropFolder = DropFolderPeer::getByDcAndPath($dropFolder->dc, $dropFolder->path);
		DropFolderPeer::setUseCriteriaFilter(true);
		if ($existingDropFolder) {
			throw new KalturaAPIException(KalturaDropFolderErrors::DROP_FOLDER_ALREADY_EXISTS, $dropFolder->path, $dropFolder->dc);
		}
		
		// set default values where null		
		if (is_null($dropFolder->fileSizeCheckInterval)) {
			$dropFolder->fileSizeCheckInterval = DropFolder::FILE_SIZE_CHECK_INTERNAL_DEFAULT_VALUE;
		}
		
		if (is_null($dropFolder->unmatchedFilePolicy)) {
			$dropFolder->unmatchedFilePolicy = DropFolderUnmatchedFilesPolicy::ADD_AS_ENTRY;
		}
		
		if (is_null($dropFolder->fileDeletePolicy)) {
			$dropFolder->fileDeletePolicy = DropFolderFileDeletePolicy::MANUAL_DELETE;
		}
		
		if (is_null($dropFolder->autoFileDeleteDays)) {
			$dropFolder->autoFileDeleteDays = DropFolder::AUTO_FILE_DELETE_DAYS_DEFAULT_VALUE;
		}
		
		// validate values
		if (!is_null($dropFolder->ingestionProfileId)) {
			$conversionProfileDb = conversionProfile2Peer::retrieveByPK($dropFolder->ingestionProfileId);
			if (!$conversionProfileDb) {
				throw new KalturaAPIException(KalturaErrors::INGESTION_PROFILE_ID_NOT_FOUND, $dropFolder->ingestionProfileId);
			}
		}
		
		// save in database
		$dbDropFolder = $dropFolder->toInsertableObject();
		$dbDropFolder->setPartnerId($this->getPartnerId()); //TODO: ok ?
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
	 */	
	public function updateAction($dropFolderId, KalturaDropFolder $dropFolder)
	{
		$dbDropFolder = DropFolderPeer::retrieveByPK($dropFolderId);
		
		if (!$dbDropFolder) {
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $dropFolderId);
		}
		
		if (!is_null($dropFolder->dc)) {
			self::errorIfDcNotExists($dropFolder->dc);
		}
		
		if (!is_null($dropFolder->fileSizeCheckInterval)) {
			$dropFolder->validatePropertyMinValue('fileSizeCheckInterval', 0);
		}

		if (!is_null($dropFolder->autoFileDeleteDays)) {
			$dropFolder->validatePropertyMinValue('autoFileDeleteDays', 0);
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
	
	
	private static function errorIfDcNotExists($dcId)
	{
		try { $tempDc = kDataCenterMgr::getDcById($dcId); }
		catch (Exception $e) { $tempDc = null; }
		if (!$tempDc) {
			throw new KalturaAPIException(KalturaErrors::DATA_CENTER_ID_NOT_FOUND, $dcId);
		}
	}
	
}
