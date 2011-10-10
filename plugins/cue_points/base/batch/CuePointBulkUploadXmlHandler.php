<?php
/**
 * Handles cue point ingestion from XML bulk upload
 * @package plugins.cuePoint
 * @subpackage batch
 */
abstract class CuePointBulkUploadXmlHandler implements IKalturaBulkUploadXmlHandler
{
	/**
	 * @var BulkUploadEngineXml
	 */
	private $xmlBulkUploadEngine = null;
	
	/**
	 * @var KalturaCuePointClientPlugin
	 */
	protected $cuePointPlugin = null;
	
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
	
	/* (non-PHPdoc)
	 * @see IKalturaBulkUploadXmlHandler::configureBulkUploadXmlHandler()
	 */
	public function configureBulkUploadXmlHandler(BulkUploadEngineXml $xmlBulkUploadEngine)
	{
		$this->xmlBulkUploadEngine = $xmlBulkUploadEngine;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaBulkUploadXmlHandler::handleItemAdded()
	 */
	public function handleItemAdded(KalturaObjectBase $object, SimpleXMLElement $item)
	{
		if(!($object instanceof KalturaBaseEntry))
			return;
			
		if(empty($item->scenes))
			return;
			
		$this->entryId = $object->id;
		$this->cuePointPlugin = KalturaCuePointClientPlugin::get($this->xmlBulkUploadEngine->getClient());
		
		$this->xmlBulkUploadEngine->impersonate();
		$this->xmlBulkUploadEngine->getClient()->startMultiRequest();
	
		$items = array();
		foreach($item->scenes->children() as $scene)
			if($this->addCuePoint($scene))
				$items[] = $scene;
			
		$results = $this->xmlBulkUploadEngine->getClient()->doMultiRequest();
		$this->xmlBulkUploadEngine->unimpersonate();
		
		if(is_array($results) && is_array($items))
			$this->handleResults($results, $items);
	}

	/* (non-PHPdoc)
	 * @see IKalturaBulkUploadXmlHandler::handleItemUpdated()
	 */
	public function handleItemUpdated(KalturaObjectBase $object, SimpleXMLElement $item)
	{
		if(!($object instanceof KalturaBaseEntry))
			return;
			
		if(empty($item->scenes))
			return;
			
		$this->entryId = $object->id;
		$this->cuePointPlugin = KalturaCuePointClientPlugin::get($this->xmlBulkUploadEngine->getClient());
		
		$this->xmlBulkUploadEngine->impersonate();
		$this->xmlBulkUploadEngine->getClient()->startMultiRequest();
		
		$items = array();
		foreach($item->scenes->children() as $scene)
			if($this->updateCuePoint($scene))
				$items[] = $scene;
			
		$results = $this->xmlBulkUploadEngine->getClient()->doMultiRequest();
		$this->xmlBulkUploadEngine->unimpersonate();
		
		$this->handleResults($results, $items);
	}

	/* (non-PHPdoc)
	 * @see IKalturaBulkUploadXmlHandler::handleItemDeleted()
	 */
	public function handleItemDeleted(KalturaObjectBase $object, SimpleXMLElement $item)
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
				
				$pluginsInstance->configureBulkUploadXmlHandler($this->xmlBulkUploadEngine);
				
				if($this->operations[$index] == KalturaBulkUploadAction::ADD)
					$pluginsInstance->handleItemAdded($cuePoint, $items[$index]);
				elseif($this->operations[$index] == KalturaBulkUploadAction::UPDATE)
					$pluginsInstance->handleItemUpdated($cuePoint, $items[$index]);
				elseif($this->operations[$index] == KalturaBulkUploadAction::DELETE)
					$pluginsInstance->handleItemDeleted($cuePoint, $items[$index]);
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
