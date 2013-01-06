<?php
/**
 * @package plugins.adCuePoint
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaAdCuePointBaseFilter extends KalturaCuePointFilter
{
	static private $map_between_objects = array
	(
		"protocolTypeEqual" => "_eq_protocol_type",
		"protocolTypeIn" => "_in_protocol_type",
		"titleLike" => "_like_title",
		"titleMultiLikeOr" => "_mlikeor_title",
		"titleMultiLikeAnd" => "_mlikeand_title",
		"endTimeGreaterThanOrEqual" => "_gte_end_time",
		"endTimeLessThanOrEqual" => "_lte_end_time",
		"durationGreaterThanOrEqual" => "_gte_duration",
		"durationLessThanOrEqual" => "_lte_duration",
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
	 * @var KalturaAdProtocolType
	 */
	public $protocolTypeEqual;

	/**
	 * @dynamicType KalturaAdProtocolType
	 * @var string
	 */
	public $protocolTypeIn;

	/**
	 * @var string
	 */
	public $titleLike;

	/**
	 * @var string
	 */
	public $titleMultiLikeOr;

	/**
	 * @var string
	 */
	public $titleMultiLikeAnd;

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
}
