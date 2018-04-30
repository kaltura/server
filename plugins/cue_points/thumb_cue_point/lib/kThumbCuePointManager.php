<?php
/**
 * @package plugins.cuePoint
 */
class kThumbCuePointManager implements kObjectDeletedEventConsumer, kObjectChangedEventConsumer, kObjectAddedEventConsumer
{
	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::shouldConsumeAddedEvent()
	 */
	public function shouldConsumeAddedEvent(BaseObject $object)
	{
		if($object instanceof timedThumbAsset && $object->getStatus() == asset::ASSET_STATUS_READY)
			return true;
			
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::shouldConsumeDeletedEvent()
	 */
	public function shouldConsumeDeletedEvent(BaseObject $object)
	{			
		if($object instanceof ThumbCuePoint)
			return true;
			
		if($object instanceof timedThumbAsset)
			return true;
			
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
	*/
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
	{
		if(self::isTimedThumbAssetChangedToReady($object, $modifiedColumns))
		{
			return true;
		}
		
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::objectAdded()
	 */
	public function objectAdded(BaseObject $object, BatchJob $raisedJob = null)
	{
		$cuePoint = $this->getCuePointByTimedThumbAsset($object);

		if(!$cuePoint)
			return true;
			
		if($cuePoint->getStatus() == CuePointStatus::PENDING)
		{
			$cuePoint->setStatus(CuePointStatus::READY);
			$cuePoint->save();	
		}
		
		return true;;
	}
	
	
	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::objectChanged()
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns)
	{
		$cuePoint = $this->getCuePointByTimedThumbAsset($object);
		
		if(!$cuePoint)
			return true;
			
		if($cuePoint->getStatus() == CuePointStatus::PENDING)
		{
			$cuePoint->setStatus(CuePointStatus::READY);
			$cuePoint->save();	
		}
		
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::objectDeleted()
	 */
	public function objectDeleted(BaseObject $object, BatchJob $raisedJob = null) 
	{					
		if($object instanceof ThumbCuePoint)
			$this->thumbCuePointDeleted($object);
			
		if($object instanceof timedThumbAsset)
			$this->timedThumbAssetDeleted($object);
			
		return true;
	}
	
	public static function isTimedThumbAssetChangedToReady(BaseObject $object, array $modifiedColumns)
	{
		if($object instanceof timedThumbAsset && in_array(assetPeer::STATUS, $modifiedColumns) && $object->getStatus() == asset::ASSET_STATUS_READY)
		{
				return true;
		}
	}
	
	/**
	 * @param ThumbCuePoint $cuePoint
	 */
	protected function thumbCuePointDeleted(ThumbCuePoint $cuePoint) 
	{
		$asset = assetPeer::retrieveById($cuePoint->getAssetId());
		
		if($asset)
		{
			$asset->setStatus(asset::ASSET_STATUS_DELETED);
			$asset->setDeletedAt(time());
			$asset->save();
		}
	}
	
	/**
	 * @param timedThumbAsset $thumbAsset
	 */
	protected function timedThumbAssetDeleted(timedThumbAsset $thumbAsset) 
	{
		$dbCuePoint = CuePointPeer::retrieveByPK($thumbAsset->getCuePointID());
		
		//Clear only if the current associated assetId is the one getting deleted
		if($dbCuePoint && $dbCuePoint->getAssetId() == $thumbAsset->getId())
		{
			/* @var $dbCuePoint ThumbCuePoint */
			$dbCuePoint->setAssetId(null);
			$dbCuePoint->save();
		}
	}
	
	public function getCuePointByTimedThumbAsset(timedThumbAsset $timedThumbAsset)
	{		
		$cuePointId = $timedThumbAsset->getCuePointID();
		if(!$cuePointId)
		{
			KalturaLog::info("CuePoint Id not found on object");
			return null;
		}
			
		$cuePoint = CuePointPeer::retrieveByPK($cuePointId);
		if(!$cuePoint)
		{
			KalturaLog::info("CuePoint with ID [$cuePointId] not found");
			return null;
		}
		
		return $cuePoint;
	}
}