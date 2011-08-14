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
	 * @filter like,mlikeor,mlikeand
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
	 * System description, error message, warnings and failure cause.
	 * @var string
	 * @readonly
	 */
	public $description;
	
	
	/**
	 * Partner private data
	 * @var string
	 */
	public $partnerData;
	
	/**
	 * Partner friendly description
	 * @var string
	 */
	public $partnerDescription;
	
		
	private static $map_between_objects = array
	(
		"id",
		"entryId",
		"partnerId",
		"version",
		"size",
		"tags",
		"fileExt",
		"createdAt",
		"updatedAt",
		"deletedAt",
		"description",
		"partnerData",
		"partnerDescription",
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