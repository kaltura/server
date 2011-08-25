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
		Metadata::TYPE_ENTRY => 'entry',
	);
	
	/**
	 * @param KalturaMetadataObjectType $objectType
	 * 
	 * @return iMetadataPeer
	 */
	public static function getObjectPeer($objectType)
	{
		if($objectType == Metadata::TYPE_ENTRY)
			return new MetadataEntryPeer();
		
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaMetadataObjects');
		foreach($pluginInstances as $pluginInstance)
		{
			/* @var $pluginInstance IKalturaMetadataObjects */
			$peer = $pluginInstance->getObjectPeer($objectType);
			if($peer)
				return $peer;
		}
		return null;
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
			
		$objectId = $metadata->getObjectId();
		return $peer->retrieveByPK($objectId);
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
				
				if (($xPathData['type'] == MetadataSearchFilter::KMC_FIELD_TYPE_DATE) || ($xPathData['type'] == MetadataSearchFilter::KMC_FIELD_TYPE_INT)){
					$availableSearchIndex = self::getAvailableSearchIndex($partnerId, $xPathData['type']);
					if (!isset($availableSearchIndex))
						throw new Exception('could not find available search index for type: ' . $xPathData['type']);
										
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
	public static function getAvailableSearchIndex($partnerId, $type)
	{
		$profileFields = MetadataProfileFieldPeer::retrieveByPartnerAndActive($partnerId);
		
		$occupiedIndexes = array();	
		foreach($profileFields as $profileField)
			$occupiedIndexes[$profileField->getType()][$profileField->getSearchIndex()] = true;
			
		$fieldsLimit =  MetadataPlugin::getSphinxLimitField($type);
		
		for ($i = 0; $i < $fieldsLimit; $i++)
		{
			if(!isset($occupiedIndexes[$type]))
				return $i;
			
			if(!isset($occupiedIndexes[$type][$i]))
				return $i;
		}
		
		throw new Exception('could not find available search index for type: ' . $type);
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
		
		if(count($searchValues) && count($searchValues[$dataFieldName]))
			$searchValues[$dataFieldName] = implode(',', $searchValues[$dataFieldName]);
		
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
		if (isset($searchValues[MetadataPlugin::getSphinxFieldName(MetadataPlugin::SPHINX_EXPANDER_FIELD_DATA)])){
			foreach ($searchValues[MetadataPlugin::getSphinxFieldName(MetadataPlugin::SPHINX_EXPANDER_FIELD_DATA)] as $DataSerachValue)
				$searchTexts[] = $DataSerachValue;
		}
		
		$key = $metadata->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA);
		$xmlPath = kFileSyncUtils::getLocalFilePathForKey($key);
		
		$xml = new DOMDocument();
		$xml->load($xmlPath);
		$xPath = new DOMXPath($xml);
		
		$profileFields = MetadataProfileFieldPeer::retrieveActiveByMetadataProfileId($metadata->getMetadataProfileId());
	
		$searchItems = array();
		$textItems = array();
		foreach($profileFields as $profileField)
		{
			/* @var  $profileField MetadataProfileField */
			$nodes = $xPath->query($profileField->getXpath());
			if(!$nodes->length)
				continue;

			if($profileField->getType() == MetadataSearchFilter::KMC_FIELD_TYPE_DATE){
				foreach($nodes as $node){
					$searchValues[MetadataPlugin::getSphinxFieldName(MetadataPlugin::SPHINX_EXPENDER_FIELD_DATE) . $profileField->getSearchIndex()] = $node->nodeValue;
					break;
				}
				continue;
			}

			if($profileField->getType() == MetadataSearchFilter::KMC_FIELD_TYPE_INT){
				foreach($nodes as $node){
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
				$textItem = implode(' ', $searchItemValues);
				$textItems[] = $textItem;
				
				if(iconv_strlen($textItem, 'UTF-8') < 128) 
					$searchItems[$profileField->getId()] = $searchItemValues;
			}
			else
			{
				$searchItems[$profileField->getId()] = $searchItemValues;
			}
		}
		
		foreach($searchItems as $key => $searchItem)
			foreach($searchItem as $searchPhrase)
				$searchTexts[] = MetadataPlugin::PLUGIN_NAME . '_' . "$key $searchPhrase " . kMetadataManager::SEARCH_TEXT_SUFFIX;
				
		if(count($textItems))
		{
			if(!isset($searchTexts['text']))
				$searchTexts['text'] = MetadataPlugin::PLUGIN_NAME . '_text';
				 
			$searchTexts['text'] .= ' ' . implode(' ', $textItems);
			$searchTexts['text'] .= ' ' . kMetadataManager::SEARCH_TEXT_SUFFIX;
		}
		
		
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
	 * @param string $prevXsdPath
	 * 
	 * @return BatchJob
	 */
	public static function diffMetadataProfile(MetadataProfile $metadataProfile, $prevVersion, $prevXsdPath, $newVersion, $newXsdPath)
	{
		$xsl = kXsd::compareXsd($prevXsdPath, $newXsdPath);
		if(!$xsl)
			return;
			
		if(is_bool($xsl))
			return self::addTransformMetadataJob($metadataProfile->getPartnerId(), $metadataProfile->getId(), $prevVersion, $newVersion);
		
		return self::addTransformMetadataJob($metadataProfile->getPartnerId(), $metadataProfile->getId(), $prevVersion, $newVersion, $xsl);
	}
	
	/**
	 * Validate the XML against the profile XSD and set the metadata status
	 * 
	 * @param Metadata $metadata
	 * 
	 * returns metadata status
	 */
	public static function validateMetadata(Metadata $metadata, &$errorMessage)
	{
		KalturaLog::debug('Validating metadata [' . $metadata->getId() . ']');
		$metadataProfile = $metadata->getMetadataProfile();
		if(!$metadataProfile)
		{
			$errorMessage = 'Metadata profile [' . $metadata->getMetadataProfileId() . '] not found';
			KalturaLog::err($errorMessage);
			return self::setMetadataStatus($metadata, Metadata::STATUS_INVALID);
		}
		
		$metadataKey = $metadata->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA);
		$xmlPath = kFileSyncUtils::getLocalFilePathForKey($metadataKey);
		if(!file_exists($xmlPath))
		{
			$errorMessage = "Metadata xml [$xmlPath] not found";
			KalturaLog::err($errorMessage);
			return self::setMetadataStatus($metadata, Metadata::STATUS_INVALID);
		}
			
		$metadataProfileKey = $metadataProfile->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_DEFINITION);
		$xsdPath = kFileSyncUtils::getLocalFilePathForKey($metadataProfileKey);
		if(!file_exists($xsdPath))
		{
			$errorMessage = "Metadata profile xsd [$xsdPath] not found";
			KalturaLog::err($errorMessage);
			return self::setMetadataStatus($metadata, Metadata::STATUS_INVALID);
		}
			
		libxml_use_internal_errors(true);
		libxml_clear_errors();
		$xml = new DOMDocument();
		$xml->load($xmlPath);
		if($xml->schemaValidate($xsdPath))
		{
			KalturaLog::debug("Metadata is valid");
			return self::setMetadataStatus($metadata, Metadata::STATUS_VALID, $metadataProfile->getVersion());
		}
		
		$errorMessage = kXml::getLibXmlErrorDescription(file_get_contents($xmlPath));
		KalturaLog::debug("Metadata is invalid:\n$errorMessage");
		return self::setMetadataStatus($metadata, Metadata::STATUS_INVALID);
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
			
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaMetadataObjects');
		foreach($pluginInstances as $pluginInstance)
		{
			/* @var $pluginInstance IKalturaMetadataObjects */
			$className = $pluginInstance->getObjectClassName($objectType);
			if($className)
			{
				self::$objectTypeNames[$objectType] = $className;
				return $className;
			}
		}
		
		return null;
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
	public static function addTransformMetadataJob($partnerId, $metadataProfileId, $srcVersion, $destVersion, $xsl = null)
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
		$job->setPartnerId($partnerId);
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
	public static function validateMetadataProfileField( $partnerId, $xsdData, $isPath  = false){
		$intFieldsCounter = 0;
		$dateFieldsCounter = 0;
		
		MetadataProfileFieldPeer::setUseCriteriaFilter(false);
		$profileFields = MetadataProfileFieldPeer::retrieveByPartnerAndActive($partnerId);
		MetadataProfileFieldPeer::setUseCriteriaFilter(true);
		
		foreach($profileFields as $profileField)
		{		
			$type = $profileField->getType();
			if($type == MetadataSearchFilter::KMC_FIELD_TYPE_DATE)
				$dateFieldsCounter++;
				
			if($type == MetadataSearchFilter::KMC_FIELD_TYPE_INT)
				$intFieldsCounter++;
		}

		$xPaths = kXsd::findXpathsByAppInfo($xsdData , kMetadataManager::APP_INFO_SEARCH, 'true', $isPath);
		foreach($xPaths as $xPath => $xPathData)
		{		
			if(isset($xPathData['type']) && ($xPathData['type'] == MetadataSearchFilter::KMC_FIELD_TYPE_DATE))
				$dateFieldsCounter++;
				
			if(isset($xPathData['type']) && ($xPathData['type'] == MetadataSearchFilter::KMC_FIELD_TYPE_INT))
				$intFieldsCounter++;
		}
		
		if ($dateFieldsCounter > MetadataPlugin::getSphinxLimitField(MetadataSearchFilter::KMC_FIELD_TYPE_DATE))
			throw new APIException(MetadataErrors::EXCEEDED_LIMIT_SEARCHABLE_DATES);
			
		if ($intFieldsCounter > MetadataPlugin::getSphinxLimitField(MetadataSearchFilter::KMC_FIELD_TYPE_INT))
			throw new APIException(MetadataErrors::EXCEEDED_LIMIT_SEARCHABLE_INTS);
	}
}