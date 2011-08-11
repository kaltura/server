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
	 * @insertonly
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
	
	/**
	 * The status of the asset
	 * 
	 * @var KalturaThumbAssetStatus
	 * @readonly 
	 * @filter eq,in,notin
	 */
	public $status;
	
	private static $map_between_objects = array
	(
		"thumbParamsId" => "flavorParamsId",
		"width",
		"height",
		"status",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}
