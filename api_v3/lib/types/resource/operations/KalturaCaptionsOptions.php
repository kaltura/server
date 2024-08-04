<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaCaptionsOptions extends KalturaObject
{
	/**
	 * @var string
	 */
	public $action;

	/**
	 * @var string
	 */
	public $fontName;

	/**
	 * @var int
	 */
	public $fontSize;

	/**
	 * @var string
	 */
	public $fontStyle;

	/**
	 * @var string
	 */
	public $primaryColour;

	/**
	 * @var KalturaBorderStyle
	 */
	public $borderStyle;

	/**
	 * @var string
	 */
	public $backColour;

	/**
	 * @var string
	 */
	public $outlineColour;

	/**
	 * @var int
	 */
	public $shadow;

	/**
	 * @var bool
	 */
	public $bold;

	/**
	 * @var bool
	 */
	public $italic;

	/**
	 * @var bool
	 */
	public $underline;

	/**
	 * @var KalturaCaptionsAlignment
	 */
	public $alignment;

	/**
	 * @var string
	 */
	public $captionAssetId;


	private static $map_between_objects = array
	(
		"action",
		"fontName",
		"fontSize" ,
		"fontStyle",
		"primaryColour",
		"borderStyle",
		"backColour",
		"outlineColour",
		"shadow",
		"bold",
		"italic",
		"underline",
		"alignment",
		"captionAssetId"
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if(is_null($object_to_fill))
		{
			$object_to_fill = new kCaptionsOptions();
		}
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
