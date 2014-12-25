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

	public function copyToEntry( $dstEntry, PropelPDO $con = null)
	{
		// Clone the cue point to the destination entry
		$dstThumbCuePoint = parent::copyToEntry( $dstEntry, $con );

		$timedThumbAsset = assetPeer::retrieveByPK($this->getAssetId());
		if ( ! $timedThumbAsset )
		{
			KalturaLog::debug("Can't retrieve timedThumbAsset with id: {$this->getAssetId()}");
			return;
		}

		$timedThumbAsset->setCuePointID( $dstThumbCuePoint->getId() );	// Set the destination cue point's id
		$timedThumbAsset->setCustomDataObj();							// Write the cached custom data object into the thumb asset

		// Make a copy of the current thumb asset
		// copyToEntry will create a filesync softlink to the original filesync
		$dstTimedThumbAsset = $timedThumbAsset->copyToEntry( $dstEntry->getId(), $dstEntry->getPartnerId() );
		$dstThumbCuePoint->setAssetId( $dstTimedThumbAsset->getId() );
		$dstThumbCuePoint->save( $con );

		// Restore the thumb asset's prev. cue point id (for good measures)
		$timedThumbAsset->setCuePointID( $this->getId() );
		$timedThumbAsset->setCustomDataObj();

		// Save the destination entry's thumb asset
		$dstTimedThumbAsset->setCuePointID( $dstThumbCuePoint->getId() );
		$dstTimedThumbAsset->save( $con );

		KalturaLog::log("Saved cue point [{$dstThumbCuePoint->getId()}] and timed thumb asset [{$dstTimedThumbAsset->getId()}]");
	}
	
	public function save(PropelPDO $con = null)
	{
		$subType = $this->getSubType();
		if(!isset($subType))
			$this->setSubType(ThumbCuePointSubType::SLIDE);
			
		return parent::save($con);
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