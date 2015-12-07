<?php
/**
 * @package api
 * @subpackage objects
 * @abstract
 */
abstract class KalturaPermissionItem extends KalturaObject implements IRelatedFilterable
{

	/**
	 * @var int
	 * @readonly
	 * @filter eq,in,order
	 */
	public $id;
		

	/**
	 * @var KalturaPermissionItemType
	 * @filter eq,in
	 * @readonly
	 */
	public $type;
	
	/**
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $partnerId;
	
	
	/**
	 * @var string
	 * @filter mlikeor, mlikeand
	 */
	public $tags;


	/**
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;

	
	/**
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $updatedAt;
	
		
	public function __construct()
	{
		$this->type = get_class($this);
	}
	
	private static $map_between_objects = array(
		'id',
		'partnerId',
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
			$dbObject = new PermissionItem();
			
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

