<?php
/**
 * @package api
 * @subpackage enum
 * 
 */
class KalturaExtendedFeature extends KalturaObject
{
	/**
	 * @var KalturaObjectFeatureType
	 */
	public $extendedFeature;
	
	/* (non-PHPdoc)
	 * @see KalturaObjectIdentifier::toObject()
	 */
	public function toObject ($dbObject = null, $propsToSkip = null)
	{
		if (!$dbObject)
			$dbObject = new kExtendedFeature();

		return parent::toObject($dbObject, $propsToSkip);
	}
	
	private static $map_between_objects = array(
			"extendedFeature",
		);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}