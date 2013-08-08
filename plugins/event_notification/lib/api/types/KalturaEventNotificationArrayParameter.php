<?php
/**
 * @package plugins.eventNotification
 * @subpackage api.objects
 */
class KalturaEventNotificationArrayParameter extends KalturaEventNotificationParameter
{
	/**
	 * @var KalturaStringArray
	 */
	public $values;
	
	/**
	 * Used to restrict the values to close list
	 * @var KalturaStringValueArray
	 */
	public $allowedValues;
	
	private static $map_between_objects = array
	(
		'values',
		'allowedValues',
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kEventNotificationArrayParameter();
			
		return parent::toObject($dbObject, $skip);
	}
}