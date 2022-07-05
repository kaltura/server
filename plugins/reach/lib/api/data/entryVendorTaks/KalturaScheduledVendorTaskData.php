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
		if (!$dbObject) {
			$dbObject = new kScheduledVendorTaskData();
		}
		return parent::toObject($dbObject, $propsToSkip);
	}

	/* (non-PHPdoc)
 	 * @see KalturaObject::toInsertableObject()
 	 */
	public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
	{
		if (is_null($object_to_fill)) {
			$object_to_fill = new kScheduledVendorTaskData();
		}

		$connectedEvent = BaseScheduleEventPeer::retrieveByPK($this->scheduledEventId);

		if ($this->startDate == null)
		{
			$this->startDate = $connectedEvent->getStartDate();
		}

		if ($this->endDate == null)
		{
			$this->endDate = $connectedEvent->getEndDate();
		}

		$this->entryDuration = ($this->endDate - $this->startDate) * 1000;

		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}

	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull('scheduledEventId');
		$this->validateScheduledEvent();

		return parent::validateForInsert($propertiesToSkip);
	}

	private function validateScheduledEvent()
	{
		$connectedEvent = BaseScheduleEventPeer::retrieveByPK($this->scheduledEventId);
		if (!$connectedEvent)
		{
			throw new KalturaAPIException(KalturaErrors::SCHEDULE_EVENT_ID_NOT_FOUND, $this->scheduledEventId);
		}

		if ($this->startDate)
		{
			$this->validatePropertyMinMaxValue('startDate', $connectedEvent->getStartDate(null), $connectedEvent->getEndDate(null));
		}
		if ($this->endDate)
		{
			$this->validatePropertyMinMaxValue('endDate', $connectedEvent->getStartDate(null), $connectedEvent->getEndDate(null));
		}
		if ($this->startDate && $this->endDate)
		{
			$this->validatePropertyMaxValue('startDate', $this->endDate);
		}
	}
}