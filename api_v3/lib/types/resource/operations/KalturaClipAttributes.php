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
	 * @var KalturaCaptionAttributesArray
	 */
	public $captionAttributes;


	private static $map_between_objects = array
	(
	 	"offset" , 
	 	"duration",
		"globalOffsetInDestination",
		"effectArray",
		"captionAttributes",
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
			throw new KalturaAPIException(KalturaErrors::PARAMETER_VALUE_OUT_OF_RANGE, 'cropAlignment', $minCropAlignment, $maxCropAlignment);
		}

		$renderCaptionAttribute = null;
		foreach ($this->captionAttributes as $captionAttribute)
		{
			if($captionAttribute instanceOf kRenderCaptionAttributes)
			{
				if($renderCaptionAttribute)
				{
					throw new KalturaAPIException(KalturaErrors::MULTIPLE_PARAMETER_NOT_SUPPORTED, 'renderCaptionAttributes');
				}
				$renderCaptionAttribute = $captionAttribute;
			}
		}
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if(is_null($object_to_fill))
		{
			$object_to_fill = new kClipAttributes();
		}

		$captionAttributes = array();
		foreach($this->captionAttributes as $captionAttribute)
		{
			$captionAttributes[] = $captionAttribute->toObject();
		}
		$object_to_fill->setCaptionAttributes($captionAttributes);

		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
