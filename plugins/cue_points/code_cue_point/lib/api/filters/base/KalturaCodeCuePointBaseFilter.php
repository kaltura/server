<?php
/**
 * @package plugins.codeCuePoint
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaCodeCuePointBaseFilter extends KalturaCuePointFilter
{
	static private $map_between_objects = array
	(
		"codeLike" => "_like_code",
		"codeMultiLikeOr" => "_mlikeor_code",
		"codeMultiLikeAnd" => "_mlikeand_code",
		"codeEqual" => "_eq_code",
		"codeIn" => "_in_code",
		"descriptionLike" => "_like_description",
		"descriptionMultiLikeOr" => "_mlikeor_description",
		"descriptionMultiLikeAnd" => "_mlikeand_description",
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
	 * @var string
	 */
	public $codeLike;

	/**
	 * @var string
	 */
	public $codeMultiLikeOr;

	/**
	 * @var string
	 */
	public $codeMultiLikeAnd;

	/**
	 * @var string
	 */
	public $codeEqual;

	/**
	 * @var string
	 */
	public $codeIn;

	/**
	 * @var string
	 */
	public $descriptionLike;

	/**
	 * @var string
	 */
	public $descriptionMultiLikeOr;

	/**
	 * @var string
	 */
	public $descriptionMultiLikeAnd;

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
