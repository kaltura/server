<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaVendorProfileAdminRule extends KalturaObject
{
	/**
	 * Define the event that should trigger this notification
	 *
	 * @var KalturaVendorProfileRuleOption
	 */
	public $rule;
	
	private static $map_between_objects = array (
		'rule',
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
			$dbObject = new kVendorProfileAdminRule();
		}
		
		return parent::toObject($dbObject, $propsToSkip);
	}
}