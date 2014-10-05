<?php
/**
 * A representation of an Akamai live stream configuration
 * 
 * @package api
 * @subpackage objects
 */
class KalturaLiveStreamAkamaiConfiguration extends KalturaLiveStreamConfiguration
{
	/**
	 * @var string
	 */
	public $akamaiUser;
	
	/**
	 * @var string
	 */
	public $akamaiPassword;
	
	/**
	 * @var string
	 */
	public $akamaiStreamId;
	
	private static $mapBetweenObjects = array
	(
		"akamaiUser", "akamaiPassword", "akamaiStreamId"
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
			$dbObject = new kLiveStreamAkamaiConfiguration();
		}
		
		return parent::toObject($dbObject, $propsToSkip);
	}
}