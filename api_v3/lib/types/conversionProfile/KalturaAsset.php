<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAsset extends KalturaObject implements IFilterable
{
	/**
	 * The ID of the Flavor Asset
	 * 
	 * @var string
	 * @readonly
	 * @filter eq,in
	 */
	public $id;
	
	/**
	 * The entry ID of the Flavor Asset
	 * 
	 * @var string
	 * @readonly
	 * @filter eq,in
	 */
	public $entryId;
	
	/**
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $partnerId;
	
	/**
	 * The status of the Flavor Asset
	 * 
	 * @var KalturaFlavorAssetStatus
	 * @readonly 
	 * @filter eq,in,notin
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
	 * @filter gte,lte,order
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
	 * @insertonly
	 */
	public $fileExt;
	
	
	/**
	 * @var int
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;
	
	
	/**
	 * @var int
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $updatedAt;
	
	
	/**
	 * @var int
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $deletedAt;
	
	
	/**
	 * @var string
	 * @readonly
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