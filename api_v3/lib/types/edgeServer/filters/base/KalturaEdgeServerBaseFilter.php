<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaEdgeServerBaseFilter extends KalturaFilter
{
	static private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"updatedAtGreaterThanOrEqual" => "_gte_updated_at",
		"updatedAtLessThanOrEqual" => "_lte_updated_at",
		"nameEqual" => "_eq_name",
		"nameIn" => "_in_name",
		"systemNameEqual" => "_eq_system_name",
		"systemNameIn" => "_in_system_name",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
		"tagsLike" => "_like_tags",
		"tagsMultiLikeOr" => "_mlikeor_tags",
		"tagsMultiLikeAnd" => "_mlikeand_tags",
		"hostNameLike" => "_like_host_name",
		"hostNameMultiLikeOr" => "_mlikeor_host_name",
		"hostNameMultiLikeAnd" => "_mlikeand_host_name",
		"playbackHostNameLike" => "_like_playback_host_name",
		"playbackHostNameMultiLikeOr" => "_mlikeor_playback_host_name",
		"playbackHostNameMultiLikeAnd" => "_mlikeand_playback_host_name",
		"parentIdEqual" => "_eq_parent_id",
		"parentIdIn" => "_in_parent_id",
	);

	static private $order_by_map = array
	(
		"+createdAt" => "+created_at",
		"-createdAt" => "-created_at",
		"+updatedAt" => "+updated_at",
		"-updatedAt" => "-updated_at",
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
	public $idEqual;

	/**
	 * @var string
	 */
	public $idIn;

	/**
	 * @var time
	 */
	public $createdAtGreaterThanOrEqual;

	/**
	 * @var time
	 */
	public $createdAtLessThanOrEqual;

	/**
	 * @var time
	 */
	public $updatedAtGreaterThanOrEqual;

	/**
	 * @var time
	 */
	public $updatedAtLessThanOrEqual;

	/**
	 * @var string
	 */
	public $nameEqual;

	/**
	 * @var string
	 */
	public $nameIn;

	/**
	 * @var string
	 */
	public $systemNameEqual;

	/**
	 * @var string
	 */
	public $systemNameIn;

	/**
	 * @var KalturaEdgeServerStatus
	 */
	public $statusEqual;

	/**
	 * @var string
	 */
	public $statusIn;

	/**
	 * @var string
	 */
	public $tagsLike;

	/**
	 * @var string
	 */
	public $tagsMultiLikeOr;

	/**
	 * @var string
	 */
	public $tagsMultiLikeAnd;

	/**
	 * @var string
	 */
	public $hostNameLike;

	/**
	 * @var string
	 */
	public $hostNameMultiLikeOr;

	/**
	 * @var string
	 */
	public $hostNameMultiLikeAnd;

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

	/**
	 * @var int
	 */
	public $parentIdEqual;

	/**
	 * @var string
	 */
	public $parentIdIn;
}
