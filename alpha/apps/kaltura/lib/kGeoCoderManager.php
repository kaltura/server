<?php

require_once(dirname(__FILE__) . '/../../../../infra/general/BaseEnum.php');
require_once(dirname(__FILE__) . '/../../../lib/enums/geoCoderType.php');
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
		if(!$type)
		{
			$type == geoCoderType::KALTURA;
		}
			
		switch($type)
		{
		case geoCoderType::KALTURA:
			// require direct path as the call may arrive for the caching layer
			require_once(dirname(__FILE__) . '/myIPGeoCoder.class.php');
			return new myIPGeocoder();
			
		case geoCoderType::MAX_MIND:
			// require direct path as the call may arrive for the caching layer			
			require_once(dirname(__FILE__) . '/kMaxMindIPGeoCoder.php');
			return new kMaxMindIPGeocoder();
		}
			
		return KalturaPluginManager::loadObject('kGeoCoder', $type);
	}
}