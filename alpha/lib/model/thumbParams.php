<?php

/**
 * Subclass for representing a row from the 'flavor_params' table, used for thumb_params
 *
 * 
 *
 * @package lib.model
 */ 
class thumbParams extends assetParams
{
	const TAG_DEFAULT_THUMB = "default_thumb";
	
	const CUSTOM_DATA_FIELD_CROP_TYPE = "ThumbCropType";
	const CUSTOM_DATA_FIELD_QUALITY = "ThumbQuality";
	const CUSTOM_DATA_FIELD_CROP_X = "ThumbCropX";
	const CUSTOM_DATA_FIELD_CROP_Y = "ThumbCropY";
	const CUSTOM_DATA_FIELD_CROP_WIDTH = "ThumbCropWidth";
	const CUSTOM_DATA_FIELD_CROP_HEIGHT = "ThumbCropHeight";
	const CUSTOM_DATA_FIELD_CROP_PROVIDER = "ThumbCropProvider";
	const CUSTOM_DATA_FIELD_CROP_PROVIDER_DATA = "ThumbCropProviderData";
	const CUSTOM_DATA_FIELD_VIDEO_OFFSET = "ThumbVideoOffset";
	const CUSTOM_DATA_FIELD_SCALE_WIDTH = "ThumbScaleWidth";
	const CUSTOM_DATA_FIELD_SCALE_HEIGHT = "ThumbScaleHeight";
	const CUSTOM_DATA_FIELD_BACKGROUND_COLOR = "ThumbBackgroundColor";
	
	public function getCropType()				{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_CROP_TYPE);}
	public function getQuality()				{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_QUALITY);}
	public function getCropX()					{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_CROP_X);}
	public function getCropY()					{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_CROP_Y);}
	public function getCropWidth()				{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_CROP_WIDTH);}
	public function getCropHeight()				{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_CROP_HEIGHT);}
	public function getCropProvider()			{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_CROP_PROVIDER);}
	public function getCropProviderData()		{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_CROP_PROVIDER_DATA);}
	public function getVideoOffset()			{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_VIDEO_OFFSET);}
	public function getScaleWidth()				{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_SCALE_WIDTH);}
	public function getScaleHeight()			{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_SCALE_HEIGHT);}
	public function getBackgroundColor()		{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_BACKGROUND_COLOR);}

	public function setCropType($v)				{return $this->putInCustomData(self::CUSTOM_DATA_FIELD_CROP_TYPE, $v);}
	public function setQuality($v)				{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_QUALITY, $v);}
	public function setCropX($v)				{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_CROP_X, $v);}
	public function setCropY($v)				{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_CROP_Y, $v);}
	public function setCropWidth($v)			{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_CROP_WIDTH, $v);}
	public function setCropHeight($v)			{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_CROP_HEIGHT, $v);}
	public function setCropProvider($v)			{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_CROP_PROVIDER, $v);}
	public function setCropProviderData($v)		{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_CROP_PROVIDER_DATA, $v);}
	public function setVideoOffset($v)			{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_VIDEO_OFFSET, $v);}
	public function setScaleWidth($v)			{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_SCALE_WIDTH, $v);}
	public function setScaleHeight($v)			{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_SCALE_HEIGHT, $v);}
	public function setBackgroundColor($v)		{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_BACKGROUND_COLOR, $v);}
}