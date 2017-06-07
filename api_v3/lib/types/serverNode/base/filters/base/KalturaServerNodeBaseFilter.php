<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaServerNodeBaseFilter extends KalturaFilter
{
	static private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"updatedAtGreaterThanOrEqual" => "_gte_updated_at",
		"updatedAtLessThanOrEqual" => "_lte_updated_at",
		"heartbeatTimeGreaterThanOrEqual" => "_gte_heartbeat_time",
		"heartbeatTimeLessThanOrEqual" => "_lte_heartbeat_time",
		"nameEqual" => "_eq_name",
		"nameIn" => "_in_name",
		"systemNameEqual" => "_eq_system_name",
		"systemNameIn" => "_in_system_name",
		"hostNameLike" => "_like_host_name",
		"hostNameMultiLikeOr" => "_mlikeor_host_name",
		"hostNameMultiLikeAnd" => "_mlikeand_host_name",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
		"typeEqual" => "_eq_type",
		"typeIn" => "_in_type",
		"tagsLike" => "_like_tags",
		"tagsMultiLikeOr" => "_mlikeor_tags",
		"tagsMultiLikeAnd" => "_mlikeand_tags",
		"dcEqual" => "_eq_dc",
		"dcIn" => "_in_dc",
		"parentIdLike" => "_like_parent_id",
		"parentIdMultiLikeOr" => "_mlikeor_parent_id",
		"parentIdMultiLikeAnd" => "_mlikeand_parent_id",
	);

	static private $order_by_map = array
	(
		"+createdAt" => "+created_at",
		"-createdAt" => "-created_at",
		"+updatedAt" => "+updated_at",
		"-updatedAt" => "-updated_at",
		"+heartbeatTime" => "+heartbeat_time",
		"-heartbeatTime" => "-heartbeat_time",
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
	 * @var time
	 */
	public $heartbeatTimeGreaterThanOrEqual;

	/**
	 * @var time
	 */
	public $heartbeatTimeLessThanOrEqual;

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
	 * @var KalturaServerNodeStatus
	 */
	public $statusEqual;

	/**
	 * @var string
	 */
	public $statusIn;

	/**
	 * @var KalturaServerNodeType
	 */
	public $typeEqual;

	/**
	 * @dynamicType KalturaServerNodeType
	 * @var string
	 */
	public $typeIn;

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
	 * @var int
	 */
	public $dcEqual;

	/**
	 * @var string
	 */
	public $dcIn;

	/**
	 * @var string
	 */
	public $parentIdLike;

	/**
	 * @var string
	 */
	public $parentIdMultiLikeOr;

	/**
	 * @var string
	 */
	public $parentIdMultiLikeAnd;
}
