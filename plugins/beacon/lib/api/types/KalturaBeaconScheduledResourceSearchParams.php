<?php
/**
 * @package plugins.beacon
 * @subpackage api.objects
 */
class KalturaBeaconScheduledResourceSearchParams extends KalturaBeaconSearchParams
{
	private static $mapBetweenObjects = array();

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}

}