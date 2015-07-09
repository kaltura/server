<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaResponseProfile extends KalturaDetachedResponseProfile implements IFilterable
{
	/**
	 * Auto generated numeric identifier
	 * 
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $id;
	
	/**
	 * Unique system name
	 * 
	 * @var string
	 * @filter eq,in
	 */
	public $systemName;
	
	/**
	 * @var int
	 * @readonly
	 */
	public $partnerId;
	
	/**
	 * Creation time as Unix timestamp (In seconds) 
	 * 
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;
	
	/**
	 * Update time as Unix timestamp (In seconds) 
	 * 
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $updatedAt;
	
	/**
	 * @var KalturaResponseProfileStatus
	 * @readonly
	 * @filter eq,in
	 */
	public $status;
	
	
	public function __construct(ResponseProfile $responseProfile = null)
	{
		if($responseProfile)
		{
			$this->fromObject($responseProfile);
		}
	}
	
	private static $map_between_objects = array(
		'id', 
		'systemName', 
		'partnerId',
		'createdAt',
		'updatedAt',
		'status',
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUsage($sourceObject, $propertiesToSkip)
	 */
	public function validateForUsage($sourceObject, $propertiesToSkip = array())
	{
		// Allow null in case of update
		$this->validatePropertyMinLength('systemName', 2, !is_null($sourceObject));
		
		$id = $this->id;
		if($sourceObject && $sourceObject->getId())
		{
			$id = $sourceObject->getId();
		}
			
		parent::validateForUsage($sourceObject, $propertiesToSkip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($object = null, $propertiesToSkip = array())
	{
		if(is_null($object))
		{
			$object = new ResponseProfile();
		}
		
		if($this->filter)
		{
			$object->setFilterApiClassName(get_class($this->filter));
			$object->setFilter($this->filter->toObject());
		}
		
		return parent::toObject($object, $propertiesToSkip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject($srcObj, $responseProfile)
	 */
	public function doFromObject($srcObj, KalturaDetachedResponseProfile $responseProfile = null)
	{
		/* @var $srcObj ResponseProfile */
		parent::doFromObject($srcObj, $responseProfile);
		
		if($srcObj->getFilter() && $this->shouldGet('filter', $responseProfile))
		{
			$filterApiClassName = $srcObj->getFilterApiClassName();
			$this->filter = new $filterApiClassName();
			$this->filter->fromObject($srcObj->getFilter());
		}
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