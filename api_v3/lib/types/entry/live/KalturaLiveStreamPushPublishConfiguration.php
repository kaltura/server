<?php
/**
 * Basic push-publish configuration for Kaltura live stream entry
 * @package api
 * @subpackage objects
 *
 */
class KalturaLiveStreamPushPublishConfiguration extends KalturaObject
{
	/**
	 * @var string
	 */
	public $publishUrl;
	
	/**
	 * @var string
	 */
	public $backupPublishUrl;
	
	/**
	 * @var KalturaPlaybackProtocol
	 */
	public $protocol;
	
	/**
	 * @var string
	 */
	public $port;
	
	private static $mapBetweenObjects = array
	(
		"publishUrl", "backupPublishUrl" , "protocol" , "port",
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
			$dbObject = new kLiveStreamPushPublishConfiguration();
		}
		
		return parent::toObject($dbObject, $propsToSkip);
	}
	
	public static function getInstance ($protocol)
	{
		switch ($protocol)
		{
			case PlaybackProtocol::RTMP:
				return new KalturaLiveStreamPushPublishRTMPConfiguration();
			default:
				return new KalturaLiveStreamPushPublishConfiguration();
		}
	}
}