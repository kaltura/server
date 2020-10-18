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
	 * source format according to delivery profile streamer type (applehttp, mpegdash etc.)
	 * @var string
	 */
	public $format;

	/**
	 * comma separated string according to deliveryProfile media protocols ('http,https' etc.)
	 * @var string
	 */
	public $protocols;

	/**
	 * comma separated string of flavor ids
	 * @var string
	 */
	public $flavorIds;

	/**
	 * @var string
	 */
	public $url;

	/**
	 * drm data object containing relevant license url ,scheme name and certificate
	 * @var KalturaDrmPlaybackPluginDataArray
	 */
	public $drm;

	/**
	 * @var KalturaKeyValueArray
	 */
	public $bumperData;

	private static $map_between_objects = array
	(
		"deliveryProfileId",
		"format",
		"protocols",
		"flavorIds",
		"url",
		"drm",
		"bumperData",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

}