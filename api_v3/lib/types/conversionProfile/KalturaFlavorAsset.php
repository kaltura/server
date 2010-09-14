<?php
class KalturaFlavorAsset extends KalturaObject 
{
	/**
	 * The ID of the Flavor Asset
	 * 
	 * @var string
	 * @readonly
	 */
	public $id;
	
	/**
	 * The entry ID of the Flavor Asset
	 * 
	 * @var string
	 * @readonly
	 */
	public $entryId;
	
	/**
	 * @var int
	 * @readonly
	 */
	public $partnerId;
	
	/**
	 * The status of the Flavor Asset
	 * 
	 * @var KalturaFlavorAssetStatus
	 * @readonly 
	 */
	public $status;
	
	/**
	 * The Flavor Params used to create this Flavor Asset
	 * 
	 * @var int
	 * @readonly
	 */
	public $flavorParamsId;
	
	/**
	 * The version of the Flavor Asset
	 * 
	 * @var int
	 * @readonly
	 */
	public $version;
	
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
	 * The overall bitrate (in KBits) of the Flavor Asset 
	 * 
	 * @var int
	 * @readonly
	 */
	public $bitrate;
	
	/**
	 * The frame rate (in FPS) of the Flavor Asset
	 * 
	 * @var int
	 * @readonly
	 */
	public $frameRate;
	
	/**
	 * The size (in KBytes) of the Flavor Asset
	 * 
	 * @var int
	 * @readonly
	 */
	public $size;
	
	/**
	 * True if this Flavor Asset is the original source
	 * 
	 * @var bool
	 */
	public $isOriginal;
	
	/**
	 * Tags used to identify the Flavor Asset in various scenarios
	 * 
	 * @var string
	 */
	public $tags;
	
	/**
	 * True if this Flavor Asset is playable in KDP
	 * 
	 * @var bool
	 */
	public $isWeb;
	
	/**
	 * The file extension
	 * 
	 * @var string
	 */
	public $fileExt;
	
	/**
	 * The container format
	 * 
	 * @var string
	 */
	public $containerFormat;
	
	/**
	 * The video codec
	 * 
	 * @var string
	 */
	public $videoCodecId;
	
	
	/**
	 * @var int
	 */
	public $createdAt;
	
	
	/**
	 * @var int
	 */
	public $updatedAt;
	
	
	/**
	 * @var int
	 */
	public $deletedAt;
	
	
	/**
	 * @var string
	 */
	public $description;
	
	private static $map_between_objects = array
	(
		"id",
		"entryId",
		"partnerId",
		"status",
		"flavorParamsId",
		"version",
		"width",
		"height",
		"bitrate",
		"frameRate",
		"size",
		"isOriginal",
		"isWeb",
		"tags",
		"fileExt",
		"containerFormat",
		"videoCodecId",
		"createdAt",
		"updatedAt",
		"deletedAt",
		"description",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
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