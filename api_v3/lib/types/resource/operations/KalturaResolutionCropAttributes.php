<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaResolutionCropAttributes extends KalturaDimensionsAttributes
{
	/**
	 * @var int
	 */
	public $targetWidth;

	/**
	 * @var int
	 */
	public $targetHeight;

	private static $map_between_objects = array(
		"targetWidth",
		"targetHeight",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUsage()
	 */
	public function validateForUsage($sourceObject, $propertiesToSkip = array())
	{
		parent::validateForUsage($sourceObject, $propertiesToSkip);
		$this->validatePropertyNotNull('targetWidth');
		$this->validatePropertyNotNull('targetHeight');
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if(is_null($object_to_fill))
		{
			$object_to_fill = new kResolutionCropAttributes();
		}
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
