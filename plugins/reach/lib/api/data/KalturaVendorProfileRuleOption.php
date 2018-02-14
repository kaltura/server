<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */

abstract class KalturaVendorProfileRuleOption extends KalturaObject
{
	/**
	 * @var string
	 */
	public $catalogItemsIds;

	private static $map_between_objects = array
	(
		"catalogItemsIds",
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

	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull(array("catalogItemsIds"));
		parent::validateForInsert($propertiesToSkip);
	}
}