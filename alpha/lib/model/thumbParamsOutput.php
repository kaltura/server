<?php
/**
 * Subclass for representing a row from the 'flavor_params_output' table, used for thumb_params_output
 *
 * 
 *
 * @package lib.model
 */ 
class thumbParamsOutput extends flavorParamsOutput
{
	
	public function getThumbCropType()	{return $this->getFromCustomData(thumbParams::CUSTOM_DATA_FIELD_CROP_TYPE);}
	public function getThumbQuality()	{return $this->getFromCustomData(thumbParams::CUSTOM_DATA_FIELD_QUALITY);}
	public function getThumbCropX()	{return $this->getFromCustomData(thumbParams::CUSTOM_DATA_FIELD_CROP_X);}
	public function getThumbCropY()	{return $this->getFromCustomData(thumbParams::CUSTOM_DATA_FIELD_CROP_Y);}
	public function getThumbCropWid()	{return $this->getFromCustomData(thumbParams::CUSTOM_DATA_FIELD_CROP_WID);}
	public function getThumbCropHgt()	{return $this->getFromCustomData(thumbParams::CUSTOM_DATA_FIELD_CROP_HGT);}
	public function getThumbCropProviders()	{return $this->getFromCustomData(thumbParams::CUSTOM_DATA_FIELD_CROP_PROVIDERS);}
	public function getThumbCropProvidersData()	{return $this->getFromCustomData(thumbParams::CUSTOM_DATA_FIELD_CROP_PROVIDERS_DATA);}
	public function getThumbVideoOffset()	{return $this->getFromCustomData(thumbParams::CUSTOM_DATA_FIELD_VIDEO_OFFSET);}
	public function getThumbScaleWid()	{return $this->getFromCustomData(thumbParams::CUSTOM_DATA_FIELD_SCALE_WID);}
	public function getThumbScaleHgt()	{return $this->getFromCustomData(thumbParams::CUSTOM_DATA_FIELD_SCALE_HGT);}
	public function getThumbBkgColor()	{return $this->getFromCustomData(thumbParams::CUSTOM_DATA_FIELD_BKG_COLOR);}
}