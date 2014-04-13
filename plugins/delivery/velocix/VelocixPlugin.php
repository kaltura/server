<?php
/**
 * @package plugins.velocix
 */
class VelocixPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaEnumerator, IKalturaEventConsumers, IKalturaObjectLoader, IKalturaTypeExtender
{
	const PLUGIN_NAME = 'velocix';
	const VELOCIX_LIVE_EVENT_CONSUMER = 'kVelocixLiveFlowManager';
	const TASK_CONFIG = 0;
	
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
	 * @return array<string> list of enum classes names that extend the base enum name
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('VelocixLiveEntrySourceType', 'VelocixDeliveryProfileType');
			
		if($baseEnumName == 'EntrySourceType')
			return array('VelocixLiveEntrySourceType');
		
		if($baseEnumName == 'DeliveryProfileType')
			return array('VelocixDeliveryProfileType');
			
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
	
	public static function getEventConsumers(){
		return array(self::VELOCIX_LIVE_EVENT_CONSUMER);
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null) {
		// for batch
		if ($baseClass == 'KalturaJobData' && $constructorArgs['coreJobSubType'] == self::getEntrySourceTypeCoreValue(VelocixLiveEntrySourceType::VELOCIX_LIVE))
			return new KalturaVelocixProvisionJobData();
		
		if ($baseClass == 'kProvisionJobData' && $enumValue == self::getEntrySourceTypeCoreValue(VelocixLiveEntrySourceType::VELOCIX_LIVE))
			return new kVelocixProvisionJobData();  
		
		if ($baseClass == 'KProvisionEngine' && $enumValue == KalturaSourceType::VELOCIX_LIVE)
			return new KProvisionEngineVelocix();
		
		if(($baseClass == 'KalturaTokenizer') && ($enumValue == 'kVelocixUrlTokenizer'))
			return new KalturaUrlTokenizerVelocix();
		
		return null;
	}
	
	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getDeliveryProfileType($valueName)
	{
		$apiValue = self::getApiValue($valueName);
		return kPluginableEnumsManager::apiToCore('DeliveryProfileType', $apiValue);
	}

	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue) {
		if ($baseClass == 'DeliveryProfile') {
			if($enumValue == self::getDeliveryProfileType(VelocixDeliveryProfileType::VELOCIX_HDS))
				return 'DeliveryProfileVelocixLiveHds';
			if($enumValue == self::getDeliveryProfileType(VelocixDeliveryProfileType::VELOCIX_HLS))
				return 'DeliveryProfileVelocixLiveHls';
		}
		return null;
		
	}
	
	public static function getExtendedTypes($baseClass, $enumValue) {
		
		if(($baseClass == 'DeliveryProfile') && ($enumValue == 'LIVE')) {
			return array(self::getDeliveryProfileType(VelocixDeliveryProfileType::VELOCIX_HDS),
					self::getDeliveryProfileType(VelocixDeliveryProfileType::VELOCIX_HLS));
		}
		
		return null;
	}
	
}
