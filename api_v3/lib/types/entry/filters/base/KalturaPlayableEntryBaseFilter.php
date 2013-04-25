<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaPlayableEntryBaseFilter extends KalturaBaseEntryFilter
{
	static private $map_between_objects = array
	(
		"durationLessThan" => "_lt_duration",
		"durationGreaterThan" => "_gt_duration",
		"durationLessThanOrEqual" => "_lte_duration",
		"durationGreaterThanOrEqual" => "_gte_duration",
		"durationTypeMatchOr" => "_matchor_duration_type",
	);

	static private $order_by_map = array
	(
		"+plays" => "+plays",
		"-plays" => "-plays",
		"+views" => "+views",
		"-views" => "-views",
		"+duration" => "+duration",
		"-duration" => "-duration",
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
	public $durationLessThan;

	/**
	 * @var int
	 */
	public $durationGreaterThan;

	/**
	 * @var int
	 */
	public $durationLessThanOrEqual;

	/**
	 * @var int
	 */
	public $durationGreaterThanOrEqual;

	/**
	 * @var string
	 */
	public $durationTypeMatchOr;
}
