<?php
/**
 * @package plugins.codeCuePoint
 * @subpackage api.filters.base
 * @abstract
 */
class KalturaCodeCuePointBaseFilter extends KalturaCuePointFilter
{
	private $map_between_objects = array
	(
		"codeLike" => "_like_code",
		"codeMultiLikeOr" => "_mlikeor_code",
		"codeMultiLikeAnd" => "_mlikeand_code",
		"codeEqual" => "_eq_code",
		"codeIn" => "_in_code",
		"descriptionLike" => "_like_description",
		"descriptionMultiLikeOr" => "_mlikeor_description",
		"descriptionMultiLikeAnd" => "_mlikeand_description",
	);

	private $order_by_map = array
	(
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
	public $codeLike;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $codeMultiLikeOr;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $codeMultiLikeAnd;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $codeEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $codeIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $descriptionLike;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $descriptionMultiLikeOr;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $descriptionMultiLikeAnd;
}
