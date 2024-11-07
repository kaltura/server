<?php
/**
 * @package plugins.annotation
 * @subpackage api.objects
 */
class KalturaAnnotation extends KalturaCuePoint
{
	/**
	 * @var string
	 * @filter eq,in
	 * @insertonly
	 */
	public $parentId;
	
	/**
	 * @var string
	 * @filter like,mlikeor,mlikeand
	 */
	public $text;
	
	/**
	 * End time in milliseconds
	 * @var int 
	 * @filter gte,lte,order
	 */
	public $endTime;
	
	/**
	 * Duration in milliseconds
	 * @var int 
	 * @filter gte,lte,order
	 * @readonly
	 */
	public $duration;
	
	/**
	 * Depth in the tree
	 * @var int
	 * @todo add filters and order after adding this field to the sphinx 
	 * @readonly
	 */
	public $depth;
	
	/**
	 * Number of all descendants
	 * @var int
	 * @todo add filters and order after adding this field to the sphinx 
	 * @readonly
	 */
	public $childrenCount;
	
	/**
	 * Number of children, first generation only.
	 * @var int
	 * @todo add filters and order after adding this field to the sphinx 
	 * @readonly
	 */
	public $directChildrenCount;
	
	/**
	 * Is the annotation public.
	 * @var KalturaNullableBoolean
	 * @filter eq
	 */
	public $isPublic;
	
	/**
	 * Should the cue point get indexed on the entry.
	 * @var KalturaNullableBoolean
	 */
	public $searchableOnEntry;
	
	

	public function __construct()
	{
		$this->cuePointType = AnnotationPlugin::getApiValue(AnnotationCuePointType::ANNOTATION);
	}
	
	private static $map_between_objects = array
	(
		"parentId",
		"text",
		"endTime",
		"duration",
		"depth",
		"childrenCount",
		"directChildrenCount",
	    "isPublic",
	    "searchableOnEntry",
	);
	
	/* (non-PHPdoc)
	 * @see KalturaCuePoint::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toInsertableObject()
	 */
	public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
	{
		if(is_null($object_to_fill))
			$object_to_fill = new Annotation();

		$retObj = parent::toInsertableObject($object_to_fill, $props_to_skip);

		if ($retObj->getParentId())
		{
			$dbParentCuePoint = null;
			if (!$retObj->getStartTime())
			{
				$dbParentCuePoint = CuePointPeer::retrieveByPK($retObj->getParentId());
				if ($dbParentCuePoint)
				{
					$retObj->setStartTime($dbParentCuePoint->getStartTime());
				}
			}
			if (!$retObj->getEndTime())
			{
				$dbParentCuePoint = $dbParentCuePoint ? $dbParentCuePoint : CuePointPeer::retrieveByPK($retObj->getParentId());
				if ($dbParentCuePoint)
				{
					$retObj->setEndTime($dbParentCuePoint->getEndTime());
				}
			}
		}

		return $retObj;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaCuePoint::validateForInsert()
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		parent::validateForInsert($propertiesToSkip);
		
		if ($this->text != null)
		{
			$this->validatePropertyMaxLength("text", CuePointPeer::MAX_TEXT_LENGTH);
		}
		$this->validateParentId();
		if ($this->parentId)
		{
			$this->validateEndTimeAndDuration($this->endTime, $this->duration);
		}

		if (!isset($this->isPublic) || is_null($this->isPublic))
		{
			$this->isPublic = false;
		}

		if (!isset($this->searchableOnEntry) || is_null($this->searchableOnEntry))
		{
			$this->searchableOnEntry=true;
		}
	}
	
	/* (non-PHPdoc)
	 * @see KalturaCuePoint::validateForUpdate()
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		/* @var $sourceObject Annotation */
		if (!$this->isNull('text'))
		{
			$this->validatePropertyMaxLength("text", CuePointPeer::MAX_TEXT_LENGTH);
		}

		if ($this->parentId)
		{
			$this->validateParentId($sourceObject->getId());
		}

		if ($this->parentId || $sourceObject->getParentId())
		{
			$this->validateEndTimeAndDuration($this->endTime, $this->duration, $sourceObject);
		}
			
		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}
	
	public function updateEndTimeAndDuration($cuePoint)
	{
		if ($this->isNull('endTime') && (!$cuePoint || is_null($cuePoint->getEndTime())))
		{
			$this->endTime = $this->startTime;
		}
		if ($this->triggeredAt && $this->isNull('duration') && (!$cuePoint || is_null($cuePoint->getDuration())))
		{
			$this->duration = 0;
		}
	}

	/*
	 * @param string $cuePointId
	 * @throw KalturaAPIException - when parent annotation doesn't belong to the same entry
	 */
	public function validateParentId($cuePointId = null)
	{
		//Backward compatibility patch
		if ($this->isNull('parentId'))
			$this->parentId = 0;

		if ($this->parentId)
		{
			$dbParentCuePoint = CuePointPeer::retrieveByPK($this->parentId);
			if (!$dbParentCuePoint)
				throw new KalturaAPIException(KalturaCuePointErrors::PARENT_ANNOTATION_NOT_FOUND, $this->parentId);

			if($cuePointId !== null)
			{// update
				$dbCuePoint = CuePointPeer::retrieveByPK($cuePointId);
				if(!$dbCuePoint)
					throw new KalturaAPIException(KalturaCuePointErrors::INVALID_OBJECT_ID, $cuePointId);

				if($dbCuePoint->isDescendant($this->parentId))
					throw new KalturaAPIException(KalturaCuePointErrors::PARENT_ANNOTATION_IS_DESCENDANT, $this->parentId, $dbCuePoint->getId());

				if ($dbParentCuePoint->getEntryId() != $dbCuePoint->getEntryId())
					throw new KalturaAPIException(KalturaCuePointErrors::PARENT_ANNOTATION_DO_NOT_BELONG_TO_THE_SAME_ENTRY);
			}
			else
			{
				if ($dbParentCuePoint->getEntryId() != $this->entryId)
					throw new KalturaAPIException(KalturaCuePointErrors::PARENT_ANNOTATION_DO_NOT_BELONG_TO_THE_SAME_ENTRY);
			}
		}
	}

	protected function validateEntryEntitlement(entry $dbEntry, $tags)
	{
		if (!kEntitlementUtils::isEntryEntitled($dbEntry) && str_contains($tags, 'hotspots'))
		{
			KalturaLog::debug("User is not allowed to edit " . get_class($this) . " on entry [$this->entryId]");
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID);
		}
	}
}
