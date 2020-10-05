<?php
/**
 * @package plugins.schedule
 * @subpackage api.objects
 */
class KalturaLiveStreamScheduleEvent extends KalturaEntryScheduleEvent
{
	/**
	 * Defines the expected audience.
	 * @var int
	 * @minValue 0
	 */
	public $projectedAudience;

	/**
	 * The entry ID of the source entry (for simulive)
	 * @var string
	 */
	public $sourceEntryId;

	/**
	 * The time relative time before the startTime considered as preStart time
	 * @var int
	 */
	public $preStartTime;

	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($sourceObject = null, $propertiesToSkip = array())
	{
		if(is_null($sourceObject))
		{
			$sourceObject = new LiveStreamScheduleEvent();
		}
		
		return parent::toObject($sourceObject, $propertiesToSkip);
	}

	/*
	 * Mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)
	 */
	private static $map_between_objects = array
	(
		'projectedAudience',
		'sourceEntryId',
		'preStartTime'
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	/**
	 * {@inheritDoc}
	 * @see KalturaScheduleEvent::getScheduleEventType()
	 */
	public function getScheduleEventType()
	{
		return ScheduleEventType::LIVE_STREAM;
	}

	/**
	 * @throws KalturaAPIException
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validateLiveStreamEventFields();
		parent::validateForInsert($propertiesToSkip);
	}

	/**
	 * @throws KalturaAPIException
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		$this->validateLiveStreamEventFields();
		parent::validateForUpdate($sourceObject, $propertiesToSkip = array());
	}

	/**
	 * @throws KalturaAPIException
	 */
	protected function validateLiveStreamEventFields()
	{
		if (isset($this->sourceEntryId) && !BaseentryPeer::retrieveByPK($this->sourceEntryId))
		{
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $this->sourceEntryId);
		}
		if (isset($this->preStartTime) && $this->preStartTime < 0)
		{
			throw new KalturaAPIException(APIErrors::INVALID_FIELD_VALUE, 'preStartTime');
		}
	}
}