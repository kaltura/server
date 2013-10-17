<?php
/**
 * @package api
 * @subpackage objects
 */
abstract class KalturaLiveEntry extends KalturaMediaEntry
{
	/**
	 * The message to be presented when the stream is offline
	 * 
	 * @var string
	 */
	public $offlineMessage;
	
	/**
	 * DVR Status Enabled/Disabled
	 * @var KalturaDVRStatus
	 * @insertonly
	 */
	public $dvrStatus;
	
	/**
	 * Window of time which the DVR allows for backwards scrubbing (in minutes)
	 * @var int
	 * @insertonly
	 */
	public $dvrWindow;
	
	/**
	 * Array of key value protocol->live stream url objects
	 * @var KalturaLiveStreamConfigurationArray
	 */
	public $liveStreamConfigurations;
	
	private static $map_between_objects = array
	(
		"offlineMessage",
	    "dvrStatus",
	    "dvrWindow",
		"liveStreamConfigurations",
	);
	
	/* (non-PHPdoc)
	 * @see KalturaMediaEntry::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}
