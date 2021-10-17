<?php
/**
 * @package plugins.virtualEvent
 */
class VirtualEventPlugin extends KalturaPlugin implements  IKalturaServices,IKalturaEventConsumers
{
	const PLUGIN_NAME = 'virtualEvent';
	const VIRTUAL_EVENT_CONSUMER = 'kVirtualEventConsumer';
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	
	public static function getServicesMap()
	{
		$map = array(
			'virtualEvent' => 'VirtualEventService',
		);
		return $map;
	}
	
	/**
	 * @param $valueName
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see IKalturaEventConsumers::getEventConsumers()
	 */
	public static function getEventConsumers()
	{
		return array(self::VIRTUAL_EVENT_CONSUMER);
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
}
