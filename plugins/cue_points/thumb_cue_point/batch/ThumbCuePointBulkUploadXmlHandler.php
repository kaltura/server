<?php
/**
 * Handles thumb cue point ingestion from XML bulk upload
 * @package plugins.thumbCuePoint
 * @subpackage batch
 */
class ThumbCuePointBulkUploadXmlHandler extends CuePointBulkUploadXmlHandler
{
	/**
	 * @var ThumbCuePointBulkUploadXmlHandler
	 */
	protected static $instance;
	
	/**
	 * @return ThumbCuePointBulkUploadXmlHandler
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new ThumbCuePointBulkUploadXmlHandler();
			
		return self::$instance;
	}
	
	/* (non-PHPdoc)
	 * @see CuePointBulkUploadXmlHandler::getNewInstance()
	 */
	protected function getNewInstance()
	{
		return new KalturaThumbCuePoint();
	}
	
	/* (non-PHPdoc)
	 * @see CuePointBulkUploadXmlHandler::parseCuePoint()
	 */
	protected function parseCuePoint(SimpleXMLElement $scene)
	{
		if($scene->getName() != 'scene-thumb-cue-point')
			return null;
			
		$cuePoint = parent::parseCuePoint($scene);
		if(!($cuePoint instanceof KalturaThumbCuePoint))
			return null;
			
		//If timedThumbAssetId is present in the XML assume an existing one is beeing updated (Action = Update)
		if(isset($scene->slide) && isset($scene->slide->timedThumbAssetId))
			$cuePoint->assetId  = $scene->slide->timedThumbAssetId;
			
		$cuePoint->title = $scene->title;
		$cuePoint->description = $scene->description;
		
		if(isset($scene->subType))
			$cuePoint->subType = $scene->subType;
		else 
			$cuePoint->subType = KalturaThumbCuePointSubType::SLIDE;
		
		return $cuePoint;
	}
	
	protected function handleResults(array $results, array $items)
	{	
		//Added to support cases where the resource is entry resource
		$conversionProfileId = null;
		try {
			KBatchBase::impersonate($this->xmlBulkUploadEngine->getCurrentPartnerId());
			$entry = KBatchBase::$kClient->baseEntry->get($this->entryId);
			KBatchBase::unimpersonate();
			if($entry && $entry->conversionProfileId)
				$conversionProfileId = $entry->conversionProfileId;
		}
		catch (Exception $ex)
		{
			KBatchBase::unimpersonate();
			KalturaLog::debug("Entry ID [" . $this->entryId . "] not found, continuing with no conversion profile");
		}
		
		foreach($results as $index => $cuePoint)
		{	
			if($cuePoint instanceof KalturaThumbCuePoint)
			{
				if(!isset($items[$index]->slide) || empty($items[$index]->slide))
					continue;
				
				$timedThumbResource = $this->xmlBulkUploadEngine->getResource($items[$index]->slide, $conversionProfileId);
				$thumbAsset = new KalturaTimedThumbAsset();
				$thumbAsset->cuePointId = $cuePoint->id;

				KBatchBase::impersonate($this->xmlBulkUploadEngine->getCurrentPartnerId());
				KBatchBase::$kClient->startMultiRequest();
				KBatchBase::$kClient->thumbAsset->add($cuePoint->entryId, $thumbAsset);
				KBatchBase::$kClient->thumbAsset->setContent(KBatchBase::$kClient->getMultiRequestResult()->id, $timedThumbResource);
				KBatchBase::$kClient->doMultiRequest();
				KBatchBase::unimpersonate();
			}
				
		}
		
		return parent::handleResults($results, $items);
	}

}