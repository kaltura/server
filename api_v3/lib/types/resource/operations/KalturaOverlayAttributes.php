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

	/**
	 * @var float
	 */
	public $marginsPercentage;

	/**
	 * @var float
	 */
	public $overlayScalePercentage;

	/**
	 * @var KalturaMediaCompositionAlignment
	 */
	public $overlayPlacement;

	/**
	 * @var KalturaOverlayShape
	 */
	public $overlayShape;

	/**
	 * @var KalturaAudioAttributes
	 */
	public $audioAttributes;

	/**
	 * @var KalturaOverlayBorderAttributes
	 */
	public $borderAttributes;

	private static $map_between_objects = array
	(
		"resource",
		"resourceMediaCompositionAttributesArray",
		"marginsPercentage",
		"overlayScalePercentage",
		"overlayPlacement",
		"overlayShape",
		"audioAttributes",
		"borderAttributes",
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

	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUsage($sourceObject, $propertiesToSkip)
	 */
	public function validateForUsage($sourceObject, $propertiesToSkip = array())
	{
		parent::validateForUsage($sourceObject, $propertiesToSkip);

		$this->validatePropertyNotNull('resource');
		$this->resource->validateForUsage($sourceObject, $propertiesToSkip);

		if(isset($this->resourceMediaCompositionAttributesArray[0]))
		{
			$mediaAttributes = $this->resourceMediaCompositionAttributesArray[0];
			if(count($this->resourceMediaCompositionAttributesArray) > 1)
			{
				throw new KalturaAPIException(KalturaErrors::RESOURCE_MEDIA_COMPOSITION_COUNT_EXCEEDED_MAX_ALLOWED_COUNT, 1);
			}

			if($mediaAttributes instanceof KalturaOverlayAttributes)
			{
				throw new KalturaAPIException(KalturaErrors::MULTIPLE_PARAMETER_NOT_SUPPORTED, "KalturaOverlayAttributes");
			}

			$mediaAttributes->validateForUsage($sourceObject, $propertiesToSkip);

		}

		$minPercentage = 0.1;
		$maxPercentage = 0.9;
		if($this->marginsPercentage)
		{
			if($this->marginsPercentage < $minPercentage || $this->marginsPercentage > $maxPercentage)
			{
				throw new KalturaAPIException(KalturaErrors::PARAMETER_VALUE_OUT_OF_RANGE, "marginsPercentage", $minPercentage, $maxPercentage);
			}
		}

		if($this->overlayScalePercentage)
		{
			if($this->overlayScalePercentage < $minPercentage || $this->overlayScalePercentage > $maxPercentage)
			{
				throw new KalturaAPIException(KalturaErrors::PARAMETER_VALUE_OUT_OF_RANGE, "overlayScalePercentage", $minPercentage, $maxPercentage);
			}
		}

		if($this->overlayScalePercentage && $this->marginsPercentage)
		{
			$minPercentage = 0;
			$maxPercentage = 1;
			if($this->overlayScalePercentage + $this->marginsPercentage > 1)
			{
				throw new KalturaAPIException(KalturaErrors::PARAMETER_VALUE_OUT_OF_RANGE, "overlayScalePercentage + marginsPercentage", $minPercentage, $maxPercentage);
			}
		}

	}

	public function doFromObject($source_object, KalturaDetachedResponseProfile $responseProfile = null)
	{
		parent::doFromObject($source_object, $responseProfile);

		if($this->shouldGet('resource', $responseProfile))
		{
			$resource = $source_object->getResource();
			if($resource instanceof kFileSyncResource)
			{
				$this->resource = new KalturaFileSyncResource();
				$this->resource->fromObject($resource, $responseProfile);
			}
		}
	}
}
