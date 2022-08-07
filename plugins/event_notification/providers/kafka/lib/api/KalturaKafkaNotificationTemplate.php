<?php

/**
 * @package plugins.kafkaNotification
 * @subpackage api.objects
 */
class KalturaKafkaNotificationTemplate extends KalturaEventNotificationTemplate
{
	/**
	 * Define the content dynamic parameters
	 * @var string
	 * @requiresPermission update
	 */
	public $topicName;
	
	/**
	 * Define the content dynamic parameters
	 * @var string
	 * @requiresPermission update
	 */
	public $partitionKey;
	
	/**
	 * Define the content dynamic parameters
	 * @var KalturaKafkaNotificationFormat
	 * @requiresPermission update
	 */
	public $messageFormat;
	
	/**
	 * Kaltura API object type
	 * @var string
	 * @requiresPermission update
	 */
	public $apiObjectType;
	
	
	private static $map_between_objects = array('topicName', 'partitionKey', 'messageFormat', 'apiObjectType');
	
	public function __construct()
	{
		$this->type = KafkaNotificationPlugin::getApiValue(KafkaNotificationTemplateType::KAFKA);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUpdate()
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		$propertiesToSkip[] = 'type';
		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $propertiesToSkip = array())
	{
		if (is_null($dbObject))
			$dbObject = new KafkaNotificationTemplate();
		
		return parent::toObject($dbObject, $propertiesToSkip);
	}
}