<?php
/**
 * @package plugins.pushNotification
 * @subpackage api.objects
 */
class KalturaPushEventNotificationParameter extends KalturaEventNotificationParameter
{
	/**
	 * @var string
	 */
	public $queueKeyToken;

	private static $map_between_objects = array('queueKeyToken');

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
			$dbObject = new kPushEventNotificationParameter();

		return parent::toObject($dbObject, $propertiesToSkip);
	}
}