<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaCategoryIdentifier extends KalturaObjectIdentifier
{
	/**
	 * Identifier of the object
	 * @var KalturaCategoryIdentifierField
	 */
	public $identifier;
	
	/* (non-PHPdoc)
	 * @see KalturaObjectIdentifier::toObject()
	 */
	public function toObject ($dbObject = null, $propsToSkip = array())
	{
		if (!$dbObject)
			$dbObject = new kCategoryIdentifier();

		return parent::toObject($dbObject, $propsToSkip);
	}
	
	private static $map_between_objects = array(
			"identifier",
		);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}