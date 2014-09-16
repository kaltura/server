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
		if ( $object instanceof timedThumbAsset
				&& $object->getStatus() == thumbAsset::ASSET_STATUS_READY && in_array(assetPeer::STATUS, $modifiedColumns) )
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
		$this->copyCuePointToVodIfLiveEntry( $object );
	}

	protected function copyCuePointToVodIfLiveEntry( $timedThumbAsset )
	{
		$thumbCuePoint = CuePointPeer::retrieveByPK( $timedThumbAsset->getCuePointID() );

		if ( $thumbCuePoint )
		{
			$entry = entryPeer::retrieveByPK( $thumbCuePoint->getEntryId() );

			if ( $entry->getType() == entryType::LIVE_STREAM )
			{
				$vodEntryId = $entry->getRecordedEntryId();
				if ( $vodEntryId )
				{
					$vodEntry = entryPeer::retrieveByPK( $vodEntryId );
					if ( $vodEntry )
					{
						KalturaLog::log("Saving the live entry [{$entry->getId()}] cue point [{$thumbCuePoint->getId()}] and timed thumb asset [{$timedThumbAsset->getId()}] to the associated VOD entry [{$vodEntryId}]");

						// Clone the cue point to the VOD entry
						$vodThumbCuePoint = $thumbCuePoint->copy();
						$vodThumbCuePoint->setEntryId( $vodEntryId );
						$vodThumbCuePoint->setAssetId( "" );
						$vodThumbCuePoint->save();

						$timedThumbAssetCuePointID = $timedThumbAsset->getCuePointID();	// Remember the current thumb asset's cue point id
						$timedThumbAsset->setCuePointID( $vodThumbCuePoint->getId() );	// Set the VOD cue point's id
						$timedThumbAsset->setCustomDataObj();							// Write the cached custom data object into the thumb asset

						// Make a copy of the current thumb asset
						// copyToEntry will create a filesync softlink to the original filesync
						$vodTimedThumbAsset = $timedThumbAsset->copyToEntry( $vodEntryId, $vodEntry->getPartnerId() );

						// Restore the thumb asset's prev. cue point id (for good measures)
						$timedThumbAsset->setCuePointID( $timedThumbAssetCuePointID );
						$timedThumbAsset->setCustomDataObj();

						// Save the VOD entry's thumb asset
						$vodTimedThumbAsset->setCuePointID( $vodThumbCuePoint->getId() );
						$vodTimedThumbAsset->save();

						KalturaLog::log("Saved recorded entry cue point [{$vodThumbCuePoint->getId()}] and timed thumb asset [{$vodTimedThumbAsset->getId()}]");
					}
				}
			}
		}
	}
}