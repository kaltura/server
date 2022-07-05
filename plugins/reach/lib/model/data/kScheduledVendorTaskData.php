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
	public $scheduleEventId;

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
	 * @param int $scheduleEventId
	 */
	public function setScheduleEventId($scheduleEventId)
	{
		$this->scheduleEventId = $scheduleEventId;
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

	/**
	 * @param VendorCatalogItem $vendorCatalogItem
	 * @throws KalturaAPIException
	 */
	public function validateCatalogLimitations($vendorCatalogItem)
	{
		// validate that the catalogItem type is appropriate
		if (!$vendorCatalogItem instanceof IVendorScheduledCatalogItem)
		{
			throw new KalturaAPIException(KalturaReachErrors::CATALOG_ITEM_AND_JOB_DATA_MISMATCH, get_class($vendorCatalogItem), 'KalturaScheduledVendorTaskData');
		}

		if ($this->getStartDate() - time() < $vendorCatalogItem->getMinimalOrderTime() * dateUtils::MINUTE)
		{
			throw new KalturaAPIException(KalturaReachErrors::TOO_LATE_ORDER, $this->getScheduleEventId(), $vendorCatalogItem->getId(), $vendorCatalogItem->getMinimalOrderTime());
		}

		$taskDurationSec = $this->getEndDate() - $this->getStartDate();
		$durationLimitSec = $vendorCatalogItem->getDurationLimit() * dateUtils::MINUTE;
		if ($taskDurationSec > $durationLimitSec)
		{
			throw new KalturaAPIException(KalturaReachErrors::TOO_LONG_SCHEDULED_TASK, $taskDurationSec, $durationLimitSec, $vendorCatalogItem->getId());
		}
	}
}