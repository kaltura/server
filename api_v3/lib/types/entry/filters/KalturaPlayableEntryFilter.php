<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaPlayableEntryFilter extends KalturaBaseEntryFilter
{
	private $map_between_objects = array
	(
		"durationLessThan" => "_lt_duration",
		"durationGreaterThan" => "_gt_duration",
		"durationLessThanOrEqual" => "_lte_duration",
		"durationGreaterThanOrEqual" => "_gte_duration",
		"msDurationLessThan" => "_lt_ms_duration",
		"msDurationGreaterThan" => "_gt_ms_duration",
		"msDurationLessThanOrEqual" => "_lte_ms_duration",
		"msDurationGreaterThanOrEqual" => "_gte_ms_duration",
		"durationTypeMatchOr" => "_matchor_duration_type",
	);

	private $order_by_map = array
	(
		"+plays" => "+plays",
		"-plays" => "-plays",
		"+views" => "+views",
		"-views" => "-views",
		"+duration" => "+duration",
		"-duration" => "-duration",
		"+msDuration" => "+ms_duration",
		"-msDuration" => "-ms_duration",
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
	public $durationLessThan;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $durationGreaterThan;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $durationLessThanOrEqual;

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
	public $msDurationLessThan;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $msDurationGreaterThan;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $msDurationLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $msDurationGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $durationTypeMatchOr;
}
