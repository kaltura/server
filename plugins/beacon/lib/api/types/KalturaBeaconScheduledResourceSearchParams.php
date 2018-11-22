<?php
/**
 * @package plugins.beacon
 * @subpackage api.objects
 */
class KalturaBeaconScheduledResourceSearchParams extends KalturaBeaconSearchParams
{
	/**
	 * @var KalturaBeaconSearchScheduledResourceOrderBy
	 */
	public $orderBy;

	/**
	 * @var KalturaBeaconScheduledResourceOperator
	 */
	public $searchOperator;

	private static $mapBetweenObjects = array
	(
		"orderBy",
		"searchOperator",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}

}