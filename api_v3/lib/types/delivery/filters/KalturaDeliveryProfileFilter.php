<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaDeliveryProfileFilter extends KalturaDeliveryProfileBaseFilter
{
	/**
	 * @var KalturaNullableBoolean
	 */
	public $isLive;
	
	static private $map_between_objects = array
	(
		"isLive" => "_is_live",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new DeliveryProfileFilter();
	}
}
