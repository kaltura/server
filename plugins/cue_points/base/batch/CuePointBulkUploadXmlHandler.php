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
	 * @var array ingested cue points
	 */
	protected $ingested = array();
	
	/**
	 * @var array each item operation
	 */
	protected $operations = array();
	
	protected function __construct()
	{
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
		$this->client->startMultiRequest();
	
		$items = array();
		foreach($item->scenes->children() as $scene)
			if($this->addCuePoint($scene))
				$items[] = $scene;
			
		$results = $this->client->doMultiRequest();
		$this->unimpersonate();
		
		$this->handleResults($results, $items);
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
		
		$items = array();
		foreach($item->scenes->children() as $scene)
			if($this->updateCuePoint($scene))
				$items[] = $scene;
			
		$results = $this->client->doMultiRequest();
		$this->unimpersonate();
		
		$this->handleResults($results, $items);
	}

	/* (non-PHPdoc)
	 * @see IKalturaBulkUploadXmlHandler::handleItemDeleted()
	 */
	public function handleItemDeleted(KalturaClient $client, KalturaObjectBase $object, SimpleXMLElement $item)
	{
		// No handling required
	}

	protected function handleResults(array $results, array $items)
	{
		if(count($results) != count($this->operations) || count($this->operations) != count($items))
		{
			KalturaLog::err("results count [" . count($results) . "] operations count [" . count($this->operations) . "] items count [" . count($items) . "]");
			return;
		}
			
		$pluginsInstances = KalturaPluginManager::getPluginInstances('IKalturaBulkUploadXmlHandler');
		
		foreach($results as $index => $cuePoint)
		{
			foreach($pluginsInstances as $pluginsInstance)
			{
				/* @var $pluginsInstance IKalturaBulkUploadXmlHandler */
				
				if($this->operations[$index] == KalturaBulkUploadAction::ADD)
					$pluginsInstance->handleItemAdded($this->client, $cuePoint, $items[$index]);
				elseif($this->operations[$index] == KalturaBulkUploadAction::UPDATE)
					$pluginsInstance->handleItemUpdated($this->client, $cuePoint, $items[$index]);
				elseif($this->operations[$index] == KalturaBulkUploadAction::DELETE)
					$pluginsInstance->handleItemDeleted($this->client, $cuePoint, $items[$index]);
			}
		}
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
			$cuePoint->systemName = $scene['systemName'] . '';
			
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
			return false;
			
		$cuePoint->entryId = $this->entryId;
		$ingestedCuePoint = $this->cuePointPlugin->cuePoint->add($cuePoint);
		$this->operations[] = KalturaBulkUploadAction::ADD;
		if($cuePoint->systemName)
			$this->ingested[$cuePoint->systemName] = $ingestedCuePoint;
			
		return true;
	}

	/**
	 * @param SimpleXMLElement $scene
	 */
	protected function updateCuePoint(SimpleXMLElement $scene)
	{
		$cuePoint = $this->parseCuePoint($scene);
		if(!$cuePoint)
			return false;
			
		if(isset($scene['sceneId']) && $scene['sceneId'])
		{
			$cuePointId = $scene['sceneId'];
			$ingestedCuePoint = $this->cuePointPlugin->cuePoint->update($cuePointId, $cuePoint);
			$this->operations[] = KalturaBulkUploadAction::UPDATE;
		}
		else 
		{
			$cuePoint->entryId = $this->entryId;
			$ingestedCuePoint = $this->cuePointPlugin->cuePoint->add($cuePoint);
			$this->operations[] = KalturaBulkUploadAction::ADD;
		}
		if($cuePoint->systemName)
			$this->ingested[$cuePoint->systemName] = $ingestedCuePoint;
			
		return true;
	}
	
	/**
	 * @param string $cuePointSystemName
	 * @return string
	 */
	protected function getCuePointId($systemName)
	{
		if(isset($this->ingested[$systemName]))
		{
			$id = $this->ingested[$systemName]->id;
			return "$id";
		}
		return null;
	
//		Won't work in the middle of multi request
//		
//		$filter = new KalturaAnnotationFilter();
//		$filter->entryIdEqual = $this->entryId;
//		$filter->systemNameEqual = $systemName;
//		
//		$pager = new KalturaFilterPager();
//		$pager->pageSize = 1;
//		
//		try
//		{
//			$cuePointListResponce = $this->cuePointPlugin->cuePoint->listAction($filter, $pager);
//		}
//		catch(Exception $e)
//		{
//			return null;
//		}
//		
//		if($cuePointListResponce->totalCount && $cuePointListResponce->objects[0] instanceof KalturaAnnotation)
//			return $cuePointListResponce->objects[0]->id;
//			
//		return null;
	}
}
