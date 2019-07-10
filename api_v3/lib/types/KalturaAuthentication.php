<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAuthentication extends KalturaObject
{
	/**
	 * @var string
	 */
	public $qrCode;

	private static $map_between_objects = array
	(
		"qrCode",
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
		if (!$dbObject) {
			$dbObject = new kAuthentication();
		}

		return parent::toObject($dbObject, $propsToSkip);
	}
}