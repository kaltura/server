<?php
/**
 * A representation of a live stream configuration
 * 
 * @package api
 * @subpackage objects
 */
class KalturaLiveStreamConfiguration extends KalturaObject
{
	/**
	 * @var KalturaPlaybackProtocol
	 */
	public $protocol;
	
	/**
	 * @var string
	 */
	public $url;
	
	/**
	 * @var string
	 * @deprecated
	 */
	public $publishUrl;
	
	/**
	 * @var string
	 * @deprecated
	 */
	public $backupUrl;
	
	/**
	 * @var string
	 */
	public $streamName;
	
	private static $mapBetweenObjects = array
	(
		"protocol", "url", "publishUrl", "backupUrl", "streamName",
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
			$dbObject = new kLiveStreamConfiguration();
		}
		
		return parent::toObject($dbObject, $propsToSkip);
	}

	
}