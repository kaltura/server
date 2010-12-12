<?php
class MetadataPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaServices, IKalturaEventConsumers, IKalturaObjectLoader, IKalturaBulkUploadHandler, IKalturaSearchDataContributor
{
	const PLUGIN_NAME = 'metadata';
	const METADATA_FLOW_MANAGER_CLASS = 'kMetadataFlowManager';
	const METADATA_COPY_HANDLER_CLASS = 'kMetadataObjectCopiedHandler';
	const METADATA_DELETE_HANDLER_CLASS = 'kMetadataObjectDeletedHandler';
	
	const BULK_UPLOAD_COLUMN_PROFILE_ID = 'metadataProfileId';
	const BULK_UPLOAD_COLUMN_XML = 'metadataXml';
	const BULK_UPLOAD_COLUMN_URL = 'metadataUrl';
	const BULK_UPLOAD_COLUMN_FIELD_PREFIX = 'metadataField_';
	const BULK_UPLOAD_MULTI_VALUES_DELIMITER = '|,|';
	
	const BULK_UPLOAD_DATE_FORMAT = '%Y-%m-%dT%H:%i:%s';

	public function getInstance($interface)
	{
		if($this instanceof $interface)
			return $this;
			
		if($interface == 'IKalturaMrssContributor')
			return kMetadataMrssManager::get();
			
		return null;
	}
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	public static function isAllowedPartner($partnerId)
	{
		if($partnerId == Partner::BATCH_PARTNER_ID)
			return true;
			
		$partner = PartnerPeer::retrieveByPK($partnerId);
		return $partner->getPluginEnabled(self::PLUGIN_NAME);
	}
	
	/**
	 * @return array<string,string> in the form array[serviceName] = serviceClass
	 */
	public static function getServicesMap()
	{
		$map = array(
			'metadata' => 'MetadataService',
			'metadataProfile' => 'MetadataProfileService',
			'metadataBatch' => 'MetadataBatchService',
		);
		return $map;
	}
	
	/**
	 * @return string - the path to services.ct
	 */
	public static function getServiceConfig()
	{
		return realpath(dirname(__FILE__).'/config/metadata.ct');
	}

	/**
	 * @return array
	 */
	public static function getEventConsumers()
	{
		return array(
			self::METADATA_FLOW_MANAGER_CLASS,
			self::METADATA_COPY_HANDLER_CLASS,
			self::METADATA_DELETE_HANDLER_CLASS,
		);
	}
	
	/**
	 * @param string $baseClass
	 * @param string $enumValue
	 * @param array $constructorArgs
	 * @return object
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		if($baseClass == 'ISyncableFile' && isset($constructorArgs['objectId']))
		{
			$objectId = $constructorArgs['objectId'];
			
			switch($enumValue)
			{
				case FileSyncObjectType::METADATA:
					MetadataPeer::setUseCriteriaFilter ( false );
					$object = MetadataPeer::retrieveByPK( $objectId );
					MetadataPeer::setUseCriteriaFilter ( true );
					return $object;
					
				case FileSyncObjectType::METADATA_PROFILE:
					MetadataProfilePeer::setUseCriteriaFilter ( false );
					$object = MetadataProfilePeer::retrieveByPK( $objectId );
					MetadataProfilePeer::setUseCriteriaFilter ( true );
					return $object;
			}
		}
		
		if($baseClass == 'kJobData')
		{
			switch($enumValue)
			{
				case KalturaBatchJobType::METADATA_IMPORT:
					return new kImportJobData();
					
				case KalturaBatchJobType::METADATA_TRANSFORM:
					return new kTransformMetadataJobData();
			}
		}
	
		if($baseClass == 'KalturaJobData')
		{
			switch($enumValue)
			{
				case KalturaBatchJobType::METADATA_IMPORT:
					return new KalturaImportJobData();
					
				case KalturaBatchJobType::METADATA_TRANSFORM:
					return new KalturaTransformMetadataJobData();
			}
		}
		
		return null;
	}
	
	/**
	 * @param string $baseClass
	 * @param string $enumValue
	 * @return string
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'ISyncableFile')
		{
			switch($enumValue)
			{
				case FileSyncObjectType::METADATA:
					return 'Metadata';
					
				case FileSyncObjectType::METADATA_PROFILE:
					return 'MetadataProfile';
			}
		}
		
		if($baseClass == 'kJobData')
		{
			switch($enumValue)
			{
				case KalturaBatchJobType::METADATA_IMPORT:
					return 'kImportJobData';
					
				case KalturaBatchJobType::METADATA_TRANSFORM:
					return 'kTransformMetadataJobData';
			}
		}
	
		if($baseClass == 'KalturaJobData')
		{
			switch($enumValue)
			{
				case KalturaBatchJobType::METADATA_IMPORT:
					return 'KalturaImportJobData';
					
				case KalturaBatchJobType::METADATA_TRANSFORM:
					return 'KalturaTransformMetadataJobData';
			}
		}
		
		return null;
	}

	
	/**
	 * @param array $fields
	 * @return string
	 */
	private static function getDateFormatRegex(&$fields = null)
	{
		$replace = array(
			'%Y' => '([1-2][0-9]{3})',
			'%m' => '([0-1][0-9])',
			'%d' => '([0-3][0-9])',
			'%H' => '([0-2][0-9])',
			'%i' => '([0-5][0-9])',
			'%s' => '([0-5][0-9])',
//			'%T' => '([A-Z]{3})',
		);
	
		$fields = array();
		$arr = null;
//		if(!preg_match_all('/%([YmdTHis])/', self::BULK_UPLOAD_DATE_FORMAT, $arr))
		if(!preg_match_all('/%([YmdHis])/', self::BULK_UPLOAD_DATE_FORMAT, $arr))
			return false;
	
		$fields = $arr[1];
		
		return '/' . str_replace(array_keys($replace), $replace, self::BULK_UPLOAD_DATE_FORMAT) . '/';
	}
	
	/**
	 * @param string $str
	 * @return int
	 */
	private static function parseFormatedDate($str)
	{
		KalturaLog::debug("parseFormatedDate($str)");
		
		if(function_exists('strptime'))
		{
			$ret = strptime($str, self::BULK_UPLOAD_DATE_FORMAT);
			if($ret)
			{
				KalturaLog::debug("Formated Date [$ret] " . date('Y-m-d\TH:i:s', $ret));
				return $ret;
			}
		}
			
		$fields = null;
		$regex = self::getDateFormatRegex($fields);
		
		$values = null;
		if(!preg_match($regex, $str, $values))
			return null;
			
		$hour = 0;
		$minute = 0;
		$second = 0;
		$month = 0;
		$day = 0;
		$year = 0;
		$is_dst = 0;
		
		foreach($fields as $index => $field)
		{
			$value = $values[$index + 1];
			
			switch($field)
			{
				case 'Y':
					$year = intval($value);
					break;
					
				case 'm':
					$month = intval($value);
					break;
					
				case 'd':
					$day = intval($value);
					break;
					
				case 'H':
					$hour = intval($value);
					break;
					
				case 'i':
					$minute = intval($value);
					break;
					
				case 's':
					$second = intval($value);
					break;
					
//				case 'T':
//					$date = date_parse($value);
//					$hour -= ($date['zone'] / 60);
//					break;
					
			}
		}
		
		KalturaLog::debug("gmmktime($hour, $minute, $second, $month, $day, $year)");
		$ret = gmmktime($hour, $minute, $second, $month, $day, $year);
		if($ret)
		{
			KalturaLog::debug("Formated Date [$ret] " . date('Y-m-d\TH:i:s', $ret));
			return $ret;
		}
			
		KalturaLog::debug("Formated Date [null]");
		return null;
	}
		
	/**
	 * @param string $entryId the new created entry
	 * @param array $data key => value pairs
	 */
	public static function handleBulkUploadData($entryId, array $data)
	{
		KalturaLog::debug("Handle metadata bulk upload data:\n" . print_r($data, true));
		
		if(!isset($data[self::BULK_UPLOAD_COLUMN_PROFILE_ID]))
			return;
			
		$metadataProfileId = $data[self::BULK_UPLOAD_COLUMN_PROFILE_ID];
		$xmlData = null;
		
		$entry = entryPeer::retrieveByPK($entryId);
		if(!$entry)
			return;
			
//		$criteriaFilter = FileSyncPeer::getCriteriaFilter();
//		$criteria = $criteriaFilter->getFilter();
//		$criteria->add(FileSyncPeer::PARTNER_ID, $entry->getPartnerId());
		
		$metadataProfile = MetadataProfilePeer::retrieveById($metadataProfileId);
		if(!$metadataProfile)
		{
			$errorMessage = "Metadata profile [$metadataProfileId] not found";
			KalturaLog::err($errorMessage);
			self::addBulkUploadResultDescription($entryId, $entry->getBulkUploadId(), $errorMessage);
			return;
		}
		
		if(isset($data[self::BULK_UPLOAD_COLUMN_URL]))
		{
			try{
				$xmlData = file_get_contents($data[self::BULK_UPLOAD_COLUMN_URL]);
				KalturaLog::debug("Metadata downloaded [" . $data[self::BULK_UPLOAD_COLUMN_URL] . "]");
			}
			catch(Exception $e)
			{
				$errorMessage = "Download metadata[" . $data[self::BULK_UPLOAD_COLUMN_URL] . "] error: " . $e->getMessage();
				KalturaLog::err($errorMessage);
				self::addBulkUploadResultDescription($entryId, $entry->getBulkUploadId(), $errorMessage);
				$xmlData = null;
			}
		}
		elseif(isset($data[self::BULK_UPLOAD_COLUMN_XML]))
		{
			$xmlData = $data[self::BULK_UPLOAD_COLUMN_XML];
		}
		else
		{
			$metadataProfileFields = array();
			MetadataProfileFieldPeer::setUseCriteriaFilter(false);
			$tmpMetadataProfileFields = MetadataProfileFieldPeer::retrieveByMetadataProfileId($metadataProfileId);
			MetadataProfileFieldPeer::setUseCriteriaFilter(true);
			
			foreach($tmpMetadataProfileFields as $metadataProfileField)
				$metadataProfileFields[$metadataProfileField->getKey()] = $metadataProfileField;
			
			KalturaLog::debug("Found fields [" . count($metadataProfileFields) . "] for metadata profile [$metadataProfileId]");
			$xml = new DOMDocument();
			$dataFound = false;
			
			foreach($data as $key => $value)
			{
				if(!$value || !strlen($value))
					continue;
					
				if(!preg_match('/^' . self::BULK_UPLOAD_COLUMN_FIELD_PREFIX . '(.+)$/', $key, $matches))
					continue;
					
				$key = $matches[1];
				if(!isset($metadataProfileFields[$key]))
				{
					$errorMessage = "Field [$key] does not exist";
					KalturaLog::debug($errorMessage);
					self::addBulkUploadResultDescription($entryId, $entry->getBulkUploadId(), $errorMessage);
					continue;
				}
				
				$metadataProfileField = $metadataProfileFields[$key];
				KalturaLog::debug("Found field [" . $metadataProfileField->getXpath() . "] for value [$value]");
				
				$fieldValues = explode(self::BULK_UPLOAD_MULTI_VALUES_DELIMITER, $value);
				foreach($fieldValues as $fieldValue)
				{
					if($metadataProfileField->getType() == MetadataSearchFilter::KMC_FIELD_TYPE_DATE && !is_numeric($fieldValue))
					{
						$value = self::parseFormatedDate($fieldValue);
						if(!$value || !strlen($value))
						{
							$errorMessage = "Could not parse date format [$fieldValue] for field [$key]";
							KalturaLog::debug($errorMessage);
							self::addBulkUploadResultDescription($entryId, $entry->getBulkUploadId(), $errorMessage);
							continue;
						}
							
						$fieldValue = $value;
					}
						
					self::addXpath($xml, $metadataProfileField->getXpath(), $fieldValue);
				}
					
				$dataFound = true;
			}
			
			if($dataFound)
			{
				$xmlData = $xml->saveXML($xml->firstChild);
				$xmlData = trim($xmlData, " \n\r\t");
			}
		}
		
		if(!$xmlData)
			return;
			
		$dbMetadata = new Metadata();
		
		$dbMetadata->setPartnerId($entry->getPartnerId());
		$dbMetadata->setMetadataProfileId($metadataProfileId);
		$dbMetadata->setMetadataProfileVersion($metadataProfile->getVersion());
		$dbMetadata->setObjectType(Metadata::TYPE_ENTRY);
		$dbMetadata->setObjectId($entryId);
		$dbMetadata->setStatus(Metadata::STATUS_INVALID);
		$dbMetadata->save();
		
		KalturaLog::debug("Metadata [" . $dbMetadata->getId() . "] saved [$xmlData]");
		
		$key = $dbMetadata->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA);
		kFileSyncUtils::file_put_contents($key, $xmlData);
		
		$errorMessage = '';
		$status = kMetadataManager::validateMetadata($dbMetadata, $errorMessage);
		if($status == Metadata::STATUS_VALID)
		{
			kEventsManager::raiseEvent(new kObjectDataChangedEvent($dbMetadata));
		}
		else
		{
			self::addBulkUploadResultDescription($entryId, $entry->getBulkUploadId(), $errorMessage);
		}
	}
	
	protected static function addBulkUploadResultDescription($entryId, $bulkUploadId, $description)
	{
		$bulkUploadResult = BulkUploadResultPeer::retrieveByEntryId($entryId, $bulkUploadId);
		if(!$bulkUploadResult)
		{
			KalturaLog::err("Bulk upload results not found for entry [$entryId]");
			return;
		}
		
		$msg = $bulkUploadResult->getErrorDescription();
		if($msg)
			$msg .= "\n";
		
		$msg .= $description;
			
		$bulkUploadResult->setErrorDescription($msg);
		$bulkUploadResult->save();
	}
	
	protected static function addXpath(DOMDocument &$xml, $xPath, $value)
	{
		KalturaLog::debug("add value [$value] to xPath [$xPath]");
		$xPaths = explode('/', $xPath);
		$currentNode = $xml;
		$currentXPath = '';
		foreach($xPaths as $index => $xPath)
		{
			if(!strlen($xPath))
			{
				KalturaLog::debug("xPath [/] already exists");
				continue;
			}
				
			$currentXPath .= "/$xPath";
			if($index + 1 < count($xPaths))
			{
				$domXPath = new DOMXPath($xml);
				$nodeList = $domXPath->query($currentXPath);
				
				if($nodeList && $nodeList->length)
				{
					$currentNode = $nodeList->item(0);
					KalturaLog::debug("xPath [$xPath] already exists");
					continue;
				}
			}
			
			if(!preg_match('/\*\[\s*local-name\(\)\s*=\s*\'([^\']+)\'\s*\]/', $xPath, $matches))
			{
				KalturaLog::err("Xpath [$xPath] doesn't match");
				return false;
			}
				
			$nodeName = $matches[1];
			if($index + 1 == count($xPaths))
			{
				KalturaLog::debug("Creating node [$nodeName] xPath [$xPath] with value [$value]");
				$valueNode = $xml->createElement($nodeName, $value);
			}
			else
			{
				KalturaLog::debug("Creating node [$nodeName] xPath [$xPath]");
				$valueNode = $xml->createElement($nodeName);				
			}
			KalturaLog::debug("Appending node [$nodeName] to current node [$currentNode->localName]");
			$currentNode->appendChild($valueNode);
			$currentNode = $valueNode;
		}
	}
	
	/**
	 * Return textual search data to be associated with the object
	 * 
	 * @param BaseObject $object
	 * @return string
	 */
	public static function getSearchData(BaseObject $object)
	{
		if($object instanceof entry)
		{
			if(self::isAllowedPartner($object->getPartnerId()))
				return kMetadataManager::getSearchValuesByObject(Metadata::TYPE_ENTRY, $object->getId());
		}
			
		return null;
	}

//	/**
//	 * @return array<KalturaAdminConsolePlugin>
//	 */
//	public static function getAdminConsolePages()
//	{
//		$metadata = new MetadataProfilesAction('Metadata', 'metadata');
//		$metadataProfiles = new MetadataProfilesAction('Profiles Management', 'profiles', 'Metadata');
//		$metadataObjects = new MetadataObjectsAction('Objects Management', 'objects', 'Metadata');
//		return array($metadata, $metadataProfiles, $metadataObjects);
//	}
}
