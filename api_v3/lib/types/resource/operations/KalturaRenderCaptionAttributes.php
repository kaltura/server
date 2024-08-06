<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaRenderCaptionAttributes extends KalturaCaptionAttributes
{
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

	public function validateForUsage($sourceObject, $propertiesToSkip = array())
	{
		parent::validateForUsage($sourceObject, $propertiesToSkip);

		if(!$this->captionAssetId)
		{
			throw new KalturaAPIException(KalturaErrors::MISSING_MANDATORY_PARAMETER, "captionAssetId");
		}
		$this->validateColorFormat($this->primaryColour, "primaryColour");
		$this->validateColorFormat($this->outlineColour, "outlineColour");
		$this->validateColorFormat($this->backColour, "backColour");
	}

	protected function validateColorFormat($color, $paramName)
	{
		if($color && !preg_match('/^\&[H|h]([0-9]|[A-F]|[a-f]){6}\&$/', $color))
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_PARAMETER_VALUE, $paramName);
		}
	}


	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if(is_null($object_to_fill))
		{
			$object_to_fill = new kRenderCaptionAttributes();
		}
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
