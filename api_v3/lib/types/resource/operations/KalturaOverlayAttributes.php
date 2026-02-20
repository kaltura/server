<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaOverlayAttributes extends KalturaMediaCompositionAttributes
{
	/**
	 * Only KalturaEntryResource and KalturaAssetResource are supported
	 * @var KalturaContentResource
	 */
	public $resource;

	/**
	 * Only KalturaReplaceBackgroundAttributes is supported
	 * @var KalturaMediaCompositionAttributesArray
	 */
	public $resourceMediaCompositionAttributesArray;

	private static $map_between_objects = array
	(
		"resource",
		"resourceMediaCompositionAttributesArray"
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		$this->validateForUsage($object_to_fill, $props_to_skip);

		if(is_null($this->resourceMediaCompositionAttributesArray) || !count($this->resourceMediaCompositionAttributesArray))
		{
			return $this->resource->toObject();
		}

		if(!$object_to_fill)
		{
			$object_to_fill = new kOverlayAttributes();
		}

		$resourceMediaCompositionAttributesArray = array();
		foreach($this->resourceMediaCompositionAttributesArray as $resourceMediaCompositionAttributes)
		{
			$resourceMediaCompositionAttributesArray[] = $resourceMediaCompositionAttributes->toObject();
		}

		$object_to_fill->setResourceMediaCompositionAttributesArray($resourceMediaCompositionAttributesArray);
		$object_to_fill->setResource($this->resource->toObject());

		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
