<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaEdgeServerNodeBaseFilter extends KalturaDeliveryServerNodeFilter
{
	static private $map_between_objects = array
	(
		"playbackDomainLike" => "_like_playback_domain",
		"playbackDomainMultiLikeOr" => "_mlikeor_playback_domain",
		"playbackDomainMultiLikeAnd" => "_mlikeand_playback_domain",
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
	public $playbackDomainLike;

	/**
	 * @var string
	 */
	public $playbackDomainMultiLikeOr;

	/**
	 * @var string
	 */
	public $playbackDomainMultiLikeAnd;
}
