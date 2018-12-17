<?php
/**
 * @package plugins.beacon
 * @subpackage api.objects
 */
abstract class KalturaBeaconSearchParams extends KalturaObject
{
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}

	/**
	 * @var string
	 */
	public $objectId;

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
		"objectId",
	);

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
		{
			$object_to_fill = new ESearchParams();
		}

		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
