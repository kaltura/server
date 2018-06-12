<?php
/**
 * @package plugins.emailNotification
 * 
 * 
 * TODO
 * Add event consumer to consume new email jobs and dispath event notification instead
 * Untill all mails are sent throgh events
 */
class EmailNotificationPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaPending, IKalturaObjectLoader, IKalturaEnumerator
{
	const PLUGIN_NAME = 'emailNotification';
	
	const EVENT_NOTIFICATION_PLUGIN_NAME = 'eventNotification';
	const EVENT_NOTIFICATION_PLUGIN_VERSION_MAJOR = 1;
	const EVENT_NOTIFICATION_PLUGIN_VERSION_MINOR = 0;
	const EVENT_NOTIFICATION_PLUGIN_VERSION_BUILD = 0;
	
	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaPermissions::isAllowedPartner()
	 */
	public static function isAllowedPartner($partnerId)
	{
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
			return array('EmailNotificationTemplateType', 'EmailNotificationFileSyncObjectType');
	
		if($baseEnumName == 'EventNotificationTemplateType')
			return array('EmailNotificationTemplateType');
			
		if($baseEnumName == 'FileSyncObjectType')
			return array('EmailNotificationFileSyncObjectType');
			
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		if($baseClass == 'ISyncableFile' && $enumValue == self::getEmailNotificationFileSyncObjectTypeCoreValue(EmailNotificationFileSyncObjectType::EMAIL_NOTIFICATION_TEMPLATE) && isset($constructorArgs['objectId']))
			return EventNotificationTemplatePeer::retrieveTypeByPK(self::getEmailNotificationTemplateTypeCoreValue(EmailNotificationTemplateType::EMAIL), $constructorArgs['objectId']);
		
			
		if ($baseClass == 'KEmailNotificationRecipientEngine')
		{
			list($recipientJobData) = $constructorArgs;
			switch ($enumValue)	
			{
				case KalturaEmailNotificationRecipientProviderType::CATEGORY:
					return new KEmailNotificationCategoryRecipientEngine($recipientJobData);
					break;
				case KalturaEmailNotificationRecipientProviderType::STATIC_LIST:
					return new KEmailNotificationStaticRecipientEngine($recipientJobData);
					break;
				case KalturaEmailNotificationRecipientProviderType::USER:
					return new KEmailNotificationUserRecipientEngine($recipientJobData);
					break;
				case KalturaEmailNotificationRecipientProviderType::GROUP:
					return new KEMailNotificationGroupRecipientEngine($recipientJobData);
					break;
			}
		}
		
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
		if($baseClass == 'KalturaEventNotificationDispatchJobData' && $enumValue == self::getEmailNotificationTemplateTypeCoreValue(EmailNotificationTemplateType::EMAIL))
			return 'KalturaEmailNotificationDispatchJobData';
	
		if($baseClass == 'EventNotificationTemplate' && $enumValue == self::getEmailNotificationTemplateTypeCoreValue(EmailNotificationTemplateType::EMAIL))
			return 'EmailNotificationTemplate';
	
		if($baseClass == 'KalturaEventNotificationTemplate' && $enumValue == self::getEmailNotificationTemplateTypeCoreValue(EmailNotificationTemplateType::EMAIL))
			return 'KalturaEmailNotificationTemplate';
	
		if($baseClass == 'Form_EventNotificationTemplateConfiguration' && $enumValue == Kaltura_Client_EventNotification_Enum_EventNotificationTemplateType::EMAIL)
			return 'Form_EmailNotificationTemplateConfiguration';
	
		if($baseClass == 'Kaltura_Client_EventNotification_Type_EventNotificationTemplate' && $enumValue == Kaltura_Client_EventNotification_Enum_EventNotificationTemplateType::EMAIL)
			return 'Kaltura_Client_EmailNotification_Type_EmailNotificationTemplate';
	
		if($baseClass == 'KDispatchEventNotificationEngine' && $enumValue == KalturaEventNotificationTemplateType::EMAIL)
			return 'KDispatchEmailNotificationEngine';
			
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

	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getEmailNotificationFileSyncObjectTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('FileSyncObjectType', $value);
	}
	
	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getEmailNotificationTemplateTypeCoreValue($valueName)
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
}
