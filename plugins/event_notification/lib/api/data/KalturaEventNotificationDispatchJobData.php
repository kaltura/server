<?php
/**
 * @package plugins.eventNotification
 * @subpackage api.objects
 */
class KalturaEventNotificationDispatchJobData extends KalturaJobData
{
	/**
	 * @var int
	 */
	public $templateId;

	/**
	 * Define the content dynamic parameters
	 * @var KalturaKeyValueArray
	 */
	public $contentParameters;
	
	private static $map_between_objects = array
	(
		'templateId' ,
		'contentParameters',
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	/**
	 * @param string $subType is the provider type
	 * @return int
	 */
	public function toSubType($subType)
	{
		return kPluginableEnumsManager::apiToCore('EventNotificationTemplateType', $subType);
	}
	
	/**
	 * @param int $subType
	 * @return string
	 */
	public function fromSubType($subType)
	{
		return kPluginableEnumsManager::coreToApi('EventNotificationTemplateType', $subType);
	}
}
