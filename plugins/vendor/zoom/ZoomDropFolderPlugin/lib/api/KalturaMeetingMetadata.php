<?php
/**
 * @package plugins.ZoomDropFolder
 * @subpackage api.objects
 */

class KalturaMeetingMetadata extends KalturaObject
{
	/**
	 * @var string
	 */
	public $uuid;
	
	/**
	 * @var string
	 */
	public $meetingId;
	
	/**
	 * @var string
	 */
	public $accountId;
	
	/**
	 * @var string
	 */
	public $hostId;
	
	/**
	 * @var string
	 */
	public $topic;
	
	/**
	 * @var string
	 */
	public $meetingStartTime;
	
	/**
	 * @var kRecordingType
	 */
	public $type;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)
	 */
	private static $map_between_objects = array(
		'uuid',
		'meetingId',
		'accountId',
		'hostId',
		'topic',
		'meetingStartTime',
		'type'
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (!$dbObject)
			$dbObject = new kMeetingMetadata();
		
		return parent::toObject($dbObject, $skip);
	}
	
}