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
	protected $xmlBulkUploadEngine = null;
	
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
	
	/**
	 * @var array of existing Cue Points with systemName
	 */
	protected static $existingCuePointsBySystemName = null;
	
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
		$this->cuePointPlugin = KalturaCuePointClientPlugin::get(KBatchBase::$kClient);
		
		KBatchBase::impersonate($this->xmlBulkUploadEngine->getCurrentPartnerId());
		KBatchBase::$kClient->startMultiRequest();
	
		$items = array();
		foreach($item->scenes->children() as $scene)
			if($this->addCuePoint($scene))
				$items[] = $scene;
			
		$results = KBatchBase::$kClient->doMultiRequest();
		KBatchBase::unimpersonate();
		
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

		$action = KBulkUploadEngine::$actionsMap[KalturaBulkUploadAction::UPDATE];
		if(isset($item->scenes->action))
			$action = strtolower($item->scenes->action);
			
		switch ($action)
		{
			case KBulkUploadEngine::$actionsMap[KalturaBulkUploadAction::UPDATE]:
				break;
			default:
				throw new KalturaBatchException("scenes->action: $action is not supported", KalturaBatchJobAppErrors::BULK_ACTION_NOT_SUPPORTED);
		}
			
		$this->entryId = $object->id;
		$this->cuePointPlugin = KalturaCuePointClientPlugin::get(KBatchBase::$kClient);
		
		KBatchBase::impersonate($this->xmlBulkUploadEngine->getCurrentPartnerId());
		
		$this->getExistingCuePointsBySystemName($this->entryId);
		KBatchBase::$kClient->startMultiRequest();
		
		$items = array();
		foreach($item->scenes->children() as $scene)
		{
			if($this->updateCuePoint($scene))
				$items[] = $scene;
		}
			
		$results = KBatchBase::$kClient->doMultiRequest();
		KBatchBase::unimpersonate();

		if(is_array($results) && is_array($items))
			$this->handleResults($results, $items);
	}

	/* (non-PHPdoc)
	 * @see IKalturaBulkUploadXmlHandler::handleItemDeleted()
	 */
	public function handleItemDeleted(KalturaObjectBase $object, SimpleXMLElement $item)
	{
		// No handling required
	}

	/**
	 * @param string $entryId
	 * @return array of cuepoint that have systemName
	 */
	protected function getExistingCuePointsBySystemName($entryId)
	{
		if (is_array(self::$existingCuePointsBySystemName))
			return;
		
		$filter = new KalturaCuePointFilter();
		$filter->entryIdEqual = $entryId;
		
		$pager = new KalturaFilterPager();
		$pager->pageSize = 500;
		
		$cuePoints = $this->cuePointPlugin->cuePoint->listAction($filter, $pager);
		self::$existingCuePointsBySystemName = array();
		
		if (!isset($cuePoints->objects))
			return;

		foreach ($cuePoints->objects as $cuePoint)
		{
			if($cuePoint->systemName != '')
				self::$existingCuePointsBySystemName[$cuePoint->systemName] = $cuePoint->id;
		}
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
			if(is_array($cuePoint) && isset($cuePoint['code']))
				throw new Exception($cuePoint['message']);
			
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
		elseif(isset($cuePoint->systemName) && isset(self::$existingCuePointsBySystemName[$cuePoint->systemName]))
		{
			$cuePoint = $this->removeNonUpdatbleFields($cuePoint);
			$cuePointId = self::$existingCuePointsBySystemName[$cuePoint->systemName];
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
	
	/* (non-PHPdoc)
	 * @see IKalturaConfigurator::getContainerName()
	*/
	public function getContainerName()
	{
		return 'scenes';
	}
	
	/**
	 * Removes all non updatble fields from the cuepoint
	 * @param KalturaCuePoint $entry
	 */
	protected function removeNonUpdatbleFields(KalturaCuePoint $cuePoint)
	{
		return $cuePoint;
	}
}
