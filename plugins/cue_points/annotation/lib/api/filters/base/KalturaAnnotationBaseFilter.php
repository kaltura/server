<?php
/**
 * @package plugins.annotation
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaAnnotationBaseFilter extends KalturaCuePointFilter
{
	static private $map_between_objects = array
	(
		"parentIdEqual" => "_eq_parent_id",
		"parentIdIn" => "_in_parent_id",
		"textLike" => "_like_text",
		"textMultiLikeOr" => "_mlikeor_text",
		"textMultiLikeAnd" => "_mlikeand_text",
		"endTimeGreaterThanOrEqual" => "_gte_end_time",
		"endTimeLessThanOrEqual" => "_lte_end_time",
		"durationGreaterThanOrEqual" => "_gte_duration",
		"durationLessThanOrEqual" => "_lte_duration",
		"isPublicEqual" => "_eq_is_public",
	);

	static private $order_by_map = array
	(
		"+endTime" => "+end_time",
		"-endTime" => "-end_time",
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
	 * @var string
	 */
	public $parentIdEqual;

	/**
	 * @var string
	 */
	public $parentIdIn;

	/**
	 * @var string
	 */
	public $textLike;

	/**
	 * @var string
	 */
	public $textMultiLikeOr;

	/**
	 * @var string
	 */
	public $textMultiLikeAnd;

	/**
	 * @var int
	 */
	public $endTimeGreaterThanOrEqual;

	/**
	 * @var int
	 */
	public $endTimeLessThanOrEqual;

	/**
	 * @var int
	 */
	public $durationGreaterThanOrEqual;

	/**
	 * @var int
	 */
	public $durationLessThanOrEqual;

	/**
	 * @var KalturaNullableBoolean
	 */
	public $isPublicEqual;
}
