<?php
/**
 * @package plugins.httpNotification
 * @subpackage api.objects
 * @abstract
 */
abstract class KalturaHttpNotificationData extends KalturaObject
{
	//TODO - ask about the defulat type - should this be int and allow null or enum and have a default value???
	/**
	 * Content Type
	 * @var KalturaResponseType
	 */
	public $contentType = KalturaResponseType::RESPONSE_TYPE_XML;

	private static $map_between_objects = array
	(
		'contentType',
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	/**
	 * @param kHttpNotificationData $coreObject
	 * @return KalturaHttpNotificationData
	 */
	public static function getInstance(kHttpNotificationData $coreObject)
	{
		$dataType = get_class($coreObject);
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

	public function getContentType()
	{
		return $this->contentType;
	}
}
