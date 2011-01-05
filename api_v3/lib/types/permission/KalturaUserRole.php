<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUserRole extends KalturaObject implements IFilterable
{
	/**
	 * @var int
	 * @readonly
	 * @filter eq,in,order
	 */
	public $id;
	
	
	/**
	 * @var string
	 * @filter eq,in,order
	 */
	public $name;
	
	
	/**
	 * @var string
	 * @filter like
	 */
	public $description;
	

	/**
	 * @var KalturaUserRoleStatus
	 * @filter eq,in
	 */
	public $status;
	
	
	/**
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $partnerId;
	
	
	/**
	 * @var string
	 */
	public $permissionNames;
	
	/**
	 * @var string
	 * @filter mlikeor,mlikeand
	 */
	public $tags;
	
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

	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array(
		'id',
		'name',
		'description',
		'status',
		'partnerId',
		'permissionNames',
		'tags',
		'createdAt',
		'updatedAt',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new UserRole();
			
		parent::toObject($dbObject, $skip);
					
		return $dbObject;
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