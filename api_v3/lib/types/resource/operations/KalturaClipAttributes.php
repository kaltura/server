<?php
/**
 * Clip operation attributes
 * 
 * @package api
 * @subpackage objects
 */
class KalturaClipAttributes extends KalturaOperationAttributes
{
	/**
	 * Offset in milliseconds
	 * @var int
	 * @requiresPermission all
	 */
	public $offset;
	
	/**
	 * Duration in milliseconds
	 * @var int
	 * @requiresPermission all
	 */
	public $duration;

	/**
	 * global Offset In Destination in milliseconds
	 * @var int
	 */
	public $globalOffsetInDestination;

	/**
	 * global Offset In Destination in milliseconds
	 * @var KalturaEffectsArray
	 */
	public $effectArray;

	/**
	 * @var int
	 */
	public $cropAlignment;

	/**
	 * @var KalturaCaptionsOptions
	 */
	public $captionsOptions;


	private static $map_between_objects = array
	(
	 	"offset" , 
	 	"duration",
		"globalOffsetInDestination",
		"effectArray",
		"captionsOptions",
		"cropAlignment"
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function validateForUsage($sourceObject, $propertiesToSkip = array())
	{
		parent::validateForUsage($sourceObject, $propertiesToSkip);

		$minCropAlignment = 0;
		$maxCropAlignment = 100;
		$cropAlignment = $this->cropAlignment;
		if ($cropAlignment && ($cropAlignment > $maxCropAlignment || $cropAlignment < $minCropAlignment))
		{
			throw new KalturaAPIException(KalturaErrors::PARAMETER_OUT_OF_RANGE, 'cropAlignment', $minCropAlignment, $maxCropAlignment);
		}

		$captionsOptions = $this->captionsOptions;
		if($captionsOptions instanceof KalturaCaptionsOptions)
		{
			if(is_null($captionsOptions->action))
			{
				throw new KalturaAPIException(KalturaErrors::MISSING_MANDATORY_PARAMETER, "action");
			}
			if(!$captionsOptions->captionAssetId)
			{
				throw new KalturaAPIException(KalturaErrors::MISSING_MANDATORY_PARAMETER, "captionAssetId");
			}
			$this->validateColorFormat($captionsOptions->primaryColour, "primaryColour");
			$this->validateColorFormat($captionsOptions->outlineColour, "outlineColour");
			$this->validateColorFormat($captionsOptions->backColour, "backColour");
		}
	}

	protected function validateColorFormat($color, $paramName)
	{
		if($color && !preg_match('/^\&[H|h]([0-9]|[A-F]|[a-f]){6}\&$/', $color))
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_PARAMETER_VALUE, $paramName);
		}
	}

	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(is_null($object_to_fill))
			$object_to_fill = new kClipAttributes();
			
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
