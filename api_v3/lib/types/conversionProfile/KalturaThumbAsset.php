<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaThumbAsset extends KalturaAsset  
{
	/**
	 * The Flavor Params used to create this Flavor Asset
	 * 
	 * @var int
	 */
	public $thumbParamsId;
	
	/**
	 * The width of the Flavor Asset 
	 * 
	 * @var int
	 * @readonly
	 */
	public $width;
	
	/**
	 * The height of the Flavor Asset
	 * 
	 * @var int
	 * @readonly
	 */
	public $height;
	
	private static $map_between_objects = array
	(
		"thumbParamsId" => "flavorParamsId",
		"width",
		"height",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}
