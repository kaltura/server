<?php
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
	 * @var int
	 */
	public $videoOffset;
	
	/**
	 * @var int
	 */
	public $scaleWidth;
	
	/**
	 * @var int
	 */
	public $scaleHeight;
	
	/**
	 * Hexadecimal value
	 * @var string
	 */
	public $backgroundColor;
	
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
		"scaleWidth",
		"scaleHeight",
		"backgroundColor",
	
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