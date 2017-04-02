<?php

/**
 * @package plugins.scheduledTask
 * @subpackage model
 */
class MediaRepurposingProfile
{

	/**
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;

	/**
	 * The value for the status field.
	 * @var        int
	 */
	protected $status;

	/**
	 * A filter object (inherits baseObjectFilter) that is used to list objects for Media repurposing profiles
	 *
	 * @var entryFilter
	 */
	protected $objectFilter;

	/**
	 * A list of tasks to execute on the founded objects
	 *
	 * @var string
	 */
	protected $scheduleTasksIds;

	/**
	 * A list of tasks to execute on the founded objects
	 *
	 * @var string
	 */
	protected $taskType;


	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return int
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @return entryFilter
	 */
	public function getObjectFilter()
	{
		return $this->objectFilter;
	}

	/**
	 * @return string
	 */
	public function getScheduleTasksIds()
	{
		return $this->scheduleTasksIds;
	}

	/**
	 * @return string
	 */
	public function getTaskType()
	{
		return $this->taskType;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @param int $status
	 */
	public function setStatus($status)
	{
		$this->status = $status;
	}

	/**
	 * @param KalturaFilter $objectFilter
	 */
	public function setObjectFilter($objectFilter)
	{
		$this->objectFilter = $objectFilter;
	}

	/**
	 * @param string $scheduleTasksIds
	 */
	public function setScheduleTasksIds($scheduleTasksIds)
	{
		$this->scheduleTasksIds = $scheduleTasksIds;
	}

	/**
	 * @param string $taskType
	 */
	public function setTaskType($taskType)
	{
		$this->taskType = $taskType;
	}
}