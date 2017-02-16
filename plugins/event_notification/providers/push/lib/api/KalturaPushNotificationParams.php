<?php
/**
 * Object which contains contextual entry-related data.
 * @package plugins.pushNotification
 * @subpackage api.objects
 */
class KalturaPushNotificationParams extends KalturaObject
{	
	/**
	 * User params
	 * @var KalturaPushEventNotificationParameterArray
	 */
	public $userParams;

	private static $map_between_objects = array('userParams');

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
			$dbObject = new kPushNotificationParams();

		return parent::toObject($dbObject, $propertiesToSkip);
	}
	
}