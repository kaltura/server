<?php
/**
 * @package plugins.metadata
 * @subpackage api.objects
 */
class KalturaFileAsset extends KalturaObject implements IFilterable 
{
	/**
	 * @var int
	 * @filter eq,in
	 * @readonly
	 */
	public $id;

	
	/**
	 * @var int
	 * @filter eq
	 * @readonly
	 */
	public $partnerId;

	
	/**
	 * 
	 * @var KalturaFileAssetObjectType
	 * @filter eq
	 * @insertonly
	 */
	public $fileAssetObjectType;

	
	/**
	 * 
	 * @var string
	 * @filter eq,in
	 * @insertonly
	 */
	public $objectId;

	
	/**
	 * 
	 * @var string
	 */
	public $name;

	
	/**
	 * 
	 * @var string
	 */
	public $systemName;

	
	/**
	 * 
	 * @var string
	 */
	public $fileExt;

	
	/**
	 * 
	 * @var int
	 * @readonly
	 */
	public $version;

	
	/**
	 * 
	 * @var int
	 * @filter gte,lte,order
	 * @readonly
	 */
	public $createdAt;


	/**
	 * 
	 * @var int
	 * @filter gte,lte,order
	 * @readonly
	 */
	public $updatedAt;

	
	/**
	 * 
	 * @var KalturaFileAssetStatus
	 * @filter eq,in
	 * @readonly
	 */
	public $status;
	
	private static $map_between_objects = array
	(
		"id",
		"partnerId",
		"fileAssetObjectType" => "objectType",
		"objectId",
		"name",
		"systemName",
		"fileExt",
		"version",
		"createdAt",
		"updatedAt",
		"status",
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
	
	public function toObject($dbFileAsset = null, $propsToSkip = array())
	{
		if(is_null($dbFileAsset))
			$dbFileAsset = new FileAsset();
			
		return parent::toObject($dbFileAsset, $propsToSkip);
	}
}