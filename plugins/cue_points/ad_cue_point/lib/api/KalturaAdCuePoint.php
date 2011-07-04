<?php
/**
 * @package plugins.adCuePoint
 * @subpackage api.objects
 */
class KalturaAdCuePoint extends KalturaCuePoint
{
	/**
	 * @var KalturaAdCuePointProviderType
	 * @filter eq,in
	 */
	public $providerType;
	
	/**
	 * @var string
	 */
	public $sourceUrl;
	
	/**
	 * @var KalturaAdType 
	 * @filter eq,in
	 */
	public $adType;
	
	/**
	 * @var string 
	 */
	public $title;
	
	/**
	 * @var int 
	 * @filter gte,lte,order
	 */
	public $endTime;

	public function __construct()
	{
		$this->type = AdCuePointPlugin::getApiValue(AdCuePointType::AD);
	}
	
	private static $map_between_objects = array
	(
		"providerType",
		"sourceUrl",
		"adType",
		"title" => "name",
		"endTime",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}
