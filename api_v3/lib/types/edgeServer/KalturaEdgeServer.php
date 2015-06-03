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
	 * @requiresPermission update
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
	 * @requiresPermission update
	 */
	public $playbackHostName;
	
	/**
	 * Delivery profile ids
	 * @var KalturaKeyValueArray
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
			
		if($this->systemName)
		{
			$c = KalturaCriteria::create(EdgeServerPeer::OM_CLASS);
			$c->add(EdgeServerPeer::SYSTEM_NAME, $this->systemName);
			if(StorageProfilePeer::doCount($c))
				throw new KalturaAPIException(KalturaErrors::SYSTEM_NAME_ALREADY_EXISTS, $this->systemName);
		}
	
		return parent::validateForInsert($propertiesToSkip);
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUpdate()
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		$this->validateMandatoryAttributes();
		
		if($this->systemName)
		{
			$c = KalturaCriteria::create(EdgeServerPeer::OM_CLASS);
			$c->add(EdgeServerPeer::ID, $sourceObject->getId(), Criteria::NOT_EQUAL);
			$c->add(EdgeServerPeer::SYSTEM_NAME, $this->systemName);
			if(EdgeServerPeer::doCount($c))
				throw new KalturaAPIException(KalturaErrors::SYSTEM_NAME_ALREADY_EXISTS, $this->systemName);
		}
		
		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}
	
	public function validateMandatoryAttributes()
	{
		$this->validatePropertyMinLength("name", 1, true);
		$this->validatePropertyNotNull("hostName");
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(is_null($object_to_fill))
			$object_to_fill = new EdgeServer();
		
		
		$object_to_fill =  parent::toObject($object_to_fill, $props_to_skip);
		
		// Delivery Profile Ids
		$deliveryProfileIds = $this->deliveryProfileIds;
		
		$deliveryProfiles = array();
		if($deliveryProfileIds)
			foreach($deliveryProfileIds->toArray() as $keyValue) 
				$this->insertObject($deliveryProfiles, $keyValue->key, $keyValue->value);
			
		$object_to_fill->setDeliveryProfileIds($deliveryProfiles);
		
		return $object_to_fill;
	}
	
	protected function insertObject(&$res, $key, $value) {
		if(strpos($key, ".") === FALSE) {
			$res[$key] = intval($value);
			return;
		}
	
		list($key, $newKey) = explode(".", $key, 2);
		if(!array_key_exists($key, $res))
			$res[$key] = array();
		$this->insertObject($res[$key], $newKey, $value);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject()
	 */
	public function doFromObject($source_object, KalturaDetachedResponseProfile $responseProfile = null)
	{
	    parent::doFromObject($source_object, $responseProfile);
	    
		if($this->shouldGet('deliveryProfileIds', $responseProfile))
			$this->deliveryProfileIds = KalturaKeyValueArray::fromKeyValueArray($source_object->getDeliveryProfileIds());
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

