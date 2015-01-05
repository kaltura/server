<?php
/**
 * @package plugins.businessProcessNotification
 */
class BusinessProcessNotificationPlugin extends KalturaPlugin implements IKalturaVersion, IKalturaPending, IKalturaObjectLoader, IKalturaEnumerator, IKalturaServices, IKalturaApplicationPartialView, IKalturaAdminConsolePages, IKalturaEventConsumers, IKalturaConfigurator
{
	const PLUGIN_NAME = 'businessProcessNotification';
	const PLUGIN_VERSION_MAJOR = 1;
	const PLUGIN_VERSION_MINOR = 0;
	const PLUGIN_VERSION_BUILD = 0;
	
	const EVENT_NOTIFICATION_PLUGIN_NAME = 'eventNotification';
	const EVENT_NOTIFICATION_PLUGIN_VERSION_MAJOR = 1;
	const EVENT_NOTIFICATION_PLUGIN_VERSION_MINOR = 0;
	const EVENT_NOTIFICATION_PLUGIN_VERSION_BUILD = 0;
	
	const BUSINESS_PROCESS_NOTIFICATION_FLOW_MANAGER = 'kBusinessProcessNotificationFlowManager';
	
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
	 * @see IKalturaEventConsumers::getEventConsumers()
	 */
	public static function getEventConsumers()
	{
		return array(self::BUSINESS_PROCESS_NOTIFICATION_FLOW_MANAGER);
	}
			
	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('BusinessProcessNotificationTemplateType');
	
		if($baseEnumName == 'EventNotificationTemplateType')
			return array('BusinessProcessNotificationTemplateType');
			
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		$class = self::getObjectClass($baseClass, $enumValue);
		if($class)
		{
			if(is_array($constructorArgs))
			{
				$reflect = new ReflectionClass($class);
				return $reflect->newInstanceArgs($constructorArgs);
			}
			
			return new $class();
		}
			
		return null;
	}
		
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'KalturaEventNotificationDispatchJobData')
		{
			if(
				$enumValue == self::getBusinessProcessNotificationTemplateTypeCoreValue(BusinessProcessNotificationTemplateType::BPM_START) || 
				$enumValue == self::getBusinessProcessNotificationTemplateTypeCoreValue(BusinessProcessNotificationTemplateType::BPM_SIGNAL) || 
				$enumValue == self::getBusinessProcessNotificationTemplateTypeCoreValue(BusinessProcessNotificationTemplateType::BPM_ABORT)
			)
				return 'KalturaBusinessProcessNotificationDispatchJobData';
		}
		
		if($baseClass == 'EventNotificationTemplate')
		{
			if($enumValue == self::getBusinessProcessNotificationTemplateTypeCoreValue(BusinessProcessNotificationTemplateType::BPM_START))
				return 'BusinessProcessStartNotificationTemplate';
				
			if($enumValue == self::getBusinessProcessNotificationTemplateTypeCoreValue(BusinessProcessNotificationTemplateType::BPM_SIGNAL))
				return 'BusinessProcessSignalNotificationTemplate';
		}
	
		if($baseClass == 'KalturaEventNotificationTemplate')
		{
			if($enumValue == self::getBusinessProcessNotificationTemplateTypeCoreValue(BusinessProcessNotificationTemplateType::BPM_START))
				return 'KalturaBusinessProcessStartNotificationTemplate';
				
			if($enumValue == self::getBusinessProcessNotificationTemplateTypeCoreValue(BusinessProcessNotificationTemplateType::BPM_SIGNAL))
				return 'KalturaBusinessProcessSignalNotificationTemplate';
		}
	
		if($baseClass == 'Form_EventNotificationTemplateConfiguration')
		{
			if(
				$enumValue == Kaltura_Client_EventNotification_Enum_EventNotificationTemplateType::BPM_START || 
				$enumValue == Kaltura_Client_EventNotification_Enum_EventNotificationTemplateType::BPM_SIGNAL || 
				$enumValue == Kaltura_Client_EventNotification_Enum_EventNotificationTemplateType::BPM_ABORT
			)
				return 'Form_BusinessProcessNotificationTemplateConfiguration';
		}
	
		if($baseClass == 'Kaltura_Client_EventNotification_Type_EventNotificationTemplate')
		{
			if($enumValue == Kaltura_Client_EventNotification_Enum_EventNotificationTemplateType::BPM_START)
				return 'Kaltura_Client_BusinessProcessNotification_Type_BusinessProcessStartNotificationTemplate';
				
			if($enumValue == Kaltura_Client_EventNotification_Enum_EventNotificationTemplateType::BPM_SIGNAL)
				return 'Kaltura_Client_BusinessProcessNotification_Type_BusinessProcessSignalNotificationTemplate';
		}
	
		if($baseClass == 'KDispatchEventNotificationEngine')
		{
			if(
				$enumValue == KalturaEventNotificationTemplateType::BPM_START ||
				$enumValue == KalturaEventNotificationTemplateType::BPM_SIGNAL ||
				$enumValue == KalturaEventNotificationTemplateType::BPM_ABORT
			)
				return 'KDispatchBusinessProcessNotificationEngine';
		}
			
		return null;
	}

	/* (non-PHPdoc)
	 * @see IKalturaPending::dependsOn()
	 */
	public static function dependsOn() 
	{
		$minVersion = new KalturaVersion(
			self::EVENT_NOTIFICATION_PLUGIN_VERSION_MAJOR,
			self::EVENT_NOTIFICATION_PLUGIN_VERSION_MINOR,
			self::EVENT_NOTIFICATION_PLUGIN_VERSION_BUILD
		);
		$dependency = new KalturaDependency(self::EVENT_NOTIFICATION_PLUGIN_NAME, $minVersion);
		
		return array($dependency);
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaApplicationPartialView::getApplicationPartialViews()
	 */
	public static function getApplicationPartialViews($controller, $action)
	{
		if($controller == 'plugin' && $action == 'EventNotificationTemplateConfigureAction')
		{
			return array(
				new Kaltura_View_Helper_BusinessProcessNotificationTemplateConfigure(),
			);
		}
		
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaAdminConsolePages::getApplicationPages()
	 */
	public static function getApplicationPages() 
	{
		return array(
			new BusinessProcessNotificationTemplatesListProcessesAction(),
		);
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaConfigurator::getConfig()
	 */
	public static function getConfig($configName)
	{
		if($configName == 'testme')
			return new Zend_Config_Ini(dirname(__FILE__) . '/config/testme.ini');
			
		if($configName == 'generator')
			return new Zend_Config_Ini(dirname(__FILE__) . '/config/generator.ini');
			
		return null;
	}

	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getBusinessProcessNotificationTemplateTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('EventNotificationTemplateType', $value);
	}
	
	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}

	/* (non-PHPdoc)
	 * @see IKalturaServices::getServicesMap()
	 */
	public static function getServicesMap() 
	{
		return array(
			'businessProcessServer' => 'BusinessProcessServerService',
		);
	}
}
