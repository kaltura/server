<?php
/**
 * @package api
 * @subpackage objects
 * @relatedService GroupUserService
 */
class KalturaGroupUser extends KalturaObject implements IRelatedFilterable
{
	/**
	 * @var string
	 * @readonly
	 */
	public $id;

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
	
	/**
	 * @insertonly
	 * @var KalturaGroupUserCreationMode
	 */
	public $creationMode;

	/**
	 * @var KalturaGroupUserRole
	 */
	public $userRole;

	private static $map_between_objects = array
	(
		"id",
		"userId" => "puserId",
		"groupId" => "pgroupId",
		"partnerId",
		"status",
		"createdAt",
		"updatedAt",
		"creationMode",
		"userRole"
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new KuserKgroup();
			
		return parent::toObject($dbObject, $skip);
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