<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaReplaceBackgroundAttributes extends KalturaMediaCompositionAttributes
{
	/**
	 * Only KalturaEntryResource and KalturaAssetResource are supported
	 * @var KalturaContentResource
	 */
	public $resource;

	private static $map_between_objects = array
	(
		"resource"
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		$this->validateForUsage($object_to_fill, $props_to_skip);

		if(!$object_to_fill)
		{
			$object_to_fill = new kReplaceBackgroundAttributes();
		}
		$object_to_fill->setResource($this->resource->toObject());

		return parent::toObject($object_to_fill, $props_to_skip);
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUsage($sourceObject, $propertiesToSkip)
	 */
	public function validateForUsage($sourceObject, $propertiesToSkip = array())
	{
		parent::validateForUsage($sourceObject, $propertiesToSkip);

		$this->validatePropertyNotNull('resource');
		$this->resource->validateForUsage($sourceObject, $propertiesToSkip);
	}
}
