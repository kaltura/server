<?php


/**
 * @package plugins.reach
 * @subpackage model
 *
 */
class kScheduledVendorTaskData extends kVendorTaskData
{
	/**
	 * @var int
	 */
	public $scheduledEventId;

	/**
	 * @var time
	 */
	public $startDate;

	/**
	 * @var time
	 */
	public $endDate;

	/**
	 * Get the schedule event id
	 *
	 * @return     int
	 */
	public function getScheduledEventId()
	{
		return $this->scheduledEventId;
	}

	/**
	 * @param int $scheduledEventId
	 */
	public function setScheduledEventId($scheduledEventId)
	{
		$this->scheduledEventId = $scheduledEventId;
	}

	/**
	 * Get schedule event object
	 *
	 * @return ScheduleEvent
	 */
	public function getScheduleEvent()
	{
		if (!$this->scheduledEventId)
		{
			return null;
		}
		return ScheduleEventPeer::retrieveByPK($this->scheduledEventId);
	}

	/**
	 * Get the task's start date
	 *
	 * @return time
	 */
	public function getStartDate()
	{
		return $this->startDate;
	}

	/**
	 * @param time $startDate
	 */
	public function setStartDate($startDate)
	{
		$this->startDate = $startDate;
	}

	/**
	 * Get the task's end date
	 *
	 * @return time
	 */
	public function getEndDate()
	{
		return $this->endDate;
	}

	/**
	 * @param time $endDate
	 */
	public function setEndDate($endDate)
	{
		$this->endDate = $endDate;
	}
}