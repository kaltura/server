<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaDeliveryServerNodeBaseFilter extends KalturaServerNodeFilter
{
	static private $map_between_objects = array
	(
		"playbackHostNameLike" => "_like_playback_host_name",
		"playbackHostNameMultiLikeOr" => "_mlikeor_playback_host_name",
		"playbackHostNameMultiLikeAnd" => "_mlikeand_playback_host_name",
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
	public $playbackHostNameLike;

	/**
	 * @var string
	 */
	public $playbackHostNameMultiLikeOr;

	/**
	 * @var string
	 */
	public $playbackHostNameMultiLikeAnd;
}
