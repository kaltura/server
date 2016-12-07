<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaPlaybackSource extends KalturaObject{

	/**
	 * @var string
	 */
	public $deliveryProfileId;
    
	/**
	 * @var string
	 */
	public $format;

	/**
	 * @var string
	 */
	public $priority;

	/**
	 * @var KalturaStringArray
	 */
	public $protocols;

	/**
	 * @var KalturaStringArray
	 */
	public $flavors;

	/**
	 * @var string
	 */
	public $url;

	/**
	 * @var KalturaDrmEntryPlayingPluginDataArray
	 */
	public $drm;

	private static $map_between_objects = array
	(
		"deliveryProfileId",
		"format",
		"priority",
		"protocols",
		"flavors",
		"url",
		"drm",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

}