<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaCropDimensions extends KalturaObject
{
	/**
	 * Crop left point
	 * 
	 * @var int
	 */
	public $left;
	
	/**
	 * Crop top point
	 * 
	 * @var int
	 */
	public $top;
	
	/**
	 * Crop width
	 * 
	 * @var int
	 */
	public $width;
	
	/**
	 * Crop height
	 * 
	 * @var int
	 */
	public $height;
	
	private static $map_between_objects = array
	(
		// the object will be mapped to conversionProfile2
		"left" => "cropLeft",
		"top" => "cropTop",
		"width" => "cropWidth",
		"height" => "cropHeight",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}