<?php
/**
 * @package Core
 * @subpackage AccessControl
 */
class kGeoCoderManager
{
	/**
	 * @param int $type of enum geoCoderType
	 * @return kGeoCoder
	 */
	public static function getGeoCoder($type = null)
	{
		if(!$type || $type == geoCoderType::KALTURA)
			return new myIPGeocoder();
			
		return KalturaPluginManager::loadObject('kGeoCoder', $type);
	}
}