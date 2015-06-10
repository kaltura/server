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
	 * Groupspec with authorizations for the RTMFP stream
	 * @var string
	 */
	public $groupspecWithAuth;
	
	/**
	 * Groupspec without authorizations for the RTMFP stream
	 * @var string
	 */
	public $groupspecWithoutAuth;
	
	/**
	 * Type of RTMFP used
	 * @var int
	 */
	public $rtmfpType;
	
	/**
	 * Multicast address
	 * @var string
	 */
	public $multicastAddress;
	
	private static $mapBetweenObjects = array
	(
		"groupspecWithAuth", "groupspecWithoutAuth", "rtmfpType", "multicastAddress"
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