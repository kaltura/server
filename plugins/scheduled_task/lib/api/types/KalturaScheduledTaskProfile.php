<?php

/**
 * @package plugins.scheduledTask
 * @subpackage api.objects
 */
class KalturaScheduledTaskProfile extends KalturaObject implements IFilterable
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
	 * @filter eq,in
	 */
	public $systemName;
	
	/**
	 * @var string
	 */
	public $description;

	/**
	 * @var KalturaScheduledTaskProfileStatus
	 * @filter eq,in
	 */
	public $status;

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
	 * A list of tasks to execute on the founded objects
	 *
	 * @var KalturaObjectTaskArray
	 */
	public $objectTasks;
	
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
	public $lastExecutionStartedAt;

	/**
	 * The maximum number of result count allowed to be processed by this profile per execution
	 *
	 * @var int
	 */
	public $maxTotalCountAllowed;

	/*
	 */
	private static $map_between_objects = array(
		'id',
		'partnerId',
		'name',
		'systemName',
		'description',
		'status',
		'objectFilterEngineType',
		'objectFilter',
		'objectTasks',
		'createdAt',
		'updatedAt',
		'lastExecutionStartedAt',
		'maxTotalCountAllowed',
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
			$this->status = KalturaScheduledTaskProfileStatus::DISABLED;

		return parent::toInsertableObject($objectToFill, $propertiesToSkip);
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::validateForInsert()
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyMinLength('name', 3, false);
		$this->validatePropertyMinLength('systemName', 3, true);
		$this->validatePropertyNotNull('objectFilterEngineType');
		$this->validatePropertyNotNull('objectFilter');
		$this->validatePropertyNotNull('objectTasks');
		$this->validatePropertyNotNull('maxTotalCountAllowed');

		parent::validateForInsert($propertiesToSkip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUpdate()
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		$this->validatePropertyMinLength('name', 3, true);
		$this->validatePropertyMinLength('systemName', 3, true);

		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $propertiesToSkip = array())
	{
		if(is_null($dbObject))
			$dbObject = new ScheduledTaskProfile();

		$dbObject = parent::toObject($dbObject, $propertiesToSkip);
		if (!is_null($this->objectFilter))
			$dbObject->setObjectFilterApiType(get_class($this->objectFilter));
		return $dbObject;
	}

	/**
	 * @param ScheduledTaskProfile $srcObj
	 */
	public function fromObject($srcObj)
	{
		parent::fromObject($srcObj);
		$this->objectTasks = KalturaObjectTaskArray::fromDbArray($srcObj->getObjectTasks());
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