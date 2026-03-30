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

	/**
	 * @var string
	 */
	public $backgroundColorCode;

	/**
	 * @var float
	 */
	public $foregroundScalePercentage;

	/**
	 * @var KalturaPosition
	 */
	public $foregroundPositionPercentage;

	/**
	 * @var KalturaAudioAttributes
	 */
	public $audioAttributes;

	private static $map_between_objects = array
	(
		"resource",
		"backgroundColorCode",
		"foregroundScalePercentage",
		"foregroundPositionPercentage",
		"audioAttributes"
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

	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUsage($sourceObject, $propertiesToSkip)
	 */
	public function validateForUsage($sourceObject, $propertiesToSkip = array())
	{
		parent::validateForUsage($sourceObject, $propertiesToSkip);

		$this->validatePropertyNotNull('resource');
		$this->resource->validateForUsage($sourceObject, $propertiesToSkip);

		$this->validateBackgroundColor();
		$this->validateForegroundPositionPercentage();
		$this->validateForegroundScalePercentage();
	}

	public function validateForegroundScalePercentage()
	{
		$minScalePercentage = 0;
		$maxScalePercentage = 5;
		if($this->foregroundScalePercentage && $this->foregroundScalePercentage < $minScalePercentage || $this->foregroundScalePercentage > $maxScalePercentage)
		{
			throw new KalturaAPIException(KalturaErrors::PARAMETER_VALUE_OUT_OF_RANGE, "foregroundScalePercentage", $minScalePercentage, $maxScalePercentage);
		}
	}

	public function validateBackgroundColor()
	{
		if($this->backgroundColorCode && preg_match('/^0x[A-Fa-f0-9]{6}$/', $this->backgroundColorCode) !== 1)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_FIELD_VALUE, "backgroundColorCode");
		}
	}

	public function validateForegroundPositionPercentage()
	{
		if($this->foregroundPositionPercentage)
		{
			$x = $this->foregroundPositionPercentage->x;
			$y = $this->foregroundPositionPercentage->y;

			$minPositionPercentage = 0;
			$maxPositionPercentage = 1;
			if($x < $minPositionPercentage || $x > $maxPositionPercentage)
			{
				throw new KalturaAPIException(KalturaErrors::PARAMETER_VALUE_OUT_OF_RANGE, "x", $minPositionPercentage, $maxPositionPercentage);
			}
			if($y < $minPositionPercentage || $y > $maxPositionPercentage)
			{
				throw new KalturaAPIException(KalturaErrors::PARAMETER_VALUE_OUT_OF_RANGE, "y", $minPositionPercentage, $maxPositionPercentage);
			}
		}
	}
}
