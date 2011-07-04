<?php


/**
 * @package plugins.adCuePoint
 * @subpackage model
 */
class AdCuePoint extends CuePoint
{
	const CUSTOM_DATA_FIELD_SOURCE_URL = 'sourceUrl';
	const CUSTOM_DATA_FIELD_AD_TYPE = 'adType';

	public function getSourceUrl()		{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_SOURCE_URL);}
	public function getAdType()			{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_AD_TYPE);}	

	public function setSourceUrl($v)	{return $this->putInCustomData(self::CUSTOM_DATA_FIELD_SOURCE_URL, (string)$v);}
	public function setAdType($v)		{return $this->putInCustomData(self::CUSTOM_DATA_FIELD_AD_TYPE, (int)$v);}
}
