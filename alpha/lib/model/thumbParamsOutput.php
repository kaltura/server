<?php
/**
 * Subclass for representing a row from the 'flavor_params_output' table, used for thumb_params_output
 *
 * 
 *
 * @package lib.model
 */ 
class thumbParamsOutput extends assetParamsOutput
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
		$this->type = assetType::THUMBNAIL;
	}

	public function getSourceParamsId()			{return $this->getFromCustomData(thumbParams::CUSTOM_DATA_FIELD_SOURCE_PARAMS_ID);}
	public function getCropType()				{return $this->getFromCustomData(thumbParams::CUSTOM_DATA_FIELD_CROP_TYPE);}
	public function getQuality()				{return $this->getFromCustomData(thumbParams::CUSTOM_DATA_FIELD_QUALITY);}
	public function getCropX()					{return $this->getFromCustomData(thumbParams::CUSTOM_DATA_FIELD_CROP_X);}
	public function getCropY()					{return $this->getFromCustomData(thumbParams::CUSTOM_DATA_FIELD_CROP_Y);}
	public function getCropWidth()				{return $this->getFromCustomData(thumbParams::CUSTOM_DATA_FIELD_CROP_WIDTH);}
	public function getCropHeight()				{return $this->getFromCustomData(thumbParams::CUSTOM_DATA_FIELD_CROP_HEIGHT);}
	public function getCropProvider()			{return $this->getFromCustomData(thumbParams::CUSTOM_DATA_FIELD_CROP_PROVIDER);}
	public function getCropProviderData()		{return $this->getFromCustomData(thumbParams::CUSTOM_DATA_FIELD_CROP_PROVIDER_DATA);}
	public function getVideoOffset()			{return $this->getFromCustomData(thumbParams::CUSTOM_DATA_FIELD_VIDEO_OFFSET);}
	public function getWidth()					{return $this->getFromCustomData(thumbParams::CUSTOM_DATA_FIELD_WIDTH);}
	public function getHeight()					{return $this->getFromCustomData(thumbParams::CUSTOM_DATA_FIELD_HEIGHT);}
	public function getBackgroundColor()		{return $this->getFromCustomData(thumbParams::CUSTOM_DATA_FIELD_BACKGROUND_COLOR);}

	public function setSourceParamsId($v)		{return $this->putInCustomData(thumbParams::CUSTOM_DATA_FIELD_SOURCE_PARAMS_ID, $v);}
	public function setCropType($v)				{return $this->putInCustomData(thumbParams::CUSTOM_DATA_FIELD_CROP_TYPE, $v);}
	public function setQuality($v)				{return $this->putInCustomData(thumbParams::CUSTOM_DATA_FIELD_QUALITY, $v);}
	public function setCropX($v)				{return $this->putInCustomData(thumbParams::CUSTOM_DATA_FIELD_CROP_X, $v);}
	public function setCropY($v)				{return $this->putInCustomData(thumbParams::CUSTOM_DATA_FIELD_CROP_Y, $v);}
	public function setCropWidth($v)			{return $this->putInCustomData(thumbParams::CUSTOM_DATA_FIELD_CROP_WIDTH, $v);}
	public function setCropHeight($v)			{return $this->putInCustomData(thumbParams::CUSTOM_DATA_FIELD_CROP_HEIGHT, $v);}
	public function setCropProvider($v)			{return $this->putInCustomData(thumbParams::CUSTOM_DATA_FIELD_CROP_PROVIDER, $v);}
	public function setCropProviderData($v)		{return $this->putInCustomData(thumbParams::CUSTOM_DATA_FIELD_CROP_PROVIDER_DATA, $v);}
	public function setVideoOffset($v)			{return $this->putInCustomData(thumbParams::CUSTOM_DATA_FIELD_VIDEO_OFFSET, $v);}
	public function setWidth($v)				{return $this->putInCustomData(thumbParams::CUSTOM_DATA_FIELD_WIDTH, $v);}
	public function setHeight($v)				{return $this->putInCustomData(thumbParams::CUSTOM_DATA_FIELD_HEIGHT, $v);}
	public function setBackgroundColor($v)		{return $this->putInCustomData(thumbParams::CUSTOM_DATA_FIELD_BACKGROUND_COLOR, $v);}
}