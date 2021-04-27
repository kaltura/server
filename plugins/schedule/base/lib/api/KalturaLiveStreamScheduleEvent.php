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
	}
	
	public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
	{
		$object_to_fill = parent ::toInsertableObject($object_to_fill, $props_to_skip);
		
		/* @var $object_to_fill LiveStreamScheduleEvent */
		if(is_null($object_to_fill))
		{
			$object_to_fill = new LiveStreamScheduleEvent();
		}
		
		$object_to_fill->setPreStartTime($this->preStartTime);
		$object_to_fill->setPostEndTime($this->postEndTime);
		$object_to_fill->setStartScreenTime($this->startDate);
		$object_to_fill->setEndScreenTime($this->endDate);
		$object_to_fill->setStartDate($this->startDate - $this->preStartTime);
		$object_to_fill->setEndDate($this->endDate + $this->postEndTime);
		
		
		return $object_to_fill;
		
	}
	
	public function toUpdatableObject($object_to_fill, $props_to_skip = array())
	{
		$class = get_class($this);
		
		/* @var $object_to_fill LiveStreamScheduleEvent */
		if(is_null($object_to_fill))
		{
			KalturaLog::err("No object supplied for type [$class]");
			return null;
		}
		//Adjust start time
		if (isset($this->preStartTime) && isset($this->startDate))
		{
			$object_to_fill->setPreStartTime($this->preStartTime);
			$object_to_fill->setStartScreenTime($this->startDate);
			$object_to_fill->setStartDate($this->startDate - $this->preStartTime);
		}
		elseif (isset($this -> preStartTime))
		{
			$object_to_fill->setPreStartTime($this->preStartTime);
			$object_to_fill->setStartDate($object_to_fill->getStartScreenTime() - $this->preStartTime);
		}
		elseif (isset($this->startDate))
		{
			$object_to_fill->setStartScreenTime($this->startDate);
			$object_to_fill->setStartDate($this->startDate - $object_to_fill->getPreStartTime());
		}
		
		//Adjust end time
		if (isset($this->postEndTime) && isset($this->endDate))
		{
			$object_to_fill->setPostEndTime($this->postEndTime);
			$object_to_fill->setEndScreenTime($this->endDate);
			$object_to_fill->setEndDate($this->endDate + $this->postEndTime);
		}
		elseif (isset($this -> postEndTime))
		{
			$object_to_fill->setPostEndTime($this->postEndTime);
			$object_to_fill->setEndDate($object_to_fill->getEndScreenTime() + $this->postEndTime);
		}
		elseif (isset($this->endDate))
		{
			$object_to_fill->setEndScreenTime($this->endDate);
			$object_to_fill->setEndDate($this->endDate + $object_to_fill->getPostEndTime());
		}
		
		return $object_to_fill;
	}
}