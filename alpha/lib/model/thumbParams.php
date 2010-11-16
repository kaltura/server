<?php

/**
 * Subclass for representing a row from the 'flavor_params' table, used for thumb_params
 *
 * 
 *
 * @package lib.model
 */ 
class thumbParams extends flavorParams
{
	const TAG_DEFAULT_THUMB = "default_thumb";
	
	const CUSTOM_DATA_FIELD_CROP_TYPE = "ThumbCropType";
	const CUSTOM_DATA_FIELD_QUALITY = "ThumbQuality";
	const CUSTOM_DATA_FIELD_CROP_X = "ThumbCropX";
	const CUSTOM_DATA_FIELD_CROP_Y = "ThumbCropY";
	const CUSTOM_DATA_FIELD_CROP_WID = "ThumbCropWid";
	const CUSTOM_DATA_FIELD_CROP_HGT = "ThumbCropHgt";
	const CUSTOM_DATA_FIELD_CROP_PROVIDERS = "ThumbCropProviders";
	const CUSTOM_DATA_FIELD_CROP_PROVIDERS_DATA = "ThumbCropProvidersData";
	const CUSTOM_DATA_FIELD_VIDEO_OFFSET = "ThumbVideoOffset";
	const CUSTOM_DATA_FIELD_SCALE_WID = "ThumbScaleWid";
	const CUSTOM_DATA_FIELD_SCALE_HGT = "ThumbScaleHgt";
	const CUSTOM_DATA_FIELD_BKG_COLOR = "ThumbBkgColor";
	
	public function getThumbCropType()	{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_CROP_TYPE);}
	public function getThumbQuality()	{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_QUALITY);}
	public function getThumbCropX()	{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_CROP_X);}
	public function getThumbCropY()	{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_CROP_Y);}
	public function getThumbCropWid()	{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_CROP_WID);}
	public function getThumbCropHgt()	{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_CROP_HGT);}
	public function getThumbCropProviders()	{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_CROP_PROVIDERS);}
	public function getThumbCropProvidersData()	{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_CROP_PROVIDERS_DATA);}
	public function getThumbVideoOffset()	{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_VIDEO_OFFSET);}
	public function getThumbScaleWid()	{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_SCALE_WID);}
	public function getThumbScaleHgt()	{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_SCALE_HGT);}
	public function getThumbBkgColor()	{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_BKG_COLOR);}
}