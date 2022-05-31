<?php
/**
 * TODO
 *
 * @package plugins.reach
 * @subpackage model
 *
 */
class kScheduledVendorTaskData extends kVendorTaskData
{
	/**
	 * TODO
	 * @var int
	 */
	public $scheduleEventId;

	/**
	 * TODO
	 * @var time
	 */
	public $startDate;

	/**
	 * TODO
	 * @var time
	 */
	public $endDate;

	/**
	 * Get the schedule event id
	 *
	 * @return     int
	 */
	public function getScheduleEventId()
	{
		return $this->scheduleEventId;
	}

	/**
	 * Get schedule event object
	 *
	 * @return ScheduleEvent
	 */
	public function getScheduleEvent()
	{
		if (!$this->scheduleEventId)
		{
			return null;
		}
		return ScheduleEventPeer::retrieveByPK($this->scheduleEventId);
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
	 * Get the task's end date
	 *
	 * @return time
	 */
	public function getEndDate()
	{
		return $this->endDate;
	}

	/**
	 * @param int $scheduleEventId
	 */
	public function setScheduleEventId($scheduleEventId)
	{
		$this->scheduleEventId = $scheduleEventId;
	}

	/**
	 * @param time $startDate
	 */
	public function setStartDate($startDate)
	{
		$this->startDate = $startDate;
	}

	/**
	 * @param time $endDate
	 */
	public function setEndDate($endDate)
	{
		$this->endDate = $endDate;
	}
}
