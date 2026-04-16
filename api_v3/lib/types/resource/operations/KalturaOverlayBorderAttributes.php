<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaOverlayBorderAttributes extends KalturaObject
{
	/**
	 * Border color as a hex code (e.g. #FFFFFF or FFFFFF).
	 * @var string
	 */
	public $color;

	/**
	 * Border width in pixels.
	 * @var int
	 */
	public $width;

	/**
	 * Border opacity as a percentage (0–100). Defaults to 100 (fully opaque).
	 * @var int
	 */
	public $opacity;

	/**
	 * Drop-shadow color as a hex code. Optional.
	 * @var string
	 */
	public $shadowColor;

	/**
	 * Drop-shadow opacity as a percentage (0–100). Optional; omit or set to 0 for no shadow.
	 * @var int
	 */
	public $shadowOpacity;

	/**
	 * Horizontal shadow offset in pixels (positive = right).
	 * @var int
	 */
	public $shadowOffsetX;

	/**
	 * Vertical shadow offset in pixels (positive = down).
	 * @var int
	 */
	public $shadowOffsetY;

	private static $map_between_objects = array(
		"color",
		"width",
		"opacity",
		"shadowColor",
		"shadowOpacity",
		"shadowOffsetX",
		"shadowOffsetY",
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
			$object_to_fill = new kOverlayBorderAttributes();
		}

		return parent::toObject($object_to_fill, $props_to_skip);
	}

	public function validateForUsage($sourceObject, $propertiesToSkip = array())
	{
		parent::validateForUsage($sourceObject, $propertiesToSkip);

		$this->validatePropertyNotNull('color');
		$this->validatePropertyNotNull('width');

		$minWidth = 0;
		$maxWidth = 20;
		if($this->width < $minWidth || $this->width > $maxWidth)
		{
			throw new KalturaAPIException(KalturaErrors::PARAMETER_VALUE_OUT_OF_RANGE, "width", $minWidth, $maxWidth);
		}

		$minOpacity = 0;
		$maxOpacity = 100;
		if(isset($this->opacity) && ($this->opacity < $minOpacity || $this->opacity > $maxOpacity))
		{
			throw new KalturaAPIException(KalturaErrors::PARAMETER_VALUE_OUT_OF_RANGE, "opacity", $minOpacity, $maxOpacity);
		}

		if(isset($this->shadowOpacity))
		{
			if($this->shadowOpacity < $minOpacity || $this->shadowOpacity > $maxOpacity)
			{
				throw new KalturaAPIException(KalturaErrors::PARAMETER_VALUE_OUT_OF_RANGE, "shadowOpacity", $minOpacity, $maxOpacity);
			}

			if($this->shadowOpacity > 0)
			{
				$this->validatePropertyNotNull('shadowColor');
			}
		}

		if(!preg_match('/^(#|0x|0X)?[0-9A-Fa-f]{6}$/', $this->color))
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_FIELD_VALUE, "color");
		}

		if(isset($this->shadowColor) && !preg_match('/^(#|0x|0X)?[0-9A-Fa-f]{6}$/', $this->shadowColor))
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_FIELD_VALUE, "shadowColor");
		}
	}
}
