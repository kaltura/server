<?php
/**
 * @package plugins.beacon
 * @subpackage api.objects
 */
class KalturaBeaconScheduledResourceSearchParams extends KalturaBeaconSearchParams
{
	/**
	 * @var KalturaBeaconScheduledResourceOperator
	 */
	public $searchOperator;

	/**
	 * @var KalturaBeaconSearchScheduledResourceOrderBy
	 */
	public $orderBy;

	private static $mapBetweenObjects = array
	(
		"searchOperator",
		"orderBy",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
		{
			$object_to_fill = new ESearchParams();
		}

		self::validateSearchOperator($this->searchOperator);

		return parent::toObject($object_to_fill, $props_to_skip);
	}

}