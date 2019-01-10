<?php
/**
 * @package plugins.caption
 * @subpackage api.objects
 */
class KalturaCaptionPlaybackPluginData extends KalturaObject{

	/**
	 * @var string
	 */
	public $label;
    
	/**
	 * @var string
	 */
	public $format;

	/**
	 * @var string
	 */
	public $language;

	/**
	 * @var string
	 */
	public $webVttUrl;

	/**
	 * @var string
	 */
	public $url;


	/**
	 * @var bool
	 */
	public $isDefault;

	private static $map_between_objects = array
	(
		"format",
		"label",
		"language",
		"url",
		"webVttUrl",
		"isDefault"
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

}