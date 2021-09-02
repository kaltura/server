<?php
/**
 * @package plugins.schedule
 * @subpackage api.objects
 */
class KalturaLiveStreamScheduleEvent extends KalturaBaseLiveScheduleEvent
{
	/**
	 * The entry ID of the source entry (for simulive)
	 * @var string
	 */
	public $sourceEntryId;
	
	/**
	 * Defines the expected audience.
	 * @var int
	 * @minValue 0
	 */
	public $projectedAudience;
	
	/**
	 * The time relative time before the startTime considered as preStart time
	 * @var int
	 */
	public $preStartTime;

	/**
	 * The time relative time before the endTime considered as postEnd time
	 * @var int
	 */
	public $postEndTime;

	/**
	 * The entry id of the pre start entry
	 * @var string
	 */
	public $preStartEntryId;

	/**
	 * The entry id of the post end entry
	 * @var string
	 */
	public $postEndEntryId;
	
	
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
		'sourceEntryId',
		'projectedAudience',
		'preStartTime',
		'postEndTime',
		'startDate' => 'startScreenTime',
		'endDate' => 'endScreenTime',
		'preStartEntryId',
		'postEndEntryId',
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(),self::$map_between_objects);
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
		if (isset($this->preStartTime) && $this->preStartTime < 0)
		{
			throw new KalturaAPIException(APIErrors::INVALID_FIELD_VALUE, 'preStartTime');
		}
		if (isset($this->postEndTime) && $this->postEndTime < 0)
		{
		    throw new KalturaAPIException(APIErrors::INVALID_FIELD_VALUE, 'postEndTime');
		}
		if (isset($this->sourceEntryId) && !entryPeer::retrieveByPK($this->sourceEntryId))
		{
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $this->sourceEntryId);
		}
		if (isset($this->preStartEntryId) && !entryPeer::retrieveByPK($this->preStartEntryId))
		{
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $this->preStartEntryId);
		}
		if (isset($this->postEndEntryId) && !entryPeer::retrieveByPK($this->postEndEntryId))
		{
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $this->postEndEntryId);
		}
	}
	
	/**
	 * @param LiveStreamScheduleEvent $object_to_fill
	 * @param array $props_to_skip
	 * @return LiveStreamScheduleEvent|mixed|null
	 * @throws PropelException
	 *
	 * Created a new LiveStreamScheduleEvent
	 */
	public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
	{
		$object_to_fill = parent ::toInsertableObject($object_to_fill, $props_to_skip);
		
		/* @var $object_to_fill LiveStreamScheduleEvent */
		$this->updatePrePostTimes($object_to_fill);
		$object_to_fill->setStartDate($this->startDate - $this->preStartTime);
		$object_to_fill->setEndDate($this->endDate + $this->postEndTime);
		
		
		return $object_to_fill;
		
	}
	
	/**
	 * @param LiveStreamScheduleEvent $object_to_fill
	 * @param array $props_to_skip
	 * @return LiveStreamScheduleEvent|mixed|null
	 * @throws PropelException
	 *
	 * Updates an existing LiveStreamScheduleEvent
	 */
	public function toUpdatableObject($object_to_fill, $props_to_skip = array())
	{
		$object_to_fill = parent ::toUpdatableObject($object_to_fill, $props_to_skip);
		
		/* @var $object_to_fill LiveStreamScheduleEvent */
		
		//For old records update, we only update startDate if it was changed
		// for new record we update the start date as relative to the screenTime

		$this->updatePrePostTimes($object_to_fill);
		//Adjust start time
		if (isset($this -> startDate) || isset($this -> preStartTime) && $object_to_fill->getFromCustomData(LiveStreamScheduleEvent::SCREENING_START_TIME))
		{
			$preStartTime = isset($this -> preStartTime) ? $this -> preStartTime : $object_to_fill -> getPreStartTime();
			$startDate = isset($this -> startDate) ? $this -> startDate : $object_to_fill -> getStartScreenTime();
			$object_to_fill -> setStartDate($startDate - $preStartTime);
		}
		//Adjust end time
		if (isset($this -> endDate) || isset($this -> postEndTime) && $object_to_fill->getFromCustomData(LiveStreamScheduleEvent::SCREENING_END_TIME))
		{
			$postEndTime = isset($this -> postEndTime) ? $this -> postEndTime : $object_to_fill -> getPostEndTime();
			$endDate = isset($this -> endDate) ? $this -> endDate : $object_to_fill -> getEndScreenTime();
			$object_to_fill -> setEndDate($endDate + $postEndTime);
		}
		
		return $object_to_fill;
	}

	/**
	 * @param LiveStreamScheduleEvent $object_to_fill
	 *
	 * If preStart / postEnd entryIds exists, the preStart / postEnd times will be updated in accordance
	 */
	protected function updatePrePostTimes($object_to_fill)
	{
		if ($this->preStartEntryId)
		{
			// the entry exists for sure (as validateForInsert passed)
			$preStartEntry = entryPeer::retrieveByPK($this->preStartEntryId);
			$this->preStartTime = round($preStartEntry->getDuration());
			$object_to_fill->setPreStartTime($this->preStartTime);
		}

		if ($this->postEndEntryId)
		{
			// the entry exists for sure (as validateForInsert passed)
			$postEndEntry = entryPeer::retrieveByPK($this->postEndEntryId);
			$this->postEndTime = round($postEndEntry->getDuration());
			$object_to_fill->setPostEndTime($this->postEndTime);
		}
	}
}
