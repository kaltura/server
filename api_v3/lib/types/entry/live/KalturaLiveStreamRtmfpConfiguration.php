<?php
/**
 * A representation of a live stream configuration
 * 
 * @package api
 * @subpackage objects
 */
class KalturaLiveStreamRtmfpConfiguration extends KalturaLiveStreamConfiguration
{
	/**
	 * Groupspec for the RTMFP stream
	 * @var string
	 */
	public $groupspec;
	
	/**
	 * Multicast stream name
	 * @var string
	 */
	public $multicastStreamName;
	

	
	private static $mapBetweenObjects = array
	(
		"groupspec", "multicastStreamName"
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($dbObject = null, $propsToSkip = array())
	{
		if (!$dbObject)
		{
			$dbObject = new kLiveStreamRtmfpConfiguration();
		}
		
		return parent::toObject($dbObject, $propsToSkip);
	}
	
}