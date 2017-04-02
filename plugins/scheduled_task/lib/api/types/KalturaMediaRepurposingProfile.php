<?php

/**
 * @package plugins.scheduledTask
 * @subpackage api.objects
 */
class KalturaMediaRepurposingProfile extends KalturaObject implements IFilterable
{

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var KalturaScheduledTaskProfileStatus
	 * @filter eq,in
	 */
	public $status;
	

	/**
	 * A filter object (inherits KalturaFilter) that is used to list objects for scheduled tasks
	 *
	 * @var KalturaFilter
	 */
	public $objectFilter;

	/**
	 * A list of tasks to execute on the founded objects
	 *
	 * @var string
	 */
	public $scheduleTasksIds;

	/**
	 * Type of task to perform
	 *
	 * @var string
	 */
	public $taskType;


	/*
	 */
	private static $map_between_objects = array(
		'name',
		'status',
		'objectFilter',
		'scheduleTasksIds',
		'taskType'
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
			$dbObject = new MediaRepurposingProfile();

		$dbObject = parent::toObject($dbObject, $propertiesToSkip);

		return $dbObject;
	}

	/**
	 * @param MediaRepurposingProfile $srcObj
	 */
	public function doFromObject($srcObj, KalturaDetachedResponseProfile $responseProfile = null)
	{
		parent::doFromObject($srcObj, $responseProfile);

		$this->objectFilter = new KalturaBaseEntryFilter();
		$this->objectFilter->fromObject($srcObj->getObjectFilter());
		$this->scheduleTasksIds = $srcObj->getScheduleTasksIds();
		$this->taskType = $srcObj->getTaskType();

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