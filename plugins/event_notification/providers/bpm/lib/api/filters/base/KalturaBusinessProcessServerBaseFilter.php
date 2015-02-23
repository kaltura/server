<?php
/**
 * @package plugins.businessProcessNotification
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaBusinessProcessServerBaseFilter extends KalturaFilter
{
	static private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"idNotIn" => "_notin_id",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"updatedAtGreaterThanOrEqual" => "_gte_updated_at",
		"updatedAtLessThanOrEqual" => "_lte_updated_at",
		"partnerIdEqual" => "_eq_partner_id",
		"partnerIdIn" => "_in_partner_id",
		"statusEqual" => "_eq_status",
		"statusNotEqual" => "_not_status",
		"statusIn" => "_in_status",
		"statusNotIn" => "_notin_status",
		"typeEqual" => "_eq_type",
		"typeIn" => "_in_type",
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
	 * @var int
	 */
	public $partnerIdEqual;

	/**
	 * @var string
	 */
	public $partnerIdIn;

	/**
	 * @var KalturaBusinessProcessServerStatus
	 */
	public $statusEqual;

	/**
	 * @var KalturaBusinessProcessServerStatus
	 */
	public $statusNotEqual;

	/**
	 * @dynamicType KalturaBusinessProcessServerStatus
	 * @var string
	 */
	public $statusIn;

	/**
	 * @dynamicType KalturaBusinessProcessServerStatus
	 * @var string
	 */
	public $statusNotIn;

	/**
	 * @var KalturaBusinessProcessProvider
	 */
	public $typeEqual;

	/**
	 * @dynamicType KalturaBusinessProcessProvider
	 * @var string
	 */
	public $typeIn;
}
