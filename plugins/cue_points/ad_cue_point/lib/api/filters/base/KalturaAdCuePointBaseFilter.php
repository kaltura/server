<?php
/**
 * @package plugins.adCuePoint
 * @subpackage api.filters.base
 * @abstract
 */
class KalturaAdCuePointBaseFilter extends KalturaCuePointFilter
{
	private $map_between_objects = array
	(
		"endTimeGreaterThanOrEqual" => "_gte_end_time",
		"endTimeLessThanOrEqual" => "_lte_end_time",
		"durationGreaterThanOrEqual" => "_gte_duration",
		"durationLessThanOrEqual" => "_lte_duration",
	);

	private $order_by_map = array
	(
		"+endTime" => "+end_time",
		"-endTime" => "-end_time",
		"+duration" => "+duration",
		"-duration" => "-duration",
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
	 * @var int
	 */
	public $endTimeGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $endTimeLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $durationGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $durationLessThanOrEqual;
}
