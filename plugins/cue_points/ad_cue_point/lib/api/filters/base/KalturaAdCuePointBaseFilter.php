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
		"protocolTypeEqual" => "_eq_protocol_type",
		"protocolTypeIn" => "_in_protocol_type",
		"titleLike" => "_like_title",
		"titleMultiLikeOr" => "_mlikeor_title",
		"titleMultiLikeAnd" => "_mlikeand_title",
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
	 * @var KalturaAdProtocolType
	 */
	public $protocolTypeEqual;

	/**
	 * 
	 * 
	 * @dynamicType KalturaAdProtocolType
	 * @var string
	 */
	public $protocolTypeIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $titleLike;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $titleMultiLikeOr;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $titleMultiLikeAnd;
}
