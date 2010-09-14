<?php
class KalturaSystemUser extends KalturaObject implements IFilterable 
{
	/**
	 * 
	 * @var int
	 * @filter order
	 * @readonly
	 */
	public $id;
	
	/**
	 * 
	 * @var string
	 */
	public $email;
	
	/**
	 * 
	 * @var string
	 */
	public $firstName;
	
	/**
	 * 
	 * @var string
	 */
	public $lastName;
	
	/**
	 * 
	 * @var string
	 */
	public $password;
	
	/**
	 * 
	 * @var int
	 * @readonly
	 */
	public $createdBy;
	
	/**
	 * 
	 * @var KalturaSystemUserStatus
	 * @filter order
	 */
	public $status;
	
	/**
	 * 
	 * @var bool
	 * @readonly
	 */
	public $isPrimary;
	
	/**
	 * 
	 * @var int
	 * @readonly
	 */
	public $statusUpdatedAt;
	
	/**
	 * 
	 * @var int
	 * @readonly
	 */
	public $createdAt;

	/**
	 * 
	 * @var KalturaSystemUserRole
	 */
	public $role;
	
	private static $map_between_objects = array
	(
		"id",
		"email",
		"firstName",
		"lastName",
		"password",
		"createdBy",
		"status",
		"isPrimary",
		"statusUpdatedAt",
		"createdAt",
		"deletedAt",
		"role",
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
	
	public function toObject($dbSystemUser = null, $propsToSkip = array())
	{
		if(is_null($dbSystemUser))
			$dbSystemUser = new SystemUser();
			
		parent::toObject($dbSystemUser, $propsToSkip);
		return $dbSystemUser;
	}
}