<?php
/**
 * @package plugins.adCuePoint
 * @subpackage api.objects
 */
class KalturaAdCuePoint extends KalturaCuePoint
{
	/**
	 * @var KalturaAdCuePointProviderType
	 * @insertonly
	 * @filter eq,in
	 * @requiresPermission insert,update
	 */
	public $providerType;
	
	/**
	 * @var string
	 * @requiresPermission insert,update
	 */
	public $sourceUrl;
	
	/**
	 * @var KalturaAdType 
	 * @insertonly
	 * @filter eq,in
	 * @requiresPermission insert,update
	 */
	public $adType;
	
	/**
	 * @var string
	 * @requiresPermission insert,update
	 */
	public $title;
	
	/**
	 * @var int 
	 * @filter gte,lte,order
	 * @requiresPermission insert,update
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
