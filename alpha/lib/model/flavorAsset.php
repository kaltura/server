<?php

/**
 * Subclass for representing a row from the 'flavor_asset' table.
 *
 * 
 *
 * @package lib.model
 */ 
class flavorAsset extends asset
{
	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or
	 * equivalent initialization method).
	 * @see        __construct()
	 */
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->type = assetType::FLAVOR;
	}
	
	const CUSTOM_DATA_FIELD_BITRATE = "FlavorBitrate";
	const CUSTOM_DATA_FIELD_FRAME_RATE = "FlavorFrameRate";
	const CUSTOM_DATA_FIELD_VIDEO_CODEC_ID = "FlavorVideoCodecId";
	
//	Should be uncommented after migration script executed
//	public function getBitrate()			{return $this->getFromCustomData(flavorAsset::CUSTOM_DATA_FIELD_BITRATE);}
//	public function getFrameRate()			{return $this->getFromCustomData(flavorAsset::CUSTOM_DATA_FIELD_FRAME_RATEF);}
//	public function getVideoCodecId()		{return $this->getFromCustomData(flavorAsset::CUSTOM_DATA_FIELD_VIDEO_CODEC_ID);}
	
	public function setBitrate($v)			{$this->putInCustomData(flavorAsset::CUSTOM_DATA_FIELD_BITRATE, $v); return parent::setBitrate($v);}
	public function setFrameRate($v)		{$this->putInCustomData(flavorAsset::CUSTOM_DATA_FIELD_FRAME_RATE, $v); return parent::setFrameRate($v);}
	public function setVideoCodecId($v)		{$this->putInCustomData(flavorAsset::CUSTOM_DATA_FIELD_VIDEO_CODEC_ID, $v); return parent::setVideoCodecId($v);}
	
	public function getIsWeb()
	{
		return $this->hasTag(flavorParams::TAG_WEB);
	}
}
