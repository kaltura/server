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

	public function copyToEntry( $entry, PropelPDO $con = null)
	{
		return $this->copyFromLiveToVodEntry( $entry, null );
	}

	public function copyFromLiveToVodEntry( $vodEntry, $adjustedStartTime )
	{
		// Clone the cue point to the destination entry
		$vodThumbCuePoint = parent::copyToEntry( $vodEntry );
		$this->copyAssets( $vodEntry, $vodThumbCuePoint, $adjustedStartTime );
		return $vodThumbCuePoint;
	}

	public function copyAssets( entry $toEntry, ThumbCuePoint $toCuePoint, $adjustedStartTime = null )
	{
		$timedThumbAsset = assetPeer::retrieveById($this->getAssetId());
		if ( ! $timedThumbAsset )
		{
			KalturaLog::info("Can't retrieve timedThumbAsset with id: {$this->getAssetId()}");
			return;
		}

		// Offset the startTime according to the duration gap between the live and VOD entries
		if ( !is_null( $adjustedStartTime ) ) {
			$toCuePoint->setStartTime( $adjustedStartTime );
		}
		$toCuePoint->save(); // Must save in order to produce an id

		$timedThumbAsset->setCuePointID( $toCuePoint->getId() );	// Set the destination cue point's id
		$timedThumbAsset->setCustomDataObj();							// Write the cached custom data object into the thumb asset

		// Make a copy of the current thumb asset
		// copyToEntry will create a filesync softlink to the original filesync
		$toTimedThumbAsset = $timedThumbAsset->copyToEntry( $toEntry->getId(), $toEntry->getPartnerId() );
		$toCuePoint->setAssetId( $toTimedThumbAsset->getId() );
		$toCuePoint->save();

		// Restore the thumb asset's prev. cue point id (for good measures)
		$timedThumbAsset->setCuePointID( $this->getId() );
		$timedThumbAsset->setCustomDataObj();

		// Save the destination entry's thumb asset
		$toTimedThumbAsset->setCuePointID( $toCuePoint->getId() );
		$toTimedThumbAsset->save();

		KalturaLog::log("Saved cue point [{$toCuePoint->getId()}] and timed thumb asset [{$toTimedThumbAsset->getId()}]");
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
	
	public function getIsPublic()	              {return true;}

	public function contributeElasticData()
	{
		$data = null;
		if($this->getText())
			$data['cue_point_text'] = $this->getText();

		if($this->getName())
			$data['cue_point_name'] = $this->getName();

		if($this->getTags())
			$data['cue_point_tags'] =  explode(',', $this->getTags());

		if($this->getSubType())
			$data['cue_point_sub_type'] = $this->getSubType();

		if($this->getAssetId())
			$data['cue_point_asset_id'] = $this->getAssetId();

		return $data;
	}
}
