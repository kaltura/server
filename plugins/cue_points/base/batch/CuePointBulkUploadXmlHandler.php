<?php
/**
 * Handles cue point ingestion from XML bulk upload
 * @package plugins.cuePoint
 * @subpackage batch
 */
abstract class CuePointBulkUploadXmlHandler implements IKalturaBulkUploadXmlHandler
{
	/**
	 * @var KalturaClient
	 */
	protected $client = null;
	
	/**
	 * @var KalturaCuePointClientPlugin
	 */
	protected $cuePointPlugin = null;
	
	/**
	 * @var int
	 */
	protected $partnerId = null;
	
	/**
	 * @var int
	 */
	protected $entryId = null;
	
	/**
	 * @var int
	 */
	private $useMultiRequest = true;
	
	/**
	 * @param bool $useMultiRequest
	 */
	protected function __construct($useMultiRequest = true)
	{
		$this->useMultiRequest = $useMultiRequest;
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
		if(!($object instanceof KalturaBaseEntry))
			return;
			
		if(empty($item->scenes))
			return;
			
		$this->client = $client;
		$this->partnerId = $object->partnerId;
		$this->entryId = $object->id;
		$this->cuePointPlugin = KalturaCuePointClientPlugin::get($client);
		
		$this->impersonate();
		if($this->useMultiRequest)
			$this->client->startMultiRequest();
		
		foreach($item->scenes->children() as $scene)
			$this->addCuePoint($scene);
			
		if($this->useMultiRequest)
			$this->client->doMultiRequest();
		$this->unimpersonate();
	}

	/* (non-PHPdoc)
	 * @see IKalturaBulkUploadXmlHandler::handleItemUpdated()
	 */
	public function handleItemUpdated(KalturaClient $client, KalturaObjectBase $object, SimpleXMLElement $item)
	{
		if(!($object instanceof KalturaBaseEntry))
			return;
			
		if(empty($item->scenes))
			return;
			
		$this->client = $client;
		$this->partnerId = $object->partnerId;
		$this->entryId = $object->id;
		$this->cuePointPlugin = KalturaCuePointClientPlugin::get($client);
		
		$this->impersonate();
		$this->client->startMultiRequest();
		
		foreach($item->scenes as $scene)
			$this->updateCuePoint($scene);
			
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

	/**
	 * @return KalturaCuePoint
	 */
	abstract protected function getNewInstance();

	/**
	 * @param SimpleXMLElement $scene
	 * @return KalturaCuePoint
	 */
	protected function parseCuePoint(SimpleXMLElement $scene)
	{
		$cuePoint = $this->getNewInstance();
		
		if(isset($scene['systemName']) && $scene['systemName'])
			$cuePoint->systemName = $scene['systemName'];
			
		$cuePoint->startTime = kXml::timeToInteger($scene->sceneStartTime);
	
		$tags = array();
		foreach ($scene->tags->children() as $tag)
		{
			$value = "$tag";
			if($value)
				$tags[] = $value;
		}
		$cuePoint->tags = implode(',', $tags);
		
		return $cuePoint;
	}
	
	/**
	 * @param SimpleXMLElement $scene
	 */
	protected function addCuePoint(SimpleXMLElement $scene)
	{
		$cuePoint = $this->parseCuePoint($scene);
		if(!$cuePoint)
			return;
			
		$cuePoint->entryId = $this->entryId;
		$this->cuePointPlugin->cuePoint->add($cuePoint);
	}

	/**
	 * @param SimpleXMLElement $scene
	 */
	protected function updateCuePoint(SimpleXMLElement $scene)
	{
		$cuePoint = $this->parseCuePoint($scene);
		if(!$cuePoint)
			return;
			
		if(isset($scene['sceneId']) && $scene['sceneId'])
		{
			$cuePointId = $scene['sceneId'];
			$this->cuePointPlugin->cuePoint->update($cuePointId, $cuePoint);
		}
		else 
		{
			$cuePoint->entryId = $this->entryId;
			$this->cuePointPlugin->cuePoint->add($cuePoint);
		}
	}
}
