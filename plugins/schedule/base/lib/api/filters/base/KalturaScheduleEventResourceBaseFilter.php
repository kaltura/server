<?php
/**
 * @package plugins.schedule
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaScheduleEventResourceBaseFilter extends KalturaFilter
{
	static private $map_between_objects = array
	(
		"eventIdEqual" => "_eq_event_id",
		"eventIdIn" => "_in_event_id",
		"resourceIdEqual" => "_eq_resource_id",
		"resourceIdIn" => "_in_resource_id",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"updatedAtGreaterThanOrEqual" => "_gte_updated_at",
		"updatedAtLessThanOrEqual" => "_lte_updated_at",
	);

	static private $order_by_map = array
	(
		"+createdAt" => "+created_at",
		"-createdAt" => "-created_at",
		"+updatedAt" => "+updated_at",
		"-updatedAt" => "-updated_at",
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
	 * @var int
	 */
	public $eventIdEqual;

	/**
	 * @var string
	 */
	public $eventIdIn;

	/**
	 * @var int
	 */
	public $resourceIdEqual;

	/**
	 * @var string
	 */
	public $resourceIdIn;

	/**
	 * @var time
	 */
	public $createdAtGreaterThanOrEqual;

	/**
	 * @var time
	 */
	public $createdAtLessThanOrEqual;

	/**
	 * @var time
	 */
	public $updatedAtGreaterThanOrEqual;

	/**
	 * @var time
	 */
	public $updatedAtLessThanOrEqual;
}
