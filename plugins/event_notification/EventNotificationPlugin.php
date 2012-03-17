<?php
/**
 * @package plugins.eventNotification
 */
class EventNotificationPlugin extends KalturaPlugin implements IKalturaVersion, IKalturaPermissions, IKalturaEventConsumers, IKalturaServices, IKalturaMemoryCleaner, IKalturaAdminConsolePages, IKalturaConfigurator, IKalturaEnumerator
{
	const PLUGIN_NAME = 'eventNotification';
	const PLUGIN_VERSION_MAJOR = 1;
	const PLUGIN_VERSION_MINOR = 0;
	const PLUGIN_VERSION_BUILD = 0;
	
	const EVENT_NOTIFICATION_FLOW_MANAGER = 'kEventNotificationFlowManager';
	
	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaVersion::getVersion()
	 */
	public static function getVersion()
	{
		return new KalturaVersion(
			self::PLUGIN_VERSION_MAJOR,
			self::PLUGIN_VERSION_MINOR,
			self::PLUGIN_VERSION_BUILD
		);		
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaPermissions::isAllowedPartner()
	 */
	public static function isAllowedPartner($partnerId)
	{
		$partner = PartnerPeer::retrieveByPK($partnerId);
		return $partner->getPluginEnabled(self::PLUGIN_NAME);		
	}
			
	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('EventNotificationBatchType', 'EventNotificationPermissionName');
	
		if($baseEnumName == 'BatchJobType')
			return array('EventNotificationBatchType');
			
		if($baseEnumName == 'PermissionName')
			return array('EventNotificationPermissionName');
			
		return array();
	}
			
	/* (non-PHPdoc)
	 * @see IKalturaEventConsumers::getEventConsumers()
	 */
	public static function getEventConsumers()
	{
		return array(self::EVENT_NOTIFICATION_FLOW_MANAGER);
	}

	/* (non-PHPdoc)
	 * @see IKalturaConfigurator::getConfig()
	 */
	public static function getConfig($configName) 
	{
		if($configName == 'generator')
			return new Zend_Config_Ini(dirname(__FILE__) . '/config/generator.ini');
	
		if($configName == 'testme')
			return new Zend_Config_Ini(dirname(__FILE__) . '/config/testme.ini');
			
		return null;
	}

	/* (non-PHPdoc)
	 * @see IKalturaAdminConsolePages::getAdminConsolePages()
	 */
	public static function getAdminConsolePages() 
	{
		return array(
			new EventNotificationTemplatesListAction(),
			new EventNotificationTemplatesConfigureAction(),
			new EventNotificationTemplatesUpdateStatusAction(),
		);
	}

	/* (non-PHPdoc)
	 * @see IKalturaMemoryCleaner::cleanMemory()
	 */
	public static function cleanMemory() 
	{
		EventNotificationTemplatePeer::clearInstancePool();
	}

	/* (non-PHPdoc)
	 * @see IKalturaServices::getServicesMap()
	 */
	public static function getServicesMap() 
	{
		return array(
			'eventNotificationTemplate' => 'EventNotificationTemplateService',
		);
	}
	
	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getBatchJobTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('BatchJobType', $value);
	}
	
	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
}
