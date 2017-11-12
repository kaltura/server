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

		$this->applyPartnerFilterForClass('MetadataProfile');
		if ($actionName != 'list')
			$this->applyPartnerFilterForClass('Metadata');
		
		if(!MetadataPlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, MetadataPlugin::PLUGIN_NAME);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaBaseService::partnerGroup()
	 */
	protected function partnerGroup($peer = null)
	{
	    if(in_array($this->actionName, array('get', 'list')) && $peer == 'Metadata'){
	        return $this->partnerGroup . ',0';
	    }
	    elseif (in_array($this->actionName, array('add', 'get', 'list', 'update')) && $peer == 'MetadataProfile'){
	        return $this->partnerGroup . ',0';
	    }
	
	    return $this->partnerGroup;
	}
	
	protected function kalturaNetworkAllowed($actionName)
	{
		if ($actionName == 'list')
		{
			$this->partnerGroup .= ',0';
			return true;
		}
			
		return parent::kalturaNetworkAllowed($actionName);
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
	 * @throws MetadataErrors::METADATA_PROFILE_NOT_FOUND
	 * @throws MetadataErrors::INCOMPATIBLE_METADATA_PROFILE_OBJECT_TYPE
	 * @throws MetadataErrors::METADATA_ALREADY_EXISTS
	 * @throws MetadataErrors::INVALID_METADATA_DATA
	 */
	function addAction($metadataProfileId, $objectType, $objectId, $xmlData)
	{
	    $metadataProfile = MetadataProfilePeer::retrieveByPK($metadataProfileId);
		if(!$metadataProfile)
		    throw new KalturaAPIException(MetadataErrors::METADATA_PROFILE_NOT_FOUND, $metadataProfileId);
		    
		if($metadataProfile->getObjectType() != kPluginableEnumsManager::apiToCore('MetadataObjectType', $objectType))
		    throw new KalturaAPIException(MetadataErrors::INCOMPATIBLE_METADATA_PROFILE_OBJECT_TYPE, $metadataProfile->getObjectType() , $objectType);
		
		if($objectType == KalturaMetadataObjectType::USER)
		{
			$kuser = kuserPeer::createKuserForPartner($this->getPartnerId(), $objectId);
			if($kuser)				
				$objectId = $kuser->getId();
		}
		
		$objectType = kPluginableEnumsManager::apiToCore('MetadataObjectType', $objectType);

		$limitEntry = $this->getKs()->getLimitEntry();
		if ($limitEntry) {
			$peer = kMetadataManager::getObjectPeer($objectType);
			if ($peer) {
				$entry = $peer->getEntry($objectId);
				if (!$entry || $entry->getId() != $limitEntry) {
					throw new KalturaAPIException(MetadataErrors::METADATA_NO_PERMISSION_ON_ENTRY, $objectId);
				}
			}
		}

		$check = MetadataPeer::retrieveByObject($metadataProfileId, $objectType, $objectId);
		if($check)
			throw new KalturaAPIException(MetadataErrors::METADATA_ALREADY_EXISTS, $check->getId());
			
		// if a metadata xslt is defined on the metadata profile - transform the given metadata
		$xmlDataTransformed = $this->transformMetadata($metadataProfileId, $xmlData);
	    if($xmlDataTransformed)
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
		$metadata->fromObject($dbMetadata, $this->getResponseProfile());
		
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
	 * @throws MetadataErrors::INVALID_METADATA_PROFILE_TYPE
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
			
		if($dbMetadataProfile->getObjectType() != $objectType)
			throw new KalturaAPIException(MetadataErrors::INVALID_METADATA_PROFILE_TYPE, $dbMetadataProfile->getObjectType());
		
		$dbMetadata = new Metadata();
		
		$dbMetadata->setPartnerId($this->getPartnerId());
		$dbMetadata->setMetadataProfileId($metadataProfileId);
		$dbMetadata->setMetadataProfileVersion($dbMetadataProfile->getVersion());
		$dbMetadata->setObjectType($objectType);
		$dbMetadata->setObjectId($objectId);
		$dbMetadata->setStatus(KalturaMetadataStatus::VALID);
		$dbMetadata->setLikeNew(true);

		// dynamic objects are metadata only, skip validating object id
		if ($objectType != KalturaMetadataObjectType::DYNAMIC_OBJECT)
		{
			// validate object exists
			$object = kMetadataManager::getObjectFromPeer($dbMetadata);
			if (!$object)
				throw new KalturaAPIException(MetadataErrors::INVALID_METADATA_OBJECT, $objectId);
		}

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
		$xmlData = file_get_contents($url);
		return $this->addAction($metadataProfileId, $objectType, $objectId, $xmlData);
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
	 * @throws MetadataErrors::METADATA_NOT_FOUND
	 */		
	function getAction($id)
	{
		$dbMetadata = MetadataPeer::retrieveByPK( $id );
		
		if(!$dbMetadata)
			throw new KalturaAPIException(MetadataErrors::METADATA_NOT_FOUND, $id);
			
		$metadata = new KalturaMetadata();
		$metadata->fromObject($dbMetadata, $this->getResponseProfile());
		
		return $metadata;
	}
	
	/**
	 * Update an existing metadata object with new XML content
	 * 
	 * @action update
	 * @param int $id 
	 * @param string $xmlData XML metadata
	 * @param int $version Enable update only if the metadata object version did not change by other process
	 * @return KalturaMetadata
	 * @throws MetadataErrors::METADATA_NOT_FOUND
	 * @throws MetadataErrors::INVALID_METADATA_DATA
	 * @throws MetadataErrors::INVALID_METADATA_VERSION
	 * @throws MetadataErrors::XSLT_VALIDATION_ERROR
	 */	
	function updateAction ($id, $xmlData = null, $version = null)
	{
		return kLock::runLocked("metadata_update_xsl_{$id}", array($this, 'updateImpl'), array($id, $xmlData, $version));
	}

	function updateImpl($id, $xmlData = null, $version = null)
	{
		$dbMetadata = MetadataPeer::retrieveByPK($id);
		if(!$dbMetadata)
			throw new KalturaAPIException(MetadataErrors::METADATA_NOT_FOUND, $id);
			
		if($version && $dbMetadata->getVersion() != $version)
			throw new KalturaAPIException(MetadataErrors::INVALID_METADATA_VERSION, $dbMetadata->getVersion());
		
		$dbMetadataProfile = MetadataProfilePeer::retrieveByPK($dbMetadata->getMetadataProfileId());
		if(!$dbMetadataProfile)
			throw new KalturaAPIException(MetadataErrors::INVALID_METADATA_PROFILE, $dbMetadata->getMetadataProfileId());
		
		if($xmlData)
		{
			// if a metadata xslt is defined on the metadata profile - transform the given metadata
		    $xmlDataTransformed = $this->transformMetadata($dbMetadata->getMetadataProfileId(), $xmlData);
		    if ($xmlDataTransformed)
	            $xmlData = $xmlDataTransformed;
			
			$errorMessage = '';
			if(!kMetadataManager::validateMetadata($dbMetadata->getMetadataProfileId(), $xmlData, $errorMessage))
			{
				// if metadata profile is transforming, and metadata profile version is not the latest, try to validate againts previous version
				if($dbMetadataProfile->getStatus() != MetadataProfile::STATUS_TRANSFORMING || $dbMetadata->getMetadataProfileVersion() >= $dbMetadataProfile->getVersion())
					throw new KalturaAPIException(MetadataErrors::INVALID_METADATA_DATA, $errorMessage);
					
				// validates against previous version
				$errorMessagePrevVersion = '';
				if(!kMetadataManager::validateMetadata($dbMetadata->getMetadataProfileId(), $xmlData, $errorMessagePrevVersion, true))
				{
					KalturaLog::err("Failed to validate metadata object [$id] against metadata profile previous version [" . $dbMetadata->getMetadataProfileVersion() . "] error: $errorMessagePrevVersion");

					// throw the error with the original error message
					throw new KalturaAPIException(MetadataErrors::INVALID_METADATA_DATA, $errorMessage);
				}
			}
			else
			{
				$dbMetadata->setMetadataProfileVersion($dbMetadataProfile->getVersion());
			}
			
			$key = $dbMetadata->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA);
			if (!kFileSyncUtils::compareContent($key, $xmlData))
			{
				MetadataPlugin::updateMetadataFileSync($dbMetadata, $xmlData);
			}
			else 
			{
				KalturaLog::info("XML data MD5 matches current filesync content MD5. Update is not necessary.");
				//adding this save() in order to save the metadata profile version field in case there are no diffrences
				$dbMetadata->save();
			}
		}
		
		$metadata = new KalturaMetadata();
		$metadata->fromObject($dbMetadata, $this->getResponseProfile());
			
		return $metadata;
	}	
	
	
	/**
	 * Update an existing metadata object with new XML file
	 * 
	 * @action updateFromFile
	 * @param int $id 
	 * @param file $xmlFile XML metadata
	 * @return KalturaMetadata
	 * @throws MetadataErrors::METADATA_NOT_FOUND
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
			$filter = new KalturaMetadataFilter();
			
		if (! $pager)
			$pager = new KalturaFilterPager();
		
		$applyPartnerFilter = true;
		if($filter->metadataObjectTypeEqual == MetadataObjectType::ENTRY)
		{
			$objectIds = $filter->getObjectIdsFiltered();
			if(!empty($objectIds))
			{
				$objectIds = entryPeer::filterEntriesByPartnerOrKalturaNetwork($objectIds, kCurrentContext::getCurrentPartnerId());
				if(count($objectIds))
				{
					$applyPartnerFilter = false;
					$filter->objectIdEqual = null;
					$filter->objectIdIn = implode(",", $objectIds);
				}
			}
		}
		
		if($applyPartnerFilter)
			$this->applyPartnerFilterForClass('Metadata');
		
		return $filter->getListResponse($pager, $this->getResponseProfile());
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
		MetadataPeer::setUseCriteriaFilter(true);
		
		foreach($metadatas as $metadata)
			kEventsManager::raiseEvent(new kObjectDeletedEvent($metadata));
		
		$update = new Criteria();
		$update->add(MetadataPeer::STATUS, KalturaMetadataStatus::DELETED);
			
		$con = Propel::getConnection(MetadataPeer::DATABASE_NAME);
		$count = BasePeer::doUpdate($c, $update, $con);
		
		return $count;
	}	
	
	
	/**
	 * Delete an existing metadata
	 * 
	 * @action delete
	 * @param int $id
	 * @throws MetadataErrors::METADATA_NOT_FOUND
	 */		
	function deleteAction($id)
	{
		$dbMetadata = MetadataPeer::retrieveByPK($id);
		
		if(!$dbMetadata)
			throw new KalturaAPIException(MetadataErrors::METADATA_NOT_FOUND, $id);
		
		$dbMetadata->setStatus(KalturaMetadataStatus::DELETED);
		$dbMetadata->save();
		kEventsManager::raiseEvent(new kObjectDataChangedEvent($dbMetadata));
	}

	
	/**
	 * Mark existing metadata as invalid
	 * Used by batch metadata transform
	 * 
	 * @action invalidate
	 * @param int $id
	 * @param int $version Enable update only if the metadata object version did not change by other process
	 * @throws MetadataErrors::METADATA_NOT_FOUND
	 * @throws MetadataErrors::INVALID_METADATA_VERSION
	 */		
	function invalidateAction($id, $version = null)
	{
		$dbMetadata = MetadataPeer::retrieveByPK($id);
		if(!$dbMetadata)
			throw new KalturaAPIException(MetadataErrors::METADATA_NOT_FOUND, $id);

		if($version && $dbMetadata->getVersion() != $version)
			throw new KalturaAPIException(MetadataErrors::INVALID_METADATA_VERSION, $dbMetadata->getVersion());

		$dbMetadata->setStatus(KalturaMetadataStatus::INVALID);
		$dbMetadata->save();
	}

	/**
	 * Index metadata by id, will also index the related object
	 *
	 * @action index
	 * @param string $id
	 * @param bool $shouldUpdate
	 * @return int
	 */
	function indexAction($id, $shouldUpdate)
	{
		if(kEntitlementUtils::getEntitlementEnforcement())
			throw new KalturaAPIException(KalturaErrors::CANNOT_INDEX_OBJECT_WHEN_ENTITLEMENT_IS_ENABLE);

		$dbMetadata = MetadataPeer::retrieveByPK($id);
		if(!$dbMetadata)
			throw new KalturaAPIException(MetadataErrors::METADATA_NOT_FOUND, $id);

		$dbMetadata->indexToSearchIndex();
		$relatedObject = kMetadataManager::getObjectFromPeer($dbMetadata);
		if($relatedObject && $relatedObject instanceof IIndexable)
			$relatedObject->indexToSearchIndex();

		return $dbMetadata->getId();

	}

	/**
	 * Serves metadata XML file
	 *  
	 * @action serve
	 * @param int $id
	 * @return file
	 *  
	 * @throws MetadataErrors::METADATA_NOT_FOUND
	 * @throws KalturaErrors::FILE_DOESNT_EXIST
	 */
	public function serveAction($id)
	{
		$dbMetadata = MetadataPeer::retrieveByPK( $id );
		
		if(!$dbMetadata)
			throw new KalturaAPIException(MetadataErrors::METADATA_NOT_FOUND, $id);
		
		$fileName = $dbMetadata->getObjectId() . '.xml';
		$fileSubType = Metadata::FILE_SYNC_METADATA_DATA;
		
		return $this->serveFile($dbMetadata, $fileSubType, $fileName);
	}
		
	
	private function transformMetadata($metadataProfileId, $xmlData)
	{
        $result = null;
	    $metadataProfile = MetadataProfilePeer::retrieveByPK($metadataProfileId); 
	    if (!$metadataProfile) {
	        KalturaLog::err('Cannot find metadata profile id ['.$metadataProfileId.']');
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
        
        $xsltErrors = array();
        $xmlDataTransformed = kXml::transformXmlUsingXslt($xmlData, $xsltString, $xsltParams, $xsltErrors);
        
        if (!empty($xsltErrors))
        {
        	throw new KalturaAPIException(MetadataErrors::XSLT_VALIDATION_ERROR, implode(',', $xsltErrors));
        }
        
        if ($xmlDataTransformed)
            return $xmlDataTransformed;
        
        KalturaLog::err('Failed XML [$xmlData] transformation for metadata with XSL [$xsltString]');
	    return null;
	}
	
	/**
	 * Action transforms current metadata object XML using a provided XSL.
	 * @action updateFromXSL
	 * 
	 * @param int $id
	 * @param file $xslFile
	 * @return KalturaMetadata
	 * @throws MetadataErrors::XSLT_VALIDATION_ERROR
	 * @throws MetadataErrors::METADATA_FILE_NOT_FOUND
	 * @throws MetadataErrors::METADATA_NOT_FOUND
	 */
	public function updateFromXSLAction ($id, $xslFile)
	{
		$xslFilePath = $xslFile['tmp_name'];
		if(!file_exists($xslFilePath))
			throw new KalturaAPIException(MetadataErrors::METADATA_FILE_NOT_FOUND, $xslFile['name']);

		$xslData = file_get_contents($xslFilePath);
		@unlink($xslFilePath);

		return kLock::runLocked("metadata_update_xsl_{$id}", array($this, 'updateFromXSLImpl'), array($id, $xslData));
	}

	public function updateFromXSLImpl ($id, $xslData)
	{
		$dbMetadataObject = MetadataPeer::retrieveByPK($id);
		if (!$dbMetadataObject)
			throw new KalturaAPIException(MetadataErrors::METADATA_NOT_FOUND);

		$dbMetadataObjectFileSyncKey = $dbMetadataObject->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA);

		$xsltErrors = array();
		$transformMetadataObjectData = kXml::transformXmlUsingXslt(kFileSyncUtils::file_get_contents($dbMetadataObjectFileSyncKey), $xslData, array(), $xsltErrors);

		if ( count($xsltErrors))
		{
			throw new KalturaAPIException(MetadataErrors::XSLT_VALIDATION_ERROR, implode(',', $xsltErrors));
		}

		return $this->updateImpl($id, $transformMetadataObjectData);
	}
}
