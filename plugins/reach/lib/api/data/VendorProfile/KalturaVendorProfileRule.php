<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaVendorProfileRule extends KalturaObject
{
	/**
	 *  @var KalturaNullableBoolean
	 */
	public $manualDispatchEnabled;
	
	/**
	 *  @var KalturaNullableBoolean
	 */
	public $automaticDispatchEnabled;
	
	/**
	 * Define the event that should trigger this notification
	 *
	 * @var KalturaVendorProfileEventType
	 */
	public $eventType;
	
	/**
	 * Define the object that raied the event that should trigger this notification
	 *
	 * @var KalturaVendorProfileEventObjectType
	 */
	public $eventObjectType;
	
	/**
	 * Define the conditions that cause this notification to be triggered
	 * @var KalturaConditionArray
	 */
	public $eventConditions;
	
	/**
	 *  @var string
	 */
	public $catalogItemIds;
	
	private static $map_between_objects = array (
		'manualDispatchEnabled',
		'automaticDispatchEnabled',
		'eventType',
		'eventObjectType',
		'eventConditions',
		'catalogItemIds',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
 */
	public function toObject($dbObject = null, $propsToSkip = array())
	{
		if (!$dbObject)
		{
			$dbObject = new kVendorProfileRule();
		}
		
		return parent::toObject($dbObject, $propsToSkip);
	}
}