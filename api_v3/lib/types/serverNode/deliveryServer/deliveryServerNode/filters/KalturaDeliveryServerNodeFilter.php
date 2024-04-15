<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaDeliveryServerNodeFilter extends KalturaDeliveryServerNodeBaseFilter
{
	static private $map_between_objects = array(
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}
