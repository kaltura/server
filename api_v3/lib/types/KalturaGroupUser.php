<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaGroupUser extends KalturaObject implements IFilterable
{

	/**
	 * @var string
	 * @insertonly
	 * @filter eq,in
	 */
	public $userId;
	
	/**
	 * @var string
	 * @insertonly
	 * @filter eq,in
	 */
	public $groupId;

	/**
	 * @var KalturaGroupUserStatus
	 * @readonly
	 * @filter eq,in
	 */
	public $status;

	/**
	 * @var int
	 * @readonly
	 */
	public $partnerId;

	/**
	 * Creation date as Unix timestamp (In seconds)
	 *
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;

	/**
	 * Last update date as Unix timestamp (In seconds)
	 *
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $updatedAt;
	
	private static $map_between_objects = array
	(
		"userId" => "puserId",
		"groupId" => "pgroupId",
		"partnerId",
		"status",
		"createdAt",
		"updatedAt",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new KuserKgroup();
			
		parent::toObject($dbObject, $skip);
		
		return $dbObject;
	}

	public function fromObject($sourceObject)
	{
		if(!$sourceObject)
			return;
			
		parent::fromObject($sourceObject);
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