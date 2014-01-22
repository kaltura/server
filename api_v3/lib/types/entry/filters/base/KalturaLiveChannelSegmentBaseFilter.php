<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaLiveChannelSegmentBaseFilter extends KalturaFilter
{
	static private $map_between_objects = array
	(
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"updatedAtGreaterThanOrEqual" => "_gte_updated_at",
		"updatedAtLessThanOrEqual" => "_lte_updated_at",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
		"channelIdEqual" => "_eq_channel_id",
		"channelIdIn" => "_in_channel_id",
		"startTimeGreaterThanOrEqual" => "_gte_start_time",
		"startTimeLessThanOrEqual" => "_lte_start_time",
	);

	static private $order_by_map = array
	(
		"+createdAt" => "+created_at",
		"-createdAt" => "-created_at",
		"+updatedAt" => "+updated_at",
		"-updatedAt" => "-updated_at",
		"+startTime" => "+start_time",
		"-startTime" => "-start_time",
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
	public $createdAtGreaterThanOrEqual;

	/**
	 * @var int
	 */
	public $createdAtLessThanOrEqual;

	/**
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual;

	/**
	 * @var int
	 */
	public $updatedAtLessThanOrEqual;

	/**
	 * @var KalturaLiveChannelSegmentStatus
	 */
	public $statusEqual;

	/**
	 * @dynamicType KalturaLiveChannelSegmentStatus
	 * @var string
	 */
	public $statusIn;

	/**
	 * @var string
	 */
	public $channelIdEqual;

	/**
	 * @var string
	 */
	public $channelIdIn;

	/**
	 * @var float
	 */
	public $startTimeGreaterThanOrEqual;

	/**
	 * @var float
	 */
	public $startTimeLessThanOrEqual;
}
