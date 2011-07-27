<?php
/**
 * Handles cue point custom metadata ingestion from XML bulk upload
 * @package plugins.cuePoint
 * @subpackage batch
 */
class CuePointMetadataBulkUploadXmlHandler implements IKalturaBulkUploadXmlHandler
{
	/**
	 * @var CuePointMetadataBulkUploadXmlHandler
	 */
	protected static $instance;
	
	/**
	 * @var KalturaClient
	 */
	protected $client = null;
	
	/**
	 * @var KalturaMetadataClientPlugin
	 */
	protected $metadataPlugin = null;
	
	/**
	 * @var int
	 */
	protected $partnerId = null;
	
	protected function __construct()
	{
	}

	/**
	 * @return CuePointMetadataBulkUploadXmlHandler
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new CuePointMetadataBulkUploadXmlHandler();
			
		return self::$instance;
	}
	
	protected function impersonate()
	{
		$clientConfig = $this->client->getConfig();
		$clientConfig->partnerId = $this->partnerId;
		$this->client->setConfig($clientConfig);
	}
	
	protected function unimpersonate()
	{
		$clientConfig = $this->client->getConfig();
		$clientConfig->partnerId = -1;
		$this->client->setConfig($clientConfig);
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaBulkUploadXmlHandler::handleItemAdded()
	 */
	public function handleItemAdded(KalturaClient $client, KalturaObjectBase $object, SimpleXMLElement $item)
	{
		if(!($object instanceof KalturaCuePoint))
			return;
			
		$sceneCustomData = 'scene-customData';
		if(empty($item->$sceneCustomData))
			return;
			
		$this->client = $client;
		$this->partnerId = $object->partnerId;
		$this->cuePointPlugin = KalturaCuePointClientPlugin::get($client);
		
		$this->impersonate();
		$this->client->startMultiRequest();
		
		foreach($item->$sceneCustomData as $customData)
			$this->addMetadata($customData);
			
		$this->client->doMultiRequest();
		$this->unimpersonate();
	}

	/* (non-PHPdoc)
	 * @see IKalturaBulkUploadXmlHandler::handleItemUpdated()
	 */
	public function handleItemUpdated(KalturaClient $client, KalturaObjectBase $object, SimpleXMLElement $item)
	{
		if(!($object instanceof KalturaCuePoint))
			return;
			
		$sceneCustomData = 'scene-customData';
		if(empty($item->$sceneCustomData))
			return;
			
		$this->client = $client;
		$this->partnerId = $object->partnerId;
		$this->cuePointPlugin = KalturaCuePointClientPlugin::get($client);
		
		$this->impersonate();
		$this->client->startMultiRequest();
		
		foreach($item->$sceneCustomData as $customData)
			$this->updateMetadata($customData);
			
		$this->client->doMultiRequest();
		$this->unimpersonate();
	}

	/* (non-PHPdoc)
	 * @see IKalturaBulkUploadXmlHandler::handleItemDeleted()
	 */
	public function handleItemDeleted(KalturaClient $client, KalturaObjectBase $object, SimpleXMLElement $item)
	{
		// No handling required
	}

	public function addMetadata(SimpleXMLElement $customMetadataElement)
	{
//		TODO
//		
//		$metadata = null;
//		$metadataProfile = null;
//		
//		if(isset($metadataElement['metadataId']))
//			$metadata = MetadataPeer::retrieveByPK($metadataElement['metadataId']);
//
//		if($metadata)
//		{
//			$metadataProfile = $metadata->getMetadataProfile();
//		}
//		else
//		{
//			if(isset($metadataElement['metadataProfileId']))
//				$metadataProfile = MetadataProfilePeer::retrieveByPK($metadataElement['metadataProfileId']);
//			elseif(isset($metadataElement['metadataProfile']))
//				$metadataProfile = MetadataProfilePeer::retrieveBySystemName($metadataElement['metadataProfile']);
//				
//			if($metadataProfile)
//				$metadata = MetadataPeer::retrieveByObject($metadataProfile->getId(), $objectType, $cuePoint->getId());
//		}
//		
//		if(!$metadataProfile)
//			continue;
//	
//		if(!$metadata)
//		{
//			$metadata = new Metadata();
//			$metadata->setPartnerId($partnerId);
//			$metadata->setMetadataProfileId($metadataProfile->getId());
//			$metadata->setMetadataProfileVersion($metadataProfile->getVersion());
//			$metadata->setObjectType($objectType);
//			$metadata->setObjectId($cuePoint->getId());
//			$metadata->setStatus(KalturaMetadataStatus::INVALID);
//			$metadata->save();
//			
//			foreach($metadataElement->children() as $metadataContent)
//			{
//				$xmlData = $metadataContent->asXML();
//				$key = $metadata->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA);
//				kFileSyncUtils::file_put_contents($key, $xmlData);
//				
//				$errorMessage = '';
//				$status = kMetadataManager::validateMetadata($metadata, $errorMessage);
//				if($status == KalturaMetadataStatus::VALID)
//					kEventsManager::raiseEvent(new kObjectDataChangedEvent($metadata));
//					
//				break;
//			}
//		}
	}
	
	public function updateMetadata(SimpleXMLElement $customMetadataElement)
	{
		
	}
}
