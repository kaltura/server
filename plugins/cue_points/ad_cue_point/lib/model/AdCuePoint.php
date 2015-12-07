<?php


/**
 * @package plugins.adCuePoint
 * @subpackage model
 */
class AdCuePoint extends CuePoint implements IMetadataObject
{
	const CUSTOM_DATA_FIELD_SOURCE_URL = 'sourceUrl';
	const CUSTOM_DATA_FIELD_AD_TYPE = 'adType';

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
		$this->setType(AdCuePointPlugin::getCuePointTypeCoreValue(AdCuePointType::AD));
	}
	
	public function getSourceUrl()		{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_SOURCE_URL);}
	public function getAdType()			{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_AD_TYPE);}	

	public function setSourceUrl($v)	{return $this->putInCustomData(self::CUSTOM_DATA_FIELD_SOURCE_URL, (string)$v);}
	public function setAdType($v)		{return $this->putInCustomData(self::CUSTOM_DATA_FIELD_AD_TYPE, (int)$v);}
	
	/* (non-PHPdoc)
	 * @see IMetadataObject::getMetadataObjectType()
	 */
	public function getMetadataObjectType()
	{
		return AdCuePointMetadataPlugin::getMetadataObjectTypeCoreValue(AdCuePointMetadataObjectType::AD_CUE_POINT);
	}
	
	public function getIsPublic()	              {return true;}
}
