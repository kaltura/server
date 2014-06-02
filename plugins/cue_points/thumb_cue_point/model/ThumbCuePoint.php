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
	
	/* (non-PHPdoc)
	 * @see CuePoint::preSave()
	 */
	public function preSave(PropelPDO $con = null)
	{
		if($this->isNew())
		{
			if(!$this->getAssetId())
			{
				$timedThumbAsset = new timedThumbAsset();
				$timedThumbAsset->setCuePointID($this->getId());
				$timedThumbAsset->setStatus(thumbAsset::ASSET_STATUS_QUEUED);
				$timedThumbAsset->setEntryId($this->getEntryId());
				$timedThumbAsset->setPartnerId($this->getPartnerId());
				$timedThumbAsset->save();
				$this->setAssetId($timedThumbAsset->getId());
			}
			else
				$this->setAssetId($this->getAssetId());
		}
		
		return parent::preSave($con);
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
	public function getAssetId()			{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_THUMB_ASSET_ID);}
	
	/* (non-PHPdoc)
	 * @see IMetadataObject::getMetadataObjectType()
	 */
	public function getMetadataObjectType()
	{
		return ThumbCuePointMetadataPlugin::getMetadataObjectTypeCoreValue(ThumbCuePointMetadataObjectType::THUMB_CUE_POINT);
	}
}