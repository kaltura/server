<?php
/**
 * Enable event notifications on metadata objects
 * @package plugins.metadataEventNotifications
 */
class MetadataEventNotificationsPlugin extends KalturaPlugin implements IKalturaPending, IKalturaEnumerator, IKalturaObjectLoader, IKalturaEmailNotificationContentEditor
{
	const PLUGIN_NAME = 'metadataEventNotifications';
	
	const METADATA_PLUGIN_NAME = 'metadata';
	
	const EVENT_NOTIFICATION_PLUGIN_NAME = 'eventNotification';
	const EVENT_NOTIFICATION_PLUGIN_VERSION_MAJOR = 1;
	const EVENT_NOTIFICATION_PLUGIN_VERSION_MINOR = 0;
	const EVENT_NOTIFICATION_PLUGIN_VERSION_BUILD = 0;
	
	const METADATA_EMAIL_NOTIFICATION_REGEX = '/\{metadata:\w+\:\w+\}/';
	
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
	public static function editTemplateFields(EmailNotificationTemplate $emailNotificationTemplate, kScope $scope)
	{
		KalturaLog::info("Sweeping Email Notification Template with id {$emailNotificationTemplate->getId()} for metadata tokens.");
		if (! ($scope instanceof kEventScope))
			return array();
		
		$sweepFields = array ('subject', 'body', 'to', 'cc', 'bcc');
		$sweepFieldValues = array();
		foreach ($sweepFields as $sweepField)
		{
			//Get the field value
			$getter = "get$sweepField";
			$fieldValue = $emailNotificationTemplate->$getter();
			
			if (is_string($fieldValue))
				$sweepFieldValues[] = $fieldValue;
			elseif ($fieldValue instanceof kEmailNotificationStaticRecipientProvider)
			{
				/* @var $fieldValue kEmailNotificationStaticRecipientProvider */
				foreach($fieldValue->getEmailRecipients() as $emailRecipient)
				{
					/* @var $emailRecipient kEmailNotificationRecipient */
					$sweepFieldValues[] = $emailRecipient->getEmail();
					$sweepFieldValues[] = $emailRecipient->getName();
				}
			}
			
		}
		$metadataContentParameters = array();
		foreach ($sweepFieldValues as $sweepFieldValue)
		{
			//Get the field value
			$getter = "get$sweepField";
			$fieldValue = $emailNotificationTemplate->$getter();
			$matches = array();
			//Obtain matches for the set structure {metadata:[profileSystemName][profileFieldSystemName]}
			preg_match(self::METADATA_EMAIL_NOTIFICATION_REGEX, $fieldValue,$matches);
			foreach ($matches as $match)
			{
				$match = str_replace(array ('{', '}'), array ('', ''), $match);
				list ($metadata, $profileSystemName, $fieldSystemName) = explode(':', $match);
				$profile = MetadataProfilePeer::retrieveBySystemName($profileSystemName, $emailNotificationTemplate->getPartnerId());
				if (!$profile)
				{
					KalturaLog::info("Metadata profile with system name $profileSystemName not found for this partner. No tokens will be replaced.");
				}
				
				$objectId = null;
				$metadataObjectId = null;
				//If the metadataProfileobjectType matches the one on the emailNotification, we can proceed
				//If the objectType of the email template is 'asset' we can use the entryId
				//If the objectType of the email template is a metadata object we can use its id
				if (kMetadataManager::getObjectTypeName($profile->getObjectType()) == KalturaPluginManager::getObjectClass('EventNotificationEventObjectType', $emailNotificationTemplate->getObjectType()))
				{
					$objectId = $scope->getEvent()->getObject()->getId();
				}
				elseif (kMetadataManager::getObjectTypeName($profile->getObjectType()) == 'entry'
						&& KalturaPluginManager::getObjectClass('EventNotificationEventObjectType', $emailNotificationTemplate->getObjectType()) == 'asset')
				{
					$objectId = $scope->getEvent()->getObject()->getEntryId();
				}
				elseif (KalturaPluginManager::getObjectClass('EventNotificationEventObjectType', $emailNotificationTemplate->getObjectType()) == MetadataPeer::OM_CLASS)
				{
					$metadataObjectIdb = $scope->getEvent()->getObject()->getId();
				}
				
				$c = new Criteria();
				$c->add(MetadataPeer::PARTNER_ID, $emailNotificationTemplate->getPartnerId());
				$c->add(MetadataPeer::STATUS, Metadata::STATUS_VALID);
				$c->add(MetadataPeer::METADATA_PROFILE_ID, $profile->getId());
				if ($objectId)
				{
					$c->add(MetadataPeer::OBJECT_ID, $objectId);
				}
				elseif ($metadataObjectId)
				{
					$c->add(MetadataPeer::ID, $metadataObjectId);
				}
				else 
				{
					//There is not enough specification regarding the required metadataObject, abort.
					KalturaLog::info("The template does not contain an object Id for which custom metadata can be retrieved");
					return array ();	
				}
				
				$result = MetadataPeer::doSelectOne($c);
				
				if (!$result)
					return array ();
				
				/* @var $result Metadata */
				$metadataXML = new SimpleXMLElement (kFileSyncUtils::file_get_contents($result->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA)));
				$values = $metadataXML->xpath($fieldSystemName);
				$strvals = array();
				foreach ($values as $value)
				{
					$strvals[] = strval($value);
				}
				
				$metadataContentParameters[$match] = implode(',', $strvals);
			}
		}

		return $metadataContentParameters;
	}
}
