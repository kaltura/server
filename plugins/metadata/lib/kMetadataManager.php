<?php
/**
 * @package plugins.metadata
 * @subpackage lib
 */
class kMetadataManager
{
	const APP_INFO_SEARCH = 'searchable';
	const APP_INFO_KEY = 'key';
	const APP_INFO_LABEL = 'label';
	
	const SEARCH_TEXT_SUFFIX = 'mdend';
	
	protected static $objectTypeNames = array(
		MetadataObjectType::ENTRY => 'entry',
		MetadataObjectType::CATEGORY => 'category',
		MetadataObjectType::USER => 'kuser',
		MetadataObjectType::PARTNER => 'Partner',
	);
	
	/**
	 * @param KalturaMetadataObjectType $objectType
	 *
	 * @return IMetadataPeer
	 */
	protected static function getObjectPeer($objectType)
	{
		switch ($objectType)
		{
		    case MetadataObjectType::ENTRY:
		        return new MetadataEntryPeer();
		        
		    case MetadataObjectType::CATEGORY:
		        return new MetadataCategoryPeer();
		        
		    case MetadataObjectType::PARTNER:
		        return new MetadataPartnerPeer();
		        
		    case MetadataObjectType::USER:
		        return new MetadataKuserPeer();
		        
			default:
				return KalturaPluginManager::loadObject('IMetadataPeer', $objectType);
		}
	}
	
	/**
	 * @param Metadata $object
	 *
	 * @return BaseObject returns the object referenced by the peer
	 */
	public static function getObjectFromPeer(Metadata $metadata)
	{
		$objectType = $metadata->getObjectType();
		$peer = self::getObjectPeer($objectType);
		if(!$peer)
			return null;
			
		return $peer->retrieveByPK($metadata->getObjectId());
	}
	
	/**
	 * Returns values from the metadata object according to the xPath
	 * @param Metadata $metadata
	 * @param string $xPathPattern
	 * @return array
	 */
	public static function parseMetadataValues(Metadata $metadata, $xPathPattern, $version = null)
	{
		$key = $metadata->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA, $version);
		$source = kFileSyncUtils::file_get_contents($key, true, false);
		if(!$source)
			return null;
		
		$xml = new KDOMDocument();
		$xml->loadXML($source);
		
		if(preg_match('/^\w[\w\d]*$/', $xPathPattern))
			$xPathPattern = "//$xPathPattern";
		
		$matches = null;
		if(preg_match_all('/\/(\w[\w\d]*)/', $xPathPattern, $matches))
		{
			if(count($matches) == 2 && implode('', $matches[0]) == $xPathPattern)
			{
				$xPathPattern = '';
				foreach($matches[1] as $match)
					$xPathPattern .= "/*[local-name()='$match']";
			}
		}
		
		$xPath = new DOMXPath($xml);
		$elementsList = $xPath->query($xPathPattern);
		$values = array();
		foreach($elementsList as $element)
		{
			/* @var $element DOMNode */
			$values[] = $element->textContent;
		}

		return $values;
	}
	
	/**
	 * Parse the XSD and update the list of search fields
	 *
	 * @param MetadataProfile $metadataProfile
	 * @param partnerId
	 *
	 * @return TBD
	 */
	public static function parseProfileSearchFields($partnerId, MetadataProfile $metadataProfile)
	{
		$key = $metadataProfile->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_DEFINITION);
		$xsdPath = kFileSyncUtils::getLocalFilePathForKey($key);
		
		$xPaths = kXsd::findXpathsByAppInfo($xsdPath, self::APP_INFO_SEARCH, 'true');
		
		MetadataProfileFieldPeer::setUseCriteriaFilter(false);
		$profileFields = MetadataProfileFieldPeer::retrieveByMetadataProfileId($metadataProfile->getId());
		MetadataProfileFieldPeer::setUseCriteriaFilter(true);
		
		// check all existing fields
		foreach($profileFields as $profileField)
		{
			$xPath = $profileField->getXpath();
			
			// field removed
			if(!isset($xPaths[$xPath]))
			{
				$profileField->setStatus(MetadataProfileField::STATUS_DEPRECATED);
				$profileField->save();
				continue;
			}
			
			$xPathData = $xPaths[$xPath];
			
			if($profileField->getStatus() != MetadataProfileField::STATUS_ACTIVE &&
				isset($xPathData['type']) &&
				($xPathData['type'] == MetadataSearchFilter::KMC_FIELD_TYPE_DATE ||
				 $xPathData['type'] == MetadataSearchFilter::KMC_FIELD_TYPE_INT))
			{
				$availableSearchIndex = self::getAvailableSearchIndex($partnerId, $metadataProfile->getObjectType());
				if (!isset($availableSearchIndex))
					throw new Exception('could not find available search index');
										
				$profileField->setSearchIndex($availableSearchIndex);
			}
			
			$profileField->setStatus(MetadataProfileField::STATUS_ACTIVE);
			$profileField->setMetadataProfileVersion($metadataProfile->getVersion());
			if(isset($xPathData['name']))
				$profileField->setKey($xPathData['name']);
			if(isset($xPathData['label']))
				$profileField->setLabel($xPathData['label']);
			if(isset($xPathData['type']))
				$profileField->setType($xPathData['type']);
			
			
				
			$profileField->save();
			
			unset($xPaths[$xPath]);
		}
		
		// add new searchable fields
		
		foreach($xPaths as $xPath => $xPathData)
		{
			$profileField = new MetadataProfileField();
			$profileField->setMetadataProfileId($metadataProfile->getId());
			$profileField->setMetadataProfileVersion($metadataProfile->getVersion());
			$profileField->setPartnerId($metadataProfile->getPartnerId());
			$profileField->setStatus(MetadataProfileField::STATUS_ACTIVE);
			$profileField->setXpath($xPath);
			
			if(isset($xPathData['name']))
				$profileField->setKey($xPathData['name']);
			if(isset($xPathData['label']))
				$profileField->setLabel($xPathData['label']);
			if(isset($xPathData['type']))
			{
				$profileField->setType($xPathData['type']);
				
				if (($xPathData['type'] == MetadataSearchFilter::KMC_FIELD_TYPE_DATE) ||
				    ($xPathData['type'] == MetadataSearchFilter::KMC_FIELD_TYPE_INT)){
					$availableSearchIndex = self::getAvailableSearchIndex($partnerId, $metadataProfile->getObjectType());
					if (!isset($availableSearchIndex))
						throw new Exception('could not find available search index');
										
					$profileField->setSearchIndex($availableSearchIndex);
				}
				
				$profileField->save();
			}
		}
	
		// set none searchable existing fields
		$xPaths = kXsd::findXpathsByAppInfo($xsdPath, self::APP_INFO_SEARCH, 'false');
		foreach($profileFields as $profileField)
		{
			$xPath = $profileField->getXpath();
			if(!isset($xPaths[$xPath]))
				continue;
				
			$xPathData = $xPaths[$xPath];
			if(isset($xPathData['name']))
				$profileField->setKey($xPathData['name']);
			if(isset($xPathData['label']))
				$profileField->setLabel($xPathData['label']);
			if(isset($xPathData['type']))
				$profileField->setType($xPathData['type']);
				
			$profileField->setStatus(MetadataProfileField::STATUS_NONE_SEARCHABLE);
			$profileField->setMetadataProfileVersion($metadataProfile->getVersion());
			$profileField->save();
			
			unset($xPaths[$xPath]);
		}
		
		// add new none searchable fields
		foreach($xPaths as $xPath => $xPathData)
		{
			$profileField = new MetadataProfileField();
			$profileField->setMetadataProfileId($metadataProfile->getId());
			$profileField->setMetadataProfileVersion($metadataProfile->getVersion());
			$profileField->setPartnerId($metadataProfile->getPartnerId());
			$profileField->setStatus(MetadataProfileField::STATUS_NONE_SEARCHABLE);
			$profileField->setXpath($xPath);
			
			if(isset($xPathData['name']))
				$profileField->setKey($xPathData['name']);
			if(isset($xPathData['label']))
				$profileField->setLabel($xPathData['label']);
			if(isset($xPathData['type']))
				$profileField->setType($xPathData['type']);

			$profileField->save();
		}
	}
	
	/**
	 * Return search index by type
	 *
	 * @param int $partnerId
	 * @param int $kmcType
	 *
	 * @return int
	 */
	public static function getAvailableSearchIndex($partnerId, $objectType)
	{
		$profileFields = MetadataProfileFieldPeer::retrieveIndexableByPartnerAndType($partnerId, $objectType);
		
		$occupiedIndexes = array();
		foreach($profileFields as $profileField)
			$occupiedIndexes[$profileField->getSearchIndex()] = true;
			
		$fieldsLimit =  MetadataPlugin::getAdditionalSearchableFieldsLimit($partnerId, $objectType);
		
		for ($i = 0; $i < $fieldsLimit; $i++)
		{
			if(!isset($occupiedIndexes[$i]))
				return $i;
		}
		
		throw new APIException(MetadataErrors::EXCEEDED_ADDITIONAL_SEARCHABLE_FIELDS_LIMIT, $fieldsLimit);
	}
	
	/**
	 * Return search texts per object id
	 *
	 * @param int $objectType
	 * @param string $objectId
	 *
	 * @return array
	 */
	public static function getSearchValuesByObject($objectType, $objectId)
	{
		$metadatas = MetadataPeer::retrieveAllByObject($objectType, $objectId);
		KalturaLog::debug("Found " . count($metadatas) . " metadata object");
		
		$dataFieldName = MetadataPlugin::getSphinxFieldName(MetadataPlugin::SPHINX_EXPANDER_FIELD_DATA);
		
		$searchValues = array();
		foreach($metadatas as $metadata)
			$searchValues = self::getDataSearchValues($metadata, $searchValues);
		
		if (isset($searchValues[$dataFieldName]))
			$searchValues[$dataFieldName] = implode(' ', $searchValues[$dataFieldName]);
		
		return $searchValues;
	}
	
	/**
	 * Parse the XML and update the list of search values
	 *
	 * @param Metadata $metadata
	 * @param array $searchValues
	 *
	 * @return array
	 */
	public static function getDataSearchValues(Metadata $metadata, $searchValues = array())
	{
		KalturaLog::debug("Parsing metadata [" . $metadata->getId() . "] search values");
		$searchTexts = array();
		if (isset($searchValues[MetadataPlugin::getSphinxFieldName(MetadataPlugin::SPHINX_EXPANDER_FIELD_DATA)])){
			foreach ($searchValues[MetadataPlugin::getSphinxFieldName(MetadataPlugin::SPHINX_EXPANDER_FIELD_DATA)] as $DataSerachValue)
				$searchTexts[] = $DataSerachValue;
		}
		
		$key = $metadata->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA);
		$xmlPath = kFileSyncUtils::getLocalFilePathForKey($key);
		
		try{
			$xml = new KDOMDocument();
			$xml->load($xmlPath);
			$xPath = new DOMXPath($xml);
		}
		catch (Exception $ex)
		{
			KalturaLog::err('Could not load metadata xml [' . $xmlPath . '] - ' . $ex->getMessage());
			return '';
		}
					
		$profileFields = MetadataProfileFieldPeer::retrieveActiveByMetadataProfileId($metadata->getMetadataProfileId());
	
		$searchItems = array();
		$textItems = array();
		foreach($profileFields as $profileField)
		{
			/* @var  $profileField MetadataProfileField */
			$nodes = $xPath->query($profileField->getXpath());
			if(!$nodes->length)
				continue;

			if($profileField->getType() == MetadataSearchFilter::KMC_FIELD_TYPE_DATE ||
			   $profileField->getType() == MetadataSearchFilter::KMC_FIELD_TYPE_INT){
				foreach($nodes as $node){
					if(!is_null($profileField->getSearchIndex()))
						$searchValues[MetadataPlugin::getSphinxFieldName(MetadataPlugin::SPHINX_EXPENDER_FIELD_INT) . $profileField->getSearchIndex()] = $node->nodeValue;
					break;
				}
				continue;
			}

			$searchItemValues = array();
			foreach($nodes as $node)
				$searchItemValues[] = $node->nodeValue;
				
			if(!count($searchItemValues))
				continue;

			if($profileField->getType() == MetadataSearchFilter::KMC_FIELD_TYPE_TEXT)
			{
				$textItems[] = implode(' ', $searchItemValues);

				$searchItems[$profileField->getId()] = array();
				foreach ($searchItemValues as $searchItemValue)
				{
					if(iconv_strlen($searchItemValue, 'UTF-8') >= 128)
						continue;
						
					$searchItems[$profileField->getId()][] = $searchItemValue;
				}
			}
			else
			{
				$searchItems[$profileField->getId()] = $searchItemValues;
			}
		}
		
		foreach($searchItems as $key => $searchItem)
			foreach($searchItem as $searchPhrase)
				$searchTexts[] = MetadataPlugin::PLUGIN_NAME . '_' . "$key $searchPhrase " . kMetadataManager::SEARCH_TEXT_SUFFIX . '_' . $key;
				
	 	if(count($textItems))
	 		$searchTexts['text'] = MetadataSearchFilter::createSphinxSearchCondition($metadata->getPartnerId(), implode(' ', $textItems) , true);
		
		$ret = array();
		foreach($searchTexts as $index => $value)
			if(is_int($index))
				$ret[$index] = $value;
		
		if(isset($searchTexts['text']))
			$ret['text'] = $searchTexts['text'];
		
		$searchValues[MetadataPlugin::getSphinxFieldName(MetadataPlugin::SPHINX_EXPANDER_FIELD_DATA)] = $ret;
		
		return $searchValues;
	}
	
	/**
	 * Check if transforming required and create job if needed
	 *
	 * @param MetadataProfile $metadataProfile
	 * @param int $prevVersion
	 * @param string $prevXsd
	 */
	public static function diffMetadataProfile(MetadataProfile $metadataProfile, $prevVersion, $prevXsd, $newVersion, $newXsd)
	{
		$xsl = true;
		if(!PermissionPeer::isValidForPartner(MetadataPermissionName::FEATURE_METADATA_NO_VALIDATION, $metadataProfile->getPartnerId()))
		{
			$xsl = kXsd::compareXsd($prevXsd, $newXsd);
		}
			
		if($xsl === true)
			return self::upgradeMetadataObjects($metadataProfile->getId(), $prevVersion, $newVersion);

		if(PermissionPeer::isValidForPartner(MetadataPermissionName::FEATURE_METADATA_NO_TRANSFORMATION, $metadataProfile->getPartnerId()))
			throw new kXsdException(kXsdException::TRANSFORMATION_REQUIRED);
			
		return self::addTransformMetadataJob($metadataProfile->getPartnerId(), $metadataProfile->getId(), $prevVersion, $newVersion, $xsl);
	}

	/**
	 * batch getTransformMetadataObjects action retrieve all metadata objects that requires upgrade and the total count
	 *
	 * @param int $metadataProfileId The id of the metadata profile
	 * @param int $srcVersion The old metadata profile version
	 * @param int $destVersion The new metadata profile version
	 */
	private static function upgradeMetadataObjects($metadataProfileId, $srcVersion, $destVersion)
	{
		$affectedRows = null;
		do
		{
			$table = MetadataPeer::TABLE_NAME;
			$colId = MetadataPeer::ID;
			$colMetadataProfileId = MetadataPeer::METADATA_PROFILE_ID;
			$colMetadataProfileVersion = MetadataPeer::METADATA_PROFILE_VERSION;
			$colStatus = MetadataPeer::STATUS;
			$validStatus = Metadata::STATUS_VALID;
			
			$sql = "UPDATE $table ";
			$sql .= "SET $colMetadataProfileVersion = $destVersion ";
			$sql .= "WHERE $colMetadataProfileId = $metadataProfileId ";
			$sql .= "AND $colMetadataProfileVersion = $srcVersion ";
			$sql .= "AND $colStatus = $validStatus ";
			$sql .= "ORDER BY $colId ";
			$sql .= "LIMIT 10000";
			
			$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_MASTER);
			$affectedRows = $con->exec($sql);
			KalturaLog::debug("Affected rows [$affectedRows]");
		}
		while($affectedRows);
	}
	
	/**
	 * Validate the XML against the profile XSD and set the metadata status
	 *
	 * @param int $metadataProfileId
	 * @param string $metadata
	 * @param string $errorMessage
	 * @param int $metadataProfileVersion leave it null to use the latest
	 *
	 * returns bool
	 */
	public static function validateMetadata($metadataProfileId, $metadata, &$errorMessage, $metadataProfileVersion = null)
	{
		KalturaLog::debug("Validating metadata [$metadata]");
		$metadataProfile = MetadataProfilePeer::retrieveByPK($metadataProfileId);
		if(!$metadataProfile)
		{
			$errorMessage = "Metadata profile [$metadataProfileId] not found";
			KalturaLog::err($errorMessage);
			return false;
		}
		
		$metadataProfileKey = $metadataProfile->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_DEFINITION, $metadataProfileVersion);
		$xsdData = kFileSyncUtils::file_get_contents($metadataProfileKey, true, false);
		if(!$xsdData)
		{
			$errorMessage = "Metadata profile xsd not found";
			KalturaLog::err($errorMessage);
			return false;
		}
			
		libxml_use_internal_errors(true);
		libxml_clear_errors();
		$xml = new KDOMDocument();
		$xml->loadXML($metadata);
		if($xml->schemaValidateSource($xsdData))
		{
			KalturaLog::debug("Metadata is valid");
			return true;
		}
		
		$errorMessage = kXml::getLibXmlErrorDescription($metadata);
		KalturaLog::err("Metadata is invalid:\n$errorMessage");
		return false;
	}
	
	/**
	 * @param Metadata $metadata
	 * @param KalturaMetadataStatus $status
	 *
	 * returns metadata status
	 */
	protected static function setMetadataStatus(Metadata $metadata, $status, $metadataProfileVersion = null)
	{
		if($metadataProfileVersion)
			$metadata->setMetadataProfileVersion($metadataProfileVersion);
			
		$metadata->setStatus($status);
		$metadata->save();
		
		return $status;
	}
	
	/**
	 * @param KalturaMetadataObjectType $objectType
	 *
	 * @return string
	 */
	public static function getObjectTypeName($objectType)
	{
		if(isset(self::$objectTypeNames[$objectType]))
			return self::$objectTypeNames[$objectType];
			
		return KalturaPluginManager::getObjectClass('IMetadataObject', $objectType);
	}
	
	/**
	 * Function returns the required Metadata object type for the given object's class name
	 * @param BaseObject $object
	 * @return int
	 */
	public static function getTypeNameFromObject (BaseObject $object)
	{
	    foreach (self::$objectTypeNames as $objectType => $objectClassName)
	    {
	        if($object instanceof  $objectClassName)

	            return $objectType;
	    }
	    
	    if($object instanceof IMetadataObject)
	    	return $object->getMetadataObjectType();
	    	
	    return MetadataObjectType::ENTRY;
	}
	
	/**
	 * @param int $metadataId
	 * @param string $url
	 *
	 * @return BatchJob
	 */
	public static function addImportMetadataJob($partnerId, $metadataId, $url)
	{
		$job = new BatchJob();
		$job->setPartnerId($partnerId);
		$job->setObjectType(kPluginableEnumsManager::apiToCore('BatchJobObjectType', MetadataBatchJobObjectType::METADATA));
		$job->setObjectId($metadataId);
		
		$data = new kImportMetadataJobData();
		$data->setMetadataId($metadataId);
		$data->setSrcFileUrl($url);
		
		return kJobsManager::addJob($job, $data, BatchJobType::METADATA_IMPORT);
	}
	
	/**
	 * @param int $metadataProfileId
	 * @param int $srcVersion
	 * @param int $destVersion
	 * @param string $xsl
	 *
	 * @return BatchJob
	 */
	private static function addTransformMetadataJob($partnerId, $metadataProfileId, $srcVersion, $destVersion, $xsl = null)
	{
		// check if any metadata objects require the transform
		$c = new Criteria();
		$c->add(MetadataPeer::METADATA_PROFILE_ID, $metadataProfileId);
		$c->add(MetadataPeer::METADATA_PROFILE_VERSION, $destVersion, Criteria::LESS_THAN);
		$c->add(MetadataPeer::STATUS, Metadata::STATUS_VALID);
		$metadataCount = MetadataPeer::doCount($c);
		if(!$metadataCount)
			return null;
		
		$job = new BatchJob();
		$job->setJobType(BatchJobType::METADATA_TRANSFORM);
		$job->setPartnerId($partnerId);
		$job->setObjectId($metadataProfileId);
		$job->setObjectType(kPluginableEnumsManager::apiToCore('BatchJobObjectType', MetadataBatchJobObjectType::METADATA_PROFILE));
		$data = new kTransformMetadataJobData();
		
		if($xsl)
		{
			$job->save();
			$key = $job->getSyncKey(BatchJob::FILE_SYNC_BATCHJOB_SUB_TYPE_CONFIG);
			kFileSyncUtils::file_put_contents($key, $xsl);
			
			$xslPath = kFileSyncUtils::getLocalFilePathForKey($key);
			$data->setSrcXslPath($xslPath);
		}
		
		$data->setMetadataProfileId($metadataProfileId);
		$data->setSrcVersion($srcVersion);
		$data->setDestVersion($destVersion);
		
		return kJobsManager::addJob($job, $data, BatchJobType::METADATA_TRANSFORM);
	}
	
	/*
	 * validate metadataProfile xsd
	 * @param int partner id
	 * @param string $xsdData XSD metadata definition
	 * @param boolean isPath - for xsdPath or actual content
	 */
	public static function validateMetadataProfileField( $partnerId, $xsdData, $isPath, $obejctType, $metadataProfileId = null)
	{
		$additionalSearchableFieldsCounter = 0;
			
		$profileFields = MetadataProfileFieldPeer::retrieveIndexableByPartnerAndType($partnerId, $obejctType);
		
		foreach($profileFields as $profileField)
		{
			if ($profileField->getMetadataProfileId() == $metadataProfileId)
				continue;
				
			$type = $profileField->getType();
			if($type == MetadataSearchFilter::KMC_FIELD_TYPE_DATE || $type == MetadataSearchFilter::KMC_FIELD_TYPE_INT)
				$additionalSearchableFieldsCounter++;
		}
		
		$xPaths = kXsd::findXpathsByAppInfo($xsdData , kMetadataManager::APP_INFO_SEARCH, 'true', $isPath);
		foreach($xPaths as $xPath => $xPathData)
		{
			if(isset($xPathData['type']) && (($xPathData['type'] == MetadataSearchFilter::KMC_FIELD_TYPE_DATE) ||
											 ($xPathData['type'] == MetadataSearchFilter::KMC_FIELD_TYPE_INT)))
				$additionalSearchableFieldsCounter++;
		}

		$partnerLimit = MetadataPlugin::getAdditionalSearchableFieldsLimit($partnerId, $obejctType);
		if ($additionalSearchableFieldsCounter > $partnerLimit)
			throw new APIException(MetadataErrors::EXCEEDED_ADDITIONAL_SEARCHABLE_FIELDS_LIMIT, $partnerLimit);
	}
}