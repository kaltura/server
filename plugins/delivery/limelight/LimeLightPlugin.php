<?php
/**
 * @package plugins.limeLight
 */
class LimeLightPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaObjectLoader, IKalturaEnumerator, IKalturaEventConsumers
{
	const PLUGIN_NAME = 'limeLight';
	const LIMELIGHT_LIVE_EVENT_CONSUMER = 'kLimeLightLiveFlowManager';
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	public static function isAllowedPartner($partnerId)
	{
		if (in_array($partnerId, array(Partner::ADMIN_CONSOLE_PARTNER_ID, Partner::BATCH_PARTNER_ID)))
			return true;
		
		$partner = PartnerPeer::retrieveByPK($partnerId);
		return $partner->getPluginEnabled(self::PLUGIN_NAME);		
	}
	
	/**
	 * @param string $baseClass
	 * @param string $enumValue
	 * @param array $constructorArgs
	 * @return object
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{			
		$objectClass = self::getObjectClass($baseClass, $enumValue);
		
		if (is_null($objectClass)) {
			return null;
		}
		
		if (!is_null($constructorArgs))
		{
			$reflect = new ReflectionClass($objectClass);
			return $reflect->newInstanceArgs($constructorArgs);
		}
		else
		{
			return new $objectClass();
		}
	}
	
	
	/**
	 * @param string $baseClass
	 * @param string $enumValue
	 * @return string
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{			
	    if ($baseClass == 'KProvisionEngine')
		{
		    if ($enumValue == self::getApiValue(LimeLightLiveEntrySourceType::LIMELIGHT_LIVE))
			{
				return 'KProvisionEngineLimelight';
			}
		}
		
		return null;
	}
	
	
	/**
	 * @return array<string> list of enum classes names that extend the base enum name
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('LimeLightLiveEntrySourceType');
			
		if($baseEnumName == 'EntrySourceType')
			return array('LimeLightLiveEntrySourceType');
			
		return array();
	}
	
	
	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
	
	//get real source_type_value from DB. 
	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getEntrySourceTypeCoreValue($valueName)
	{
		$apiValue = self::getApiValue($valueName);
		return kPluginableEnumsManager::apiToCore('EntrySourceType', $apiValue);
	}
	
	
	/**
	 * @return kLimeLightLiveParams
	 */
	public static function getLimeLightLiveParams($partner)
	{
		$limeLightLiveParams = unserialize($partner->getFromCustomData(self::getPluginName() . '_live_params'));
		if (!$limeLightLiveParams) {
			return null;
		}
		return $limeLightLiveParams;
	}
	
	public static function setLimeLightLiveParams($partner, $limeLightLiveParams)
	{		
		$content = serialize($limeLightLiveParams);
		$partner->putInCustomData(self::getPluginName() . '_live_params', $content);
	}
		
	
	public static function getEventConsumers(){
		return array(self::LIMELIGHT_LIVE_EVENT_CONSUMER);
	}
	
	
}
