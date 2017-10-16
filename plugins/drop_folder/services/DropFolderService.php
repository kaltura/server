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
		
		if (!DropFolderPlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, DropFolderPlugin::PLUGIN_NAME);
			
		$this->applyPartnerFilterForClass('DropFolder');
		$this->applyPartnerFilterForClass('DropFolderFile');
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
		$dropFolder->validatePropertyMinValue('fileSizeCheckInterval', 0, true);
		$dropFolder->validatePropertyMinValue('autoFileDeleteDays', 0, true);
		$dropFolder->validatePropertyNotNull('fileHandlerType');
		$dropFolder->validatePropertyNotNull('fileHandlerConfig');
		
		// validate values
		
		if (is_null($dropFolder->fileSizeCheckInterval)) {
			$dropFolder->fileSizeCheckInterval = DropFolder::FILE_SIZE_CHECK_INTERVAL_DEFAULT_VALUE;
		}
		
		if (is_null($dropFolder->fileNamePatterns)) {
			$dropFolder->fileNamePatterns = DropFolder::FILE_NAME_PATTERNS_DEFAULT_VALUE;
		}
		
		if (!kDataCenterMgr::dcExists($dropFolder->dc)) {
			throw new KalturaAPIException(KalturaErrors::DATA_CENTER_ID_NOT_FOUND, $dropFolder->dc);
		}
		
		if (!PartnerPeer::retrieveByPK($dropFolder->partnerId)) {
			throw new KalturaAPIException(KalturaErrors::INVALID_PARTNER_ID, $dropFolder->partnerId);
		}
		
		if (!DropFolderPlugin::isAllowedPartner($dropFolder->partnerId))
		{
			throw new KalturaAPIException(KalturaErrors::PLUGIN_NOT_AVAILABLE_FOR_PARTNER, DropFolderPlugin::getPluginName(), $dropFolder->partnerId);
		}

		if($dropFolder->type == KalturaDropFolderType::LOCAL)
		{
			$existingDropFolder = DropFolderPeer::retrieveByPathDefaultFilter($dropFolder->path);
			if ($existingDropFolder) {
				throw new KalturaAPIException(KalturaDropFolderErrors::DROP_FOLDER_ALREADY_EXISTS, $dropFolder->path);
			}
		}
		
		if (!is_null($dropFolder->conversionProfileId)) {
			$conversionProfileDb = conversionProfile2Peer::retrieveByPK($dropFolder->conversionProfileId);
			if (!$conversionProfileDb) {
				throw new KalturaAPIException(KalturaErrors::INGESTION_PROFILE_ID_NOT_FOUND, $dropFolder->conversionProfileId);
			}
		}
		
		// save in database
		$dbDropFolder = $dropFolder->toInsertableObject();
		$dbDropFolder->save();
		
		// return the saved object
		$dropFolder = KalturaDropFolder::getInstanceByType($dbDropFolder->getType());
		$dropFolder->fromObject($dbDropFolder, $this->getResponseProfile());
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
			
		$dropFolder = KalturaDropFolder::getInstanceByType($dbDropFolder->getType());
		$dropFolder->fromObject($dbDropFolder, $this->getResponseProfile());
		
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
		
		if (!is_null($dropFolder->path) && $dropFolder->path != $dbDropFolder->getPath() && $dropFolder->type == KalturaDropFolderType::LOCAL) 
		{
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
		
		if (!is_null($dropFolder->conversionProfileId)) {
			$conversionProfileDb = conversionProfile2Peer::retrieveByPK($dropFolder->conversionProfileId);
			if (!$conversionProfileDb) {
				throw new KalturaAPIException(KalturaErrors::INGESTION_PROFILE_ID_NOT_FOUND, $dropFolder->conversionProfileId);
			}
		}

		$dbDropFolder = $dropFolder->toUpdatableObject($dbDropFolder);
		$dbDropFolder->save();
	
		$dropFolder = KalturaDropFolder::getInstanceByType($dbDropFolder->getType());
		$dropFolder->fromObject($dbDropFolder, $this->getResponseProfile());
		
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
			
		$dropFolder = KalturaDropFolder::getInstanceByType($dbDropFolder->getType());
		$dropFolder->fromObject($dbDropFolder, $this->getResponseProfile());
		
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
		
		if (! $pager)
			$pager = new KalturaFilterPager ();
		$pager->attachToCriteria ( $c );
		$list = DropFolderPeer::doSelect($c);
		
		$response = new KalturaDropFolderListResponse();
		$response->objects = KalturaDropFolderArray::fromDbArray($list, $this->getResponseProfile());
		$response->totalCount = $count;
		
		return $response;
	}

	/**
	 * getExclusive KalturaDropFolder object
	 *
	 * @action getExclusiveDropFolder
	 * @param string $tag
	 * @param int $maxTime
	 * @return KalturaDropFolder
	 */
	public function getExclusiveDropFolderAction($tag, $maxTime)
	{
		$allocateDropFolder = kDropFolderAllocator::getDropFolder($tag, $maxTime);
		if ($allocateDropFolder && self::isValidForWatch($allocateDropFolder))
		{
			$dropFolder = KalturaDropFolder::getInstanceByType($allocateDropFolder->getType());
			$dropFolder->fromObject($allocateDropFolder, $this->getResponseProfile());
			return $dropFolder;
		}
	}
 	
	/**
	 * freeExclusive KalturaDropFolder object
	 *
	 * @action freeExclusiveDropFolder
	 * @param int $dropFolderId
	 * @param string $errorCode
	 * @param string $errorDescription
	 * @throws KalturaAPIException
	 * @return KalturaDropFolder
	 */
	public function freeExclusiveDropFolderAction($dropFolderId, $errorCode = null, $errorDescription = null)
	{
		kDropFolderAllocator::freeDropFolder($dropFolderId);

		$dbDropFolder = DropFolderPeer::retrieveByPK($dropFolderId);
		if (!$dbDropFolder)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $dropFolderId);

		$dbDropFolder->setLastAccessedAt(time());
		$dbDropFolder->setErrorCode($errorCode);
		$dbDropFolder->setErrorDescription($errorDescription);
		$dbDropFolder->save();

		$dropFolder = KalturaDropFolder::getInstanceByType($dbDropFolder->getType());
		$dropFolder->fromObject($dbDropFolder, $this->getResponseProfile());

		return $dropFolder;
	}

	private static function isValidForWatch(DropFolder $dropFolder)
	{
		$partner = PartnerPeer::retrieveByPK($dropFolder->getPartnerId());
		if (!$partner || $partner->getStatus() != Partner::PARTNER_STATUS_ACTIVE
			|| !$partner->getPluginEnabled(DropFolderPlugin::PLUGIN_NAME))
			return false;

		return true;
	}
	
}
