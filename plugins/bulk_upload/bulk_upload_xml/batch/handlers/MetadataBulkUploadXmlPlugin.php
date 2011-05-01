<?php
/**
 * @package plugins.metadataBulkUploadXml
 */
class MetadataBulkUploadXmlPlugin extends KalturaPlugin implements IKalturaPending, IKalturaBulkUploadXmlHandler
{
	const PLUGIN_NAME = 'metadataBulkUploadXml';
	
	const BULK_UPLOAD_XML_VERSION_MAJOR = 1;
	const BULK_UPLOAD_XML_VERSION_MINOR = 0;
	const BULK_UPLOAD_XML_VERSION_BUILD = 0;
	
	/**
	 * @var array<string, int> of metadata profiles by their system name
	 */
	private $metadataProfiles = null;
	
	/**
	 * @var KalturaClient
	 */
	private $client = null;
	
	/**
	 * @return string
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaPending::dependsOn()
	 */
	public static function dependsOn()
	{
		$bulkUploadXmlVersion = new KalturaVersion(
			self::BULK_UPLOAD_XML_VERSION_MAJOR,
			self::BULK_UPLOAD_XML_VERSION_MINOR,
			self::BULK_UPLOAD_XML_VERSION_BUILD);
			
		$bulkUploadXmlDependency = new KalturaDependency(BulkUploadXmlPlugin::getPluginName(), $bulkUploadXmlVersion);
		$metadataDependency = new KalturaDependency(MetadataPlugin::getPluginName());
		
		return array($bulkUploadXmlDependency, $metadataDependency);
	}
	
	public function getMetadataProfileId($systemName)
	{
		if(is_null($this->metadataProfiles))
		{
			$metadataPlugin = KalturaMetadataClientPlugin::get($this->client);
			$metadataProfileListResponse = $metadataPlugin->metadataProfile->listAction();
			if(!is_array($metadataProfileListResponse->objects))
				return null;
				
			$this->metadataProfiles = array();
			foreach($metadataProfileListResponse->objects as $metadataProfile)
				if(!is_null($metadataProfile->systemName))
					$this->metadataProfiles[$metadataProfile->systemName] = $metadataProfile->id;
		}
		
		if(isset($this->metadataProfiles[$systemName]))
			return $this->metadataProfiles[$systemName];
			
		return null;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaBulkUploadXmlHandler::handleItemAdded()
	 */
	public function handleItemAdded(KalturaClient $client, KalturaObjectBase $object, SimpleXMLElement $item)
	{
		if(!($object instanceof KalturaBaseEntry))
			return;
			
		if(empty($item->customData))
			return;
			
		$this->client = $client;
		foreach($item->customData as $customData)
			$this->handleCustomData(KalturaMetadataObjectType::ENTRY, $object->id, $customData);
	}
	
	public function handleCustomData($objectType, $objectId, SimpleXMLElement $customData)
	{
		$metadataProfileId = null;
		if(!empty($customData['metadataProfileId']))
			$metadataProfileId = (int)$customData['metadataProfileId'];

		if(!$metadataProfileId && !empty($customData['metadataProfile']))
			$metadataProfileId = $this->getMetadataProfileId($customData['metadataProfile']);
				
		if(!$metadataProfileId)
			throw new KalturaBatchException("Missing custom data metadataProfile attribute", KalturaBatchJobAppErrors::BULK_MISSING_MANDATORY_PARAMETER);
		
		$metadataPlugin = KalturaMetadataClientPlugin::get($this->client);
		
		$metadataFilter = new KalturaMetadataFilter();
		$metadataFilter->metadataObjectTypeEqual = $objectType;
		$metadataFilter->objectIdEqual = $objectId;
		
		$pager = new KalturaFilterPager();
		$pager->pageSize = 1;
		
		$metadataListResponse = $metadataPlugin->metadata->listAction($metadataFilter, $pager);
		
		$metadataId = null;
		if(is_array($metadataListResponse->objects))
		{
			$metadata = reset($metadataListResponse->objects);
			$metadataId = $metadata->id;
		}
		
		$metadataXmlObject = $customData->children();
		$metadataXml = $metadataXmlObject->asXML();
		
		if($metadataId)
			$metadataPlugin->metadata->update($metadataId, $metadataXml);
		else
			$metadataPlugin->metadata->add($metadataProfileId, $objectType, $objectId, $metadataXml);
	}

	/* (non-PHPdoc)
	 * @see IKalturaBulkUploadXmlHandler::handleItemUpdated()
	 */
	public function handleItemUpdated(KalturaClient $client, KalturaObjectBase $object, SimpleXMLElement $item)
	{
		$this->handleItemAdded($client, $object, $item);
	}

	/* (non-PHPdoc)
	 * @see IKalturaBulkUploadXmlHandler::handleItemDeleted()
	 */
	public function handleItemDeleted(KalturaClient $client, KalturaObjectBase $object, SimpleXMLElement $item)
	{
		// No handling required
	}
}
