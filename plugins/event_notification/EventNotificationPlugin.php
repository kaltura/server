<?php
/**
 * @package plugins.eventNotification
 */
class EventNotificationPlugin extends KalturaPlugin implements IKalturaVersion, IKalturaPermissions, IKalturaEventConsumers, IKalturaServices, IKalturaAdminConsolePages, IKalturaConfigurator, IKalturaEnumerator, IKalturaObjectLoader
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
		if($partnerId == Partner::ADMIN_CONSOLE_PARTNER_ID || $partnerId == Partner::BATCH_PARTNER_ID)
			return true;
			
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if ($partner)
			return $partner->getPluginEnabled(self::PLUGIN_NAME);

		return false;
	}
			
	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('EventNotificationBatchType', 'EventNotificationPermissionName', 'EventNotificationConditionType');
	
		if($baseEnumName == 'BatchJobType')
			return array('EventNotificationBatchType');
			
		if($baseEnumName == 'PermissionName')
			return array('EventNotificationPermissionName');
			
		if($baseEnumName == 'ConditionType')
			return array('EventNotificationConditionType');
			
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
	 * @see IKalturaAdminConsolePages::getApplicationPages()
	 */
	public static function getApplicationPages() 
	{
		return array(
			new EventNotificationTemplatesListAction(),
			new EventNotificationTemplateConfigureAction(),
			new EventNotificationTemplateUpdateStatusAction(),
		);
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

	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		if($baseClass == 'KalturaJobData' && $enumValue == self::getApiValue(EventNotificationBatchType::EVENT_NOTIFICATION_HANDLER) && isset($constructorArgs['coreJobSubType']))
			return KalturaPluginManager::loadObject('KalturaEventNotificationDispatchJobData', $constructorArgs['coreJobSubType']);
	
		if($baseClass == 'KalturaCondition')
		{
			if($enumValue == EventNotificationPlugin::getConditionTypeCoreValue(EventNotificationConditionType::EVENT_NOTIFICATION_FIELD))
				return new KalturaEventFieldCondition();
				
			if($enumValue == EventNotificationPlugin::getConditionTypeCoreValue(EventNotificationConditionType::EVENT_NOTIFICATION_OBJECT_CHANGED))
				return new KalturaEventObjectChangedCondition();
		}
		
		return null;
	}
		
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'KalturaJobData' && $enumValue == self::getApiValue(EventNotificationBatchType::EVENT_NOTIFICATION_HANDLER))
			return 'KalturaEventNotificationDispatchJobData';
			
		if($baseClass == 'EventNotificationEventObjectType')
		{
			switch($enumValue)
			{
			    case EventNotificationEventObjectType::ENTRY:
			    	return 'entry';
			    	
			    case EventNotificationEventObjectType::CATEGORY:
					return 'category';

			    case EventNotificationEventObjectType::ASSET:
					return 'asset';

			    case EventNotificationEventObjectType::FLAVORASSET:
					return 'flavorAsset';

			    case EventNotificationEventObjectType::THUMBASSET:
					return 'thumbAsset';

			    case EventNotificationEventObjectType::KUSER:
					return 'kuser';

			    case EventNotificationEventObjectType::ACCESSCONTROL:
					return 'accessControl';

				case EventNotificationEventObjectType::BATCHJOB:
					return 'BatchJob';

				case EventNotificationEventObjectType::BULKUPLOADRESULT:
					return 'BulkUploadResult';

				case EventNotificationEventObjectType::CATEGORYKUSER:
					return 'categoryKuser';

				case EventNotificationEventObjectType::CONVERSIONPROFILE2:
					return 'conversionProfile2';

				case EventNotificationEventObjectType::FLAVORPARAMS:
					return 'flavorParams';

				case EventNotificationEventObjectType::FLAVORPARAMSCONVERSIONPROFILE:
					return 'flavorParamsConversionProfile';

				case EventNotificationEventObjectType::FLAVORPARAMSOUTPUT:
					return 'flavorParamsOutput';

				case EventNotificationEventObjectType::GENERICSYNDICATIONFEED:
					return 'genericSyndicationFeed';

				case EventNotificationEventObjectType::KUSERTOUSERROLE:
					return 'KuserToUserRole';

				case EventNotificationEventObjectType::PARTNER:
					return 'Partner';

				case EventNotificationEventObjectType::PERMISSION:
					return 'Permission';

				case EventNotificationEventObjectType::PERMISSIONITEM:
					return 'PermissionItem';

				case EventNotificationEventObjectType::PERMISSIONTOPERMISSIONITEM:
					return 'PermissionToPermissionItem';

				case EventNotificationEventObjectType::SCHEDULER:
					return 'Scheduler';

				case EventNotificationEventObjectType::SCHEDULERCONFIG:
					return 'SchedulerConfig';

				case EventNotificationEventObjectType::SCHEDULERSTATUS:
					return 'SchedulerStatus';

				case EventNotificationEventObjectType::SCHEDULERWORKER:
					return 'SchedulerWorker';

				case EventNotificationEventObjectType::STORAGEPROFILE:
					return 'StorageProfile';

				case EventNotificationEventObjectType::SYNDICATIONFEED:
					return 'syndicationFeed';

				case EventNotificationEventObjectType::THUMBPARAMS:
					return 'thumbParams';

				case EventNotificationEventObjectType::THUMBPARAMSOUTPUT:
					return 'thumbParamsOutput';

				case EventNotificationEventObjectType::UPLOADTOKEN:
					return 'UploadToken';

				case EventNotificationEventObjectType::USERLOGINDATA:
					return 'UserLoginData';

				case EventNotificationEventObjectType::USERROLE:
					return 'UserRole';

				case EventNotificationEventObjectType::WIDGET:
					return 'widget';

				case EventNotificationEventObjectType::CATEGORYENTRY:
					return 'categoryEntry';
			}
		}
		
		return null;
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
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getConditionTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('ConditionType', $value);
	}
	
	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
}
