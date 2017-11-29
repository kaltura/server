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
		"entryIdEqual" => "_eq_entry_id",
		"entryIdIn" => "_in_entry_id",
		"serverNodeIdEqual" => "_eq_server_node_id",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"updatedAtGreaterThanOrEqual" => "_gte_updated_at",
		"updatedAtLessThanOrEqual" => "_lte_updated_at",
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
	 * @var string
	 */
	public $entryIdEqual;

	/**
	 * @var string
	 */
	public $entryIdIn;

	/**
	 * @var int
	 */
	public $serverNodeIdEqual;

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
	public $updatedAtGreaterThanOrEqual;

	/**
	 * @var time
	 */
	public $updatedAtLessThanOrEqual;

	/**
	 * @var KalturaEntryServerNodeStatus
	 */
	public $statusEqual;

	/**
	 * @var string
	 */
	public $statusIn;

	/**
	 * @var KalturaEntryServerNodeType
	 */
	public $serverTypeEqual;
}
