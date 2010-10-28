<?php
/**
 * Metadata Profile service
 *
 * @service metadataProfile
 */
class MetadataProfileService extends KalturaBaseService
{
	public function initService($partnerId, $puserId, $ksStr, $serviceName, $action)
	{
		parent::initService($partnerId, $puserId, $ksStr, $serviceName, $action);

		myPartnerUtils::addPartnerToCriteria(new MetadataProfilePeer(), $this->getPartnerId(), $this->private_partner_data, $this->partnerGroup());
		myPartnerUtils::addPartnerToCriteria(new MetadataPeer(), $this->getPartnerId(), $this->private_partner_data, $this->partnerGroup());
		myPartnerUtils::addPartnerToCriteria(new entryPeer(), $this->getPartnerId(), $this->private_partner_data, $this->partnerGroup());
//		myPartnerUtils::addPartnerToCriteria(new FileSyncPeer(), $this->getPartnerId(), $this->private_partner_data, $this->partnerGroup());
		
		if(!MetadataPlugin::isAllowedPartner(kCurrentContext::$ks_partner_id))
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN);
	}
	
	
	/**
	 * List metadata profile objects by filter and pager
	 * 
	 * @action list
	 * @param KalturaMetadataProfileFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaMetadataProfileListResponse
	 */
	function listAction(KalturaMetadataProfileFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaMetadataProfileFilter;
			
		$metadataProfileFilter = new MetadataProfileFilter();
		$filter->toObject($metadataProfileFilter);
		
		$c = new Criteria();
		$metadataProfileFilter->attachToCriteria($c);
		$count = MetadataProfilePeer::doCount($c);
		
		if ($pager)
			$pager->attachToCriteria($c);
		$list = MetadataProfilePeer::doSelect($c);
		
		$response = new KalturaMetadataProfileListResponse();
		$response->objects = KalturaMetadataProfileArray::fromMetadataProfileArray($list);
		$response->totalCount = $count;
		
		return $response;
	}
	
	
	/**
	 * List metadata profile fields by metadata profile id
	 * 
	 * @action listFields
	 * @param int $metadataProfileId
	 * @return KalturaMetadataProfileFieldListResponse
	 */
	function listFieldsAction($metadataProfileId)
	{
		$dbFields = MetadataProfileFieldPeer::retrieveActiveByMetadataProfileId($metadataProfileId);
		
		$response = new KalturaMetadataProfileFieldListResponse();
		$response->objects = KalturaMetadataProfileFieldArray::fromMetadataProfileFieldArray($dbFields);
		$response->totalCount = count($dbFields);
		
		return $response;
	}
	
	
	/**
	 * Allows you to add a metadata profile object and metadata profile content associated with Kaltura object type
	 * 
	 * @action add
	 * @param KalturaMetadataProfile $metadataProfile
	 * @param string $xsdData XSD metadata definition
	 * @param string $viewsData UI views definition
	 * @return KalturaMetadataProfile
	 */
	function addAction(KalturaMetadataProfile $metadataProfile, $xsdData, $viewsData = null)
	{
		$dbMetadataProfile = $metadataProfile->toInsertableObject();
		$dbMetadataProfile->setStatus(KalturaMetadataProfileStatus::ACTIVE);
		$dbMetadataProfile->setPartnerId($this->getPartnerId());
		$dbMetadataProfile->save();
		
		$xsdData = html_entity_decode($xsdData);
		$key = $dbMetadataProfile->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_DEFINITION);
		kFileSyncUtils::file_put_contents($key, $xsdData);
		
		if($viewsData)
		{
			$viewsData = html_entity_decode($viewsData);
			$key = $dbMetadataProfile->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_VIEWS);
			kFileSyncUtils::file_put_contents($key, $viewsData);
		}
		kMetadataManager::parseProfileSearchFields($dbMetadataProfile);
		
		$metadataProfile = new KalturaMetadataProfile();
		$metadataProfile->fromObject($dbMetadataProfile);
		
		return $metadataProfile;
	}
	
	
	/**
	 * Allows you to add a metadata profile object and metadata profile file associated with Kaltura object type
	 * 
	 * @action addFromFile
	 * @param KalturaMetadataProfile $metadataProfile
	 * @param file $xsdFile XSD metadata definition
	 * @param file $viewsFile UI views definition
	 * @return KalturaMetadataProfile
	 * @throws MetadataErrors::METADATA_FILE_NOT_FOUND
	 */
	function addFromFileAction(KalturaMetadataProfile $metadataProfile, $xsdFile, $viewsFile = null)
	{
		$filePath = $xsdFile['tmp_name'];
		if(!file_exists($filePath))
			throw new KalturaAPIException(MetadataErrors::METADATA_FILE_NOT_FOUND, $xsdFile['name']);
			
		$dbMetadataProfile = $metadataProfile->toInsertableObject();
		$dbMetadataProfile->setStatus(KalturaMetadataProfileStatus::ACTIVE);
		$dbMetadataProfile->setPartnerId($this->getPartnerId());
		$dbMetadataProfile->save();
		
		$key = $dbMetadataProfile->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_DEFINITION);
		kFileSyncUtils::moveFromFile($filePath, $key);
		
		if($viewsFile && $viewsFile['size'])
		{
			$filePath = $viewsFile['tmp_name'];
			if(!file_exists($filePath))
				throw new KalturaAPIException(MetadataErrors::METADATA_FILE_NOT_FOUND, $viewsFile['name']);
				
			$key = $dbMetadataProfile->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_VIEWS);
			kFileSyncUtils::moveFromFile($filePath, $key);
		}
		kMetadataManager::parseProfileSearchFields($dbMetadataProfile);
		
		$metadataProfile = new KalturaMetadataProfile();
		$metadataProfile->fromObject($dbMetadataProfile);
		
		return $metadataProfile;
	}
	
	
	/**
	 * Delete an existing metadata profile
	 * 
	 * @action delete
	 * @param int $id
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */		
	function deleteAction($id)
	{
		$dbMetadataProfile = MetadataProfilePeer::retrieveByPK($id);
		
		if(!$dbMetadataProfile)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);
		
		$dbMetadataProfile->setStatus(KalturaMetadataProfileStatus::DEPRECATED);
		$dbMetadataProfile->save();
		
		$c = new Criteria();
		$c->add(MetadataPeer::METADATA_PROFILE_ID, $id);
		$c->add(MetadataPeer::STATUS, KalturaMetadataStatus::DELETED, Criteria::NOT_EQUAL);
	
		$peer = null;
		MetadataPeer::setUseCriteriaFilter(false);
		$metadatas = MetadataPeer::doSelect($c);
		foreach($metadatas as $metadata)
		{
			kEventsManager::raiseEvent(new kObjectDeletedEvent($metadata));
			
			kMetadataManager::updateSearchIndex($metadata);
		}
		
		$update = new Criteria();
		$update->add(MetadataPeer::STATUS, KalturaMetadataStatus::DELETED);
			
		$con = Propel::getConnection(MetadataPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		BasePeer::doUpdate($c, $update, $con);
	}

	
	/**
	 * Retrieve a metadata profile object by id
	 * 
	 * @action get
	 * @param int $id 
	 * @return KalturaMetadataProfile
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */		
	function getAction($id)
	{
		$dbMetadataProfile = MetadataProfilePeer::retrieveByPK( $id );
		
		if(!$dbMetadataProfile)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);
			
		$metadataProfile = new KalturaMetadataProfile();
		$metadataProfile->fromObject($dbMetadataProfile);
		
		return $metadataProfile;
	}
	
	
	/**
	 * Update an existing metadata object
	 * 
	 * @action update
	 * @param int $id 
	 * @param KalturaMetadataProfile $metadataProfile
	 * @param string $xsdData XSD metadata definition
	 * @param string $viewsData UI views definition
	 * @return KalturaMetadataProfile
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 * @throws MetadataErrors::METADATA_UNABLE_TO_TRANSFORM
	 * @throws MetadataErrors::METADATA_TRANSFORMING
	 */	
	function updateAction($id, KalturaMetadataProfile $metadataProfile, $xsdData = null, $viewsData = null)
	{
		$dbMetadataProfile = MetadataProfilePeer::retrieveByPK($id);
		
		if(!$dbMetadataProfile)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);

		if($dbMetadataProfile->getStatus() != MetadataProfile::STATUS_ACTIVE)
			throw new KalturaAPIException(MetadataErrors::METADATA_TRANSFORMING);
		
		$dbMetadataProfile = $metadataProfile->toUpdatableObject($dbMetadataProfile);
		
		$key = $dbMetadataProfile->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_DEFINITION);
		$oldXsd = kFileSyncUtils::getLocalFilePathForKey($key);
		$oldVersion = $dbMetadataProfile->getVersion();
		
		if($xsdData)
		{
			$xsdData = html_entity_decode($xsdData);
			$dbMetadataProfile->incrementVersion();
		}
			
		if(!is_null($viewsData))
		{
			$viewsData = html_entity_decode($viewsData);
			$dbMetadataProfile->incrementViewsVersion();
		}
			
		$dbMetadataProfile->save();
	
		if(!is_null($viewsData) && $viewsData != '')
		{
			$key = $dbMetadataProfile->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_VIEWS);
			kFileSyncUtils::file_put_contents($key, $viewsData);
		}
		
		if($xsdData)
		{
			$key = $dbMetadataProfile->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_DEFINITION);
			kFileSyncUtils::file_put_contents($key, $xsdData);
			
			try
			{
				kMetadataManager::diffMetadataProfile($dbMetadataProfile, $oldVersion, $oldXsd);
			}
			catch(kXsdException $e)
			{
				// revert back to previous version
				$dbMetadataProfile->setVersion($oldVersion);
				$dbMetadataProfile->save();
				
				throw new KalturaAPIException(MetadataErrors::METADATA_UNABLE_TO_TRANSFORM, $e->getMessage());
			}
		}
	
		kMetadataManager::parseProfileSearchFields($dbMetadataProfile);
		
		$metadataProfile->fromObject($dbMetadataProfile);
		return $metadataProfile;
	}	
	
	
	/**
	 * Update an existing metadata object definition file
	 * 
	 * @action updateDefinitionFromFile
	 * @param int $id 
	 * @param file $xsdFile XSD metadata definition
	 * @return KalturaMetadataProfile
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 * @throws MetadataErrors::METADATA_FILE_NOT_FOUND
	 * @throws MetadataErrors::METADATA_UNABLE_TO_TRANSFORM
	 */	
	function revertAction($id, $toVersion)
	{
		$dbMetadataProfile = MetadataProfilePeer::retrieveByPK($id);
		
		if(!$dbMetadataProfile)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);
	
		$oldKey = $dbMetadataProfile->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_DEFINITION, $toVersion);
		if(!kFileSyncUtils::fileSync_exists($oldKey))
			throw new KalturaAPIException(MetadataErrors::METADATA_FILE_NOT_FOUND, $oldKey);
		
		$dbMetadataProfile->incrementVersion();
		$dbMetadataProfile->save();
		
		$key = $dbMetadataProfile->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_DEFINITION);
		kFileSyncUtils::createSyncFileLinkForKey($key, $oldKey);
		
		kMetadataManager::parseProfileSearchFields($dbMetadataProfile);
		
		MetadataPeer::setUseCriteriaFilter(false);
		$metadatas = MetadataPeer::retrieveByProfile($id, $toVersion);
		foreach($metadatas as $metadata)
		{
			// validate object exists
			$object = kMetadataManager::getObjectFromPeer($metadata);
			if(!$object)
				continue;
				
			$oldKey = $metadata->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA, $toVersion);
			if(!kFileSyncUtils::fileSync_exists($oldKey))
				continue;
			
			$metadata->incrementVersion();
			$metadata->setMetadataProfileVersion($dbMetadataProfile->getVersion());
			$metadata->save();
			
			$key = $metadata->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_DEFINITION);
			$fileSync = kFileSyncUtils::createSyncFileLinkForKey($key, $oldKey, false);
			if(!$fileSync)
				continue;
			
			$errorMessage = '';
			kMetadataManager::validateMetadata($metadata, $errorMessage);
		}
		
		$metadataProfile = new KalturaMetadataProfile();
		$metadataProfile->fromObject($dbMetadataProfile);
		
		return $metadataProfile;
	}	
	
	
	/**
	 * Update an existing metadata object definition file
	 * 
	 * @action updateDefinitionFromFile
	 * @param int $id 
	 * @param file $xsdFile XSD metadata definition
	 * @return KalturaMetadataProfile
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 * @throws MetadataErrors::METADATA_FILE_NOT_FOUND
	 * @throws MetadataErrors::METADATA_UNABLE_TO_TRANSFORM
	 */	
	function updateDefinitionFromFileAction($id, $xsdFile)
	{
		$dbMetadataProfile = MetadataProfilePeer::retrieveByPK($id);
		
		if(!$dbMetadataProfile)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);
	
		$filePath = null;
		if($xsdFile)
		{
			$filePath = $xsdFile['tmp_name'];
			if(!file_exists($filePath))
				throw new KalturaAPIException(MetadataErrors::METADATA_FILE_NOT_FOUND, $xsdFile['name']);
		}
		
		$key = $dbMetadataProfile->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_DEFINITION);
		$oldXsd = kFileSyncUtils::getLocalFilePathForKey($key);
		$oldVersion = $dbMetadataProfile->getVersion();
		
		$dbMetadataProfile->incrementVersion();
		$dbMetadataProfile->save();
		
		$key = $dbMetadataProfile->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_DEFINITION);
		kFileSyncUtils::moveFromFile($filePath, $key);
		
		try
		{
			kMetadataManager::diffMetadataProfile($dbMetadataProfile, $oldVersion, $oldXsd);
		}
		catch(kXsdException $e)
		{
			throw new KalturaAPIException(MetadataErrors::METADATA_UNABLE_TO_TRANSFORM);
		}
		kMetadataManager::parseProfileSearchFields($dbMetadataProfile);
		
		$metadataProfile = new KalturaMetadataProfile();
		$metadataProfile->fromObject($dbMetadataProfile);
		
		return $metadataProfile;
	}
	
	
	/**
	 * Update an existing metadata object views file
	 * 
	 * @action updateViewsFromFile
	 * @param int $id 
	 * @param file $viewsFile UI views file
	 * @return KalturaMetadataProfile
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 * @throws MetadataErrors::METADATA_FILE_NOT_FOUND
	 */	
	function updateViewsFromFileAction($id, $viewsFile)
	{
		$dbMetadataProfile = MetadataProfilePeer::retrieveByPK($id);
		
		if(!$dbMetadataProfile)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);
	
		$filePath = null;
		if($viewsFile)
		{
			$filePath = $viewsFile['tmp_name'];
			if(!file_exists($filePath))
				throw new KalturaAPIException(MetadataErrors::METADATA_FILE_NOT_FOUND, $viewsFile['name']);
		}
		
		$dbMetadataProfile->incrementViewsVersion();
		$dbMetadataProfile->save();
		
		if(trim(file_get_contents($filePath)) != '')
		{
			$key = $dbMetadataProfile->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_VIEWS);
			kFileSyncUtils::moveFromFile($filePath, $key);
		}
		
		$metadataProfile = new KalturaMetadataProfile();
		$metadataProfile->fromObject($dbMetadataProfile);
		
		return $metadataProfile;
	}	
}
