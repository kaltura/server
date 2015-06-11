<?php
/**
 * @package plugins.quiz
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaQuestionCuePointBaseFilter extends KalturaCuePointFilter
{
	static private $map_between_objects = array
	(
		"questionLike" => "_like_question",
		"questionMultiLikeOr" => "_mlikeor_question",
		"questionMultiLikeAnd" => "_mlikeand_question",
	);

	static private $order_by_map = array
	(
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
	public $questionLike;

	/**
	 * @var string
	 */
	public $questionMultiLikeOr;

	/**
	 * @var string
	 */
	public $questionMultiLikeAnd;
}
