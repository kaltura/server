<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaExtendingItemMrssParameter extends KalturaObject
{
	/**
	 * XPath for the extending item
	 * @var string
	 */
	public $xpath;
	
	/**
	 * Object identifier
	 * @var KalturaObjectIdentifier
	 */
	public $identifier;
	
	private static $map_between_objects = array(
			"xpath",
			"identifier",
		);
		
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($dbObject, $propsToSkip)
	{
		if (!$dbObject)
			$dbObject = new KExtendingItemMrssParameter();
			
		return parent::toObject($dbObject, $propsToSkip);
	}
}