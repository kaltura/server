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
			
		return $cuePoint;
	}
	
	protected function handleResults(array $results, array $items)
	{
		KBatchBase::$kClient->startMultiRequest();
		
		foreach($results as $index => $cuePoint)
		{	
			if($cuePoint instanceof KalturaThumbCuePoint)
			{
				if(empty($items[$index]->slide))
					continue;
				$timedThumbResource = $this->xmlBulkUploadEngine->getResource($items[$index]->slide, null);
				$thumbAsset = new KalturaTimedThumbAsset();
				$thumbAsset->cuePointId = $cuePoint->id;
				KBatchBase::$kClient->thumbAsset->add($cuePoint->entryId, $thumbAsset);
				KBatchBase::$kClient->thumbAsset->setContent(KBatchBase::$kClient->getMultiRequestResult()->id, $timedThumbResource);
			}
				
		}
		
		KBatchBase::$kClient->doMultiRequest();
		
		return parent::handleResults($results, $items);
	}

}