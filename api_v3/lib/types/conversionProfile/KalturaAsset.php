<?php
class KalturaAsset extends KalturaObject 
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
	 * The version of the Flavor Asset
	 * 
	 * @var int
	 * @readonly
	 */
	public $version;
	
	/**
	 * The size (in KBytes) of the Flavor Asset
	 * 
	 * @var int
	 * @readonly
	 */
	public $size;
	
	/**
	 * Tags used to identify the Flavor Asset in various scenarios
	 * 
	 * @var string
	 */
	public $tags;
	
	/**
	 * The file extension
	 * 
	 * @var string
	 */
	public $fileExt;
	
	
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
		"version",
		"size",
		"tags",
		"fileExt",
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