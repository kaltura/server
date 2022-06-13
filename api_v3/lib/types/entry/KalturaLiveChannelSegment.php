<?php
/**
 * @package api
 * @subpackage objects
 * @relatedService LiveChannelSegmentService
 */
class KalturaLiveChannelSegment extends KalturaObject implements IRelatedFilterable
{
	/**
	 * Unique identifier
	 * 
	 * @var bigint
	 * @readonly
	 */
	public $id;
	
	/**
	 * @var int
	 * @readonly
	 */
	public $partnerId;
	
	/**
	 * Segment creation date as Unix timestamp (In seconds)
	 * 
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;
	
	/**
	 * Segment update date as Unix timestamp (In seconds)
	 * 
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $updatedAt;
	
	/**
	 * Segment name
	 * 
	 * @var string
	 */
	public $name;
	
	/**
	 * Segment description
	 * 
	 * @var string
	 */
	public $description;
	
	/**
	 * Segment tags
	 * 
	 * @var string
	 */
	public $tags;
	
	/**
	 * Segment could be associated with the main stream, as additional stream or as overlay
	 * 
	 * @var KalturaLiveChannelSegmentType
	 */
	public $type;
	
	/**
	 * @var KalturaLiveChannelSegmentStatus
	 * @readonly
	 * @filter eq,in
	 */
	public $status;
	
	/**
	 * Live channel id
	 * 
	 * @var string
	 * @filter eq,in
	 */
	public $channelId;
	
	/**
	 * Entry id to be played
	 * 
	 * @var string
	 */
	public $entryId;
	
	/**
	 * Segment start time trigger type
	 * 
	 * @var KalturaLiveChannelSegmentTriggerType
	 */
	public $triggerType;
	
	/**
	 * Live channel segment that the trigger relates to
	 * 
	 * @var bigint
	 */
	public $triggerSegmentId;
	
	/**
	 * Segment play start time, in mili-seconds, according to trigger type
	 * 
	 * @var float
	 * @filter gte,lte,order
	 */
	public $startTime;
	
	/**
	 * Segment play duration time, in mili-seconds
	 * 
	 * @var float
	 */
	public $duration;
	
	private static $map_between_objects = array
	(
		'id',
		'partnerId',
		'createdAt',
		'updatedAt',
		'name',
		'description',
		'tags',
		'type',
		'status',
		'channelId',
		'entryId',
		'triggerType',
		'triggerSegmentId',
		'startTime',
		'duration',
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($liveChannelSegment = null, $propsToSkip = array())
	{
		if(!$liveChannelSegment)
			$liveChannelSegment = new LiveChannelSegment();
			
		return parent::toObject($liveChannelSegment, $propsToSkip);
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
}
