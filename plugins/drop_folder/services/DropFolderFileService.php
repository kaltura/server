<?php

/**
 * DropFolderFile service lets you create and manage drop folder files
 * @service dropFolderFile
 * @package plugins.dropFolder
 * @subpackage api.services
 */
class DropFolderFileService extends KalturaBaseService
{
	const MYSQL_CODE_DUPLICATE_KEY = 23000;
	
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		
		if (!DropFolderPlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, DropFolderPlugin::PLUGIN_NAME);
		
		$this->applyPartnerFilterForClass('DropFolder');
		$this->applyPartnerFilterForClass('DropFolderFile');
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
	 */
	public function addAction(KalturaDropFolderFile $dropFolderFile)
	{
		return $this->newFileAddedOrDetected($dropFolderFile, DropFolderFileStatus::UPLOADING);
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
			
		$dropFolderFile = KalturaDropFolderFile::getInstanceByType($dbDropFolderFile->getType());
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
	 * Update status of KalturaDropFolderFile
	 * 
	 * @action updateStatus
	 * @param int $dropFolderFileId
	 * @param KalturaDropFolderFileStatus $status
	 * @return KalturaDropFolderFile
	 *
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */	
	public function updateStatusAction($dropFolderFileId, $status)
	{
		$dbDropFolderFile = DropFolderFilePeer::retrieveByPK($dropFolderFileId);
		if (!$dbDropFolderFile)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $dropFolderFileId);
			
		if ($status != KalturaDropFolderFileStatus::PURGED && $dbDropFolderFile->getStatus() == KalturaDropFolderFileStatus::DELETED)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $dropFolderFileId);
		
		$dbDropFolderFile->setStatus($status);
		$dbDropFolderFile->save();
	
		$dropFolderFile = KalturaDropFolderFile::getInstanceByType($dbDropFolderFile->getType());
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
			
		$dropFolderFile = KalturaDropFolderFile::getInstanceByType($dbDropFolderFile->getType());
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
		
		if (! $pager)
			$pager = new KalturaFilterPager ();
		$pager->attachToCriteria ( $c );
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
	
	private function newFileAddedOrDetected(KalturaDropFolderFile $dropFolderFile, $fileStatus)
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
				
		// save in database
		$dropFolderFile->status = null;		
		$dbDropFolderFile = $dropFolderFile->toInsertableObject();
		/* @var $dbDropFolderFile DropFolderFile  */
		$dbDropFolderFile->setPartnerId($dropFolder->getPartnerId());
		$dbDropFolderFile->setStatus($fileStatus);
		$dbDropFolderFile->setType($dropFolder->getType());
		try 
		{
			$dbDropFolderFile->save();	
		}
		catch(PropelException $e)
		{
			if($e->getCause() && $e->getCause()->getCode() == self::MYSQL_CODE_DUPLICATE_KEY) //unique constraint
			{
				$existingDropFolderFile = DropFolderFilePeer::retrieveByDropFolderIdAndFileName($dropFolderFile->dropFolderId, $dropFolderFile->fileName);
				KalturaLog::debug('drop folder file exists ['.$existingDropFolderFile->getId().']');
				switch($existingDropFolderFile->getStatus())
				{					
					case DropFolderFileStatus::PARSED:
						KalturaLog::debug('Exisiting file status is PARSED, updating status to ['.$fileStatus.']');
						$existingDropFolderFile = $dropFolderFile->toUpdatableObject($existingDropFolderFile);
						$existingDropFolderFile->setStatus($fileStatus);						
						$existingDropFolderFile->save();
						$dbDropFolderFile = $existingDropFolderFile;
						break;
					case DropFolderFileStatus::DETECTED:
						KalturaLog::debug('Exisiting file status is DETECTED, updating status to ['.$fileStatus.']');
						$existingDropFolderFile = $dropFolderFile->toUpdatableObject($existingDropFolderFile);
						if($existingDropFolderFile->getStatus() != $fileStatus)
							$existingDropFolderFile->setStatus($fileStatus);
						$existingDropFolderFile->save();
						$dbDropFolderFile = $existingDropFolderFile;
						break;
					case DropFolderFileStatus::UPLOADING:
						if($fileStatus == DropFolderFileStatus::UPLOADING)
						{
							KalturaLog::debug('Exisiting file status is UPLOADING, updating properties');
							$existingDropFolderFile = $dropFolderFile->toUpdatableObject($existingDropFolderFile);
							$existingDropFolderFile->save();
							$dbDropFolderFile = $existingDropFolderFile;
							break;							
						}
					default:
						KalturaLog::debug('Setting current file to PURGED ['.$existingDropFolderFile->getId().']');
						$existingDropFolderFile->setStatus(DropFolderFileStatus::PURGED);				
						$existingDropFolderFile->save();
						
						$newDropFolderFile = $dbDropFolderFile->copy();
						if(	$existingDropFolderFile->getLeadDropFolderFileId() && 
							$existingDropFolderFile->getLeadDropFolderFileId() != $existingDropFolderFile->getId())
						{
							KalturaLog::debug('Updating lead id ['.$existingDropFolderFile->getLeadDropFolderFileId().']');							
							$newDropFolderFile->setLeadDropFolderFileId($existingDropFolderFile->getLeadDropFolderFileId());	
						}
						KalturaLog::debug('Creating new drop folder file');
						$newDropFolderFile->save();
						$dbDropFolderFile = $newDropFolderFile;
				}
			}
			else 
			{
				throw $e;
			}
		}	
		// return the saved object
		$dropFolderFile = KalturaDropFolderFile::getInstanceByType($dbDropFolderFile->getType());
		$dropFolderFile->fromObject($dbDropFolderFile);
		return $dropFolderFile;		
		
	}
	
}
