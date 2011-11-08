<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaThumbParams extends KalturaAssetParams 
{
	/**
	 * @var KalturaThumbCropType
	 */
	public $cropType;
	
	/**
	 * @var int
	 */
	public $quality;
	
	/**
	 * @var int
	 */
	public $cropX;
	
	/**
	 * @var int
	 */
	public $cropY;
	
	/**
	 * @var int
	 */
	public $cropWidth;
	
	/**
	 * @var int
	 */
	public $cropHeight;
	
	/**
	 * @var float
	 */
	public $videoOffset;
	
	/**
	 * @var int
	 */
	public $width;
	
	/**
	 * @var int
	 */
	public $height;
	
	/**
	 * @var float
	 */
	public $scaleWidth;
	
	/**
	 * @var float
	 */
	public $scaleHeight;
	
	/**
	 * Hexadecimal value
	 * @var string
	 */
	public $backgroundColor;
	
	/**
	 * Id of the flavor params or the thumbnail params to be used as source for the thumbnail creation
	 * @var int
	 */
	public $sourceParamsId;

	/**
	 * The container format of the Flavor Params
	 *  
	 * @var KalturaContainerFormat
	 * @filter eq
	 */
	public $format;
	
	public $density;
	
	
//	Maybe support will be added in the future
//	
//	/**
//	 * @var KalturaCropProvider
//	 */
//	public $cropProvider;
//	
//	/**
//	 * @var KalturaCropProviderData
//	 */
//	public $cropProviderData;

	
	private static $map_between_objects = array
	(
		"cropType",
		"quality",
		"cropX",
		"cropY",
		"cropWidth",
		"cropHeight",
		"videoOffset",
		"width",
		"height",
		"scaleWidth",
		"scaleHeight",
		"backgroundColor",
		"sourceParamsId",
		"format",
		"density"
	
//		Maybe support will be added in the future
//		"cropProvider",
//		"cropProviderData",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function getExtraFilters()
	{
		return array();
	}
	
	public function getFilterDocs()
	{
		return array();
	}
}