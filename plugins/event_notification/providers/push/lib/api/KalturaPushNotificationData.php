<?php
/**
 * @package plugins.pushNotification
 * @subpackage api.objects
 */
class KalturaPushNotificationData extends KalturaObject 
{
	/**
	 * @var string
	 * @readonly
	 */
	public $queueName;
	
	/**
	 * @var string
	 * @readonly
	 */
	public $queueKey;
	
	/**
	 * @var string
	 * @readonly
	 */
	public $url;

	private static $map_between_objects = array('queueName', 'queueKey', 'url');

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	/* (non-PHPdoc)
   * @see KalturaObject::toObject()
   */
	public function toObject($dbObject = null, $propertiesToSkip = array())
	{
		if(is_null($dbObject))
			$dbObject = new kPushNotificationData();

		return parent::toObject($dbObject, $propertiesToSkip);
	}
}