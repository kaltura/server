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
	 */
	public $text;
	
	/**
	 * @var int 
	 * @filter gte,lte,order
	 */
	public $endTime;

	public function __construct()
	{
		$this->type = AnnotationPlugin::getApiValue(AnnotationCuePointType::ANNOTATION);
	}
	
	private static $map_between_objects = array
	(
		"parentId",
		"text",
		"endTime",
	);
	
	/* (non-PHPdoc)
	 * @see KalturaCuePoint::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/*
	 * @param string $cuePointId
	 * @throw KalturaAPIException - when parent annotation doesn't belong to the same entry, or parent annotation
	 * doesn't belong to the same entry
	 */
	public function validateParentId($cuePointId = null)
	{
		if ($this->parentId === null || $this->parentId === "" || $this->parentId === "0")
			$this->parentId = 0;
			
		if ($this->parentId !== 0)
		{
			$dbParentCuePoint = CuePointPeer::retrieveByPK($this->parentId);
			if (!$dbParentCuePoint)
				throw new KalturaAPIException(KalturaAnnotationErrors::PARENT_ANNOTATION_NOT_FOUND, $this->parentId);
			
			if($cuePointId !== null){ // update
				$dbCuePoint = CuePointPeer::retrieveByPK($cuePointId);
				if(!$dbCuePoint)
					throw new KalturaAPIException(KalturaAnnotationErrors::INVALID_OBJECT_ID, $cuePointId);
				 
				if($dbCuePoint->isDescendant($this->parentId))
					throw new KalturaAPIException(KalturaAnnotationErrors::PARENT_ANNOTATION_IS_DESCENDANT, $this->parentId, $dbCuePoint->getId());
			}
			
			if ($dbParentCuePoint->getEntryId() != $this->entryId)
				throw new KalturaAPIException(KalturaAnnotationErrors::PARENT_ANNOTATION_DO_NOT_BELONG_TO_THE_SAME_ENTRY);
		}
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toInsertableObject()
	 */
	public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
	{
		if(is_null($object_to_fill))
			$object_to_fill = new Annotation();
			
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaCuePoint::validateForInsert()
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		parent::validateForInsert($propertiesToSkip);
		
		$this->validateParentId();
	}
	
	/* (non-PHPdoc)
	 * @see KalturaCuePoint::validateForUpdate()
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		if($this->parentId !== null)
			$this->validateParentId($sourceObject->getId());
			
		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}
}
