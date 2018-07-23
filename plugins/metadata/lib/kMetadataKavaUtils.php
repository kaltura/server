<?php

/**
 * @package plugins.metadata
 * @subpackage lib
 */
class kMetadataKavaUtils
{
	const FILE_SYNC_CHUNK_SIZE = 100;
	
	// Note: the purpose of the functions below is to efficiently get metadata values
	//		for large numbers of objects, in order to enrich kava reports.
	//		they are not suitable for general use since they don't support -
	//		1. pulling file syncs from remote dcs
	//		2. file sync links
	//		3. file sync encryption
	
	protected static function getMetadataReadyFileSyncs($objectIds, $partnerId, $metadataProfileId)
	{
		$criteria = new Criteria();

		// Note: cannot use Propel's Join object, since the CAST makes 
		//	Join::getRightTableName return wrong result and break the query
		$criteria->add(FileSyncPeer::OBJECT_ID, 
			FileSyncPeer::OBJECT_ID . '=CAST(' . MetadataPeer::ID . ' AS CHAR) AND ' . 
			FileSyncPeer::VERSION . '=' . MetadataPeer::VERSION, 
			Criteria::CUSTOM);
		
		$criteria->add(MetadataPeer::METADATA_PROFILE_ID, $metadataProfileId);
		$criteria->add(MetadataPeer::OBJECT_ID, $objectIds, Criteria::IN);
		$criteria->add(MetadataPeer::PARTNER_ID, $partnerId);
		$criteria->add(MetadataPeer::STATUS, Metadata::STATUS_VALID);
		
		$criteria->add(FileSyncPeer::DC, kDataCenterMgr::getCurrentDcId());
		$criteria->add(FileSyncPeer::STATUS, FileSync::FILE_SYNC_STATUS_READY);
		$criteria->add(FileSyncPeer::FILE_TYPE, FileSync::FILE_SYNC_FILE_TYPE_FILE);
		$criteria->add(FileSyncPeer::OBJECT_TYPE, FileSyncObjectType::METADATA);
		$criteria->add(FileSyncPeer::OBJECT_SUB_TYPE, Metadata::FILE_SYNC_METADATA_DATA);
				
		$criteria->addSelectColumn(MetadataPeer::ID);
		$criteria->addSelectColumn(MetadataPeer::OBJECT_ID);
		$criteria->addSelectColumn(MetadataPeer::VERSION);
		$criteria->addSelectColumn(FileSyncPeer::FILE_ROOT);
		$criteria->addSelectColumn(FileSyncPeer::FILE_PATH);
		
		$stmt = FileSyncPeer::doSelectStmt($criteria);
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	protected static function getMetadataFieldValues($objectIds, $partnerId, $metadataProfileId, $xPathPatterns)
	{
		$fileSyncs = self::getMetadataReadyFileSyncs($objectIds, $partnerId, $metadataProfileId);

		$cacheStore = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_FILE_SYNC);
		
		$result = array();
		for ($i = 0; $i < count($fileSyncs); $i += self::FILE_SYNC_CHUNK_SIZE)
		{
			// get the current chunk associative by cache key
			$curFileSyncs = array();
			foreach (array_slice($fileSyncs, $i, self::FILE_SYNC_CHUNK_SIZE) as $fileSync)
			{
				$cacheKey = kFileSyncUtils::CACHE_KEY_PREFIX . implode('_', array(
					$fileSync['ID'],
					FileSyncObjectType::METADATA,
					Metadata::FILE_SYNC_METADATA_DATA,
					$fileSync['VERSION'],
				));
				
				$curFileSyncs[$cacheKey] = $fileSync;
			}
			
			// try to get from cache 
			if ($cacheStore)
			{
				$cacheItems = $cacheStore->multiGet(array_keys($curFileSyncs));
			}
			else
			{
				$cacheItems = array();
			}
			
			foreach ($curFileSyncs as $cacheKey => $fileSync)
			{
				// get the file data
				if (isset($cacheItems[$cacheKey]))
				{
					$source = $cacheItems[$cacheKey];
				}
				else
				{
					$fullPath = realpath($fileSync['FILE_ROOT'] . $fileSync['FILE_PATH']);
					$source = file_get_contents($fullPath);
				}
				
				if (!$source)
				{
					continue;
				}
				
				// parse the xml
				$xml = new KDOMDocument();
				if (!$xml->loadXML($source))
				{
					continue;
				}
		
				$xPath = new DOMXPath($xml);
				
				// get the fields
				$fields = array();
				foreach ($xPathPatterns as $xPathPattern)
				{
					$elementsList = $xPath->query($xPathPattern);
					$values = array();
					foreach($elementsList as $element)
					{
						$values[] = $element->textContent;
					}
					$fields[] = implode(',', $values);
				}
				
				// update the cache
				if ($cacheStore && !isset($cacheItems[$cacheKey]))
				{
					$cacheStore->set($cacheKey, $source, kFileSyncUtils::FILE_SYNC_CACHE_EXPIRY);
				}
				
				$result[$fileSync['OBJECT_ID']] = $fields;
			}
		}
		
		return $result;
	}
	
	public static function metadataEnrich($ids, $partnerId, $context)
	{
		return self::getMetadataFieldValues(
			$ids, 
			$partnerId, 
			$context['metadata_profile_id'], 
			$context['xpath_patterns']);
	}
}
