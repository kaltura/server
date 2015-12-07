<?php
/**
 * @package api
 * @subpackage objects
 */
abstract class KalturaServerNode extends KalturaObject implements IFilterable, IApiObjectFactory
{
	/**
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $id;
	
	/**
	 * @var int
	 * @readonly
	 */
	public $partnerId;
	
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
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $heartbeatTime;
	
	/**
	 * serverNode name
	 * 
	 * @var string
	 * @filter eq,in
	 */
	public $name;
	
	/**
	 * serverNode uniqe system name
	 * 
	 * @var string
	 * @filter eq,in
	 */
	public $systemName;
	
	/**
	 * 
	 * @var string
	 */
	public $description;
	
	/**
	 * serverNode hostName
	 *
	 * @var string
	 * @filter like,mlikeor,mlikeand
	 */
	public $hostName;
	
	/**
	 * @var KalturaServerNodeStatus
	 * @readonly
	 * @filter eq,in
	 */
	public $status;
	
	/**
	 * @var KalturaServerNodeType
	 * @readonly
	 * @filter eq,in
	 */
	public $type;
	
	/**
	 * serverNode tags
	 *
	 * @var string
	 * @filter like,mlikeor,mlikeand
	 */
	public $tags;
	
	/**
	 * DC where the serverNode is located
	 *
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $dc;
	
	/**
	 * Id of the parent serverNode
	 *
	 * @var int
	 * @filter eq,in
	 */
	public $parentId;
	
	private static $map_between_objects = array
	(
		"id",
		"partnerId",
		"createdAt",
		"updatedAt",
		"heartbeatTime",
		"name",
		"systemName",
		"description",
		"hostName",
		"status",
		"type",
		"tags",
		"dc",
		"parentId"
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}	
	
	public function validateForInsertByType($propertiesToSkip, $type)
	{
		$this->validateMandatoryAttributes(true);
		$this->validateDuplications(null, $type);
	
		return parent::validateForInsert($propertiesToSkip);
	}
	
	public function validateForUpdateByType($sourceObject, $propertiesToSkip = array(), $type)
	{
		$this->validateMandatoryAttributes();
		$this->validateDuplications($sourceObject->getId(), $type);
				
		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}
	
	public function validateMandatoryAttributes($isInsert = false)
	{
		$this->validatePropertyMinLength("hostName", 1, !$isInsert);
		
		$this->validatePropertyMinLength("name", 1, !$isInsert);
	}
	
	public function validateDuplications($serverNodeId = null, $type)
	{
		if($this->hostName)		
			$this->validateHostNameDuplication($serverNodeId, $type);
		
		if($this->systemName)
			$this->validateSystemNameDuplication($serverNodeId, $type);
	}
	
	public function validateHostNameDuplication($serverNodeId = null, $type)
	{
		$c = KalturaCriteria::create(ServerNodePeer::OM_CLASS);
		
		if($serverNodeId)
			$c->add(ServerNodePeer::ID, $serverNodeId, Criteria::NOT_EQUAL);
		
		$c->add(ServerNodePeer::HOST_NAME, $this->hostName);
		$c->add(ServerNodePeer::TYPE, $type);
		
		if(ServerNodePeer::doCount($c))
			throw new KalturaAPIException(KalturaErrors::HOST_NAME_ALREADY_EXISTS, $this->hostName);
	}
	
	public function validateSystemNameDuplication($serverNodeId = null, $type)
	{
		$c = KalturaCriteria::create(ServerNodePeer::OM_CLASS);
	
		if($serverNodeId)
			$c->add(ServerNodePeer::ID, $serverNodeId, Criteria::NOT_EQUAL);
	
		$c->add(ServerNodePeer::SYSTEM_NAME, $this->systemName);
		$c->add(ServerNodePeer::TYPE, $type);
	
		if(ServerNodePeer::doCount($c))
			throw new KalturaAPIException(KalturaErrors::SYSTEM_NAME_ALREADY_EXISTS, $this->systemName);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject()
	 */
	public function doFromObject($source_object, KalturaDetachedResponseProfile $responseProfile = null)
	{
		parent::doFromObject($source_object, $responseProfile);
		
		if($this->shouldGet('status', $responseProfile) && !is_null($source_object->getHeartbeatTime())
				&& $source_object->getHeartbeatTime(null) < (time() - 120) && $this->status !== ServerNodeStatus::DISABLED)
			$this->status = ServerNodeStatus::NOT_REGISTERED;
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
	
	public static function getInstance($sourceObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$type = $sourceObject->getType();
		
		switch ($type)
		{
			case KalturaServerNodeType::EDGE:
				$object = new KalturaEdgeServerNode();
				break;
		
			default:
				$object = KalturaPluginManager::loadObject('KalturaServerNode', $type);
				if(!$object)
					$object = new KalturaServerNode();
				break;
		}
		
		if (!$object)
			return null;
		 
		$object->fromObject($sourceObject, $responseProfile);
		return $object;
	}
}