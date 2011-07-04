<?php
/**
 * @package plugins.annotation
 * @subpackage api.filters.base
 * @abstract
 */
class KalturaAnnotationBaseFilter extends KalturaCuePointFilter
{
	private $map_between_objects = array
	(
		"parentIdEqual" => "_eq_parent_id",
		"parentIdIn" => "_in_parent_id",
		"endTimeGreaterThanOrEqual" => "_gte_end_time",
		"endTimeLessThanOrEqual" => "_lte_end_time",
	);

	private $order_by_map = array
	(
		"+endTime" => "+end_time",
		"-endTime" => "-end_time",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), $this->map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), $this->order_by_map);
	}

	/**
	 * 
	 * 
	 * @var string
	 */
	public $parentIdEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $parentIdIn;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $endTimeGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $endTimeLessThanOrEqual;
}
