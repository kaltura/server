<?php

/**
 * DropFolderFile service lets you create and manage drop folder files
 * @service dropFolderFile
 * @package plugins.dropFolder
 * @subpackage api.services
 */
class DropFolderFileService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		
		if (!DropFolderPlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN, $this->serviceName.'->'.$this->actionName);
		
		myPartnerUtils::addPartnerToCriteria(new DropFolderPeer(), $this->getPartnerId(), $this->private_partner_data, $this->partnerGroup());
		myPartnerUtils::addPartnerToCriteria(new DropFolderFilePeer(), $this->getPartnerId(), $this->private_partner_data, $this->partnerGroup());
	}
		
	/**
	 * Allows you to add a new KalturaDropFolderFile object
	 * 
	 * @action add
	 * @param KalturaDropFolderFile $dropFolderFile
	 * @return KalturaDropFolderFile
	 * 
	 * @throws KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL
	 * @throws KalturaDropFolderErrors::DROP_FOLDER_NOT_FOUND
	 * @throws KalturaDropFolderErrors::DROP_FOLDER_FILE_ALREADY_EXISTS
	 */
	public function addAction(KalturaDropFolderFile $dropFolderFile)
	{
		// check for required parameters
		$dropFolderFile->validatePropertyNotNull('dropFolderId');
		$dropFolderFile->validatePropertyNotNull('fileName');
		$dropFolderFile->validatePropertyMinValue('fileSize', 0);
		
		// check that drop folder id exists in the system
		$dropFolder = DropFolderPeer::retrieveByPK($dropFolderFile->dropFolderId);
		if (!$dropFolder) {
			throw new KalturaAPIException(KalturaDropFolderErrors::DROP_FOLDER_NOT_FOUND, $dropFolderFile->dropFolderId);
		}
				
		// check that the file doesn't already exist in the drop folder
		if (DropFolderFilePeer::retrieveByDropFolderIdAndFileName($dropFolderFile->dropFolderId, $dropFolderFile->fileName)) {
			throw new KalturaAPIException(KalturaDropFolderErrors::DROP_FOLDER_FILE_ALREADY_EXISTS, $dropFolderFile->dropFolderId, $dropFolderFile->fileName);
		}
		
		// save in database
		$dbDropFolderFile = $dropFolderFile->toInsertableObject();
		$dbDropFolderFile->setPartnerId($dropFolder->getPartnerId());
		$dbDropFolderFile->save();
		
		// return the saved object
		$dropFolderFile = new KalturaDropFolderFile();
		$dropFolderFile->fromObject($dbDropFolderFile);
		return $dropFolderFile;
	}
	
	/**
	 * Retrieve a KalturaDropFolderFile object by ID
	 * 
	 * @action get
	 * @param int $dropFolderFileId 
	 * @return KalturaDropFolderFile
	 * 
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */		
	public function getAction($dropFolderFileId)
	{
		$dbDropFolderFile = DropFolderFilePeer::retrieveByPK($dropFolderFileId);
		
		if (!$dbDropFolderFile) {
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $dropFolderFileId);
		}
			
		$dropFolderFile = new KalturaDropFolderFile();
		$dropFolderFile->fromObject($dbDropFolderFile);
		
		return $dropFolderFile;
	}
	

	/**
	 * Update an existing KalturaDropFolderFile object
	 * 
	 * @action update
	 * @param int $dropFolderFileId
	 * @param KalturaDropFolderFile $dropFolderFile
	 * @return KalturaDropFolderFile
	 *
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */	
	public function updateAction($dropFolderFileId, KalturaDropFolderFile $dropFolderFile)
	{
		$dbDropFolderFile = DropFolderFilePeer::retrieveByPK($dropFolderFileId);
		
		if (!$dbDropFolderFile) {
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $dropFolderFileId);
		}
		
		if (!is_null($dropFolderFile->fileSize)) {
			$dropFolderFile->validatePropertyMinValue('fileSize', 0);
		}
					
		$dbDropFolderFile = $dropFolderFile->toUpdatableObject($dbDropFolderFile);
		$dbDropFolderFile->save();
	
		$dropFolderFile = new KalturaDropFolderFile();
		$dropFolderFile->fromObject($dbDropFolderFile);
		
		return $dropFolderFile;
	}

	/**
	 * Mark the KalturaDropFolderFile object as deleted
	 * 
	 * @action delete
	 * @param int $dropFolderFileId 
	 * @return KalturaDropFolderFile
	 *
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */		
	public function deleteAction($dropFolderFileId)
	{
		$dbDropFolderFile = DropFolderFilePeer::retrieveByPK($dropFolderFileId);
		
		if (!$dbDropFolderFile) {
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $dropFolderFileId);
		}
		
		$dbDropFolderFile->setStatus(DropFolderFileStatus::DELETED);
		$dbDropFolderFile->save();
			
		$dropFolderFile = new KalturaDropFolderFile();
		$dropFolderFile->fromObject($dbDropFolderFile);
		
		return $dropFolderFile;
	}
	
	/**
	 * List KalturaDropFolderFile objects
	 * 
	 * @action list
	 * @param KalturaDropFolderFileFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaDropFolderFileListResponse
	 */
	public function listAction(KalturaDropFolderFileFilter  $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaDropFolderFileFilter();
			
		$dropFolderFileFilter = $filter->toObject();

		$c = new Criteria();
		$dropFolderFileFilter->attachToCriteria($c);		
		$count = DropFolderFilePeer::doCount($c);
		
		if ($pager)
			$pager->attachToCriteria($c);
		$list = DropFolderFilePeer::doSelect($c);
		
		$response = new KalturaDropFolderFileListResponse();
		$response->objects = KalturaDropFolderFileArray::fromDbArray($list);
		$response->totalCount = $count;
		
		return $response;
	}
	
	
	/**
	 * Set the KalturaDropFolderFile status to ignore (KalturaDropFolderFileStatus::IGNORE)
	 * 
	 * @action ignore
	 * @param int $dropFolderFileId 
	 * @return KalturaDropFolderFile
	 *
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */		
	public function ignoreAction($dropFolderFileId)
	{
		$dbDropFolderFile = DropFolderFilePeer::retrieveByPK($dropFolderFileId);
		
		if (!$dbDropFolderFile) {
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $dropFolderFileId);
		}

		$dbDropFolderFile->setStatus(DropFolderFileStatus::IGNORE);
		$dbDropFolderFile->save();
			
		$dropFolderFile = new KalturaDropFolderFile();
		$dropFolderFile->fromObject($dbDropFolderFile);
		
		return $dropFolderFile;
	}
	
		
}
