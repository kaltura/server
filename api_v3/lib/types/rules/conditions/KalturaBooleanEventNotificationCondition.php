<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaBooleanEventNotificationCondition  extends KalturaCondition
{
	/**
	 * The boolean event notification ids
	 *
	 * @var string
	 */
	public $booleanEventNotificationIds;

	private static $mapBetweenObjects = array
	(
		'booleanEventNotificationIds',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kBooleanEventNotificationCondition();
		return parent::toObject($dbObject, $skip);
	}

}