<?php

/**
 * @package plugins.virtualEvent
 * @subpackage api.objects
 */
class KalturaVirtualEvent extends KalturaObject implements IFilterable
{
	/**
	 * @var int
	 * @readonly
	 * @filter eq,in,order
	 */
	public $id;
	
	/**
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $partnerId;
	
	/**
	 * @var string
	 */
	public $name;
	
	/**
	 * @var string
	 */
	public $description;
	
	/**
	 * @var KalturaVirtualEventStatus
	 * @filter eq,in
	 */
	public $status;
	
	/**
	 * @var int[]
	 * @filter eq,in,order
	 */
	public $attendeesGroupIds;
	
	/**
	 * @var int
	 */
	public $registrationScheduleEventId;
	
	/**
	 * @var int
	 */
	public $agendaScheduleEventId;
	
	/**
	 * @var int
	 */
	public $eventScheduleEventId;
	
	/**
	 * The type of engine to use to list objects using the given "objectFilter"
	 *
	 * @var KalturaObjectFilterEngineType
	 */
	public $objectFilterEngineType;
	
	/**
	 * A filter object (inherits KalturaFilter) that is used to list objects for scheduled tasks
	 *
	 * @var KalturaFilter
	 */
	public $objectFilter;
	
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
	 */
	private static $map_between_objects = array(
		'id',
		'partnerId',
		'name',
		'description',
		'status',
		'attendeesGroupIds',
		'registrationScheduleEventId',
		'agendaScheduleEventId',
		'eventScheduleEventId',
		'objectFilterEngineType',
		'objectFilter',
		'objectTasks',
		'createdAt',
		'updatedAt',
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toInsertableObject($objectToFill = null, $propertiesToSkip = array())
	{
		if (is_null($this->status))
			$this->status = KalturaVirtualEventStatus::DELETED;
		
		return parent::toInsertableObject($objectToFill, $propertiesToSkip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForInsert()
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyMinLength('name', 3, false);
		$this->validatePropertyNotNull('objectFilterEngineType');
		$this->validatePropertyNotNull('objectFilter');
		parent::validateForInsert($propertiesToSkip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUpdate()
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		$this->validatePropertyMinLength('name', 3, true);
		
		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $propertiesToSkip = array())
	{
		if(is_null($dbObject))
			$dbObject = new VirtualEvent();
		
		$dbObject = parent::toObject($dbObject, $propertiesToSkip);
		if (!is_null($this->objectFilter))
			$dbObject->setObjectFilterApiType(get_class($this->objectFilter));
		return $dbObject;
	}
	
	/**
	 * @param VirtualEvent $srcObj
	 */
	public function doFromObject($srcObj, KalturaDetachedResponseProfile $responseProfile = null)
	{
		parent::doFromObject($srcObj, $responseProfile);
		$filterType = $srcObj->getObjectFilterApiType();
		if (!class_exists($filterType))
		{
			KalturaLog::err(sprintf('Class %s not found, cannot initiate object filter instance', $filterType));
			$this->objectFilter = new KalturaFilter();
		}
		else
		{
			$this->objectFilter = new $filterType();
		}
		
		$this->objectFilter->fromObject($srcObj->getObjectFilter());
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