<?php


/**
 * @package plugins.thumbCuePoint
 * @subpackage model
 */
class ThumbCuePoint extends CuePoint implements IMetadataObject
{
	const CUSTOM_DATA_FIELD_THUMB_ASSET_ID = 'thumbAssetId';
	
	public function __construct() 
	{
		parent::__construct();
		$this->applyDefaultValues();
	}

	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or equivalent initialization method).
	 * @see __construct()
	 */
	public function applyDefaultValues()
	{
		$this->setType(ThumbCuePointPlugin::getCuePointTypeCoreValue(ThumbCuePointType::THUMB));
	}
	
	public function setAssetId($v)		{return $this->putInCustomData(self::CUSTOM_DATA_FIELD_THUMB_ASSET_ID, (string)$v);}
	public function getAssetId()		{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_THUMB_ASSET_ID);}
	
	/* (non-PHPdoc)
	 * @see IMetadataObject::getMetadataObjectType()
	 */
	public function getMetadataObjectType()
	{
		return ThumbCuePointMetadataPlugin::getMetadataObjectTypeCoreValue(ThumbCuePointMetadataObjectType::THUMB_CUE_POINT);
	}

	public function getCopyFromLiveToVodEntryOffset( array $liveRecordingSegmentInfoArray )
	{
		$startTime = $this->getStartTime();
		$vodToLiveDeltaTime = 0;

		/* @var $liveRecordingSegmentInfo kLiveRecordingSegmentInfo */
		foreach ( $liveRecordingSegmentInfoArray as $liveRecordingSegmentInfo )
		{
			if ( $startTime <= $liveRecordingSegmentInfo->getVodEntryEndTime() )
			{
				if ( $startTime >= $liveRecordingSegmentInfo->getLiveStreamStartTime() )
				{
					return $vodToLiveDeltaTime;
				}
				else
				{
					return null;
				}
			}
			else
			{
				$vodToLiveDeltaTime = $liveRecordingSegmentInfo->getVodToLiveDeltaTime();
			}
		}

		return null;
	}

	public function copyFromLiveToVodEntry( $liveEntry, $vodEntry, array $liveRecordingSegmentInfoArray )
	{
		$startTimeOffset = $this->getCopyFromLiveToVodEntryOffset( $liveRecordingSegmentInfoArray );
		if ( is_null( $startTimeOffset ) ) // Null offset means we should not copy this live cuepoint to the VOD entry
		{
			return;
		}

		// Clone the cue point to the destination entry
		$vodThumbCuePoint = parent::copyToEntry( $vodEntry );

		$timedThumbAsset = assetPeer::retrieveById($this->getAssetId());
		if ( ! $timedThumbAsset )
		{
			KalturaLog::debug("Can't retrieve timedThumbAsset with id: {$this->getAssetId()}");
			return;
		}

		// Offset the startTime according to the duration gap between the live and VOD entries
		$startTime = $vodThumbCuePoint->getStartTime();
		$vodThumbCuePoint->setStartTime( $startTime - $startTimeOffset );

		$timedThumbAsset->setCuePointID( $vodThumbCuePoint->getId() );	// Set the destination cue point's id
		$timedThumbAsset->setCustomDataObj();							// Write the cached custom data object into the thumb asset

		// Make a copy of the current thumb asset
		// copyToEntry will create a filesync softlink to the original filesync
		$vodTimedThumbAsset = $timedThumbAsset->copyToEntry( $vodEntry->getId(), $vodEntry->getPartnerId() );
		$vodThumbCuePoint->setAssetId( $vodTimedThumbAsset->getId() );
		$vodThumbCuePoint->save();

		// Restore the thumb asset's prev. cue point id (for good measures)
		$timedThumbAsset->setCuePointID( $this->getId() );
		$timedThumbAsset->setCustomDataObj();

		// Save the destination entry's thumb asset
		$vodTimedThumbAsset->setCuePointID( $vodThumbCuePoint->getId() );
		$vodTimedThumbAsset->save();

		KalturaLog::log("Saved cue point [{$vodThumbCuePoint->getId()}] and timed thumb asset [{$vodTimedThumbAsset->getId()}]");
	}
	
	/* (non-PHPdoc)
	 * @see BaseCuePoint::preInsert()
	 */
	public function preInsert(PropelPDO $con = null)
	{
		$subType = $this->getSubType();
		if(!isset($subType))
			$this->setSubType(ThumbCuePointSubType::SLIDE);
		
		if($this->getSubType() == ThumbCuePointSubType::SLIDE)
			$this->setStatus(CuePointStatus::PENDING);
		
		return parent::preInsert($con);
	}
	
	public function contributeData()
	{
		$data = null;
		
		if($this->getText())
			$data = $data . $this->getText() . ' ';
		
		if($this->getName())
			$data = $data . $this->getName() . ' ';
		
		if($this->getTags())
			$data = $data . $this->getTags() . ' ';
		
		return $data;
	}
}