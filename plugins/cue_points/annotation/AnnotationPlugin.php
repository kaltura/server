<?php
/**
 * @package plugins.annotation
 */
class AnnotationPlugin extends KalturaPlugin implements IKalturaServices, IKalturaPermissions, IKalturaEnumerator
{
	const PLUGIN_NAME = 'annotation';
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	public static function isAllowedPartner($partnerId)
	{
		$partner = PartnerPeer::retrieveByPK($partnerId);
		return $partner->getPluginEnabled(self::PLUGIN_NAME);
	}

	/**
	 * @return array<string,string> in the form array[serviceName] = serviceClass
	 */
	public static function getServicesMap()
	{
		$map = array(
			'annotation' => 'AnnotationService',
		);
		return $map;
	}
	
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('AnnotationCuePointType');
	
		if($baseEnumName == 'CuePointType')
			return array('AnnotationCuePointType');
			
		return array();
	}
	
	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getCuePointTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('CuePointType', $value);
	}
}
