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

	private static $map_between_objects = array(
		"color",
		"width",
		"opacity",
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

		if(!preg_match('/^(#|0x|0X)?[0-9A-Fa-f]{6}$/', $this->color))
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_FIELD_VALUE, "color");
		}
	}
}
