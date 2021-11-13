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
	 * @filter eq,in,notin
	 */
	public $id;
	
	/**
	 * @var int
	 * @filter eq,in
	 * @readonly
	 */
	public $partnerId;
	
	/**
	 * @var string
	 * @minLength 3
	 * @maxLength 256
	 */
	public $name;
	
	/**
	 * @var string
	 */
	public $description;
	
	/**
	 * @var KalturaVirtualEventStatus
	 * @readonly
	 * @filter eq,in
	 */
	public $status;
	
	/**
	 * @var string
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
	public $mainEventScheduleEventId;
	
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
		'mainEventScheduleEventId',
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
		return parent::toInsertableObject($objectToFill, $propertiesToSkip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $propertiesToSkip = array())
	{
		if(is_null($dbObject))
		{
			$dbObject = new VirtualEvent();
		}
		
		return parent::toObject($dbObject, $propertiesToSkip);
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
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForInsert($propertiesToSkip)
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull('name');
		parent::validateForInsert($propertiesToSkip);
	}
}