<?php
/**
 * @package plugins.scheduleBulkUpload
 * @subpackage api.objects
 */
class KalturaBulkUploadResultScheduleEvent extends KalturaBulkUploadResult
{
    /**
     * @var string
     */
    public $referenceId;
	
	/**
	 * @var string
	 */
	public $templateEntryId;
	
	/**
	 * @var KalturaScheduleEventType
	 */
	public $eventType;
	
	/**
	 * @var string
	 */
	public $title;
	
	/**
	 * @var string
	 */
	public $description;
	
	/**
	 * @var string
	 */
	public $tags;
	
	/**
	 * @var string
	 */
	public $categoryIds;
	
	/**
	 * ID of the resource specified for the new event.
	 * @var string
	 */
	public $resourceId;
	
	/**
	 * @var time
	 */
	public $startTime;
	
	/**
	 * @var int
	 */
	public $duration;
	
	/**
	 * @var time
	 */
	public $endTime;
	
	/**
	 * @var string
	 */
	public $recurrence;
	
	/**
	 * @var string
	 */
	public $coEditors;
	
	/**
	 * @var string
	 */
	public $coPublishers;
	
	/**
	 * @var string
	 */
	public $eventOrganizerId;
	
	/**
	 * @var string
	 */
	public $contentOwnerId;
	
	/**
	 * @var string
	 */
	public $templateEntryType;
    
    private static $mapBetweenObjects = array
	(
	    'referenceId',
	    'templateEntryId',
	    'eventType',
	    'title',
	    'description',
	    'tags',
	    'categoryIds',
	    'resourceId',
	    'startTime',
	    'duration',
	    'endTime',
	    'recurrence',
	    'coEditors',
	    'coPublishers',
	    'eventOrganizerId',
	    'contentOwnerId',
	    'templateEntryType',
	    
	);
	
    public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
    /* (non-PHPdoc)
     * @see KalturaBulkUploadResult::toInsertableObject()
     */
    public function toInsertableObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		return parent::toInsertableObject(new BulkUploadResultScheduleEvent(), $props_to_skip);
	}
}