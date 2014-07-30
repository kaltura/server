<?php
/**
 * @package plugins.cuePoint
 * @subpackage api.objects
 * @abstract
 */
abstract class KalturaCuePoint extends KalturaObject implements IFilterable 
{
	/**
	 * @var string
	 * @filter eq,in
	 * @readonly
	 */
	public $id;
	
	/**
	 * @var KalturaCuePointType
	 * @filter eq,in
	 * @readonly
	 */
	public $cuePointType;
	
	/**
	 * @var KalturaCuePointStatus
	 * @filter eq,in
	 * @readonly
	 */
	public $status;
	
	/**
	 * @var string
	 * @filter eq,in
	 * @insertonly
	 */
	public $entryId;
	
	/**
	 * @var int
	 * @readonly
	 */
	public $partnerId;
	
	/**
	 * @var time
	 * @filter gte,lte,order
	 * @readonly
	 */
	public $createdAt;

	/**
	 * @var time
	 * @filter gte,lte,order
	 * @readonly
	 */
	public $updatedAt;

	/**
	 * @var time
	 * @filter gte,lte,order
	 */
	public $triggeredAt;
	
	/**
	 * @var string
	 * @filter like,mlikeor,mlikeand
	 */
	public $tags;

	/**
	 * Start time in milliseconds
	 * @var int 
	 * @filter gte,lte,order
	 */
	public $startTime;
	
	/**
	 * @var string
	 * @filter eq,in
	 * @readonly
	 */
	public $userId;
	
	/**
	 * @var string
	 */
	public $partnerData;
	
	/**
	 * @var int
	 * @filter eq,in,gte,lte,order
	 */
	public $partnerSortValue;
	
	/**
	 * @var KalturaNullableBoolean
	 * @filter eq
	 */
	public $forceStop;
	
	/**
	 * @var int
	 */
	public $thumbOffset;
	
	/**
	 * @var string
	 * @filter eq,in
	 */
	public $systemName;
	
	private static $map_between_objects = array
	(
		"id",
		"cuePointType" => "type",
		"status",
		"entryId",
		"partnerId",
		"createdAt",
		"updatedAt",
		"tags",
		"startTime",
		"partnerData",
		"partnerSortValue",
		"forceStop",
		"thumbOffset",
		"systemName",
		"triggeredAt",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function getExtraFilters()
	{
		return array();
	}
	
	public function getFilterDocs()
	{
		return array();
	}
	
	public function fromObject($dbCuePoint)
	{
		parent::fromObject($dbCuePoint);
		
		if($dbCuePoint->getKuserId() !== null){
			$dbKuser = kuserPeer::retrieveByPK($dbCuePoint->getKuserId());
			if($dbKuser){
				if (!kConf::hasParam('protect_userid_in_api') || !in_array($dbCuePoint->getPartnerId(), kConf::get('protect_userid_in_api')) || !in_array(kCurrentContext::getCurrentSessionType(), array(kSessionBase::SESSION_TYPE_NONE,kSessionBase::SESSION_TYPE_WIDGET)))
					$this->userId = $dbKuser->getPuserId();
			}
		}
	}
	
	/*
	 * @param string $cuePointId
	 * @throw KalturaAPIException
	 */
	public function validateEntryId($cuePointId = null)
	{	
		$dbEntry = entryPeer::retrieveByPK($this->entryId);
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $this->entryId);
			
		if($cuePointId !== null){ // update
			$dbCuePoint = CuePointPeer::retrieveByPK($cuePointId);
			if(!$dbCuePoint)
				throw new KalturaAPIException(KalturaCuePointErrors::INVALID_OBJECT_ID, $cuePointId);
				
			if($this->entryId !== null && $this->entryId != $dbCuePoint->getEntryId())
				throw new KalturaAPIException(KalturaCuePointErrors::CANNOT_UPDATE_ENTRY_ID);
		}
	}
	
	/*
	 * @param string $cuePointId
	 * @throw KalturaAPIException - when parent annotation doesn't belong to the same entry, or parent annotation
	 * doesn't belong to the same entry
	 */
	public function validateParentId($cuePointId = null)
	{
		if ($this->isNull('parentId'))
			$this->parentId = 0;
			
		if ($this->parentId !== 0)
		{
			$dbParentCuePoint = CuePointPeer::retrieveByPK($this->parentId);
			if (!$dbParentCuePoint)
				throw new KalturaAPIException(KalturaCuePointErrors::PARENT_ANNOTATION_NOT_FOUND, $this->parentId);
			
			if($cuePointId !== null){ // update
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
	
	/*
	 * @param CuePoint $cuePoint
	 * @throw KalturaAPIException
	 */
	public function validateEndTime(CuePoint $cuePoint = null)
	{
		if(is_null($this->startTime) && $cuePoint && $cuePoint->getStartTime())
			$this->startTime = $cuePoint->getStartTime();
			
		if(is_null($this->triggeredAt) && $cuePoint && $cuePoint->getTriggeredAt())
			$this->triggeredAt = $cuePoint->getTriggeredAt();
			
		if($this->startTime)
		{
			if ($this->isNull('endTime') && (!$cuePoint || is_null($cuePoint->getEndTime())))
				$this->endTime = $this->startTime;
				
			if(!is_null($this->endTime) && $this->endTime < $this->startTime)
				throw new KalturaAPIException(KalturaCuePointErrors::END_TIME_CANNOT_BE_LESS_THAN_START_TIME, $this->parentId);
		}
		elseif($this->triggeredAt)
		{
			if ($this->isNull('duration') && (!$cuePoint || is_null($cuePoint->getDuration())))
				$this->duration = 0;
				
			if($this->duration && $this->duration < 0)
				throw new KalturaAPIException(KalturaCuePointErrors::END_TIME_CANNOT_BE_LESS_THAN_START_TIME, $this->parentId);
		}
		else
		{
			if(!$this->isNull('duration') || !$this->isNull('endTime'))
				throw new KalturaAPIException(KalturaCuePointErrors::END_TIME_WITHOUT_START_TIME);
		}
		
		if($cuePoint)
		{
			$dbEntry = entryPeer::retrieveByPK($cuePoint->getEntryId());
		}
		else //add
		{ 
			$dbEntry = entryPeer::retrieveByPK($this->entryId);
		}
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $this->entryId);
		
		if($dbEntry->getLengthInMsecs())
		{
			if($this->endTime && $dbEntry->getLengthInMsecs() < $this->endTime)
				throw new KalturaAPIException(KalturaCuePointErrors::END_TIME_IS_BIGGER_THAN_ENTRY_END_TIME, $this->endTime, $dbEntry->getLengthInMsecs());
				
			if($this->duration && $dbEntry->getLengthInMsecs() < $this->duration)
				throw new KalturaAPIException(KalturaCuePointErrors::END_TIME_IS_BIGGER_THAN_ENTRY_END_TIME, $this->duration, $dbEntry->getLengthInMsecs());
		}	
	}
	
	/*
	 * @param string $cuePointId
	 * @throw KalturaAPIException
	 */
	public function validateStartTime($cuePointId = null)
	{	
		if ($this->startTime === null)
			$this->startTime = 0;
		
		if($this->startTime < 0)
			throw new KalturaAPIException(KalturaCuePointErrors::START_TIME_CANNOT_BE_LESS_THAN_0);
		
		if($cuePointId !== null){ //update
			$dbCuePoint = CuePointPeer::retrieveByPK($cuePointId);
			if(!$dbCuePoint)
				throw new KalturaAPIException(KalturaCuePointErrors::INVALID_OBJECT_ID, $cuePointId);
				
			$dbEntry = entryPeer::retrieveByPK($dbCuePoint->getEntryId());
		}
		else //add
		{ 
			$dbEntry = entryPeer::retrieveByPK($this->entryId);
			if (!$dbEntry)
				throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $this->entryId);
		}
		
		if($dbEntry->getLengthInMsecs() && $dbEntry->getLengthInMsecs() < $this->startTime)
			throw new KalturaAPIException(KalturaCuePointErrors::START_TIME_IS_BIGGER_THAN_ENTRY_END_TIME, $this->startTime, $dbEntry->getLengthInMsecs());
	}
	
	public function validateForInsert($propertiesToSkip = array())
	{
		$propertiesToSkip[] = 'cuePointType';
		parent::validateForInsert($propertiesToSkip);
		
		$this->validatePropertyNotNull("entryId");
		$this->validateEntryId();
		$this->validateStartTime();
				
		if($this->tags != null)
			$this->validatePropertyMaxLength("tags", CuePointPeer::MAX_TAGS_LENGTH);
	}
	
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		if($this->tags !== null)
			$this->validatePropertyMaxLength("tags", CuePointPeer::MAX_TAGS_LENGTH);
		
		if($this->entryId !== null)
			$this->validateEntryId($sourceObject->getId());
		
		if($this->startTime !== null)
			$this->validateStartTime($sourceObject->getId());
					
		$propertiesToSkip[] = 'cuePointType';
		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}

	/**
	 * @param int $type
	 * @return KalturaCuePoint
	 */
	public static function getInstance($type)
	{
		return KalturaPluginManager::loadObject('KalturaCuePoint', $type);
	}
}
