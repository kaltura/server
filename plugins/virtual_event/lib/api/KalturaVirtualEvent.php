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
	 */
	public $id;
	
	/**
	 * @var int
	 * @filter eq
	 * @readonly
	 */
	public $partnerId;
	
	/**
	 * @var string
	 * @filter like,mlikeor,mlikeand,eq,order
	 */
	public $name;
	
	/**
	 * @var string
	 * @filter like,mlikeor,mlikeand,eq,order
	 */
	public $description;
	
	/**
	 * @var KalturaVirtualEventStatus
	 * @filter eq
	 */
	public $status;
	
	/**
	 * @var string
	 * @filter like,mlikeor,mlikeand,eq,order
	 */
	public $tags;
	
	/**
	 * @var string
	 */
	public $attendeesGroupId;
	
	/**
	 * @var string
	 */
	public $adminsGroupId;
	
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
	 * @filter gte,lte,order
	 */
	public $deletionDueDate;
	
	/*
	 */
	private static $map_between_objects = array(
		'id',
		'partnerId',
		'name',
		'description',
		'status',
		'tags',
		'attendeesGroupId',
		'adminsGroupId',
		'registrationScheduleEventId',
		'agendaScheduleEventId',
		'eventScheduleEventId',
		'createdAt',
		'updatedAt',
		'deletionDueDate',
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
		{
			$this->status = KalturaVirtualEventStatus::DELETED;
		}
		return parent::toInsertableObject($objectToFill, $propertiesToSkip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForInsert()
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyMinLength('name', 3, false);
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