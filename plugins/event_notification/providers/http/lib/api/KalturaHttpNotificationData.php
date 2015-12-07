<?php
/**
 * @package plugins.httpNotification
 * @subpackage api.objects
 * @abstract
 */
abstract class KalturaHttpNotificationData extends KalturaObject
{
	/**
	 * @param kHttpNotificationData $coreObject
	 * @return KalturaHttpNotificationData
	 */
	public static function getInstance(kHttpNotificationData $coreObject)
	{
		$dataType = get_class($coreObject);
		KalturaLog::debug("Loading KalturaHttpNotificationData from type [$dataType]");
		$data = null;
		switch ($dataType)
		{
			case 'kHttpNotificationDataFields':
				$data = new KalturaHttpNotificationDataFields();
				break;
				
			case 'kHttpNotificationDataText':
				$data = new KalturaHttpNotificationDataText();
				break;
				
			case 'kHttpNotificationObjectData':
				$data = new KalturaHttpNotificationObjectData();
				break;
				
			default:
				$data = KalturaPluginManager::loadObject('KalturaHttpNotificationData', $dataType);
				break;
		}
		
		if($data)
			$data->fromObject($coreObject);
			
		return $data;
	}

	/**
	 * @param $jobData kHttpNotificationDispatchJobData
	 * @return string the data to be sent
	 */
	abstract public function getData(kHttpNotificationDispatchJobData $jobData = null);
}
