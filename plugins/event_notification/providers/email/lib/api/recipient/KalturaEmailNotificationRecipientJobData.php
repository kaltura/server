<?php
/**
 * Abstract class representing the final output recipients going into the batch mechanism
 * @package plugins.emailNotification
 * @subpackage model.data
 */
abstract class KalturaEmailNotificationRecipientJobData extends KalturaObject
{
	 /**
	  * Provider type of the job data.
	  * @var KalturaEmailNotificationRecipientProviderType
	  * 
	  * @readonly
	  */
	 public $providerType;
	 
	/**
	 * Protected setter to set the provider type of the job data
	 */
	abstract protected function setProviderType ();
	
	/**
	 * Function returns correct API recipient data type based on the DB class received.
	 * @param kEmailNotificationRecipientJobData $dbData
	 * @return Kaltura
	 */
	public static function getDataInstance ($dbData)
	{
		$instance = null;
		if ($dbData)
		{
			switch (get_class($dbData))
			{
				case 'kEmailNotificationCategoryRecipientJobData':
					$instance = new KalturaEmailNotificationCategoryRecipientJobData();
					break;
				case 'kEmailNotificationStaticRecipientJobData':
					$instance = new KalturaEmailNotificationStaticRecipientJobData();
					break;
				case 'kEmailNotificationUserRecipientJobData':
					$instance = new KalturaEmailNotificationUserRecipientJobData();
					break;
				case 'kEmailNotificationGroupRecipientJobData':
					$instance = new KalturaEmailNotificationGroupRecipientJobData();
					break;
				default:
					$instance = KalturaPluginManager::loadObject('KalturaEmailNotificationRecipientJobData', $dbData->getProviderType());
					break;
			}
			
			if ($instance)
				$instance->fromObject($dbData);
		}
			
		return $instance;
		
	}
}