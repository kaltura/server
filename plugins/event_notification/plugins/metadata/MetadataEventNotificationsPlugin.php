<?php
/**
 * Enable event notifications on metadata objects
 * @package plugins.metadataEventNotifications
 */
class MetadataEventNotificationsPlugin extends KalturaPlugin implements IKalturaPending, IKalturaEnumerator, IKalturaObjectLoader, IKalturaEventNotificationContentEditor
{
	const PLUGIN_NAME = 'metadataEventNotifications';
	
	const METADATA_PLUGIN_NAME = 'metadata';
	
	const EVENT_NOTIFICATION_PLUGIN_NAME = 'eventNotification';
	const EVENT_NOTIFICATION_PLUGIN_VERSION_MAJOR = 1;
	const EVENT_NOTIFICATION_PLUGIN_VERSION_MINOR = 0;
	const EVENT_NOTIFICATION_PLUGIN_VERSION_BUILD = 0;
	
	const METADATA_EMAIL_NOTIFICATION_REGEX = '/\{metadata\:[^:]+\:[^}]+\}/';

	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaPending::dependsOn()
	 */
	public static function dependsOn()
	{
		$eventNotificationVersion = new KalturaVersion(self::EVENT_NOTIFICATION_PLUGIN_VERSION_MAJOR, self::EVENT_NOTIFICATION_PLUGIN_VERSION_MINOR, self::EVENT_NOTIFICATION_PLUGIN_VERSION_BUILD);
		
		$metadataDependency = new KalturaDependency(self::METADATA_PLUGIN_NAME);
		$eventNotificationDependency = new KalturaDependency(self::EVENT_NOTIFICATION_PLUGIN_NAME, $eventNotificationVersion);
		
		return array($metadataDependency, $eventNotificationDependency);
	}
			
	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('MetadataEventNotificationEventObjectType');
	
		if($baseEnumName == 'EventNotificationEventObjectType')
			return array('MetadataEventNotificationEventObjectType');
			
		return array();
	}

	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		return null;
	}
		
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'EventNotificationEventObjectType' && $enumValue == self::getEventNotificationEventObjectTypeCoreValue(MetadataEventNotificationEventObjectType::METADATA))
		{
			return MetadataPeer::OM_CLASS;
		}
					
		return null;
	}
	
	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getEventNotificationEventObjectTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('EventNotificationEventObjectType', $value);
	}
	
	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
	
	/**
	 * Function sweeps the given fields of the emailNotificationTemplate, and parses expressions of the type
	 * {metadata:[metadataProfileSystemName]:[metadataProfileFieldSystemName]}
	 */
	public static function editTemplateFields($sweepFieldValues, $scope, $objectType)
	{
		KalturaLog::debug ('Field values to sweep: ' . print_r($sweepFieldValues, true));
		
		if (! ($scope instanceof kEventScope))
			return array();
		
		if (!method_exists($scope->getObject(), 'getPartnerId'))
			return array();
		
		$partnerId = $scope->getObject()->getPartnerId();
		/* @var $scope kEventScope */
		$metadataContentParameters = array();
		foreach ($sweepFieldValues as $sweepFieldValue)
		{
			//Obtain matches for the set structure {metadata:[profileSystemName][profileFieldSystemName]}
			preg_match_all(self::METADATA_EMAIL_NOTIFICATION_REGEX, $sweepFieldValue, $matches);
			foreach ($matches[0] as $match)
			{				
				$match = str_replace(array ('{', '}'), array ('', ''), $match);
				list ($metadata, $profileSystemName, $fieldSystemName, $format) = explode(':', $match, 4);
				$profile = MetadataProfilePeer::retrieveBySystemName($profileSystemName, $partnerId);
				if (!$profile)
				{
					KalturaLog::info("Metadata profile with system name $profileSystemName not found for this partner. Token will be replaced with empty string.");
					$metadataContentParameters[$match] = '';
					continue;
				}
				
				$objectId = null;
				$metadataObjectId = null;
				//If the metadataProfileobjectType matches the one on the emailNotification, we can proceed
				//If the objectType of the email template is 'asset' we can use the entryId
				//If the objectType of the email template is a metadata object we can use its id
				if (kMetadataManager::getObjectTypeName($profile->getObjectType()) == KalturaPluginManager::getObjectClass('EventNotificationEventObjectType', $objectType))
				{
					$objectId = $scope->getObject()->getId();
				}
				elseif (kMetadataManager::getObjectTypeName($profile->getObjectType()) == 'entry'
						&& ($scope->getObject() instanceof asset))
				{
					$objectId = $scope->getObject()->getEntryId();
				}
				elseif ($scope->getObject() instanceof categoryEntry)
				{
					$profileObject = kMetadataManager::getObjectTypeName($profile->getObjectType());
					$getter = "get{$profileObject}Id";
					KalturaLog::info ("Using $getter in order to retrieve the metadata object ID");
					$categoryEntry = $scope->getObject();
					$objectId = $categoryEntry->$getter();
				}
				elseif (KalturaPluginManager::getObjectClass('EventNotificationEventObjectType', $objectType) == MetadataPeer::OM_CLASS)
				{
					$metadataObjectId = $scope->getObject()->getId();
				}
				
				
				if ($objectId)
				{
					$result = MetadataPeer::retrieveByObject($profile->getId(), $profile->getObjectType(), $objectId);
				}
				elseif ($metadataObjectId)
				{
					$result = MetadataPeer::retrieveByPK($metadataObjectId);
				}
				else 
				{
					//There is not enough specification regarding the required metadataObject, abort.
					KalturaLog::info("The template does not contain an object Id for which custom metadata can be retrieved. Token will be replaced with empty string.");
					$metadataContentParameters[$match] = '';
					continue;	
				}
				
				if (!$result)
				{
					KalturaLog::info("Metadata object could not be retrieved. Token will be replaced with empty string.");
					$metadataContentParameters[$match] = '';
					continue;
				}
				
				$strvals = kMetadataManager::getMetadataValueForField($result, $fieldSystemName);
				foreach ($strvals as &$strval)
				{
					if ($format && is_numeric($strval))
					{
						$strval = date($format,$strval);
					}
				}
				
				$metadataContentParameters[$match] = implode(',', $strvals);
			}
		}

		return $metadataContentParameters;
	}
}
