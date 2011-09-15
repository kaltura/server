<?php
/**
 * Metadata service
 *
 * @service metadata
 * @package plugins.metadata
 * @subpackage api.services
 */
class MetadataService extends KalturaBaseService
{

	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);

		myPartnerUtils::addPartnerToCriteria(new MetadataProfilePeer(), $this->getPartnerId(), $this->private_partner_data, $this->partnerGroup());
		myPartnerUtils::addPartnerToCriteria(new MetadataPeer(), $this->getPartnerId(), $this->private_partner_data, $this->partnerGroup());
		myPartnerUtils::addPartnerToCriteria(new entryPeer(), $this->getPartnerId(), $this->private_partner_data, $this->partnerGroup());
//		myPartnerUtils::addPartnerToCriteria(new FileSyncPeer(), $this->getPartnerId(), $this->private_partner_data, $this->partnerGroup());
		
		if(!MetadataPlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN, $this->serviceName.'->'.$this->actionName);
	}
	

/**
	 * Allows you to add a metadata object and metadata content associated with Kaltura object
	 * 
	 * @action add
	 * @param int $metadataProfileId

	 * @param KalturaMetadataObjectType $objectType
	 * @param string $objectId
	 * @param string $xmlData XML metadata
	 * @return KalturaMetadata
	 * @throws MetadataErrors::METADATA_ALREADY_EXISTS
	 * @throws MetadataErrors::INVALID_METADATA_DATA

	 */
	function addAction($metadataProfileId, $objectType, $objectId, $xmlData)
	{
		$objectType = kPluginableEnumsManager::apiToCore('MetadataObjectType', $objectType);
		$check = MetadataPeer::retrieveByObject($metadataProfileId, $objectType, $objectId);
		if($check)
			throw new KalturaAPIException(MetadataErrors::METADATA_ALREADY_EXISTS, $check->getId());
			
		// if a metadata xslt is defined on the metadata profile - transform the given metadata
		$xmlDataTransformed = $this->transformMetadata($metadataProfileId, $xmlData);
	    if ($xmlDataTransformed)
            $xmlData = $xmlDataTransformed;
		
		$errorMessage = '';
		if(!kMetadataManager::validateMetadata($metadataProfileId, $xmlData, $errorMessage))
			throw new KalturaAPIException(MetadataErrors::INVALID_METADATA_DATA, $errorMessage);
		
		$dbMetadata = $this->addMetadata($metadataProfileId, $objectType, $objectId);
		
		$key = $dbMetadata->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA);
		kFileSyncUtils::file_put_contents($key, $xmlData);
		
		$this->deleteOldVersions($dbMetadata);
		kEventsManager::raiseEvent(new kObjectDataChangedEvent($dbMetadata));
				
		$metadata = new KalturaMetadata();
		$metadata->fromObject($dbMetadata);
		
		return $metadata;
	}

	
	/**
	 * Adds a metadata object associated with Kaltura object
	 * 
	 * @param int $metadataProfileId
	 * @param KalturaMetadataObjectType $objectType

	 * @param string $objectId
	 * @return Metadata
	 * @throws MetadataErrors::METADATA_ALREADY_EXISTS
	 * @throws MetadataErrors::INVALID_METADATA_PROFILE
	 * @throws MetadataErrors::INVALID_METADATA_OBJECT
	 */
	protected function addMetadata($metadataProfileId, $objectType, $objectId)
	{
		$objectType = kPluginableEnumsManager::apiToCore('MetadataObjectType', $objectType);
		
		$check = MetadataPeer::retrieveByObject($metadataProfileId, $objectType, $objectId);
		if($check)
			throw new KalturaAPIException(MetadataErrors::METADATA_ALREADY_EXISTS, $check->getId());
			
		$dbMetadataProfile = MetadataProfilePeer::retrieveByPK($metadataProfileId);
		if(!$dbMetadataProfile)
			throw new KalturaAPIException(MetadataErrors::INVALID_METADATA_PROFILE, $metadataProfileId);
		
		$dbMetadata = new Metadata();
		
		$dbMetadata->setPartnerId($this->getPartnerId());
		$dbMetadata->setMetadataProfileId($metadataProfileId);
		$dbMetadata->setMetadataProfileVersion($dbMetadataProfile->getVersion());
		$dbMetadata->setObjectType($objectType);
		$dbMetadata->setObjectId($objectId);
		$dbMetadata->setStatus(KalturaMetadataStatus::VALID);

		// validate object exists
		$object = kMetadataManager::getObjectFromPeer($dbMetadata);
		if(!$object)
			throw new KalturaAPIException(MetadataErrors::INVALID_METADATA_OBJECT, $objectId);
		
		$dbMetadata->save();
		
		$this->deleteOldVersions($dbMetadata);
		
		return $dbMetadata;
	}

/**
	 * Allows you to add a metadata object and metadata file associated with Kaltura object
	 * 
	 * @action addFromFile
	 * @param int $metadataProfileId
	 * @param KalturaMetadataObjectType $objectType
	 * @param string $objectId
	 * @param file $xmlFile XML metadata
	 * @return KalturaMetadata
	 * @throws MetadataErrors::METADATA_ALREADY_EXISTS
	 * @throws MetadataErrors::METADATA_FILE_NOT_FOUND
	 * @throws MetadataErrors::INVALID_METADATA_DATA
	 */
	function addFromFileAction($metadataProfileId, $objectType, $objectId, $xmlFile)
	{
		$filePath = $xmlFile['tmp_name'];
		if(!file_exists($filePath))
			throw new KalturaAPIException(MetadataErrors::METADATA_FILE_NOT_FOUND, $xmlFile['name']);
		
		$xmlData = file_get_contents($filePath);
		@unlink($filePath);
		return $this->addAction($metadataProfileId, $objectType, $objectId, $xmlData);
	}
	
	
	/**
	 * Allows you to add a metadata xml data from remote URL
	 * 

	 * @action addFromUrl
	 * @param int $metadataProfileId
	 * @param KalturaMetadataObjectType $objectType
	 * @param string $objectId
	 * @param string $url XML metadata remote url

	 * @return KalturaMetadata
	 */
	function addFromUrlAction($metadataProfileId, $objectType, $objectId, $url)
	{
		$objectType = kPluginableEnumsManager::apiToCore('MetadataObjectType', $objectType);
	
		$dbMetadata = $this->addMetadata($metadataProfileId, $objectType, $objectId);
		
		kMetadataManager::addImportMetadataJob($dbMetadata->getPartnerId(), $dbMetadata->getId(), $url);
		
		$metadata = new KalturaMetadata();
		$metadata->fromObject($dbMetadata);
		
		return $metadata;
	}
	
	
	/**
	 * Allows you to add a metadata xml data from remote URL.
	 * Enables different permissions than addFromUrl action.
	 * 
	 * @action addFromBulk
	 * @param int $metadataProfileId

	 * @param KalturaMetadataObjectType $objectType
	 * @param string $objectId
	 * @param string $url XML metadata remote url
	 * @return KalturaMetadata
	 */
	function addFromBulkAction($metadataProfileId, $objectType, $objectId, $url)
	{
		$this->addFromUrlAction($metadataProfileId, $objectType, $objectId, $url);
	}

	
	/**
	 * Retrieve a metadata object by id
	 * 
	 * @action get
	 * @param int $id 
	 * @return KalturaMetadata

	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */		
	function getAction($id)
	{
		$dbMetadata = MetadataPeer::retrieveByPK( $id );
		
		if(!$dbMetadata)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);
			
		$metadata = new KalturaMetadata();
		$metadata->fromObject($dbMetadata);
		
		return $metadata;
	}
	
	
//	/**
//	 * Update an existing metadata object with new XML content
//	 * 
//	 * @action update
//	 * @param int $id 
//	 * @param KalturaMetadata $metadata
//	 * @param string $xmlData XML metadata
//	 * @return KalturaMetadata
//	 */	
//	function updateAction($id, KalturaMetadata $metadata, $xmlData = null)
	
	/**

	 * Update an existing metadata object with new XML content
	 * 
	 * @action update
	 * @param int $id 
	 * @param string $xmlData XML metadata
	 * @return KalturaMetadata

	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 * @throws MetadataErrors::INVALID_METADATA_DATA
	 */	
	function updateAction($id, $xmlData = null)
	{
		$dbMetadata = MetadataPeer::retrieveByPK($id);
		
		if(!$dbMetadata)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);
		
		$previousVersion = null;
		if($dbMetadata->getStatus() == Metadata::STATUS_VALID)
			$previousVersion = $dbMetadata->getVersion();
				
		if($xmlData)
		{
			// if a metadata xslt is defined on the metadata profile - transform the given metadata
		    $xmlDataTransformed = $this->transformMetadata($dbMetadata->getMetadataProfileId(), $xmlData);
		    if ($xmlDataTransformed)
	            $xmlData = $xmlDataTransformed;
			
			$errorMessage = '';
			if(!kMetadataManager::validateMetadata($dbMetadata->getMetadataProfileId(), $xmlData, $errorMessage))
				throw new KalturaAPIException(MetadataErrors::INVALID_METADATA_DATA, $errorMessage);
			
			$dbMetadata->incrementVersion();
			$dbMetadata->save();
		
			$key = $dbMetadata->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA);
			kFileSyncUtils::file_put_contents($key, $xmlData);
			
			kEventsManager::raiseEvent(new kObjectDataChangedEvent($dbMetadata, $previousVersion));
		}
		
		$metadata = new KalturaMetadata();
		$metadata->fromObject($dbMetadata);
			
		return $metadata;
	}	
	
	
	/**
	 * Update an existing metadata object with new XML file

	 * 
	 * @action updateFromFile
	 * @param int $id 
	 * @param file $xmlFile XML metadata
	 * @return KalturaMetadata
	 * @throws KalturaErrors::INVALID_OBJECT_ID

	 * @throws MetadataErrors::METADATA_FILE_NOT_FOUND
	 * @throws MetadataErrors::INVALID_METADATA_DATA
	 */	
	function updateFromFileAction($id, $xmlFile = null)
	{
		$filePath = $xmlFile['tmp_name'];
		if(!file_exists($filePath))
			throw new KalturaAPIException(MetadataErrors::METADATA_FILE_NOT_FOUND, $xmlFile['name']);
		
		$xmlData = file_get_contents($filePath);
		@unlink($filePath);
		return $this->updateAction($id, $xmlData);
	}		
	
	/**
	 * List metadata objects by filter and pager
	 * 
	 * @action list
	 * @param KalturaMetadataFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaMetadataListResponse
	 */
	function listAction(KalturaMetadataFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaMetadataFilter;
			
		$metadataFilter = new MetadataFilter();
		$filter->toObject($metadataFilter);
		
		$c = new Criteria();
		$metadataFilter->attachToCriteria($c);
		$count = MetadataPeer::doCount($c);
		
		if (! $pager)
			$pager = new KalturaFilterPager ();
		$pager->attachToCriteria($c);
		$list = MetadataPeer::doSelect($c);
		
		$response = new KalturaMetadataListResponse();
		$response->objects = KalturaMetadataArray::fromDbArray($list);
		$response->totalCount = $count;
		
		return $response;
	}
	

	
	
	/**
	 * @param Metadata $metadata
	 * @return int affected records
	 */
	protected function deleteOldVersions(Metadata $metadata)
	{
		$c = new Criteria();
		$c->add(MetadataPeer::OBJECT_ID, $metadata->getObjectId());
		$c->add(MetadataPeer::OBJECT_TYPE, $metadata->getObjectType());
		$c->add(MetadataPeer::METADATA_PROFILE_ID, $metadata->getMetadataProfileId());
		$c->add(MetadataPeer::METADATA_PROFILE_VERSION, $metadata->getMetadataProfileVersion(), Criteria::LESS_THAN);
		$c->add(MetadataPeer::STATUS, KalturaMetadataStatus::DELETED, Criteria::NOT_EQUAL);
		
		MetadataPeer::setUseCriteriaFilter(false);
		$metadatas = MetadataPeer::doSelect($c);
		foreach($metadatas as $metadata)
			kEventsManager::raiseEvent(new kObjectDeletedEvent($metadata));
		
		$update = new Criteria();
		$update->add(MetadataPeer::STATUS, KalturaMetadataStatus::DELETED);
			
		$con = Propel::getConnection(MetadataPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		$count = BasePeer::doUpdate($c, $update, $con);
		return $count;
	}	
	
	
	/**
	 * Delete an existing metadata
	 * 
	 * @action delete
	 * @param int $id
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */		
	function deleteAction($id)
	{
		$dbMetadata = MetadataPeer::retrieveByPK($id);
		
		if(!$dbMetadata)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);
		
		$dbMetadata->setStatus(KalturaMetadataStatus::DELETED);
		$dbMetadata->save();
	}

	
	/**
	 * Mark existing metadata as invalid
	 * Used by batch metadata transform
	 * 
	 * @action invalidate
	 * @param int $id
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */		
	function invalidateAction($id)
	{
		$dbMetadata = MetadataPeer::retrieveByPK($id);
		
		if(!$dbMetadata)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);
		
		$dbMetadata->setStatus(KalturaMetadataStatus::INVALID);
		$dbMetadata->save();
	}

	

	/**
	 * Serves metadata XML file
	 *  
	 * @action serve
	 * @param int $id
	 * @return file
	 *  
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 * @throws KalturaErrors::FILE_DOESNT_EXIST
	 */
	public function serveAction($id)
	{
		$dbMetadata = MetadataPeer::retrieveByPK( $id );
		
		if(!$dbMetadata)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);
		
		$fileName = $dbMetadata->getObjectId() . '.xml';
		$fileSubType = Metadata::FILE_SYNC_METADATA_DATA;
		
		return $this->serveFile($dbMetadata, $fileSubType, $fileName);
	}
		
	
	private function transformMetadata($metadataProfileId, $xmlData)
	{
        $result = null;
	    $metadataProfile = MetadataProfilePeer::retrieveByPK($metadataProfileId); 
	    if (!$metadataProfile) {
	        KalturaLog::err('Cannot find metadata profile for metadata id ['.$dbMetadata->getId().']');
	        return null;
	    }
	    
	    $metadataXsltKey = $metadataProfile->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_XSLT);
	    if (!kFileSyncUtils::file_exists($metadataXsltKey, true))
	    	return null;
	    
	    $xsltString = kFileSyncUtils::file_get_contents($metadataXsltKey, true, false);
	    if (!$xsltString)
	    	return null;
	    
        $xsltParams = array(
        	XsltParameterName::KALTURA_CURRENT_TIMESTAMP => time(),
        );
        
        $xmlDataTransformed = kXml::transformXmlUsingXslt($xmlData, $xsltString, $xsltParams);
        if ($xmlDataTransformed)
            return $xmlDataTransformed;
        
        KalturaLog::err('Failed XML [$xmlData] transformation for metadata with XSL [$xsltString]');
	    return null;
	}
}
