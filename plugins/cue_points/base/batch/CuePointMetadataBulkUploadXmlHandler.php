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
		{
			KalturaLog::debug("not a cue point");
			return;
		}
			
		$sceneCustomData = 'scene-customData';
		if(empty($item->$sceneCustomData))
		{
			KalturaLog::debug("scene-customData not found");
			return;
		}
			
		$this->client = $client;
		$this->partnerId = $object->partnerId;
		$this->cuePointPlugin = KalturaCuePointClientPlugin::get($client);
		
		$this->impersonate();
		$this->client->startMultiRequest();
		
		foreach($item->$sceneCustomData as $customData)
			KalturaLog::debug("customData [" . print_r($customData, true) . "]");
			
		$this->client->doMultiRequest();
		$this->unimpersonate();
	}

	/* (non-PHPdoc)
	 * @see IKalturaBulkUploadXmlHandler::handleItemUpdated()
	 */
	public function handleItemUpdated(KalturaClient $client, KalturaObjectBase $object, SimpleXMLElement $item)
	{
		if(!($object instanceof KalturaCuePoint))
		{
			KalturaLog::debug("not a cue point");
			return;
		}
			
		$sceneCustomData = 'scene-customData';
		if(empty($item->$sceneCustomData))
		{
			KalturaLog::debug("scene-customData not found");
			return;
		}
			
		$this->client = $client;
		$this->partnerId = $object->partnerId;
		$this->cuePointPlugin = KalturaCuePointClientPlugin::get($client);
		
		$this->impersonate();
		$this->client->startMultiRequest();
		
		foreach($item->$sceneCustomData as $customData)
			KalturaLog::debug("customData [" . print_r($customData, true) . "]");
			
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
}
