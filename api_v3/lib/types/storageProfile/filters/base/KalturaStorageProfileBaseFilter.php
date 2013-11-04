<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaStorageProfileBaseFilter extends KalturaFilter
{
	static private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"updatedAtGreaterThanOrEqual" => "_gte_updated_at",
		"updatedAtLessThanOrEqual" => "_lte_updated_at",
		"partnerIdEqual" => "_eq_partner_id",
		"partnerIdIn" => "_in_partner_id",
		"systemNameEqual" => "_eq_system_name",
		"systemNameIn" => "_in_system_name",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
		"protocolEqual" => "_eq_protocol",
		"protocolIn" => "_in_protocol",
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
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual;

	/**
	 * @var int
	 */
	public $createdAtLessThanOrEqual;

	/**
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual;

	/**
	 * @var int
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
	 * @var string
	 */
	public $systemNameEqual;

	/**
	 * @var string
	 */
	public $systemNameIn;

	/**
	 * @var KalturaStorageProfileStatus
	 */
	public $statusEqual;

	/**
	 * @var string
	 */
	public $statusIn;

	/**
	 * @var KalturaStorageProfileProtocol
	 */
	public $protocolEqual;

	/**
	 * @dynamicType KalturaStorageProfileProtocol
	 * @var string
	 */
	public $protocolIn;
}
