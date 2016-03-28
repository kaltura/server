<?php
/**
 * @package plugins.schedule
 * @subpackage api.objects
 * @abstract
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
	 * @readonly
	 * @filter eq,in
	 */
	public $organizerUserId;

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
	 * @var KalturaScheduleEventRecuranceType
	 * @filter eq,in
	 * @insertonly
	 */
	public $recuranceType;

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
	 * @var KalturaScheduleEventRecuranceArray
	 */
	public $recurances;
	
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
		'geoLatitude',
		'geoLongitude',
		'location',
		'organizerUserId' => 'organizerPuserId',
		'priority',
		'sequence',
		'recuranceType',
		'duration',
		'contact',
		'comment',
		'tags',
		'createdAt',
		'updatedAt',
		'recurances',
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
	
	public function validate($startDate, $endDate)
	{
		if($this->recuranceType === ScheduleEventRecuranceType::RECURRENCE)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_ENUM_VALUE, $this->recuranceType, 'recuranceType', 'KalturaScheduleEventRecuranceType');
		}
		
		if($startDate > $endDate)
		{
			throw new KalturaAPIException(KalturaScheduleErrors::INVALID_SCHEDULE_END_BEFORE_START, $startDate, $endDate);
		}
		
		$maxDuration = SchedulePlugin::getScheduleEventmaxDuration();
		if(($endDate - $startDate) > $maxDuration)
		{
			throw new KalturaAPIException(KalturaScheduleErrors::MAX_SCHEDULE_DURATION_REACHED, $maxDuration);
		}
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForInsert($propertiesToSkip)
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull('recuranceType');
		$this->validatePropertyNotNull('summary');
		$this->validatePropertyNotNull('startDate');
		$this->validatePropertyNotNull('endDate');
		$this->validate($this->startDate, $this->endDate);
		
		parent::validateForInsert($propertiesToSkip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUpdate($sourceObject, $propertiesToSkip)
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		if($this->endDate instanceof KalturaNullField)
		{
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, $this->getFormattedPropertyNameWithClassName('endDate'));
		}
		
		/* @var $sourceObject ScheduleEvent */
		$startDate = $sourceObject->getStartDate(null);
		$endDate = $sourceObject->getEndDate(null);
		
		if($this->startDate)
			$startDate = $this->startDate;
		if($this->endDate)
			$endDate = $this->endDate;
			
		$this->validate($startDate, $endDate);

		if($this->isNull('sequence') || $this->sequence <= $sourceObject->getSequence())
		{
			$sourceObject->incrementSequence();
		}
		
		if(!$this->isNull('duration'))
		{
			if(!$this->isNull('endDate'))
			{
				if(($startDate + $this->duration) != $this->endDate)
				{
					throw new KalturaAPIException(KalturaScheduleErrors::MAX_SCHEDULE_DURATION_MUST_MATCH_END_TIME);
				}
			}
			else
			{
				$this->endDate = $startDate + $this->duration;
				$this->duration = null;
			}
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
				if(is_null($responseProfile))
				{
					$responseProfile = new KalturaDetachedResponseProfile();
					$responseProfile->type = KalturaResponseProfileType::EXCLUDE_FIELDS;
					$responseProfile->fields = implode(',', $skipAttributes);
				}
				elseif($responseProfile->type == KalturaResponseProfileType::EXCLUDE_FIELDS)
				{
					$responseProfile->fields = implode(',', array_intersect(explode(',', $responseProfile->fields), $skipAttributes));
				}
				elseif($responseProfile->type == KalturaResponseProfileType::INCLUDE_FIELDS)
				{
					$responseProfile->fields = implode(',', array_diff(explode(',', $responseProfile->fields), $skipAttributes));
				}
				
				$parentObj = ScheduleEventPeer::retrieveByPK($srcObj->getParentId());
				$this->fromObject($parentObj, $responseProfile);
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