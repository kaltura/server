<?php
class KalturaAnnotation extends KalturaObject implements IFilterable 
{
	/**
	 * @var int
	 * @filter eq
	 * @readonly
	 */
	public $id;
	
	/**
	 * @var string
	 * @filter eq
	 * 
	 */
	public $entryId;
	
	/**
	 * @var string
	 * @filter eq,in
	 */
	public $parentId;
	
	/**
	 * @var int
	 * @filter gte,lte,order
	 * @readonly
	 */
	public $createdAt;

	/**
	 * @var int
	 * @filter gte,lte,order
	 * @readonly
	 */
	public $updatedAt;
	
	/**
	 * @var string
	 */
	public $text;
	
	/**
	 * @var string
	 */
	public $tags;
	

	/**
	 * @var int 
	 */
	public $startTime;
	
	/**
	 * @var int 
	 */
	public $endTime;
	
	/**
	 * @var string
	 * @filter eq,in
	 * @readonly
	 */
	public $userId;

	
	
	private static $map_between_objects = array
	(
		"id",
		"parentId",
		"entryId",
		"createdAt",
		"updatedAt",
		"text",
		"tags",
		"startTime",
		"endTime",
		"userId" => "puserId",
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
	
	/*
	 * @param KalturaAnnotation $annotation
	 * @param string $annotationId
	 * @throw KalturaAPIException - when parent annotation doesn't belong to the same entry, or parent annotation
	 * doesn't belong to the same entry
	 */
	public function validateParentId(KalturaAnnotation $annotation, $annotationId = null)
	{
		if ($annotation->parentId === null || $annotation->parentId === "")
		{
			$annotation->parentId = 0;
		}
			
		if ($annotation->parentId !== 0)
		{
			$dbParentAnnotation = AnnotationPeer::retrieveByPK($annotation->parentId);
			if (!$dbParentAnnotation)
				throw new KalturaAPIException(KalturaAnnotationErrors::PARENT_ANNOTATION_NOT_FOUND, $annotation->parentId);
			
			if($annotationId !== null){ // update
				$dbAnnotation = AnnotationPeer::retrieveByPK($annotationId);
				if(!$dbAnnotation)
					throw new KalturaAPIException(KalturaAnnotationErrors::INVALID_OBJECT_ID, $annotationId);
				 
				if($dbAnnotation->isOffspring($annotation->parentId))
					throw new KalturaAPIException(KalturaAnnotationErrors::PARENT_ANNOTATION_IS_OFFSPRING, $annotation->parentId, $dbAnnotation->getId());
			}
			
			if ($dbParentAnnotation->getEntryId() != $annotation->entryId)
				throw new KalturaAPIException(KalturaAnnotationErrors::PARENT_ANNOTATION_DO_NOT_BELONG_TO_THE_SAME_ENTRY);
		}
	}
	
	/*
	 * @param KalturaAnnotation $annotation
	 * @param string $annotationId
	 * @throw KalturaAPIException
	 */
	public function validateEntryId(KalturaAnnotation $annotation, $annotationId = null)
	{	
		$dbEntry = entryPeer::retrieveByPK($annotation->entryId);
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $annotation->entryId);
			
		if($annotationId !== null){ // update
			$dbAnnotation = AnnotationPeer::retrieveByPK($annotationId);
			if(!$dbAnnotation)
				throw new KalturaAPIException(KalturaAnnotationErrors::INVALID_OBJECT_ID, $annotationId);
				
			if($annotation->entryId !== null && $annotation->entryId != $dbAnnotation->getEntryId())
				throw new KalturaAPIException(KalturaAnnotationErrors::CANNOT_UPDATE_ENTRY_ID);
		}
	}
	
	/*
	 * @param KalturaAnnotation $annotation
	 * @param string $annotationId
	 * @throw KalturaAPIException
	 */
	public function validateEndTime(KalturaAnnotation $annotation, $annotationId = null)
	{
		if(($annotation->startTime === null) && ($annotation->endTime !== null))
				throw new KalturaAPIException(KalturaAnnotationErrors::END_TIME_WITHOUT_START_TIME);
		
		if ($annotation->endTime === null)
			$annotation->endTime = $annotation->startTime;
			
		if($annotation->endTime < $annotation->startTime)
			throw new KalturaAPIException(KalturaAnnotationErrors::END_TIME_CANNOT_BE_LESS_THEN_START_TIME, $annotation->parentId);
		
		if($annotationId !== null){ //update
			$dbAnnotation = AnnotationPeer::retrieveByPK($annotationId);
			if(!$dbAnnotation)
				throw new KalturaAPIException(KalturaAnnotationErrors::INVALID_OBJECT_ID, $annotationId);
				
			$dbEntry = entryPeer::retrieveByPK($dbAnnotation->getEntryId());
		}
		else //add
		{ 
			$dbEntry = entryPeer::retrieveByPK($annotation->entryId);
			if (!$dbEntry)
				throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND,$annotation->entryId);
		}
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $annotation->entryId);
		
		if($dbEntry->getLengthInMsecs() < $annotation->endTime)
			throw new KalturaAPIException(KalturaAnnotationErrors::END_TIME_IS_BIGGER_THAN_ENTRY_END_TIME, $annotation->endTime, $dbEntry->getLengthInMsecs());	
	}
	
	/*
	 * @param KalturaAnnotation $annotation
	 * @param string $annotationId
	 * @throw KalturaAPIException
	 */
	public function validateStartTime(KalturaAnnotation $annotation, $annotationId = null)
	{	
		if ($annotation->startTime === null)
			$annotation->startTime = 0;
		
		if($annotation->startTime < 0)
			throw new KalturaAPIException(KalturaAnnotationErrors::START_TIME_CANNOT_BE_LESS_THAN_0);
		
		if($annotationId !== null){ //update
			$dbAnnotation = AnnotationPeer::retrieveByPK($annotationId);
			if(!$dbAnnotation)
				throw new KalturaAPIException(KalturaAnnotationErrors::INVALID_OBJECT_ID, $annotationId);
				
			$dbEntry = entryPeer::retrieveByPK($dbAnnotation->getEntryId());
		}
		else //add
		{ 
			$dbEntry = entryPeer::retrieveByPK($annotation->entryId);
			if (!$dbEntry)
				throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $annotation->entryId);
		}
		
		if($dbEntry->getLengthInMsecs() < $annotation->startTime)
			throw new KalturaAPIException(KalturaAnnotationErrors::START_TIME_IS_BIGGER_THEN_ENTRY_END_TIME, $annotation->startTime, $dbEntry->getLengthInMsecs());
	}
	
	/**
	 * @param Annotation $dbAnnotation
	 * @param array $propsToSkip
	 * @return Annotation
	 */
	public function toObject($dbAnnotation = null, $propsToSkip = array())
	{
		if(is_null($dbAnnotation))
			$dbAnnotation = new Annotation();
	
		return parent::toObject($dbAnnotation, $propsToSkip);
	}
	
	public function fromObject($dbAnnotation)
	{
		parent::fromObject($dbAnnotation);
		
		if($dbAnnotation->getKuserId() !== null){
			$dbKuser = kuserPeer::retrieveByPK($dbAnnotation->getKuserId());
			$this->userId = $dbKuser->getPuserId();
		}
	}


	/**
	 * @param Annotation $dbAnnotation
	 * @param array $propsToSkip
	 * @return Annotation
	 */
	public function toInsertableObject($dbAnnotation = null, $propsToSkip = array())
	{
		
		if(is_null($dbAnnotation))
			$dbAnnotation = new Annotation();
			
		return parent::toInsertableObject($dbAnnotation, $propsToSkip);
	}
}
