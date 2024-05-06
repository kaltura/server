<?php
/**
 * @package plugins.schedule
 * @subpackage api.objects
 * @abstract
 * @relatedService ScheduleEventService
 */
abstract class KalturaScheduleEvent extends KalturaObject implements IRelatedFilterable, IApiObjectFactory
{
	/**
	 * Auto-generated unique identifier
	 * @var int
	 * @readonly
	 * @filter eq,in,notin
	 */
	public $id;

	/**
	 * @var int
	 * @readonly
	 */
	public $partnerId;

	/**
	 * @var int
	 * @readonly
	 * @filter eq,in,notin
	 */
	public $parentId;

	/**
	 * Defines a short summary or subject for the event
	 * @var string
	 * @filter order
	 * @minLength 1
	 * @maxLength 256
	 */
	public $summary;

	/**
	 * @var string
	 */
	public $description;

	/**
	 * @var KalturaScheduleEventStatus
	 * @readonly
	 * @filter eq,in
	 */
	public $status;

	/**
	 * @var time
	 * @filter gte,lte,order
	 */
	public $startDate;

	/**
	 * @var time
	 * @filter gte,lte,order
	 */
	public $endDate;

	/**
	 * @var string
	 * @filter eq,in
	 */
	public $referenceId;
	
	
	/**
	 * Contains the Id of the event that influences the timing of this event and the offset of time.
	 * @var KalturaLinkedScheduleEvent
	 */
	public $linkedTo;
	
	/**
	 * An array of Schedule Event Ids that their start time depends on the end of the current.
	 * @var string
	 */
	public $linkedBy;
	
	/**
	 * @var KalturaScheduleEventClassificationType
	 */
	public $classificationType;

	/**
	 * Specifies the global position for the activity
	 * @var float
	 * @minValue 0
	 */
	public $geoLatitude;

	/**
	 * Specifies the global position for the activity
	 * @var float
	 * @minValue 0
	 */
	public $geoLongitude;

	/**
	 * Defines the intended venue for the activity
	 * @var string
	 */
	public $location;

	/**
	 * @var string
	 */
	public $organizer;

	/**
	 * @var string
	 * @filter eq,in
	 */
	public $ownerId;

	/**
	 * The value for the priority field.
	 * @var int
	 * @filter eq,in,gte,lte,order
	 * @minValue 0
	 * @maxValue 9
	 */
	public $priority;

	/**
	 * Defines the revision sequence number.
	 * @var int
	 * @minValue 0
	 */
	public $sequence;

	/**
	 * @var KalturaScheduleEventRecurrenceType
	 * @filter eq,in
	 */
	public $recurrenceType;

	/**
	 * Duration in seconds
	 * @var int
	 * @minValue 0
	 */
	public $duration;

	/**
	 * Used to represent contact information or alternately a reference to contact information.
	 * @var string
	 */
	public $contact;

	/**
	 * Specifies non-processing information intended to provide a comment to the calendar user.
	 * @var string
	 */
	public $comment;

	/**
	 * @var string
	 * @filter like,mlikeor,mlikeand
	 */
	public $tags;

	/**
	 * Creation date as Unix timestamp (In seconds)
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;

	/**
	 * Last update as Unix timestamp (In seconds)
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $updatedAt;

	/**
	 * @var KalturaScheduleEventRecurrence
	 */
	public $recurrence;
	
	/*
	 * Mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array 
	 (	
	 	'id',
		'partnerId',
	 	'parentId',
		'summary',
		'description',
		'status',
		'startDate',
		'endDate',
		'referenceId',
		'classificationType',
		'geoLatitude' => 'GeoLat',
		'geoLongitude' => 'GeoLong',
		'location',
		'organizer',
		'ownerId',
		'priority',
		'sequence',
		'recurrenceType',
		'duration',
		'contact',
		'comment',
		'tags',
		'createdAt',
		'updatedAt',
		'recurrence',
		'linkedBy',
		'linkedTo',
	 );
		 
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
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
	
	/**
	 * @return ScheduleEventType
	 */
	abstract public function getScheduleEventType();
	
	public function validate($startDate, $endDate)
	{
		if($this->recurrenceType === ScheduleEventRecurrenceType::RECURRENCE)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_ENUM_VALUE, $this->recurrenceType, 'recurrenceType', 'KalturaScheduleEventRecurrenceType');
		}
		
		$this->validateDates($startDate, $endDate);
	}
	
	protected function validateDates($startDate, $endDate)
	{
		if($startDate > $endDate)
		{
			throw new KalturaAPIException(KalturaScheduleErrors::INVALID_SCHEDULE_END_BEFORE_START, $startDate, $endDate);
		}
		
		$maxDuration = $this->getScheduleEventMaxDuration();
		if(($endDate - $startDate) > $maxDuration)
		{
			throw new KalturaAPIException(KalturaScheduleErrors::MAX_SCHEDULE_DURATION_REACHED, $maxDuration);
		}
	}

	/***
	 * @param $targetRecurrenceType
	 * @param $sourceRecurrenceType
	 * @throws KalturaScheduleErrors::INVALID_SCHEDULE_EVENT_TYPE_TO_UPDATE
	 */
	public function validateScheduleEventType($targetRecurrenceType, $sourceRecurrenceType)
	{
		if (!is_null($targetRecurrenceType))
		{
			if ($sourceRecurrenceType === ScheduleEventRecurrenceType::RECURRENCE && $targetRecurrenceType != ScheduleEventRecurrenceType::RECURRENCE)
			{
				throw new KalturaAPIException(KalturaScheduleErrors::INVALID_SCHEDULE_EVENT_TYPE_TO_UPDATE, $sourceRecurrenceType, $targetRecurrenceType);
			}

			if ($sourceRecurrenceType === ScheduleEventRecurrenceType::RECURRING && $targetRecurrenceType === ScheduleEventRecurrenceType::RECURRENCE)
			{
				throw new KalturaAPIException(KalturaScheduleErrors::INVALID_SCHEDULE_EVENT_TYPE_TO_UPDATE, $sourceRecurrenceType, $targetRecurrenceType);
			}

			if ($sourceRecurrenceType === ScheduleEventRecurrenceType::NONE && $targetRecurrenceType === ScheduleEventRecurrenceType::RECURRENCE)
			{
				throw new KalturaAPIException(KalturaScheduleErrors::INVALID_SCHEDULE_EVENT_TYPE_TO_UPDATE, $sourceRecurrenceType, $targetRecurrenceType);
			}

			if ($sourceRecurrenceType === ScheduleEventRecurrenceType::NONE && $targetRecurrenceType === ScheduleEventRecurrenceType::NONE && !is_null($this->recurrence))
			{
				throw new KalturaAPIException("Can't update single schedule event with recurring data when recurrenceType is not \"RECURRING\".");
			}
		}
		else
		{
			if ($sourceRecurrenceType === ScheduleEventRecurrenceType::NONE && !is_null($this->recurrence))
			{
				throw new KalturaAPIException("Can't update single schedule event with recurring data when recurrenceType is not \"RECURRING\".");
			}
		}
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForInsert($propertiesToSkip)
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull('recurrenceType');
		$this->validatePropertyNotNull('summary');
		if (is_null($this->startDate) && is_null($this->linkedTo))
		{
			throw new KalturaAPIException(KalturaScheduleErrors::START_TIME_AND_LINKED_TO_NOT_SET);
		}
		if (!is_null($this->startDate) && !is_null($this->linkedTo))
		{
			throw new KalturaAPIException(KalturaScheduleErrors::START_TIME_AND_LINKED_TO_CONFLICT);
		}
		if (!is_null($this->linkedTo) && $this->recurrenceType != KalturaScheduleEventRecurrenceType::NONE)
		{
			throw new KalturaAPIException(KalturaScheduleErrors::RECURRENCE_LINKED_EVENT_CONFLICT);
		}

		if($this->recurrenceType == KalturaScheduleEventRecurrenceType::RECURRING)
		{
			$this->validateRecurringEventForInsert();
		}

		if (!is_null($this->startDate))
		{
			$this->validatePropertyNotNull('endDate');
		}
		if (!is_null($this->linkedTo))
		{
			// retrieve relevant event's start time and calculate the current event start and end time
			$this->validatePropertyNotNull('duration');
			$this->setTimingFromLinkedToEvent($this->linkedTo->getEventId(), $this->duration);
		}
		
		$this->validate($this->startDate, $this->endDate);
		$maxSingleEventDuration = $this->getSingleScheduleEventMaxDuration();
		if (($this->endDate - $this->startDate) > $maxSingleEventDuration)
			throw new KalturaAPIException(KalturaScheduleErrors::MAX_SCHEDULE_DURATION_REACHED, $maxSingleEventDuration);

		parent::validateForInsert($propertiesToSkip);
	}
	
	protected function setTimingFromLinkedToEvent($linkedToEventId, $duration)
	{
		if (!is_null($linkedToEventId)) //if it is null we are unlinking
		{
			$linkedToEvent = ScheduleEventPeer::retrieveByPK($linkedToEventId);
			/* @var $linkedToEvent BaseScheduleEvent */
			if ($linkedToEvent)
			{
				$this->startDate = strtotime($linkedToEvent->getEndDate()) + $this->linkedTo->getOffset();
				$this->endDate = $this->startDate + $duration;
			}
			else
			{
				throw new KalturaAPIException(KalturaScheduleErrors::LINKED_TO_EVENT_NOT_FOUND_OR_NOT_ACCESSIBLE);
			}
		}
	}
	
	protected function getSingleScheduleEventMaxDuration()
	{
		return SchedulePlugin::getSingleScheduleEventMaxDuration();
	}
	
	protected function getScheduleEventMaxDuration()
	{
		return SchedulePlugin::getScheduleEventmaxDuration();
	}

	private function validateRecurringEventForInsert()
	{
		if (($this->isNull('duration') || $this->duration < 1) && !$this->isNull('endDate'))
		{
			$this->duration = $this->endDate - $this->startDate;
		}
		else if($this->isNull('endDate') && !$this->isNull('duration') && $this->duration >0)
		{
			$this->endDate = $this->startDate + $this->duration;
		}

		$this->validatePropertyNotNull('recurrence');
		$this->validatePropertyNotNull('duration');

	}

	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUpdate($sourceObject, $propertiesToSkip)
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		if ($this->endDate instanceof KalturaNullField)
		{
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, $this->getFormattedPropertyNameWithClassName('endDate'));
		}

		/* @var $sourceObject ScheduleEvent */
		$startDate = $sourceObject->getStartDate(null);
		$endDate = $sourceObject->getEndDate(null);

		if ($this->startDate)
			$startDate = $this->startDate;
		if ($this->endDate)
			$endDate = $this->endDate;
		
		if (is_null($this->id))
		{
			$this->id = $sourceObject->getId();
		}
		
		$linkedTo = $sourceObject->getLinkedTo();
		if ($this->linkedTo && $this->linkedTo->getEventId() && $this->startDate)
		{
			throw new KalturaAPIException(KalturaScheduleErrors::START_TIME_AND_LINKED_TO_CONFLICT);
		}
		elseif ($this->linkedTo)
		{
			$duration = is_null($this->duration) ? $sourceObject->getDuration() : $this->duration;
			$this->setTimingFromLinkedToEvent($this->linkedTo->getEventId(), $duration);
			if ($this->startDate && $this->endDate)
			{
				$startDate = $this->startDate;
				$endDate = $this->endDate;
			}
		}
		if ((!is_null($this->linkedTo) || $sourceObject->getLinkedTo()) && $this->recurrenceType != KalturaScheduleEventRecurrenceType::NONE)
		{
			throw new KalturaAPIException(KalturaScheduleErrors::RECURRENCE_LINKED_EVENT_CONFLICT);
		}
		
		$this->validateScheduleEventType($this->recurrenceType, $sourceObject->getRecurrenceType());
		if (is_null($this->recurrenceType))
		{
			$this->recurrenceType = $sourceObject->getRecurrenceType();
		}

		if (is_null($this->linkedBy) && !is_null($sourceObject->getLinkedBy()))
		{
			$this->linkedBy = $sourceObject->getLinkedBy();
		}
		
		$this->validate($startDate, $endDate);

		if ($this->isNull('sequence') || $this->sequence <= $sourceObject->getSequence())
		{
			$sourceObject->incrementSequence();
		}

		$maxSingleEventDuration = $this->getSingleScheduleEventMaxDuration();

		if (!$this->isNull('duration'))
		{
			if (!$this->isNull('endDate'))
			{
				if (is_null($this->linkedTo))
				{
					if (($startDate + $this->duration) != $this->endDate)
					{
						throw new KalturaAPIException(KalturaScheduleErrors::MAX_SCHEDULE_DURATION_MUST_MATCH_END_TIME);
					}
				}
			}

			if (!is_null($this->recurrenceType) && $this->recurrenceType != ScheduleEventRecurrenceType::RECURRING)
			{
				$this->endDate = $startDate + $this->duration;
				$this->startDate = $startDate;
				$this->duration = null;
			}

			// validate single event duration in recurring event or in single event is 24 hours at most
			if ($this->recurrenceType == KalturaScheduleEventRecurrenceType::RECURRING)
			{
				if ($this->duration > $maxSingleEventDuration)
				{
					throw new KalturaAPIException(KalturaScheduleErrors::MAX_SCHEDULE_DURATION_REACHED, $maxSingleEventDuration);
				}
			}
			elseif ($this->recurrenceType == KalturaScheduleEventRecurrenceType::NONE)
			{
				if (($this->endDate - $this->startDate) > $maxSingleEventDuration)
				{
					throw new KalturaAPIException(KalturaScheduleErrors::MAX_SCHEDULE_DURATION_REACHED, $maxSingleEventDuration);
				}
			}
		}
		else
		{
			if ($endDate - $startDate > $maxSingleEventDuration)
			{
				throw new KalturaAPIException(KalturaScheduleErrors::MAX_SCHEDULE_DURATION_REACHED, $maxSingleEventDuration);
			}
		}

		// we can't update a recurrence object and set both until and count so if one of them is going to be updated we set remove the other one.
		if(($this->recurrence && !is_null($this->recurrence->until)) && !is_null($sourceObject->getRecurrence()) &&  !is_null($sourceObject->getRecurrence()->getCount()))
		{
			$this->recurrence->count = null;
		}
		if(($this->recurrence && !is_null($this->recurrence->count)) && !is_null($sourceObject->getRecurrence()) &&  !is_null($sourceObject->getRecurrence()->getUntil()))
		{
			$this->recurrence->until = null;
		}

		//if we are updating an event from recurring to single event we need to remove the duration on the object since we calculate it from scratch according to start and end date
		if (!is_null($this->recurrenceType) && $this->recurrenceType == ScheduleEventRecurrenceType::NONE && $sourceObject->getRecurrenceType() == ScheduleEventRecurrenceType::RECURRING)
		{
			$this->duration = null;
		}

		parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::doFromObject()
	 */
	protected function doFromObject($srcObj, KalturaDetachedResponseProfile $responseProfile= null)
	{
		/* @var $srcObj ScheduleEvent */
		if($srcObj->getParentId())
		{
			$attributes = $this->getMapBetweenObjects();
			$skipAttributes = array();
			
			foreach($attributes as $apiPropName => $dbPropName)
			{
				if (is_numeric($apiPropName))
					$apiPropName = $dbPropName;
					
				if(!is_null($this->$apiPropName)){
					$skipAttributes[] = $apiPropName;
				}
			}
			if(count($skipAttributes) < count($attributes))
			{
				$parentResponseProfile = new KalturaDetachedResponseProfile();
				if(is_null($responseProfile))
				{
					$parentResponseProfile->type = KalturaResponseProfileType::EXCLUDE_FIELDS;
					$parentResponseProfile->fields = implode(',', $skipAttributes);
				}
				elseif($responseProfile->type == KalturaResponseProfileType::EXCLUDE_FIELDS)
				{
					$parentResponseProfile->type = KalturaResponseProfileType::EXCLUDE_FIELDS;
					$parentResponseProfile->fields = implode(',', array_merge(explode(',', $responseProfile->fields), $skipAttributes));
				}
				elseif($responseProfile->type == KalturaResponseProfileType::INCLUDE_FIELDS)
				{
					$parentResponseProfile->type = KalturaResponseProfileType::INCLUDE_FIELDS;
					$parentResponseProfile->fields = implode(',', array_diff(explode(',', $responseProfile->fields), $skipAttributes));
				}
				
				$parentObj = ScheduleEventPeer::retrieveByPK($srcObj->getParentId());
				$this->fromObject($parentObj, $parentResponseProfile);
			}
		}
		
		parent::doFromObject($srcObj, $responseProfile);
	}
		
	/*
	 * (non-PHPdoc)
	 * @see IApiObjectFactory::getInstance($sourceObject, KalturaDetachedResponseProfile $responseProfile)
	 */
	public static function getInstance($sourceObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$object = null;
		switch($sourceObject->getType())
		{
			case ScheduleEventType::RECORD:
				$object = new KalturaRecordScheduleEvent();
				break;
			
			case ScheduleEventType::LIVE_STREAM:
				$object = new KalturaLiveStreamScheduleEvent();
				break;

			case ScheduleEventType::BLACKOUT:
				$object = new KalturaBlackoutScheduleEvent();
				break;

			case ScheduleEventType::MEETING:
				$object = new KalturaMeetingScheduleEvent();
				break;

			case ScheduleEventType::LIVE_REDIRECT:
				$object = new KalturaLiveRedirectScheduleEvent();
				break;

			case ScheduleEventType::VOD:
				$object = new KalturaVodScheduleEvent();
				break;
				
			default:
				$object = KalturaPluginManager::loadObject('KalturaScheduleEvent', $sourceObject->getType());
				if(!$object)
				{
					return null;
				}
		}
		
		/* @var $object KalturaScheduleEvent */
		$object->fromObject($sourceObject, $responseProfile);
		return $object;
	}
}
