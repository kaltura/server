<?php
/**
 * @package plugins.beacon
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaBeaconBaseFilter extends KalturaFilter
{
	static private $map_between_objects = array
	(
		"updatedAtGreaterThanOrEqual" => "_gte_updated_at",
		"updatedAtLessThanOrEqual" => "_lte_updated_at",
		"relatedObjectTypeIn" => "_in_related_object_type",
		"eventTypeIn" => "_in_event_type",
		"objectIdIn" => "_in_object_id",
	);

	static private $order_by_map = array
	(
		"+updatedAt" => "+updated_at",
		"-updatedAt" => "-updated_at",
		"+objectId" => "+object_id",
		"-objectId" => "-object_id",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), self::$order_by_map);
	}

	/**
	 * @var time
	 */
	public $updatedAtGreaterThanOrEqual;

	/**
	 * @var time
	 */
	public $updatedAtLessThanOrEqual;

	/**
	 * @dynamicType KalturaBeaconObjectTypes
	 * @var string
	 */
	public $relatedObjectTypeIn;

	/**
	 * @var string
	 */
	public $eventTypeIn;

	/**
	 * @var string
	 */
	public $objectIdIn;
}
