<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaEntryServerNodeBaseFilter extends KalturaRelatedFilter
{
	static private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"idNotIn" => "_notin_id",
		"entryIdEqual" => "_eq_entry_id",
		"entryIdIn" => "_in_entry_id",
		"entryIdNotIn" => "_notin_entry_id",
		"serverNodeIdEqual" => "_eq_server_node_id",
		"serverNodeIdIn" => "_in_server_node_id",
		"serverNodeIdNotIn" => "_notin_server_node_id",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"updatedAtLessThanOrEqual" => "_lte_updated_at",
		"updatedAtGreaterThanOrEqual" => "_gte_updated_at",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
		"serverTypeEqual" => "_eq_server_type",
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
	 * @var string
	 */
	public $idNotIn;

	/**
	 * @var string
	 */
	public $entryIdEqual;

	/**
	 * @var string
	 */
	public $entryIdIn;

	/**
	 * @var string
	 */
	public $entryIdNotIn;

	/**
	 * @var int
	 */
	public $serverNodeIdEqual;

	/**
	 * @var string
	 */
	public $serverNodeIdIn;

	/**
	 * @var string
	 */
	public $serverNodeIdNotIn;

	/**
	 * @var time
	 */
	public $createdAtLessThanOrEqual;

	/**
	 * @var time
	 */
	public $createdAtGreaterThanOrEqual;

	/**
	 * @var time
	 */
	public $updatedAtLessThanOrEqual;

	/**
	 * @var time
	 */
	public $updatedAtGreaterThanOrEqual;

	/**
	 * @var KalturaEntryServerNodeStatus
	 */
	public $statusEqual;
	
	/**
	 * @var KalturaEntryServerNodeStatus
	 */
	public $statusIn;

	/**
	 * @var KalturaEntryServerNodeType
	 */
	public $serverTypeEqual;
}
