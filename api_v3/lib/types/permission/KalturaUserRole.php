<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUserRole extends KalturaObject implements IRelatedFilterable
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
	 * @filter eq,in
	 */
	public $systemName;
	
	
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

	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array(
		'id',
		'name',
		'systemName',
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
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUpdate()
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		$this->validatePropertyMinLength("name", 1, true);
	
		if($this->systemName)
		{
			$c = KalturaCriteria::create(UserRolePeer::OM_CLASS);
			$c->add(UserRolePeer::ID, $sourceObject->getId(), Criteria::NOT_EQUAL);
			$c->add(UserRolePeer::SYSTEM_NAME, $this->systemName);
			if(UserRolePeer::doCount($c))
				throw new KalturaAPIException(KalturaErrors::SYSTEM_NAME_ALREADY_EXISTS, $this->systemName);
		}
		
		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForInsert()
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyMinLength("name", 1);
		
		if($this->systemName)
		{
			$c = KalturaCriteria::create(UserRolePeer::OM_CLASS);
			$c->add(UserRolePeer::SYSTEM_NAME, $this->systemName);
			if(UserRolePeer::doCount($c))
				throw new KalturaAPIException(KalturaErrors::SYSTEM_NAME_ALREADY_EXISTS, $this->systemName);
		}
		
		return parent::validateForInsert($propertiesToSkip);
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