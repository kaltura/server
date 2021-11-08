<?php
/**
 * @package plugins.virtualEvent
 */
class VirtualEventPlugin extends KalturaPlugin implements IKalturaServices, IKalturaObjectLoader,IKalturaPending
{
	const PLUGIN_NAME = 'virtualEvent';
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	public static function dependsOn()
	{
		$dependency = new KalturaDependency(SchedulePlugin::getPluginName());
		return array($dependency);
	}
	
	public static function getServicesMap()
	{
		return array(self::PLUGIN_NAME => 'VirtualEventService',);
	}
	
	/**
	 * @param $valueName
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
	
	public static function isAllowedPartner($partnerId)
	{
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if ($partner)
		{
			return $partner->getPluginEnabled(self::PLUGIN_NAME);
		}
		
		return false;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		if (($baseClass == 'KalturaScheduleEvent') && ($enumValue == VirtualScheduleEventType::VIRTUAL))
		{
			return new KalturaVirtualScheduleEvent();
		}
	}
	
	/*
	 * (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if(($baseClass == 'ScheduleEvent') && ($enumValue == VirtualScheduleEventType::VIRTUAL))
		{
			return 'VirtualScheduleEvent';
		}
	}
	

}
