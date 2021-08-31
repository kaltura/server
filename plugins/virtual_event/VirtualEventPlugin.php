<?php
/**
 * @package plugins.virtualEvent
 */
class VirtualEventPlugin extends KalturaPlugin implements  IKalturaServices
{
	const PLUGIN_NAME = 'virtualEvent';
	
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
	
	public static function isAllowedPartner($partnerId)
	{
		if($partnerId == Partner::ADMIN_CONSOLE_PARTNER_ID || $partnerId == Partner::BATCH_PARTNER_ID)
			return true;
		
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if ($partner)
			return $partner->getPluginEnabled(self::PLUGIN_NAME);
		
		return false;
	}
}
