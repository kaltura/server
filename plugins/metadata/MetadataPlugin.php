<?php
class MetadataPlugin implements KalturaPlugin, KalturaServicesPlugin, KalturaEventConsumersPlugin, KalturaObjectLoaderPlugin, KalturaBulkUploadHandlerPlugin
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
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}

	public static function isAllowedPartner($partnerId)
	{
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
			'entryMetadata' => 'EntryMetadataService',
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
	 * @param KalturaPluginManager::OBJECT_TYPE $objectType
	 * @param string $enumValue
	 * @param array $constructorArgs
	 * @return object
	 */
	public static function loadObject($objectType, $enumValue, array $constructorArgs = null)
	{
		if($objectType != KalturaPluginManager::OBJECT_TYPE_SYNCABLE)
			return null;
			
		if(!isset($constructorArgs['objectId']))
			return null;
			
		$objectId = $constructorArgs['objectId'];
		
		switch($enumValue)
		{
			case FileSync::FILE_SYNC_OBJECT_TYPE_METADATA:
				MetadataPeer::setUseCriteriaFilter ( false );
				$object = MetadataPeer::retrieveByPK( $objectId );
				MetadataPeer::setUseCriteriaFilter ( true );
				return $object;
				
			case FileSync::FILE_SYNC_OBJECT_TYPE_METADATA_PROFILE:
				MetadataProfilePeer::setUseCriteriaFilter ( false );
				$object = MetadataProfilePeer::retrieveByPK( $objectId );
				MetadataProfilePeer::setUseCriteriaFilter ( true );
				return $object;
		}
		return null;
	}
	
	/**
	 * @param KalturaPluginManager::OBJECT_TYPE $objectType
	 * @param string $enumValue
	 * @return string
	 */
	public static function getObjectClass($objectType, $enumValue)
	{
		if($objectType != KalturaPluginManager::OBJECT_TYPE_SYNCABLE)
			return null;
			
		switch($enumValue)
		{
			case FileSync::FILE_SYNC_OBJECT_TYPE_METADATA:
				return 'Metadata';
				
			case FileSync::FILE_SYNC_OBJECT_TYPE_METADATA_PROFILE:
				return 'MetadataProfile';
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
			
		$criteriaFilter = FileSyncPeer::getCriteriaFilter();
		$criteria = $criteriaFilter->getFilter();
		$criteria->add(FileSyncPeer::PARTNER_ID, $entry->getPartnerId());
		
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
					$errorMessage = "No field found for key[$key]";
					KalturaLog::debug($errorMessage);
					self::addBulkUploadResultDescription($entryId, $entry->getBulkUploadId(), $errorMessage);
					continue;
				}
				
				$metadataProfileField = $metadataProfileFields[$key];
				KalturaLog::debug("Found field [" . $metadataProfileField->getXpath() . "] for value [$value]");
				
				$fieldValues = explode(self::BULK_UPLOAD_MULTI_VALUES_DELIMITER, $value);
				foreach($fieldValues as $fieldValue)
				{
					if($metadataProfileField->getType() == MetadataSearchFilter::KMC_FIELD_TYPE_DATE && !is_numeric($value))
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
			kMetadataManager::updateSearchIndex($dbMetadata);
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
		
		$msg = $bulkUploadResult->getDescription();
		if($msg)
			$msg .= "\n";
		
		$msg .= $description;
			
		$bulkUploadResult->setDescription($msg);
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
			if($currentXPath != $xPath)
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
