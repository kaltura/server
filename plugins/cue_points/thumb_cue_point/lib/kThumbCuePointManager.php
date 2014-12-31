<?php
/**
 * @package plugins.cuePoint
 */
class kThumbCuePointManager implements kObjectDeletedEventConsumer, kObjectChangedEventConsumer
{
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
		
		if($dbCuePoint)
		{
			/* @var $dbCuePoint ThumbCuePoint */
			$dbCuePoint->setAssetId(null);
			$dbCuePoint->save();
		}
	}

	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
	 */
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
	{
		if ( kCuePointManager::isCopyCuePointsFromLiveToVodEvent($object, $modifiedColumns) )
		{
			return true;
		}

		return false;
	}

	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::objectChanged()
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns)
	{
		$this->deleteObsoleteThumbCuePoints( $object );
	}

	public function deleteObsoleteThumbCuePoints( $liveEntry )
	{
		$c = new KalturaCriteria();
		$c->add(CuePointPeer::ENTRY_ID, $liveEntry->getId());
		$c->add( CuePointPeer::START_TIME, $liveEntry->getLengthInMsecs(), KalturaCriteria::GREATER_THAN );
		$c->add( CuePointPeer::TYPE, ThumbCuePointPlugin::getCuePointTypeCoreValue(ThumbCuePointType::THUMB) );

		kCuePointManager::deleteCuePoints( $c );
	}
}