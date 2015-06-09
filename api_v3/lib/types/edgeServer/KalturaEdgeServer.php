<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaEdgeServer extends KalturaObject implements IFilterable
{
	/**
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $id;
	
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
	
	/**
	 * @var int
	 * @readonly
	 */
	public $partnerId;
	
	/**
	 * edgeServer name
	 * 
	 * @var string
	 * @filter eq,in
	 */
	public $name;
	
	/**
	 * edgeServer uniqe system name
	 * 
	 * @var string
	 * @filter eq,in
	 */
	public $systemName;
	
	/**
	 * edgeServer description
	 * 
	 * @var string
	 */
	public $desciption;
	
	/**
	 * @var KalturaEdgeServerStatus
	 * @filter eq,in
	 */
	public $status;
	
	/**
	 * edgeServer tags
	 *
	 * @var string
	 * @filter like,mlikeor,mlikeand
	 */
	public $tags;
	
	/**
	 * edgeServer host name
	 * 
	 * @var string
	 * @filter like,mlikeor,mlikeand
	 * @requiresPermission update
	 */
	public $hostName;
	
	/**
	 * edgeServer playback hostName
	 *
	 * @var string
	 * @filter like,mlikeor,mlikeand
	 * @requiresPermission update
	 */
	public $playbackHostName;
	
	/**
	 * Delivery profile ids comma seperated
	 * @var string
	 */
	public $deliveryProfileIds;
	
	/**
	 * Id of the parent edge server
	 * 
	 * @var int
	 * @filter eq,in
	 */
	public $parentId;
	
	private static $map_between_objects = array
	(
		"id",
		"createdAt",
		"updatedAt",
		"partnerId",
		"name",
		"systemName",
		"desciption",
		"status",
		"tags",
		"hostName",
		"playbackHostName",
		"deliveryProfileIds",
		"parentId",
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}	
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toInsertableObject()
	 */
	public function toInsertableObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(is_null($object_to_fill))
			$object_to_fill = new EdgeServer();
			
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}
	

	/* (non-PHPdoc)
	 * @see KalturaObject::validateForInsert()
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validateMandatoryAttributes();
		$this->validateDuplications();
	
		return parent::validateForInsert($propertiesToSkip);
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUpdate()
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		$this->validateMandatoryAttributes();
		$this->validateDuplications($sourceObject->getId());
				
		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}
	
	public function validateMandatoryAttributes()
	{
		$this->validatePropertyMinLength("name", 1, true);
		$this->validatePropertyNotNull("hostName");
	}
	
	public function validateDuplications($edgeId = null)
	{		
		$this->validateHostNameDuplication($edgeId);
		
		if($this->systemName)
			$this->validateSystemNameDuplication($edgeId);
	}
	
	public function validateHostNameDuplication($edgeId = null)
	{
		$c = KalturaCriteria::create(EdgeServerPeer::OM_CLASS);
		
		if($edgeId)
			$c->add(EdgeServerPeer::ID, $sourceObject->getId(), Criteria::NOT_EQUAL);
		
		$c->add(EdgeServerPeer::HOST_NAME, $this->hostName);
		$c->add(EdgeServerPeer::STATUS, array(EdgeServerStatus::ACTIVE, EdgeServerStatus::DISABLED), Criteria::IN);
		
		if(EdgeServerPeer::doCount($c))
			throw new KalturaAPIException(KalturaErrors::HOST_NAME_ALREADY_EXISTS, $this->hostName);
	}
	
	public function validateSystemNameDuplication($edgeId = null)
	{
		$c = KalturaCriteria::create(EdgeServerPeer::OM_CLASS);
	
		if($edgeId)
			$c->add(EdgeServerPeer::ID, $sourceObject->getId(), Criteria::NOT_EQUAL);
	
		$c->add(EdgeServerPeer::SYSTEM_NAME, $this->systemName);
		$c->add(EdgeServerPeer::STATUS, array(EdgeServerStatus::ACTIVE, EdgeServerStatus::DISABLED), Criteria::IN);
	
		if(EdgeServerPeer::doCount($c))
			throw new KalturaAPIException(KalturaErrors::SYSTEM_NAME_ALREADY_EXISTS, $this->systemName);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(is_null($object_to_fill))
			$object_to_fill = new EdgeServer();
		
		
		$object_to_fill =  parent::toObject($object_to_fill, $props_to_skip);
		
		return $object_to_fill;
	}
	
	/* (non-PHPdoc)
	 * @see IFilterable::getExtraFilters()
	 */
	public function getExtraFilters()
	{
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IFilterable::getFilterDocs()
	 */
	public function getFilterDocs()
	{
		return array();
	}
}

