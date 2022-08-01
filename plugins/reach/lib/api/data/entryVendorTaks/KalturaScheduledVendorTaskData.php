<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 * @relatedService EntryVendorTaskService
 */
class KalturaScheduledVendorTaskData extends KalturaVendorTaskData
{
	/**
	 * @var time
	 * @insertonly
	 */
	public $startDate;

	/**
	 * @var time
	 * @insertonly
	 */
	public $endDate;

	/**
	 * @var int
	 * @insertonly
	 */
	public $scheduledEventId;

	private static $map_between_objects = array
	(
		'scheduledEventId',
		'startDate',
		'endDate',
	);

	/* (non-PHPdoc)
	 * @see KalturaCuePoint::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	/* (non-PHPdoc)
 	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
 	 */
	public function toObject($dbObject = null, $propsToSkip = array())
	{
		if (!$dbObject)
		{
			$dbObject = new kScheduledVendorTaskData();
		}

		$dbObject = parent::toObject($dbObject, $propsToSkip);

		$connectedEvent = BaseScheduleEventPeer::retrieveByPK($this->scheduledEventId);

		if (!$this->startDate)
		{
			$dbObject->setStartDate($connectedEvent->getStartDate(null));
		}

		if (!$this->endDate)
		{
			$dbObject->setEndDate($connectedEvent->getEndDate(null));
		}

		$dbObject->setEntryDuration(($dbObject->getEndDate() - $dbObject->getStartDate()) * 1000);

		return $dbObject;
	}

	/* (non-PHPdoc)
 	 * @see KalturaObject::toInsertableObject()
 	 */
	public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
	{
		if (is_null($object_to_fill))
		{
			$object_to_fill = new kScheduledVendorTaskData();
		}

		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}

	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull('scheduledEventId');
		$this->validateScheduledEvent();

		return parent::validateForInsert($propertiesToSkip);
	}

	protected function validateScheduledEvent()
	{
		$connectedEvent = BaseScheduleEventPeer::retrieveByPK($this->scheduledEventId);
		if (!$connectedEvent)
		{
			throw new KalturaAPIException(KalturaErrors::SCHEDULE_EVENT_ID_NOT_FOUND, $this->scheduledEventId);
		}

		if ($this->startDate)
		{
			$this->validatePropertyMinMaxValue('startDate', $connectedEvent->getStartDate(null) - ScheduleEventPeer::TIME_MARGIN, $connectedEvent->getEndDate(null));
		}
		if ($this->endDate)
		{
			$this->validatePropertyMinMaxValue('endDate', $connectedEvent->getStartDate(null), $connectedEvent->getEndDate(null) + ScheduleEventPeer::TIME_MARGIN);
		}
		if ($this->startDate && $this->endDate)
		{
			$this->validatePropertyMaxValue('startDate', $this->endDate);
		}
	}

	/**
	 * @param $catalogItemId
	 * @throws KalturaAPIException
	 * @throws PropelException
	 */
	public function validateCatalogLimitations($catalogItemId)
	{
		$vendorCatalogItem = VendorCatalogItemPeer::retrieveByPK($catalogItemId);
		$connectedEvent = BaseScheduleEventPeer::retrieveByPK($this->scheduledEventId);

		// validate that the catalogItem type is appropriate
		if (!$vendorCatalogItem instanceof IVendorScheduledCatalogItem)
		{
			throw new KalturaAPIException(KalturaReachErrors::CATALOG_ITEM_AND_JOB_DATA_MISMATCH, get_class($vendorCatalogItem), 'KalturaScheduledVendorTaskData');
		}

		//validate that the scheduled event type fits the catalog item
		switch (get_class($vendorCatalogItem)){
			case 'VendorLiveCaptionCatalogItem':
				if (get_class($connectedEvent) !== 'LiveStreamScheduleEvent')
				{
					throw new KalturaAPIException(KalturaReachErrors::CATALOG_ITEM_AND_JOB_DATA_MISMATCH, get_class($connectedEvent));
				}
		}

		$startTime = $this->startDate ? $this->startDate : $connectedEvent->getStartDate(null);
		$minimalOrderTimeSec = $vendorCatalogItem->getMinimalOrderTime() * dateUtils::MINUTE;
		if ($startTime - time() < $minimalOrderTimeSec)
		{
			throw new KalturaAPIException(KalturaReachErrors::TOO_LATE_ORDER, $this->scheduledEventId, $vendorCatalogItem->getId(), $vendorCatalogItem->getMinimalOrderTime());
		}

		$endTime = $this->endDate ? $this->endDate : $connectedEvent->getEndDate(null);
		$taskDurationSec = $endTime - $startTime;
		$durationLimitSec = $vendorCatalogItem->getDurationLimit() * dateUtils::MINUTE;
		if ($taskDurationSec > $durationLimitSec)
		{
			throw new KalturaAPIException(KalturaReachErrors::TOO_LONG_SCHEDULED_TASK, $taskDurationSec, $durationLimitSec, $vendorCatalogItem->getId());
		}
	}
}